<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Page;
use App\Models\Fundraiser;
use App\Models\Blog;
use Illuminate\Support\Facades\DB;

class PageController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function index()
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
  public function show(string $slug)
  {
  	  	// $page_by_slug = Page::select('name->en as name', 'slug->en as slug', 'data->en as data')->whereJsonContains('slug->en', $slug)->first();

  	$page_by_slug = Page::select('pages.name->en as name', 'pages.slug->en as slug', 'pages.data->en as page_data')->whereJsonContains('pages.slug->en', $slug)->first();

  	if(!$page_by_slug){
  		return response()->json([
  			'status' => 'failed',
  			'message' => 'Page not found.',
  			'data' => null,
  		]);
  	}else{
  		// print_r($page_by_slug);
  		$page_by_slug = $page_by_slug->toArray();
  		$page_data = json_decode($page_by_slug['page_data']);
  		$page_by_slug['page_data'] = $page_data;

  		if(isset($page_data->funndraisers_near_you)){
  			$fundraisers = Fundraiser::select('id','title','slug')->with('fundraiserCategory')->whereIn('id',$page_data->funndraisers_near_you)->get();
 				$page_by_slug['fundraisers'] = $fundraisers;
  		}
  		if(isset($page_data->featured_blogs)){
  			$fundraisers = Blog::select('id','title','slug')->with('blogCategories')->whereIn('id',$page_data->featured_blogs)->get();
 				$page_by_slug['blogs'] = $fundraisers;
  		}
  		return response()->json([
  			'status' => 'success',
  			'message' => 'Page fetched successfully.',
  			'data' => $page_by_slug,
  		]);
  	}
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
   * Fetch all menus.
   */
  public function getMenus(Request $request, $menu_slug = null) {
  	if( $menu_slug ){
  		$menusResponse = nova_get_menu_by_slug( $menu_slug, $locale = null );
  	}else{
  		$menusResponse = nova_get_menus();
  	}
  	if(!$menusResponse){
  		return response()->json([
  			'status' => 'failed',
  			'message' => 'Menu not found.',
  			'data' => null,
  		]);
  	}else{
  		return response()->json([
  			'status' => 'success',
  			'message' => 'Menus fetched successfully.',
  			'data' => $menusResponse,
  		]);
  	}
  	// return response()->json($menusResponse);
  }
}
