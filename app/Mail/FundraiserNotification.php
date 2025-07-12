<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class FundraiserNotification extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
 	public function __construct( $requests )
    {
        $this->data = $requests;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(        
        	from: env('MAIL_FROM_ADDRESS'),
            subject: $this->data['subject'],
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        // return new Content(
        //     view: 'view.name',
        // );
        $html = nova_get_setting('fundraiser_seller_notification_email_content', $default = null);
        $html = str_replace('{message}',$this->data['message'],$html);
        // $this->subject = setting($this->data['subject']);
        // return $this->view('emails.newaccount')->with('html', $html);
        return new Content(
	        view: 'emails.fundraiser.notification', 
	        with: [
                'html' => $html
            ],
	    );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
