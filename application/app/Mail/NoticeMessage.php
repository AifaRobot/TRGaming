<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class NoticeMessage extends Mailable
{
    use Queueable, SerializesModels;

    private $mode;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($mode = null)
    {
        $this->mode = $mode;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {

        if ($this->mode === 'newUser') {
            return $this->view('mails/newUser')
                ->from("info@trgaming.com", 'TRgaming')
                ->subject("Te damos la Bienvenida a nuestra experiencia de Gamificación");
        }

        if ($this->mode === 'alarmOne') {
            return $this->view('mails/alarmOne')
                ->from("info@trgaming.com", 'TRgaming')
                ->subject("Recordatorio");
        }

        if ($this->mode === 'alarmTwo') {
            return $this->view('mails/alarmTwo')
                ->from("info@trgaming.com", 'TRgaming')
                ->subject("Recordatorio");
        }

        if ($this->mode === 'alarmTree') {
            return $this->view('mails/alarmTree')
                ->from("info@trgaming.com", 'TRgaming')
                ->subject("Recordatorio");
        }
        
        return $this->view('mails/noticeMessage')
            ->from("info@trgaming.com", 'TRgaming')
            ->subject("Gracias por jugar");
    }
}