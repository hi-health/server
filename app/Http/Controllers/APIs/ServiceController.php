<?php

namespace App\Http\Controllers\APIs;

use App\Events\MemberServiceCompletedEvent;
use App\Http\Controllers\Controller;
use App\Mail\ServiceExportedByDoctor;
use App\Mail\InvoiceExportedByPayment;
use App\Mail\ServicePlanExportedById;
use App\MemberRequest;
use App\MemberRequestDoctor;
use App\PaymentHistory;
use App\Service;
use App\ServicePlan;
use App\ServicePlanVideo;
use App\Services\Pay2GoInvoice;
use App\Services\Facades\Pay2GoCancel;
use App\Traits\SettingUtility;
use App\Traits\SlackNotify;
use App\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Log;

class ServiceController extends Controller
{
    use SlackNotify, SettingUtility;

    public function getById(Request $request, $service_id)
    {
        $service = Service
            ::with('member', 'doctor')
            ->where('id', $service_id)
            ->first();
        if (!$service) {
            $doctor_id = $request->get('doctor_id');
            if ($doctor_id and $service->opened_at === null) {
                $service->opened_at = Carbon::now();
                $service->save();
            }

            return response()->json(null, 404);
        }

        return response()->json($service);
    }

    public function getUploadedVideo(Request $request, $service_id)
    {
        $service = Service::where('id', $service_id)
            ->first();\Log::info($service_id);
        if (!$service) {
            return response()->json(null, 404);
        }
        $arr_video = [];
        foreach($service->plans as $plan){
            foreach($plan['videos'] as $videos){ 
                $arr_video[] = $videos; //要檢查video欄位
                $attribute_video[] = $videos->video;
                $unique_video = array_unique($attribute_video);
                foreach($arr_video as $key=>$value){
                    if(isset($unique_video[$key])){
                        continue;
                    } else{
                        unset($arr_video[$key]);
                    } 
                }
            }
        }   
        return response()->json(array_values($arr_video));
    }

    public function getInvoice($service_id)
    {
        $service = Service
            ::with('member', 'doctor')
            ->where('id', $service_id)
            ->first();
        if (!$service) {
            return response()->json(null, 404);
        }

        return response()->json([
            'resource' => $service->invoice ? url($service->invoice) : url('invoice_none.png'),
        ]);
    }

    public function updateOrCreateInvoice(Request $request, $service_id)
    {
        $this->validate($request, [
            //'invoice' => ['image', 'max:1024'],
        ]);

        $pay2go_invoice_response = (new Pay2GoInvoice)->sendInvoiceRequest($service_id);
        $service = Service
            ::with('member', 'doctor')
            ->where('id', $service_id)
            ->first();
        if (!$service) {
            return response()->json(null, 404);
        }    
        if(json_decode($pay2go_invoice_response['web_info'])->Status === 'SUCCESS'){
            $service->invoice = $pay2go_invoice_response['web_info'];
            $service->save();
        }

        //send email
        $new_service = Service::with('member', 'doctor')->where('id', $service_id)->where('payment_status',3)->first();
        if (!$new_service) {
            return response()->json(null, 404);
        }
        $email = $service->member->email;
        Mail::to($email)->send(
            new InvoiceExportedByPayment(json_decode((json_decode($new_service->invoice)->Result)))
        );
        $this->slackNotify('發票資訊輸出信件已寄出給:'.$email);

        return response()->json($pay2go_invoice_response);
    }

    private function updateOrCreateInvoice_private($service_id)
    {
        $pay2go_invoice_response = (new Pay2GoInvoice)->sendInvoiceRequest($service_id);
        $service = Service
            ::with('member', 'doctor')
            ->where('id', $service_id)
            ->first();
        if (!$service) {
            return response()->json(null, 404);
        }    
        if(json_decode($pay2go_invoice_response['web_info'])->Status === 'SUCCESS'){
            $service->invoice = $pay2go_invoice_response['web_info'];
            $service->save();
        }

        //send email
        $new_service = Service::with('member', 'doctor')->where('id', $service_id)->where('payment_status',3)->first();
        if (!$new_service) {
            return response()->json(null, 404);
        }
        $email = $service->member->email;
        try {
            Mail::to($email)->send(
                new InvoiceExportedByPayment(json_decode((json_decode($new_service->invoice)->Result)))
            );
            $this->slackNotify('發票資訊輸出信件已寄出給:'.$email);
            return true;
        } catch (Exception $exception) {
            return $exception;
        }
        
    }

