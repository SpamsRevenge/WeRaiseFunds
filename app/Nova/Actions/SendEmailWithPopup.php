<?php

namespace App\Nova\Actions;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Fields\Text;


class SendEmailWithPopup extends Action
{
    use InteractsWithQueue, Queueable;

    /**
     * Perform the action on the given models.
     *
     * @param  \Laravel\Nova\Fields\ActionFields  $fields
     * @param  \Illuminate\Support\Collection  $models
     * @return mixed
     */

    public $name = 'Send Email with Popup';
    
    public function handle(ActionFields $fields, Collection $models)
    {
        // Implement your email sending logic here
        $email = $fields->email;
        $subject = $fields->subject;

        // Send the email
        // You can use a mailer or any other method to send the email

        // Return a response (optional)
        return Action::message("Email sent to $email with subject: $subject");

    }

    /**
     * Get the fields available on the action.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        return [
            Text::make('Email')->rules('required', 'email'),
            Text::make('Subject')->rules('required'),
        ];
    }
}
