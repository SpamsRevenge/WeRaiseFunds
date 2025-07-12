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

class About extends Template
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

        new Tab('Content', [
          Textarea::make('Content'),
          Image::make('Image')
          ->path("homepage")
        ]),

        new Tab('Three Column Section', [

          Text::make('First Section Heading'),
          Text::make('First Section Content'),
          Text::make('Second Section Heading'),
          Text::make('Second Section Content'),
          Text::make('Third Section Heading'),
          Text::make('Third Section Content')
          
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
