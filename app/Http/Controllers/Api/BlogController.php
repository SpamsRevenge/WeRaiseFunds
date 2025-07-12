<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Blog;
use App\Models\BlogCategory;

class BlogController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function index( Request $request )
  {
  	$blogs = Blog::select('*');
  	if($request->category){
  		$blogs = $blogs->whereHas('blogCategories', function ($query) use ($request) {
  			$query->where('slug', $request->category);
  		});     
  	}
  	$blogs = $blogs->with('blogCategories')->get();
  	if(!$blogs){
  		return response()->json([
  			'status' => 'failed',
  			'message' => 'Blogs not found.',
  			'data' => null,
  		]);
  	}else{
  		return response()->json([
  			'status' => 'success',
  			'message' => 'Blogs fetched successfully.',
  			'data' => $blogs,
  		]);
  	}
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
  	$blog = Blog::where('slug', $slug)->first();
  	if(!$blog){
	  	return response()->json([
	  		'status' => 'failed',
	  		'message' => 'Blog not found.',
	  		'data' => null,
	  	]);
	  }else{
	  	return response()->json([
	  		'status' => 'success',
	  		'message' => 'Blog fetched successfully.',
	  		'data' => $blog,
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
   * Display the all Blog categories.
   */
  public function blogCategories()
  {
  	$categories = BlogCategory::all();
  	if(!$categories){
	  	return response()->json([
	  		'status' => 'failed',
	  		'message' => 'Categories not found.',
	  		'data' => null,
	  	]);
	  }else{
	  	return response()->json([
	  		'status' => 'success',
	  		'message' => 'Categories fetched successfully.',
	  		'data' => $categories,
	  	]);
	  }
  }

  /**
   * Display the specified blog category.
   */
  public function blogCategoryBySlug(string $slug)
  {
  	$category = BlogCategory::where('slug', $slug)->first();
  	if(!$category){
	  	return response()->json([
	  		'status' => 'failed',
	  		'message' => 'Category not found.',
	  		'data' => null,
	  	]);
	  }else{
	  	return response()->json([
	  		'status' => 'success',
	  		'message' => 'Category fetched successfully.',
	  		'data' => $category,
	  	]);
	  }
  }
}