    public function invoiceOrCancelPayment(Request $request, $service_id){
        Log::info('invoiceOrCancelPayment:' . $service_id , $request -> input());
        $this->validate($request, [
            'accept' => ['required', 'in:1,2'],
        ]);
        $service = Service::where('id', $service_id)->where('payment_status', 1)->first();
        if (!$service) {
            return response()->json(null, 404);
        }

        $accept = $request->input('accept');

        if ($accept == 2) {
            
            $this->slackNotify('服務編號：{order_number}{br}治療時間只有{current_treatment_time}分鐘，開始進行取消信用卡授權...', [
                '{order_number}' => $service->order_number,
                '{current_treatment_time}' => $service->current_treatment_time,
                ]);
            $result = Pay2GoCancel::setOrderNumber($service->order_number)->setAmount($service->charge_amount)->send();
            
            if ($result and $result->success) {
                $service->payment_status = '2';
                $service->save();
                $service->paymentHistory()->save(
                    new PaymentHistory([
                        'data' => $result->rawdata,
                    ])
                );
            }

            return response()->json(["service"=>$service, "email"=>null, "confirm_status"=>$service->payment_status]);
        } else {
            // $accept == 1
            //這邊把stopped_at填進去，維持第一版的服務完成邏輯
            $service->stopped_at = Carbon::now();
            $service->payment_status = '3';
            $service->save();
            event(
                new MemberServiceCompletedEvent($service)
            );
            $member_request_model = MemberRequest::where('members_id', $service->members_id);
            $member_requests = $member_request_model->get();
            $member_request_model->forceDelete();
            $this->slackNotify('服務完成，清除會員('.$service->members_id.')的需求接單，共'.$member_requests->count().'筆');

            //0927應該補上繼承之前課表的功能
            $before_service = Service::where('members_id',$service->members_id)
                            ->where('doctors_id',$service->doctors_id)
                            ->where('payment_status', '3')
                            ->where('order_number','!=',$service->order_number)
                            ->get()
                            ->last();
            
            if($before_service){
                try{
                    
                    $Service_plan = ServicePlan::where('services_id', $before_service->id)
                                    ->get();
                    foreach($Service_plan as $plan){
                        error_log('aaaaa1');
                        $extend_plan = $plan->replicate();
                        $extend_plan->services_id = $service->id;
                        $extend_plan->push();

                        $ServicePlanVideo = ServicePlanVideo::where('service_plans_id', $plan->id)
                                            ->get();
                        foreach($ServicePlanVideo as $video){
                            error_log('aaaaa2');
                            $extend_video = $video->replicate();
                            $extend_video->service_plans_id = $extend_plan->id;
                            $extend_video->push();
                        }
                    }
                } catch(Exception $e){
                    return $e;
                }
            }
            //0920應該補上刪除之前該member的所有service (無論doctor或是payment_status)
            $member_services = Service::where('members_id',$service->members_id)->whereNotIn('id',[$service_id])->delete();
            $member_service_plan = ServicePlan::whereNotIn('services_id',[$service_id])->delete();

            $isEmailSuccess = $this->updateOrCreateInvoice_private($service_id);
            return response()->json(["service"=>$service, "email"=>$isEmailSuccess, "confirm_status"=>$service->payment_status]);
        }

        
    }

