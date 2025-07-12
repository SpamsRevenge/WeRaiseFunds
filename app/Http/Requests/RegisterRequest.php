<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class RegisterRequest extends FormRequest
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
   * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
   */
  public function rules(): array
  {
  	return [
  		'name' => 'required|string|max:150',
  		'email' => 'required|email|max:150|unique:users',
  		'password' => 'required|confirmed'
  	];
  }
  public function getData()
  {
  	$data = $this->validated();
  	$data['password'] = Hash::make($data['password']);
  	return $data;
  }
  
  protected function failedValidation(Validator $validator)
  {
  	throw new HttpResponseException(response()->json([
  		'message' => 'Somethig went wrong!' ,
  		'errors' => $validator->errors(),
  		'status' => 'failed',
  	], 200));
  }
}
