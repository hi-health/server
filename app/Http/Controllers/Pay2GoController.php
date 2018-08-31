<?php

namespace App\Http\Controllers;

use App\PaymentHistory;
use App\Service;
use App\Services\Facades\Pay2GoMPG;
use App\Traits\SlackNotify;
use Carbon\Carbon;
use Illuminate\Http\Request;

class Pay2GoController extends Controller
{
    use SlackNotify;

    public function notifyProcess(Request $request)
    {
        $this->slackNotify('收錢通知啦～～～{br}{request}', [
            '{request}' => json_encode($request->all()),
        ]);
        $result = Pay2GoMPG::checkNotify($request);
        if (!$result) {
            // Invalid notify
            $this->slackNotify('Invalid notify');
            return response('', 400);
        }
        $service = Service
            ::where('order_number', $result->order_number)
            ->first();
        if (!$service) {
            // Service Invalid
            $this->slackNotify('Invalid service');
            return response('', 400);
        }
        if ($result->success) {
            $service->payment_status = 1;
            $service->paid_at = Carbon::now();
            $service->save();
        }
        $service->paymentHistory()->save(
            new PaymentHistory([
                'data' => $request->input(),
            ])
        );
    }
}
