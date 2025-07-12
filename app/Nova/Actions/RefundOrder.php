<?php

namespace App\Nova\Actions;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Http\Requests\NovaRequest;
use App\Models\Order;
use App\Models\Fundraiser;
use App\Models\User;
use App\Models\FundraiserSeller;
use App\Mail\OrderRefund;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Mail\MailException;

class RefundOrder extends Action
{
	use InteractsWithQueue, Queueable;

    /**
     * Perform the action on the given models.
     *
     * @param  \Laravel\Nova\Fields\ActionFields  $fields
     * @param  \Illuminate\Support\Collection  $models
     * @return mixed
     */
    public function handle(ActionFields $fields, Collection $models)
    {    
    	foreach ($models as $model) {
    		$orderId = $model->id;
    		$order = Order::where('id',$orderId)->first();
    		if(!$order){
    			return Action::danger('Order not found.');
    		}
    		$sellerID = $order->seller_id;
    		$fundraiserID = $order->fundraiser_page_id;
    		$transactionID = $order->transaction_id;
    		$uniqueID = $order->transaction_unique;
    		// $sub_amount = $request->order_subtotal;
    		$finalAmount = $order->donation_total;
    		$fundraiser = Fundraiser::where('id',$fundraiserID)->first();
    		if(!$fundraiser){
    			return Action::danger('Fundraiser not found.');
    		}
    		$admin = isset($fundraiser->admin_id)?$fundraiser->admin_id:[];
    		$adminUser = User::where('id',$admin)->with('userInfo')->first();

    		$adminUserApiKey = isset($adminUser->userInfo->payment_api_key)?$adminUser->userInfo->payment_api_key:'';
    		if(!$adminUserApiKey){
    			$adminUserApiKey = env('JETPAY_DEFAULT_API_KEY');
    		}
    		$store_id = $adminUserApiKey;

    		$amount = number_format($finalAmount, 2, '.', '');
    		$amount = str_replace(".", "", trim($amount));

    		$jeytranstoken = substr(md5(time()), 0, 18);

    		$transact_it = "<JetPay Version='3.0'>
    		<TransactionType>CREDIT</TransactionType>
    		<TerminalID>".$store_id."</TerminalID>
    		<TransactionID>".$jeytranstoken."</TransactionID>
    		<UniqueID>".$uniqueID."</UniqueID>
    		<TotalAmount>".$amount."</TotalAmount>
    		<IndustryInfo Type='RETAIL'></IndustryInfo>
    		<Application Version='4.2'>VirtPOS</Application>
    		<Library Version='1.5'>VirtPOS SDK</Library>
    		<Gateway>NCR NPP</Gateway>
    		<DeveloperID>NCR</DeveloperID>
    		</JetPay>";

        // test or production?
        //$URL = "https://test1.jetpay.com/jetpay";
    		$URL = "https://gateway17.jetpay.com/jetpay";

    		$ch = curl_init($URL);
        //curl_setopt($ch, CURLOPT_MUTE, 1);
    		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    		curl_setopt($ch, CURLOPT_POST, 1);
    		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/xml'));
    		curl_setopt($ch, CURLOPT_POSTFIELDS, $transact_it);
    		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    		$output = curl_exec($ch);
    		curl_close($ch);

    		$xml = simplexml_load_string($output);
    		$responded = $xml;
    		$formatted_response = array('refid' => $responded->TransactionID,'unique_id' => $responded->UniqueID, 'payement_response' => $responded->ResponseText, 'approval' => $responded->Approval, 'type' => 'refund'  );

    		if ($responded->ActionCode == '000') {

    			$order->refund_data = json_encode($formatted_response);
    			$order->order_status = 'refund';

    			$order->save();

    			if($fundraiser){
    				$fundraiser->total_collected = ($fundraiser->total_collected - $order->donation_total);
    				$fundraiser->save();
    			}

    			if($sellerID ){
    				$seller = FundraiserSeller::where('user_id',$sellerID)->first();
    				if($seller){
    					$seller->total_collected = ($seller->total_collected - $order->donation_total);
    					$seller->save();
    				}
    			}
    			if($order->donor_email){
    				try {
    					Mail::to( $order->donor_email )->send( new OrderRefund($order) );
    				} catch (MailException $e) {
            			// Log the error message for debugging purposes
    					Log::error('Refund Email - Mail sending failed: ' . $e->getMessage());

            			// Optionally, return a more user-friendly error message or handle it according to your application's needs
    					return Action::message('Refund initiated successfully. But Email not sent.');
    				} catch (\Exception $e) {
        				// Handle any other exceptions that might occur
    					Log::error('Refund Email - An error occurred: ' . $e->getMessage());
    					return Action::message('Refund initiated successfully. But Email not sent.');
    				}
    			}

    			return Action::message('Refund initiated successfully.');
    		} else {    			
    			return Action::danger('Refund process failed.');
    		}
    	}
    }

    /**
     * Get the fields available on the action.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function fields(NovaRequest $request)
    {
    	return [];
    }
}
