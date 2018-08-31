<?php

namespace App\Mail;

use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ServiceExportedByDoctor extends Mailable
{
    use Queueable, SerializesModels;

    protected $doctor;

    protected $services;

    public function __construct(User $doctor, Collection $services)
    {
        $this->doctor = $doctor;
        $this->services = $services;
    }

    public function build()
    {
        return $this
            ->subject('匯出服務記錄')
            ->view('mails.services.exported_by_doctor', [
                'doctor' => $this->doctor,
                'services' => $this->services,
            ]);
    }
}
