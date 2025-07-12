<?php

namespace App\Nova;
use Laravel\Nova\Nova;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Country;
use Laravel\Nova\Panel;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Actions\ExportAsCsv;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Fields\Currency;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\FormData;
// use Dnwjn\NovaButton\Button;
use Sietse85\NovaButton\Button;
use App\Nova\Actions\RefundOrder;


use App\Models\Fundraiser;
use App\Models\User;

use App\Nova\Metrics\OrderTotals;
use App\Nova\Metrics\TotalOrders;
use App\Nova\Metrics\TaxCollected;
use App\Nova\Metrics\OrderTotalPie;

class Order extends Resource
{
	/**
	 * The model the resource corresponds to.
	 *
	 * @var class-string<\App\Models\Order>
	 */
	public static $model = \App\Models\Order::class;

	/**
	 * The single value that should be used to represent the resource when being displayed.
	 *
	 * @var string
	 */
	public static $title = 'id';

	/**
	 * The columns that should be searched.
	 *
	 * @var array
	 */
	public static $search = [
		'id','donor_first_name','donor_last_name','order_data',
	];

	public static function perPageOptions()
    {
        return [50, 100, 150];
    }

	/**
	 * Get the fields displayed by the resource.
	 *
	 * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
	 * @return array
	 */

	public function fields(NovaRequest $request)
	{
		return [
			ID::make()->sortable()->hideFromIndex(),

			belongsTo::make("Fundraiser", 'fundraiser')
			// ->searchable()
			->filterable()
			->readonly(function ($request) {
				return $request->isUpdateOrUpdateAttachedRequest();
			}),

			Text::make('Name', function () {
				return sprintf('%s %s', $this->donor_first_name, $this->donor_last_name);
			}),

			Text::make('First Name','donor_first_name')->hideFromIndex(),
		    // ->filterable(function ($request, $query, $value, $attribute) {
		    //  $query->where($attribute, 'LIKE', "{$value}%");
		    // }),
			// Text::make('order_data','Card Last 4 Number')->onlyOnIndex()->searchable(),
			Text::make('Card Last 4 Number', function () {
			$jsonData = $this->order_data; // Replace 'your_json_column' with the actual column name
			$data = json_decode($jsonData, true);

			return isset($data['card_last_4']) ? $data['card_last_4'] : null;
			})->onlyOnIndex(),


			Text::make('Last Name','donor_last_name')->hideFromIndex(),
      		// ->filterable(function ($request, $query, $value, $attribute) {
      		//  $query->where($attribute, 'LIKE', "{$value}%");
      		// }),

			belongsTo::make('Student', 'user', 'App\Nova\User')
			// ->dependsOn( ['fundraiser'], function( BelongsTo $field, NovaRequest $request, FormData $data){
			// 	if($data->fundraiser === null){
			// 		$field->hide();
			// 	}else{
			// 		$field->relatableQueryUsing( function( NovaRequest $request, $query ) use ($data) {
			// 			$fundraiser = Fundraiser::find($data->fundraiser)->with('FundraiserSeller:user_id')->first();
			// 			$seller_ids = $fundraiser->FundraiserSeller;
			// 			// print_r($seller_ids);
			// 			$query->where('id', $fundraiser->user_id)->orWhereIn('id', $seller_ids  );
			// 		});
			// 	}
			// })
			// ->searchable()
			->filterable(),
			
			// }),
			// Select::make('Student', 'seller_id')
			// ->options(function () {
			// 	return User::all()->pluck("name", "id");
			// })
			// ->dependsOn( ['fundraiser'], function(  $field, NovaRequest $request, FormData $data){
			// 	$fundraiserId = $data->fundraiser;
			// 	if($fundraiserId){
			// 		$fundraiser = Fundraiser::find($fundraiserId)->with('FundraiserSeller:user_id')->first();
			// 		$seller_ids = $fundraiser->FundraiserSeller;
			// 		return User::where('id', $fundraiser->user_id)->orWhereIn('id', $seller_ids  )->pluck("name", "id");
			// 		// $field->options(User::where('id', $fundraiser->user_id)->orWhereIn('id', $seller_ids  )->pluck("name", "id"));
			// 	}

			// }),

			Text::make('Donation Name','donation_name')->hideFromIndex(),

			Text::make('Email','donor_email')->hideFromIndex(),

			Currency::make('Amount','donation_total')
			->locale('en')
			->min(1)
			->step(1)
			->readonly(function ($request) {
				return $request->isUpdateOrUpdateAttachedRequest();
			}),

	        // Text::make('Amount','donation_total'),
			Text::make('Transaction ID','transaction_id')->hideFromIndex(),

			Text::make('Transaction Mode','transaction_mode')->hideFromIndex(),

			new Panel("Donor Address", $this->addressFields()),
	        // $table->text('fundraiser_page_id')->nullable();
	        // $table->text('seller_id')->nullable();

			Select::make("Status",'order_status')->searchable()->options([
				'completed' => 'Complete',
				'on-hold' => 'Hold',
				'pending' => 'Pending',
				'failed' => 'Failed',
				'refund' => 'Refund'
			])->displayUsingLabels()->filterable(),

			Text::make('Ticket Quantity','raffle_quantity'),

			Text::make('Creator','creator'),

			Text::make('Phone','phone'),

			// Select::make('Donation Type','raffle_quantity')->options([
			// 	'donation' => 'Donation',
			// 	'raffle' => 'Raffle',
			// ])->filterable(),

			Select::make('Donation Type','donation_type')->options([
				'donation' => 'Donation',
				'raffle' => 'Raffle',
			])->rules('required')->filterable(),

			DateTime::make('Created At')->displayUsing(function ($date) {
				return $date->format('m-d-Y H:i:s');
			})->hideWhenCreating()->hideWhenUpdating()->filterable(),

			Button::make('Refund')
			// ->title('Refund')
			->style('danger') 
			->confirm('Confirmation', 'Are you sure you want to refund this order?', 'Cancel')
			->visible($this->order_status == 'completed')
			->action(\App\Nova\Actions\RefundOrder::class)->reload(),
			// ->link('/api/refund_initiate_nova?user_id='.$this->id,'_self'),
			// ->action(function ($model) {
			// 	return redirect()->route('refund_initiate', ['order_id' => $model->id]);
			// }),
			// ->url(route('refund_initiate', ['order_id' => $this->id])),

		];
	}

