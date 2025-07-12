<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WelcomeEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct($user,$role)
    {
        $this->data = $user;
        $this->role = $role;

    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
    	$subject = self::getSubject($this->role);
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

        $html = self::getContent($this->role);
        // // print_r();
        // print_r($this->data['name']);die('--here--');
        $html = str_replace('{firstname}',$this->data['name'],$html);
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
     * Get the email subject by user role.
     */
    public function getSubject($role)
    {
    	if($role == 1){
    		return nova_get_setting('sup_admin_welcome_email_subject', $default = null);
    	}else if($role == 2){
    		return nova_get_setting('admin_welcome_email_subject', $default = null);
    	}else if($role == 5){
    		return nova_get_setting('school_welcome_email_subject', $default = null);
    	}else if($role == 3){
    		return nova_get_setting('fundraiser_manager_welcome_email_subject', $default = null);
    	}else {
       		return nova_get_setting('seller_welcome_email_subject', $default = null);
    	}
    }
    /**
     * Get the email content by user role.
     */
    public function getContent($role)
    {
    	if($role == 1){
    		return nova_get_setting('sup_admin_welcome_email_content', $default = null);
    	}else if($role == 2){
    		return nova_get_setting('admin_welcome_email_content', $default = null);
    	}else if($role == 5){
    		return nova_get_setting('school_welcome_email_content', $default = null);
    	}else if($role == 3){
    		return nova_get_setting('fund_manager_welcome_email_content', $default = null);
    	}else {
       		return nova_get_setting('seller_welcome_email_content', $default = null);
    	}
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
