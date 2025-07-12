<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Raffle;
use App\Models\Fundraiser;
use App\Models\Order;

class RaffleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
    	$fund = Fundraiser::where('slug',$request->slug)->first();
    	if(!$fund){
    		return response()->json([
    			'status' => 'failed',
    			'message' => 'Fundraiser not found.',
    			'data' => null,
    		]);
    	}
    	$raffles = Raffle::where('fundraiser_id',$fund->id)->with(['fundraiser','order'])->get();
    	if($raffles){
    		return response()->json([
    			'status' => 'success',
    			'message' => 'Raffles fetched successfully.',
    			'data' => $raffles,
    		]);
    	}else{
    		return response()->json([
    			'status' => 'failed',
    			'message' => 'Raffles not found.',
    			'data' => null,
    		]);
    	}
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
    	$fund = Fundraiser::where('slug',$request->slug)->first();
    	if(!$fund){
    		return response()->json([
    			'status' => 'failed',
    			'message' => 'Fundraiser not found.',
    			'data' => null,
    		]);
    	}
		$Allorder = Raffle::where('fundraiser_id',$fund->id)->get()->pluck('donor_email');
		$order = Order::where('fundraiser_page_id',$fund->id)->where('order_status','completed')->where('donation_type','raffle');
		if($Allorder){
			$order = $order->whereNotIn('donor_email',$Allorder);
		}
		$order = $order->orderByRaw('RAND()')
		->first();
		if(!$order){
			return response()->json([
    			'status' => 'failed',
    			'message' => 'Winner not found, Please try again later.',
    			'data' => null,
    		]);
		}
    	$raffle = Raffle::where('fundraiser_id',$fund->id)->where('donor_email',$order->donor_email)->first();
    	// print_r($order);
    	if($raffle){
    		return response()->json([
    			'status' => 'failed',
    			'message' => 'Winner already exists, Please try again later.',
    			'data' => null,
    		]);
    	}else{

    		// $randomWinner = random_int(0, count($orderEmails));
    		$raffle = new Raffle();
    		$raffle->fundraiser_id = $fund->id;
    		$raffle->order_id = $order->id;
    		$raffle->donor_email = $order->donor_email;
    		$raffle->status = 'active';
    		$raffle->position = count($Allorder)+1;
    		$raffle->save();
    		$raffleNew = Raffle::where('id',$raffle->id)->with(['fundraiser','order'])->first();
    		return response()->json([
    			'status' => 'success',
    			'message' => 'Winner created successfully.',
    			'data' => $raffleNew,
    		]);
    	}
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
}
