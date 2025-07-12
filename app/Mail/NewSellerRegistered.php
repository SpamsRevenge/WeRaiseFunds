<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewSellerRegistered extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct($data,$fund)
    {
        $this->data = $data;
        $this->fund = $fund;

    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = nova_get_setting('seller_registered_email_subject', $default = null);
        $subject = str_replace('{seller_name}',$this->data['name'],$subject);
        $subject = str_replace('{fundraiser_title}',$this->fund['title'],$subject);

        return new Envelope(        
        	from: env('MAIL_FROM_ADDRESS'),
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {        
    	$html = nova_get_setting('seller_registered_email_content', $default = null);
        $html = str_replace('{seller_name}',$this->data['name'],$html);
        $html = str_replace('{fundraiser_title}',$this->fund['title'],$html);
        // $this->subject = setting($this->data['subject']);
        // return $this->view('emails.newaccount')->with('html', $html);
        return new Content(
	        view: 'emails.user.default', 
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
