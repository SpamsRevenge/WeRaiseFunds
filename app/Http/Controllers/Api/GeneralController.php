<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class GeneralController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
    	$allSettings = nova_get_settings();
    	if($request->group && $allSettings){
    		$groupArray = $request->group;
    		// $groupArray = explode(',',$request->group);
    		$allSettings = array_filter(
    			$allSettings,
    			function ($key) use ($groupArray) {
    				// foreach ($group/Array as $groupkey => $group) {
    					return preg_grep('~' . $groupArray . '~', array($key));
    				// }
    			},
    			ARRAY_FILTER_USE_KEY
    		);
    	}
    	if(!$allSettings){
    		return response()->json([
    			'status' => 'failed',
    			'message' => 'Site settings not found.',
    			'data' => null,
    		]);
    	}else{
    		return response()->json([
    			'status' => 'success',
    			'message' => 'Site settings fetched successfully.',
    			'data' => $allSettings,
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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $key)
    {
    	$singleSetting = nova_get_setting($key);
    	if(!$singleSetting){
    		return response()->json([
    			'status' => 'failed',
    			'message' => 'Site setting not found.',
    			'data' => null,
    		]);
    	}else{
    		return response()->json([
    			'status' => 'success',
    			'message' => 'Site setting fetched successfully.',
    			'data' => [$key => $singleSetting],
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
}