    public function getHistoryByDoctor(Request $request, $doctor_id)
    {
        $doctor = User::withDoctor($doctor_id)
            ->first();
        if (!$doctor) {
            return response()->json(null, 404);
        }
        $per_page = $request->get('per_page', 20);
        $pagination = Service::withTrashed()
            ->where('doctors_id', $doctor_id)
            ->where('payment_status', 3)
            ->whereNotNull('paid_at')
            ->whereNotNull('started_at')
            ->whereNotNull('stopped_at')
            ->orderBy('paid_at', 'DESC')
            ->paginate($per_page)
            ->toArray();
        $total_amount = Service::withTrashed()
            ->where('doctors_id', $doctor_id)
            ->where('payment_status', 3)
            ->whereNotNull('paid_at')
            ->whereNotNull('started_at')
            ->whereNotNull('stopped_at')
            ->orderBy('paid_at', 'DESC')
            ->where('created_at', '>', date('Y-m-01'))
            ->sum('charge_amount');
        $total_amount = round(intval($total_amount) / 1.05);
        $pagination['total_amount'] = $total_amount;
        $pagination['income_percentage'] = floatval($this->getSetting('income_percentage', 0.7));
        $pagination['income_amount'] = round($total_amount * $pagination['income_percentage']);
        $pagination['membership_fee'] = intval($this->getSetting('membership_fee', 1000));

        return response()->json($pagination);
    }

    public function getHistoryByMember(Request $request, $member_id)
    {
        $member = User::withMember($member_id)
            ->first();
        if (!$member) {
            return response()->json(null, 404);
        }
        $per_page = $request->get('per_page', 20);
        $pagination = Service::withTrashed()
            ->where('members_id', $member_id)
            ->where('payment_status', 3)
            ->whereNotNull('paid_at')
            ->whereNotNull('started_at')
            ->whereNotNull('stopped_at')
            ->orderBy('paid_at', 'DESC')
            ->paginate($per_page);

        return response()->json($pagination);
    }
    public function getStatusByMember(Request $request, $member_id)
    {
        $member = User::withMember($member_id)
            ->first();

        if (!$member) {
            return response()->json(null, 404);
        }

        $service = Service::where('members_id', $member_id)
            ->orderBy('created_at', 'DESC')
            ->first();

        if (!$service) {
            return response()->json(null, 404);
        }   

        return response()->json($service);
    }
    public function create(Request $request)
    {
        $this->validate($request, [
            'doctors_id' => ['required', 'exists:doctors,users_id'],
            'charge_amount' => ['required', 'integer', 'min:1'],
            'treatment_type' => ['nullable', 'in:1,2'],
        ]);
        $doctors_id = $request->input('doctors_id');
        $treatment_type = $request->input('treatment_type');
        $charge_amount = $request->input('charge_amount');
        $membership_fee_1 = intval($this->getSetting('membership_fee_1', 1000));
        $membership_fee_2 = intval($this->getSetting('membership_fee_2', 1000));
        if($treatment_type == 1){
            if($membership_fee_1>$charge_amount){
                Log::debug(sprintf('醫師編號:%d  一般服務收費過低，最低收費為%d$',$doctors_id,$membership_fee_1));
                return response()->json(['reason'=>['wrong price'=>[sprintf('一般服務收費過低，最低收費為%d$',$membership_fee_1)]]],422);
            }
        }
        else if($treatment_type == 2){
            if($membership_fee_2>$charge_amount){
                Log::debug(sprintf('醫師編號:%d  高級服務收費過低，最低收費為%d$',$doctors_id,$membership_fee_2));
                return response()->json(['reason'=>['wrong price'=>[sprintf('高級服務收費過低，最低收費為%d$',$membership_fee_2)]]],422);
            }
        }
        

        try {
            DB::beginTransaction();
            $service = new Service($request->input());
            $service->generateOrderNumber();
            $service->charge_amount = $request->input('charge_amount',1);
            $service->save();
            DB::commit();
        } catch (QueryException $exception) {
            DB::rollback();

            return response()->json(null, 500);
        }

        return response()->json($service);
    }

