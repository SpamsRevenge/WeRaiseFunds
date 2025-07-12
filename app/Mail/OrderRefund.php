<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderRefund extends Mailable
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
    	$subject = nova_get_setting('oder_refund_notification_email_subject', $default = null);

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
    	$html = nova_get_setting('oder_refund_notification_email_content', $default = null);
    	$html = str_replace('{donor_first_name}',$this->data['donor_first_name'],$html);
    	$html = str_replace('{donor_last_name}',$this->data['donor_last_name'],$html);
    	$html = str_replace('{donor_name}',$this->data['donor_first_name'],$html);
    	$html = str_replace('{donation_total}',number_format($this->data['donation_total'],'2'),$html);
    	$html = str_replace('{order_fee}',number_format($this->data['order_fee'],'2'),$html);
    	$html = str_replace('{order_subtotal}',number_format($this->data['order_subtotal'],'2'),$html);
    	$html = str_replace('{fundraiser_title}',$this->data['fundraiser']['title'],$html);
    	$html = str_replace('{order_date}',$this->data['created_at'],$html);
    	$html = str_replace('{donor_email}',$this->data['donor_email'],$html);
    	$html = str_replace('{doner_country}',$this->data['doner_country'],$html);
    	$html = str_replace('{doner_zip}',$this->data['doner_zip'],$html);
    	$html = str_replace('{phone}',$this->data['phone'],$html);
    	$html = str_replace('{payment_method}','Credit Card',$html);
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