	protected function addressFields()
	{
		return [
			Text::make("Address", "donor_address_1")->hideFromIndex(),

			Text::make("Address Line 2",'donor_address_2')->hideFromIndex(),

			Text::make("City",'donor_city')->hideFromIndex(),

			Text::make("State",'donor_state')->hideFromIndex(),

			Text::make("Postal Code", "donor_zip")->hideFromIndex(),

			// Country::make("Country",'donor_country')->hideFromIndex(),
		];
	}
	/**
	 * Get the cards available for the request.
	 *
	 * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
	 * @return array
	 */
	public function cards(NovaRequest $request)
	{
		return [
			OrderTotals::make()->refreshWhenFiltersChange()->width('1/4'),
			TotalOrders::make()->refreshWhenFiltersChange()->width('1/4'),
			TaxCollected::make()->refreshWhenFiltersChange()->width('1/4'),
			// OrderTotalPie::make()->refreshWhenFiltersChange()->width('1/4'),
		];
	}

	/**
	 * Get the filters available for the resource.
	 *
	 * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
	 * @return array
	 */
	public function filters(NovaRequest $request)
	{
		return [];
	}

	/**
	 * Get the lenses available for the resource.
	 *
	 * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
	 * @return array
	 */
	public function lenses(NovaRequest $request)
	{
		return [];
	}

	/**
	 * Get the actions available for the resource.
	 *
	 * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
	 * @return array
	 */
	public function actions(NovaRequest $request)
	{
		return [
			ExportAsCsv::make()->withFormat(function ($model) {
		        return [
		            'ID' => $model->getKey(),
		            'Fundraiser' => $model->fundraiser ? $model->fundraiser->title : 'N/A',
		            'Donor First Name' => $model->donor_first_name,
		            'Donor Last Name' => $model->donor_last_name,
		            'Donor Email' => $model->donor_email,
                'Ticket Count' => $model->raffle_quantity,
                'Order Tip' => $model->order_tip,
                'Order Fee 1' => '$'.$model->order_fee,
                'Order Fee 2' => '$'.$model->order_fee_2,
                'Order Subtotal' => '$'.$model->order_subtotal,
                'Order Total' => '$'.$model->donation_total,
                'Donation Type' => $model->donation_type,
		           	'Donor Mobile' => $model->donor_mobile,
		            'Donor Address' => $model->donor_address_1,
		            'Donor Address 1' => $model->donor_address_2,
		            'Donor State' => $model->donor_state,
		            'Donor Country' => $model->donor_country,
		            'Phone' => $model->phone,
		            'Creator' => $model->creator,
		            'Transaction Id' => $model->transaction_id,
		            'Transaction unique' => $model->transaction_unique,
		            'Transaction Mode' => $model->transaction_mode,
		            'Order Status' => $model->order_status,
                'Created At' => $model->created_at->format('m-d-Y H:i:s')
		        ];
   			}),
			// new Actions\RefundOrder,
		];
	}

	public static function indexQuery(NovaRequest $request, $query)
	{
		if( auth()->user()->hasRole('super-admin') ){
			return $query;
		}
		return $query->whereHas( 'Fundraiser', function($modelQuery) use ($request){
			$modelQuery->where( 'admin_id', auth()->user()->id );
		});
	}

	public function authorizedToDelete(Request $request)
	{
		return false;
	}

    public static function authorizedToCreate(Request $request)
	{
		return false;
	}
}
