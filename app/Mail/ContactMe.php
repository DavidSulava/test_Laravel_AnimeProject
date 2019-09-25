<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ContactMe extends Mailable
{
    use Queueable, SerializesModels;

    public $payload;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct( $data )
    {
        $this->payload = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
        {
            $toEmail = env('MAIL_USERNAME');

            return $this->to($toEmail)
                        ->subject( $this->payload['host'] )
                        ->view('mail.contact', [ 'post'=> $this->payload ] );
        }
}
