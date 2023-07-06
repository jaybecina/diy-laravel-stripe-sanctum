<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $subject,$header,$content,$code;

    public function __construct($subject,$header ,$content,$code=null)
    {
        //
        $this->subject  = $subject;
        $this->header   = $header;
        $this->content  = $content;
        $this->code     = $code;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('template.email');
    }
}
