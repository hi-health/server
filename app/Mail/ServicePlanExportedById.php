<?php

namespace App\Mail;

use App\Service;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ServicePlanExportedById extends Mailable
{
    use Queueable, SerializesModels;

    protected $service;

    public function __construct(Service $service)
    {
        $this->service = $service;
    }

    public function build()
    {
        return $this
            ->subject('匯出課程評分記錄')
            ->view('mails.services.plans.exported_by_id', [
                'service' => $this->service,
            ]);
    }
}
