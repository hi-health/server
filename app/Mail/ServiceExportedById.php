<?php

namespace App\Mail;

use App\Service;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ServiceExportedById extends Mailable
{
    use Queueable, SerializesModels;
    
    protected $service;
    
    public function __construct(Service $service)
    {
        $this->service = $service;
    }

    public function build()
    {
        return $this->view('mails.services.exported_by_id');
    }
}
