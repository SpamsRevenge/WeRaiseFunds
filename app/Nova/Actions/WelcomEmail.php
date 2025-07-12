<?php

namespace App\Nova\Actions;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Http\Requests\NovaRequest;
use App\Models\Order;
use App\Models\Fundraiser;
use App\Models\User;
use App\Models\FundraiserSeller;
// use App\Mail\OrderRefund;
use App\Mail\WelcomeEmail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Mail\MailException;


class WelcomEmail extends Action
{
	use InteractsWithQueue, Queueable;

    /**
     * Perform the action on the given models.
     *
     * @param  \Laravel\Nova\Fields\ActionFields  $fields
     * @param  \Illuminate\Support\Collection  $models
     * @return mixed
     */
    public function handle(ActionFields $fields, Collection $models)
    {    
    	foreach ($models as $model) {

    		$fundraiser_id = $model->id;
    		$fundraiser_data = Fundraiser::where('id',$fundraiser_id)->first();
    		$manager = User::where('id',$fundraiser_data->user_id)->first()->toArray();

            // $manager->email

    		try {
    			Mail::to( 'forte.test.only@gmail.com.com' )->send( new WelcomeEmail($manager,3) );
    		} catch (MailException $e) {
            			// Log the error message for debugging purposes
    			Log::error('Welcome Email - Mail sending failed: ' . $e->getMessage());

            			// Optionally, return a more user-friendly error message or handle it according to your application's needs
    			return Action::danger('Mail sending failed. Please check logs for more info.');
    		} catch (\Exception $e) {
        				// Handle any other exceptions that might occur
    			Log::error('Welcome Email - An error occurred: ' . $e->getMessage());
    			return Action::danger('An error occurred while sending email. Please check logs for more info.');
    		}
    		return Action::message('Welcome email send successfully.');

    	}
    }

    /**
     * Get the fields available on the action.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function fields(NovaRequest $request)
    {
    	return [];
    }
}
