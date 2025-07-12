<?php

namespace App\Observers;

use App\Models\Order;
use App\Models\Fundraiser;
use App\Models\FundraiserSeller;

class OrderObserver
{
  /**
   * Handle the Order "created" event.
   */
  public function created(Order $order): void
  {	
  	// Fundraiser collection update
  	$fundraiser = Fundraiser::find($order->fundraiser_page_id);
  	$ordersTotal = Order::where('fundraiser_page_id',$order->fundraiser_page_id)->pluck('donation_total')->sum();
  	$ordersTotalSellers = Order::where('fundraiser_page_id',$order->fundraiser_page_id)->where('seller_id', $order->seller_id)->pluck('donation_total')->sum();
  	if($fundraiser){
  		$fundraiser->update(['total_collected' => ( $ordersTotal?($ordersTotal):0 )]);
  	}

  	//Fundraiser Seller collection update
  	$fundraiserSeller = FundraiserSeller::where('fundraiser_id', $order->fundraiser_page_id)->where('user_id', $order->seller_id)->first();
  	if($fundraiserSeller){
  		$fundraiserSeller->update(['total_collected' => ( $ordersTotalSellers?($ordersTotalSellers):0 )]);
  	}
  }

  /**
   * Handle the Order "updated" event.
   */
  public function updated(Order $order): void
  {
		
  	$oldDonationTotal = $order->getOriginal('donation_total');
  	$ordersTotal = Order::where('fundraiser_page_id',$order->fundraiser_page_id)->pluck('donation_total')->sum();
  	$ordersTotalSellers = Order::where('fundraiser_page_id',$order->fundraiser_page_id)->where('seller_id', $order->seller_id)->pluck('donation_total')->sum();
  	// $fundraiser->update(['total_collected' => $fundraiser->total_collected + $order->donation_total]);
  	// if( $oldDonationTotal != $order->donation_total ){
	  	// Fundraiser collection update
	  	$fundraiser = Fundraiser::find($order->fundraiser_page_id);
  		if($fundraiser){
	  		$fundraiser->update(['total_collected' => ( $ordersTotal?($ordersTotal):0 )]);
	  	}

	  	//Fundraiser Seller collection update
	  	$fundraiserSeller = FundraiserSeller::where('fundraiser_id', $order->fundraiser_page_id)->where('user_id', $order->seller_id)->first();
	  	if($fundraiserSeller){
	  		$fundraiserSeller->update(['total_collected' => ( $ordersTotalSellers?($ordersTotalSellers):0 )]);
	  	}
  	// }
  	
  }

  /**
   * Handle the Order "deleted" event.
   */
  public function deleted(Order $order): void
  {        
  	//Fundraiser Seller collection update
  	$ordersTotal = Order::where('fundraiser_page_id',$order->fundraiser_page_id)->pluck('donation_total')->sum();
  	$ordersTotalSellers = Order::where('fundraiser_page_id',$order->fundraiser_page_id)->where('seller_id', $order->seller_id)->pluck('donation_total')->sum();
  	$fundraiser = Fundraiser::find($order->fundraiser_page_id);
  	$fundraiser->update(['total_collected' => ( $ordersTotal?($ordersTotal):0 ) ]);
  	
  	//Fundraiser Seller collection update
  	$fundraiserSeller = FundraiserSeller::where('fundraiser_id', $order->fundraiser_page_id)->where('user_id', $order->seller_id)->first();
  	if($fundraiserSeller){
  		$fundraiserSeller->update(['total_collected' =>  ( $ordersTotalSellers?($ordersTotalSellers):0 ) ]);
  	}
  }

  /**
   * Handle the Order "restored" event.
   */
  public function restored(Order $order): void
  {
      //
  }

  /**
   * Handle the Order "force deleted" event.
   */
  public function forceDeleted(Order $order): void
  {
      //
  }
}



// namespace App\Observers;

// use App\Models\Order;
// use App\Models\Fundraiser;
// use App\Models\FundraiserSeller;

// class OrderObserver
// {
//   /**
//    * Handle the Order "created" event.
//    */
//   public function created(Order $order): void
//   {	
//   	// Fundraiser collection update
//   	$fundraiser = Fundraiser::find($order->fundraiser_page_id);
//   	if($fundraiser){
//   		$fundraiser->update(['total_collected' => $fundraiser->total_collected + $order->donation_total]);
//   	}

//   	//Fundraiser Seller collection update
//   	$fundraiserSeller = FundraiserSeller::where('fundraiser_id', $order->fundraiser_page_id)->where('user_id', $order->seller_id)->first();
//   	if($fundraiserSeller){
//   		$fundraiserSeller->update(['total_collected' => $fundraiserSeller->total_collected + $order->donation_total]);
//   	}
//   }

//   /**
//    * Handle the Order "updated" event.
//    */
//   public function updated(Order $order): void
//   {
		
//   	$oldDonationTotal = $order->getOriginal('donation_total');
//   	// $fundraiser->update(['total_collected' => $fundraiser->total_collected + $order->donation_total]);
//   	if( $oldDonationTotal != $order->donation_total ){
// 	  	// Fundraiser collection update
// 	  	$fundraiser = Fundraiser::find($order->fundraiser_page_id);
//   		if($fundraiser){
// 	  		$fundraiser->update(['total_collected' => ( ( ( $fundraiser->total_collected + $order->donation_total )- $oldDonationTotal)>0?( ( $fundraiser->total_collected + $order->donation_total )- $oldDonationTotal):$order->donation_total)]);
// 	  	}

// 	  	//Fundraiser Seller collection update
// 	  	$fundraiserSeller = FundraiserSeller::where('fundraiser_id', $order->fundraiser_page_id)->where('user_id', $order->seller_id)->first();
// 	  	if($fundraiserSeller){
// 	  		$fundraiserSeller->update(['total_collected' => ( ( ( $fundraiserSeller->total_collected + $order->donation_total )- $oldDonationTotal)>0?( ( $fundraiserSeller->total_collected + $order->donation_total )- $oldDonationTotal):$order->donation_total)]);
// 	  	}
//   	}
  	
//   }

//   /**
//    * Handle the Order "deleted" event.
//    */
//   public function deleted(Order $order): void
//   {        
//   	//Fundraiser Seller collection update
//   	$fundraiser = Fundraiser::find($order->fundraiser_page_id);
//   	$fundraiser->update(['total_collected' => ( $fundraiser->total_collected >= $order->donation_total?($fundraiser->total_collected - $order->donation_total):0 ) ]);
  	
//   	//Fundraiser Seller collection update
//   	$fundraiserSeller = FundraiserSeller::where('fundraiser_id', $order->fundraiser_page_id)->where('user_id', $order->seller_id)->first();
//   	if($fundraiserSeller){
//   		$fundraiserSeller->update(['total_collected' => ( $fundraiserSeller->total_collected >= $order->donation_total?($fundraiserSeller->total_collected - $order->donation_total):0 ) ]);
//   	}
//   }

//   /**
//    * Handle the Order "restored" event.
//    */
//   public function restored(Order $order): void
//   {
//       //
//   }

//   /**
//    * Handle the Order "force deleted" event.
//    */
//   public function forceDeleted(Order $order): void
//   {
//       //
//   }
// }

