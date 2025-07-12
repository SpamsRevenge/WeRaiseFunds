<?php

namespace App\Http\Controllers\Api;

use App\Models\ContactUs;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Mail\ContactUsNotification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Mail\MailException;

class ContactUsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
    	$contactUs = ContactUs::create($request->all());
    	if($contactUs){
    		$admin_email = nova_get_setting('contactus_form_submission_admin_notification_emails', $default = null);

        try {
          $notification = Mail::to( explode(',', $admin_email) )->send( new ContactUsNotification($request) );
        } catch (MailException $e) {
            // Log the error message for debugging purposes
          Log::error('Mail sending failed: ' . $e->getMessage());

            // Optionally, return a more user-friendly error message or handle it according to your application's needs
            // return response()->json(['error' => 'Unable to send email at this time.'], 500);
          return response()->json([
            'status' => 'info',
            'message' => 'Unable to send email at this time.',
            'data' => $contactUs,
          ]);
        } catch (\Exception $e) {
            // Handle any other exceptions that might occur
          Log::error('An error occurred: ' . $e->getMessage());
            // return response()->json(['error' => 'An unexpected error occurred.'], 500);
          return response()->json([
            'status' => 'info',
            'message' => 'An unexpected error occurred.',
            'data' => $contactUs,
          ]);
        }
        return response()->json([
         'status' => 'success',
         'message' => 'Contact Us form submitted successfully.',
         'data' => $contactUs,
       ]);
      }else{
        return response()->json([
         'status' => 'failed ',
         'message' => 'Contact Us form is not submitted.',
         'data' => null,
       ]);
      }
    }

    /**
     * Display the specified resource.
     */
    public function show(ContactUs $contactUs)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ContactUs $contactUs)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ContactUs $contactUs)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ContactUs $contactUs)
    {
        //
    }
  }
