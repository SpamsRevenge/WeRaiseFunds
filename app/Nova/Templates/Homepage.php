<?php

namespace App\Nova\Templates;

use Illuminate\Http\Request;
use Outl1ne\PageManager\Template;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Heading;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Fields\Image;
use Laravel\Nova\Fields\MultiSelect;
use App\Models\Fundraiser;
use App\Models\Blog;

use Eminiarts\Tabs\Tab;
use Eminiarts\Tabs\Tabs;
class Homepage extends Template
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
          // Heading::make('Banner Content')
					Image::make('Banner Image')
					->path("homepage"),
					Text::make('Banner Title'),
					Text::make('Banner Button Text')
					// Text::make('Banner Button Link'),
				]),
				new Tab('Image And Icon', [

					Image::make('First Section Image')
					->path("homepage"),
					Text::make('First Section Heading'),
					Text::make('First Section link'),

					Image::make('Second Section Image')
					->path("homepage"),
					Text::make('Second Section Heading'),
					Text::make('Second Section link'),

					Image::make('Third Section Image')
					->path("homepage"),
					Text::make('Third Section Heading'),
					Text::make('Third Section link')

				]),
				new Tab('What to expect', [

					Text::make('Section Title'),
					Text::make('Section Heading'),
					
				
					Text::make('What to expect First Heading'),
					Text::make('What to expect First Subheading'),

					Text::make('What to expect Second Heading'),
					Text::make('What to expect Second Subheading'),

					Text::make('What to expect Third Heading'),
					Text::make('What to expect Third Subheading'),


				]),
				new Tab('Featured topics', [

					Text::make('Top Heading'),
					Text::make('Top Subheading'),
					Text::make('Third Section Description'),
					Text::make('Topic Button Title'),
					Text::make('Topic Button Link'),

				]),

				new Tab('Trust & Safety', [
					
					Text::make('Trust Heading'),
					Text::make('Trust Subheading'),
					Textarea::make('Trust Content'),
					Text::make('Trust Button Title'),
					Text::make('Trust Button Link'),

					Image::make('Trust First Image')
					->path("homepage"),
					Image::make('Trust Second Image')
					->path("homepage"),
					Image::make('Trust Third Image')
					->path("homepage"),
					Image::make('Trust Fourth Image')
					->path("homepage"),

				]),
				new Tab('Happening near you', [

					Text::make('local Heading'),
					Text::make('local Subheading'),
					Text::make('local Button Title'),
					Text::make('local Button Link'),

				]),

				new Tab('Home CTA', [

					Text::make('CTA Heading'),
					Text::make('CTA First Button Title'),
					Text::make('CTA First Button Link'),
					Text::make('CTA Second Button Title'),
					Text::make('CTA Second Button Link'),

				]),
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