<?php

namespace App\Http\Controllers;

use App\Service;
use App\Services\Facades\Pay2GoMPG;
use App\Traits\SlackNotify;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    use SlackNotify;

    public function showPurchaseForm($service_id)
    {
        $service = Service
            ::where('id', $service_id)
            ->first();
        if ($service and $service->canPay()) {
            $return_url = route('services_purchase_return', [
                'service_id' => $service->id,
            ]);
            $this->slackNotify('開始進行信用卡交易{br}交易編號：{order_number}{br}交易金額：{amount}', [
                '{order_number}' => $service->order_number,
                '{amount}' => $service->charge_amount,
            ]);

            return response(
                Pay2GoMPG
                    ::setOrderNumber($service->order_number)
                    ->setAmount($service->charge_amount)
                    ->setReturnUrl($return_url)
                    ->getPurchaseForm()
            );
        }

        return $this->response404();
    }

    public function returnProcess(Request $request, $service_id)
    {
        $service = Service
            ::where('id', $service_id)
            ->first();
        if (!$service) {
            return $this->response404();
        }
        if (Pay2GoMPG::isReturnSuccess($request)) {
            return redirect()->route('services_purchase_success', [
                'service_id' => $service_id,
            ]);
        }

        return redirect()->route('services_purchase_failure', [
            'service_id' => $service_id,
        ]);
    }

    public function showSuccessPage($service_id)
    {
        $service = Service
            ::where('id', $service_id)
            ->first();
//        $service->payment_status = '1';
//        $service->save();
        if ($service) {
            $result = '成功';

            return $this->response($service->id, $service->order_number, $service->charge_amount, $result);
        }

        return $this->response404();
    }

    public function showFailurePage($service_id)
    {
        $service = Service
            ::where('id', $service_id)
            ->first();
        if ($service and $service->canPay()) {
            $result = '失敗';

            return $this->response($service->id, $service->order_number, $service->charge_amount, $result);
        }

        return $this->response404();
    }

    protected function response($service_id, $order_number, $amount, $result)
    {
        return response(
            strtr('服務編號：{service_id}<br/>交易編號：{order_number}<br />交易金額：${amount}<br />交易結果：{result}', [
                '{service_id}' => $service_id,
                '{order_number}' => $order_number,
                '{amount}' => $amount,
                '{result}' => $result,
            ])
        );
    }

    protected function response404()
    {
        return response('交易不存在，請聯絡系統人員', 404);
    }
}
