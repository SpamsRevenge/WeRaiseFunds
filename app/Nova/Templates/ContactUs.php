<?php

namespace App\Nova\Templates;

use Illuminate\Http\Request;
use Outl1ne\PageManager\Template;
use Laravel\Nova\Fields\Text;
use Eminiarts\Tabs\Tab;
use Eminiarts\Tabs\Tabs;
use Laravel\Nova\Fields\Heading;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Fields\Image;
use Laravel\Nova\Fields\MultiSelect;

class ContactUs extends Template
{
    // Name displayed in CMS
	public function name(Request $request): string
	{
		return parent::name($request);
	}

    // Fields displayed in CMS
	public function fields(Request $request): array
	{
		return [
			
			new Tabs('Banner Section', [
				new Tab('Banner', [
					Image::make('Banner Image')
					->path("homepage"),
					Text::make('Banner Title'),
					Text::make('Banner Content')
					// Text::make('Banner Button Link'),
				]),
				new Tab('About Contact', [
					Text::make('Phone Number'),
					Text::make('Phone Number Content'),
					Text::make('Email'),
					Text::make('Email Content'),
					Text::make('Address'),
					Text::make('Address Content')
					// Text::make('Banner Button Link'),
				]),
				new Tab('Form Section', [

					Textarea::make('Left Content'),
					Textarea::make('Map Section')
					// Text::make('Banner Button Link'),
				]),
				new Tab('CTA', [
					Text::make('CTA Heading'),
					Text::make('CTA First Button Title'),
					Text::make('CTA First Button Link'),
					Text::make('CTA Second Button Title'),
					Text::make('CTA Second Button Link'),
				])
			]),
		];
	}

    // Resolve data for serialization
	public function resolve($page, $params): array
	{
        // Modify data as you please (ie turn ID-s into models)
		return $page->data ?? [];
	}

    // Optional suffix to the route (ie {blogPostName})
	public function pathSuffix(): string|null
	{
		return null;
	}
}
