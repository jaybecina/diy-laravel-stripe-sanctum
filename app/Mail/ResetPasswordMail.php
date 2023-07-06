<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ResetPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    public $email;
    public $token;

    public function __construct($email, $token, $role)
    {
        $this->email = $email;
        $this->token = $token;
        $this->role = $role;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        if($this->role == "admin") {
            $url = env('REACT_APP_BASE_URL'). '/admin/reset-password/' .$this->email. '/' .$this->token;
        } else {
            $url = env('REACT_APP_BASE_URL'). '/reset-password/' .$this->email. '/' .$this->token;
        }

        return $this->from("noreply@diydesigner.com.au", "DIY Designer Team")
        ->subject('Password Reset Link')
        ->view('template.reset-password', ['url' => $url]);
    }
}
