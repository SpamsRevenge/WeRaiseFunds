<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderInvoice extends Mailable
{
	use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct( $requests,$adminUser )
    {
    	$this->data = $requests;
        $this->adminuser = $adminUser;


        // print_r($adminUser);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {   
        if($this->data['donation_type'] != 'raffle'){
    	
           $subject = nova_get_setting('new_order_donor_notification_email_subject', $default = null);
        
        }else{

            $subject = nova_get_setting('new_order_raffle_notification_email_subject', $default = null);
        }

        // echo $subject;

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


        $fee_label = $this->adminuser['userInfo']['fee_label'];
        $fee_2_label = $this->adminuser['userInfo']['fee_2_label'];
        if(!$fee_label){
            $fee_label = 'Convenience fee';
        }
        if(!$fee_2_label){
            $fee_2_label = 'Surcharge';
        }


        $raf_single = 0;
        if($this->data['donation_type'] != 'raffle'){
    	   $html = nova_get_setting('new_order_donor_notification_email_content', $default = null);

        }else{
           $html = nova_get_setting('new_order_raffle_notification_email_content', $default = null);
           $raf_single = $this->data['donation_total']/$this->data['raffle_quantity'];
        }


        //// Fee Labels
    	$html = str_replace('{fee_label_1}',$fee_label,$html);
        $html = str_replace('{fee_label_2}',$fee_2_label,$html);


        ///// Order Content
        $html = str_replace('{donor_first_name}',$this->data['donor_first_name'],$html);
    	$html = str_replace('{donor_last_name}',$this->data['donor_last_name'],$html);
    	$html = str_replace('{donor_name}',$this->data['donor_first_name'],$html);
    	$html = str_replace('{donation_total}',number_format($this->data['donation_total'],'2'),$html);
        $html = str_replace('{order_fee}',number_format($this->data['order_fee'],'2'),$html);
        $html = str_replace('{order_fee_2}',number_format($this->data['order_fee_2'],'2'),$html);
    	$html = str_replace('{order_subtotal}',number_format($this->data['order_subtotal'],'2'),$html);
    	$html = str_replace('{fundraiser_title}',$this->data['fundraiser']['title'],$html);
    	$html = str_replace('{order_date}',$this->data['created_at'],$html);
    	$html = str_replace('{donor_email}',$this->data['donor_email'],$html);
    	$html = str_replace('{doner_country}',$this->data['doner_country'],$html);
        $html = str_replace('{raffle_qty}',$this->data['raffle_quantity'],$html);
    	$html = str_replace('{doner_zip}',$this->data['doner_zip'],$html);
    	$html = str_replace('{phone}',$this->data['phone'],$html);
    	$html = str_replace('{payment_method}','Credit Card',$html);

        $html = str_replace('{ticket_single}',number_format($raf_single,'2'),$html);

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
