<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;

class StoreFundraiser extends FormRequest
{
  /**
   * Determine if the user is authorized to make this request.
   */
  public function authorize(): bool
  {
  	return true;
  }

  /**
   * Get the validation rules that apply to the request.
   *
   * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
   */
  public function rules(): array
  {
  	return [
  		'admin_id'=> 'required',
  		'user_id'=> 'required',
  		'title'=> 'required',
  		'slug'  => 'required|alpha_dash|lowercase|unique:fundraisers,slug',
  		'status'=> 'required',
  		'total'=> 'required|numeric',
  		'state'=> 'required',
  		'fundraiser_category_id'=> 'required',
  		'featured_image'=> 'mimes:png,jpg,jpeg|max:2048',
  		'banner_image'=> 'mimes:png,jpg,jpeg|max:8192',
  	];
  }

  protected function failedValidation(Validator $validator)
  {
  	// throw new HttpResponseException(response()->json([
  	// 	'message' => 'Somethig went wrong!' ,
  	// 	'errors' => $validator->errors(),
  	// 	'status' => 'failed',
  	// ], Response::HTTP_UNPROCESSABLE_ENTITY));
  throw new HttpResponseException(response()->json([
  		'message' => 'Somethig went wrong!' ,
  		'errors' => $validator->errors(),
  		'status' => 'failed',
  	], 200));
  }
}
