<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;

class OrderRequest extends FormRequest
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
    		'cc_number'=> 'required|numeric',
    		'cc_exp'=> 'required',
    		'cc_holder_name'=> 'required',
    		'cc_cvv'=> 'required|numeric',
    		'cc_card_brand'=> 'required',
    		'donor_first_name'=> 'required',
    		'donor_last_name'=> 'required',
    		'donor_email'=> 'required',
    	];
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
