<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactUsNotification extends Mailable
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
    	$subject = nova_get_setting('contact_us_notification_email_subject', $default = null);

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
    	$html = nova_get_setting('contact_us_notification_email_content', $default = null);
    	$html = str_replace('{name}',$this->data['name'],$html);
    	$html = str_replace('{school}',$this->data['school'],$html);
    	$html = str_replace('{program_name}',$this->data['program_name'],$html);
    	$html = str_replace('{city}',$this->data['city'],$html);
    	$html = str_replace('{state}',$this->data['state'],$html);
    	$html = str_replace('{program_start_date}',$this->data['program_start_date'],$html);
    	$html = str_replace('{501c}',$this->data['501c'],$html);
    	$html = str_replace('{other_details}',$this->data['other_details'],$html);
    	$html = str_replace('{referer_url}',$this->data['referer_url'],$html);
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
