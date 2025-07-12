<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\OrderRequest;
use App\Models\Fundraiser;
use App\Models\Order;
use App\Models\User;
use App\Models\FundraiserSeller;
use App\Mail\OrderInvoice;
use App\Mail\NewOrderAdminNotification;
use App\Mail\OrderRefund;
use Illuminate\Support\Facades\Mail;
use Laravel\Nova\Nova;
use Illuminate\Support\Facades\Log;
use Illuminate\Mail\MailException;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
    	$orders = Order::with(['fundraiser','user']);
    	if($request->user()->hasRole('fundraiser-manager')){
    		$fund = Fundraiser::where('user_id',$request->user()->id)->get()->pluck('id');
    		if($fund){
    			$orders = $orders->whereIn('fundraiser_page_id',$fund);
    		}
    	}else{
    		$orders = $orders->where('seller_id',$request->user()->id);
    	}
    	if($request->slug){
    		$fundraiser = Fundraiser::where('slug',$request->slug)->first();
    		if($fundraiser){
    			$orders = $orders->where('fundraiser_page_id',$fundraiser->id);
    		}
    	}
    	$orders = $orders->get();
    	if($orders){
    		return response()->json([
    			'status' => 'success',
    			'message' => 'Orders fetched successfully.',
    			'data' => $orders,
    		]);
    	}else{
    		return response()->json([
    			'status' => 'failed',
    			'message' => 'Orders not found.',
    			'data' => null,
    		]);
    	}
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(OrderRequest $request)
    {
    	// return 'sqd';
    	$slug = $request->fundraiser_slug;
    	$fundraiser = Fundraiser::where('slug',$slug)->first();
    	if(!$fundraiser){
    		return response()->json([
    			'status' => 'failed',
    			'message' => 'Fundraiser not found.',
    			'data' => null,
    		]);
    	}

    	$admin = $fundraiser->admin_id;
    	$adminUser = User::where('id',$admin)->with('userInfo')->first();
    	if(!$adminUser || !isset($adminUser->userInfo) ){
    		return response()->json([
    			'status' => 'failed',
    			'message' => 'Admin not found.',
    			'data' => null,
    		]);
    	}

    	$adminUserApiKey = isset($adminUser->userInfo->payment_api_key)?$adminUser->userInfo->payment_api_key:'';
    	$adminUserJetpayFeeType = isset($adminUser->userInfo->jetpay_fee_type)?$adminUser->userInfo->jetpay_fee_type:'CNV';
    	// print_r($adminUserApiKey);die;
    	if(!$adminUserApiKey){
    		$adminUserApiKey = nova_get_setting('default_api_key', $default = null);
    	}

    	// $sub_amount = $request->order_subtotal;
    	$finalAmount = $request->order_total;
    	if($finalAmount < 1){
    		return response()->json([
    			'status' => 'failed',
    			'message' => 'Order total should be greater then or equal to $1.',
    			'data' => null,
    		]);
    	}
    	$holder_name = $request->cc_holder_name;

    	$store_id = $adminUserApiKey;

    	$tax_total1 = $request->order_fee?$request->order_fee:0;
    	$tax_total2 = $request->order_fee_2?$request->order_fee_2:0;

    	$tax_total1 = number_format($tax_total1, 2, '.', '');
    	$tax_total1 = str_replace(".", "", trim($tax_total1));

    	$tax_total2 = number_format($tax_total2, 2, '.', '');
    	$tax_total2 = str_replace(".", "", trim($tax_total2));

    	$tax_total = $tax_total1 + $tax_total2;

    	$tip_total = $request->order_tip?$request->order_tip:0;

    	$tip_total = number_format($tip_total, 2, '.', '');
    	$tip_total = str_replace(".", "", trim($tip_total));

    	$order_subtotal = $request->order_subtotal?$request->order_subtotal:0;

        // $api_token = $gateway['gw_pw'];

    	$expire_month = substr($request->cc_exp, 0, 2);
    	$expire_year = substr($request->cc_exp, -2);
        // $amount = $request->payment_details->totals->final;
    	$card_cvv = $request->cc_cvv;

    	$card_num = $request->cc_number;

    	$amount = number_format($finalAmount, 2, '.', '');
    	$amount = str_replace(".", "", trim($amount));

    	$jeytranstoken = substr(md5(time()), 0, 18);

    	$transact_it = "<JetPay Version='3.0'>
    	<TransactionID>".$jeytranstoken."</TransactionID>
    	<TransactionType>SALE</TransactionType>
    	<CardNum CardPresent='true'>".$card_num."</CardNum>
    	<CardExpMonth>".$expire_month ."</CardExpMonth>
    	<CardExpYear>".$expire_year."</CardExpYear>
    	<CVV2>".$card_cvv."</CVV2>
    	<TotalAmount>".$amount."</TotalAmount>
    	<TerminalID>".$store_id."</TerminalID>
    	<FeeAmount>".$tax_total."</FeeAmount>
    	<FeeType>".$adminUserJetpayFeeType."</FeeType>
    	<Origin>POS</Origin>
    	<IndustryInfo Type='RETAIL'>
    	<BaseAmount>".$order_subtotal."</BaseAmount>
    	<TipAmount>".$tip_total."</TipAmount>
    	</IndustryInfo>
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
        // print_r($responded);die;
    	$params['cc-brand'] = strtoupper($request->cc_card_brand);
    	// if (strpos($params['cc-brand'], 'VIS') !== false) {
    	// 	$params['cc-brand'] = "VISA";
    	// } elseif (strpos($params['cc-brand'], 'DIS') !== false) {
    	// 	$params['cc-brand'] = "DISCOVER";
    	// } elseif (strpos($params['cc-brand'], 'AME') !== false) {
    	// 	$params['cc-brand'] = "AMEX";
    	// } elseif (strpos($params['cc-brand'], 'MAS') !== false) {
    	// 	$params['cc-brand'] = "MASTERCARD";
    	// }

    	$formatted_response = array('refid' => $responded->TransactionID, 'unique_id' => $responded->UniqueID, 'payement_response' => $responded->ResponseText, 'payement_token' => $responded->Token, 'approval' => $responded->Approval, 'type' => 'sale', 'brand' => $params['cc-brand'], 'card_last_4' => substr($card_num, -4), 'card_exp' => $expire_month.'/'.$expire_year,'card_cvv' => $card_cvv );

    	if ($responded->ActionCode == '000') {

    		if($request->username){
    			$seller = User::where('username',$request->username)->first();
    			if($seller){
    				$orderUser = $seller->id;
	    			// update seller total collection
    				$sellerFund = FundraiserSeller::where('id',$orderUser)->first();
    				if($sellerFund){
    					$sellerFund->total_collected = ($sellerFund->total_collected + $request->order_total);
    					$sellerFund->save();
    				}
    			}
    		}
    		if(!isset($orderUser)){
    			$orderUser = $fundraiser->user_id;
    		}

    		$order = new Order();
    		$order->seller_id = $orderUser;
    		$order->fundraiser_page_id = $fundraiser->id;
    		$order->donation_total = $request->order_total;
    		$order->order_fee = $request->order_fee;
    		$order->order_fee_2 = $request->order_fee_2;
    		$order->order_tip = $request->order_tip;
    		$order->order_subtotal = $request->order_subtotal;

    		$order->transaction_id = $responded->TransactionID;
    		$order->transaction_unique = $responded->UniqueID;
    		$order->transaction_mode = 'credit_card';
    		$order->order_status = 'completed';

    		$order->donor_first_name = $request->donor_first_name;
    		$order->donor_last_name = $request->donor_last_name;
    		$order->donor_email = $request->donor_email;

    		$order->donation_type = $request->donation_type?$request->donation_type:'donation';

    		$order->raffle_quantity = $request->raffle_quantity?$request->raffle_quantity:'';

    		// $order->donor_address_1 = $request->donor_address_1;
    		// $order->donor_address_2 = $request->donor_address_2;
    		// $order->donor_city = $request->donor_city;
    		// $order->donor_state = $request->donor_state;
    		$order->donor_zip = $request->donor_zip;
    		$order->creator = $request->creator?$request->creator:0;
    		$order->phone = $request->phone?$request->phone:'';
    		$order->donor_country = $request->donor_country;
    		// $order->donor_mobile = $request->donor_mobile;
    		$order->donation_name = $request->donation_name;    		
    		$order->order_data = json_encode($formatted_response);

    		$order->save();

    		if($fundraiser){
    			$fundraiser->total_collected = ($fundraiser->total_collected + $request->order_total);
    			$fundraiser->save();
    		}

    		if($request->donor_email){
    			try {
    				Mail::to( $request->donor_email )->send( new OrderInvoice($order,$adminUser) );
    			} catch (MailException $e) {
	    			// Log the error message for debugging purposes
    				Log::error('Order Email - Mail sending failed: ' . $e->getMessage());

	    			// Optionally, return a more user-friendly error message or handle it according to your application's needs
    				return response()->json([
    					'status' => 'success',
    					'message' => 'Unable to send donor invoice email at this time.',
    					'data' => $order,
    					'payement_response' => $responded->ResponseText, 
    					'pay_api_res' => $responded
    				]);
    			} catch (\Exception $e) {
					// Handle any other exceptions that might occur
    				Log::error('Order Email - An error occurred: ' . $e->getMessage());
    				return response()->json([
    					'status' => 'success',
    					'message' => 'Unable to send donor invoice email at this time.',
    					'data' => $order,
    					'payement_response' => $responded->ResponseText, 
    					'pay_api_res' => $responded
    				]);
    			}

    			// Mail::to( $request->donor_email )->send( new OrderInvoice($order,$adminUser) );
    		}
    		$admin_emails = nova_get_setting('order_admin_notification_emails', $default = null);
    		if($admin_emails){
    			try {
    				Mail::to( explode(',', $admin_emails) )->send( new NewOrderAdminNotification($order,$adminUser) );
    			} catch (MailException $e) {
	    			// Log the error message for debugging purposes
    				Log::error('Order Admin Email - Mail sending failed: ' . $e->getMessage());

	    			// Optionally, return a more user-friendly error message or handle it according to your application's needs
    				return response()->json([
    					'status' => 'success',
    					'message' => 'Unable to send admin notification email at this time.',
    					'data' => $order,
    					'payement_response' => $responded->ResponseText, 
    					'pay_api_res' => $responded
    				]);
    			} catch (\Exception $e) {
					// Handle any other exceptions that might occur
    				Log::error('Order Admin Email - An error occurred: ' . $e->getMessage());
    				return response()->json([
    					'status' => 'success',
    					'message' => 'Unable to send admin notification email at this time.',
    					'data' => $order,
    					'payement_response' => $responded->ResponseText, 
    					'pay_api_res' => $responded
    				]);
    			}
    		}
    		// return array('status' => 'success', 'formatted' => $formatted_response, 'gw_resp' => $responded);

    		return response()->json([
    			'status' => 'success',
    			'message' => 'Payment success.',
    			'data' => $order,
    			'payement_response' => $responded->ResponseText, 
    			'pay_api_res' => $responded
    		]);
    	} else {
    		// return array('status' => 'failed', 'formatted' => $responded->ResponseText, 'gw_resp' => $responded);
    		return response()->json([
    			'status' => 'failed',
    			'message' => 'Payment failed.',
    			'payement_response' => $responded->ResponseText, 
    			'pay_api_res' => $responded,
    			'data' => null
    		]);
    	}
    }



    public function orderemail(Request $request)
    {
    	$order = Order::where('id',$request->id)->first();

    	$fundraiser = Fundraiser::where('id',$order->fundraiser_page_id)->first();

    	$admin = $fundraiser->admin_id;

    	$adminUser = User::where('id',$admin)->with('userInfo')->first();



    	Mail::to( 'forte.test.only@gmail.com' )->send( new OrderInvoice($order,$adminUser) );

    	echo "send";
    }

    /**
     * Refund a order .
     */
    public function refundOrder(Request $request)
    {
    	// echo 'ddfds';die;
    	$orderId = $request->order_id;
    	$order = Order::where('id',$orderId)->with('fundraiser')->first();
    	if(!$order){
    		return response()->json([
    			'status' => 'failed',
    			'message' => 'Order not found.',
    			'data' => null,
    		]);
    	}
    	$sellerID = $order->seller_id;
    	$fundraiserID = $order->fundraiser_page_id;
    	$transactionID = $order->transaction_id;
    	$uniqueID = $order->transaction_unique;
    	// $sub_amount = $request->order_subtotal;
    	$finalAmount = $order->donation_total;
    	$fundraiser = Fundraiser::where('id',$fundraiserID)->first();
    	if(!$fundraiser){
    		return response()->json([
    			'status' => 'failed',
    			'message' => 'Fundraiser not found.',
    			'data' => null,
    		]);
    	}
    	$admin = $fundraiser->admin_id;
    	$adminUser = User::where('id',$admin)->with('userInfo')->first();
    	if(!$adminUser || !isset($adminUser->userInfo) ){
    		return response()->json([
    			'status' => 'failed',
    			'message' => 'Admin not found.',
    			'data' => null,
    		]);
    	}

    	$adminUserApiKey = isset($adminUser->userInfo->payment_api_key)?$adminUser->userInfo->payment_api_key:'';
    	// print_r($adminUserApiKey);die;
    	if(!$adminUserApiKey){
    		$adminUserApiKey = nova_get_setting('default_api_key', $default = null);
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
		// print_r($responded);die;
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
    			Mail::to( $order->donor_email )->send( new OrderRefund($order) );

    		}

    		// return array('status' => 'success', 'formatted' => $formatted_response, 'gw_resp' => $responded);

    		return response()->json([
    			'status' => 'success',
    			'message' => 'Refund success.',
    			'data' => $order,
    			'payement_response' => $responded->ResponseText, 
    			'pay_api_res' => $responded
    		]);
    	} else {
    		// return array('status' => 'failed', 'formatted' => $responded->ResponseText, 'gw_resp' => $responded);
    		return response()->json([
    			'status' => 'failed',
    			'message' => 'Refund failed.',
    			'payement_response' => $responded->ResponseText, 
    			'pay_api_res' => $responded,
    			'data' => null
    		]);
    	}
    }



    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
    	$order = Order::with(['fundraiser','user'])->where('id',$id);
    	$order = $order->first();
    	if($order){
    		return response()->json([
    			'status' => 'success',
    			'message' => 'Order fetched successfully.',
    			'data' => $order,
    		]);
    	}else{
    		return response()->json([
    			'status' => 'failed',
    			'message' => 'Order not found.',
    			'data' => null,
    		]);
    	}
    }

}