    public function setPayment(Request $request, $service_id)
    {
        $this->validate($request, [
            'members_id' => ['required', 'exists:users,id'],
            'payment_method' => ['required', 'in:1,2'],
        ]);
        $service = Service
            ::where('id', $service_id)
            ->first();
        if (!$service) {
            return response()->json(null, 404);
        }
        $service->members_id = $request->input('members_id');
        $service->payment_method = $request->input('payment_method');
        $service->save();

        return response()->json($service);
    }

    public function setTreatment(Request $request, $service_id)
    {
        $this->validate($request, [
            'treatment_type' => ['required', 'in:1,2'],
        ]);
        $service = Service::where('id', $service_id)
            ->first();
        if (!$service) {
            return response()->json(null, 404);
        }
        $service->treatment_type = $request->input('treatment_type');
        $service->save();

        return response()->json($service);
    }

    public function setStartedAt(Request $request, $service_id)
    {
        $service = Service::where('id', $service_id)
            ->first();
        if (!$service) {
            return response()->json(null, 404);
        }
        $service->started_at = Carbon::now();
        $service->save();

        return response()->json($service);
    }

    public function setStoppedAt(Request $request, $service_id)
    {
        $service = Service::where('id', $service_id)
            ->first();
        if (!$service) {
            return response()->json(null, 404);
        }
        $service->stopped_at = Carbon::now();
        $service->save();
        if (!$service->isPaid()) {
            return response()->json($service);
        }if(false){
        //if ($service->current_treatment_time < $service->treatment_time) {
            $this->slackNotify('服務編號：{order_number}{br}治療時間只有{current_treatment_time}分鐘，開始進行取消信用卡授權...', [
                '{order_number}' => $service->order_number,
                '{current_treatment_time}' => $service->current_treatment_time,
            ]);
            $result = Pay2GoCancel
                ::setOrderNumber($service->order_number)
                ->setAmount($service->charge_amount)
                ->send();
            if ($result and $result->success) {
                $service->payment_status = 2;
                $service->save();
                $service->paymentHistory()->save(
                    new PaymentHistory([
                        'data' => $result->rawdata,
                    ])
                );
            }
        } else {
            event(
                new MemberServiceCompletedEvent($service)
            );

            //刪除Member的MemberRequest及對應的MemberRequestDoctor
            $member_request = MemberRequest::where('members_id', $service->members_id)
                ->first();
            MemberRequestDoctor::where('member_requests_id', $member_request->id)
                ->forceDelete();
            $member_request->forceDelete();

            $this->slackNotify('服務完成，清除會員('.$service->members_id.')的需求接單，共'.$member_requests->count().'筆');
        }

        return response()->json($service);
    }

    public function exportHistoryById(Request $request, $service_id)
    {
        $this->validate($request, [
            'email' => ['required', 'email'],
        ]);
        $service = Service::where('id', $service_id)
            ->first();
        if (!$service) {
            return response()->json(null, 404);
        }
        $email = $request->input('email');
        $result = false;
        try {
            $result = Mail::to($email)
                ->send(
                    new ServicePlanExportedById($service)
                );
        } catch (Exception $exception) {
            $this->slackNotify($exception->getMessage());
        }

        return response()->json($result);
    }

    public function exportHistoryByDoctor(Request $request, $doctor_id)
    {
        $this->validate($request, [
            'email' => ['required', 'email'],
        ]);
        $doctor = User::withDoctor($doctor_id)
            ->first();
        if (!$doctor) {
            return response()->json(null, 404);
        }
        $services = Service::withTrashed()
            ->where('doctors_id', $doctor_id)
            ->where('payment_status', 3)
            ->whereNotNull('paid_at')
            ->whereNotNull('started_at')
            ->whereNotNull('stopped_at')
            ->orderBy('paid_at', 'DESC')
            ->where('created_at', '>', date('Y-m-01'))
            ->get();
        $email = $request->input('email'); 
        try {
            Mail::to($email)->send(
                new ServiceExportedByDoctor($doctor, $services)
            );
            $this->slackNotify('服務記錄輸出信件已寄出給:'.$email);
            $result = true;
        } catch (Exception $exception) {
            $result = false;
//            dump($exception->getMessage());
        }

        return response()->json($result);
    }
} 
