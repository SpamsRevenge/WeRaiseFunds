<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Table names
    |--------------------------------------------------------------------------
    |
    | Set table names for the pages and regions tables.
    |
    */

    'pages_table' => 'pages',
    'regions_table' => 'regions',

    /*
    |--------------------------------------------------------------------------
    | Templates
    |--------------------------------------------------------------------------
    |
    | Register all templates (for both pages and regions) here.
    |
    */

    'templates' => [
        'pages' => [
            'Privacy' => [
                'class' => '\App\Nova\Templates\Privacy',
            ],
            'about-us' => [
                'class' => '\App\Nova\Templates\About',
                // 'unique' => true, // Whether more than one page can be created with this template
            ],
            'contact-page' => [
                'class' => '\App\Nova\Templates\ContactUS',
                // 'unique' => true, // Whether more than one page can be created with this template
            ],
            'Homepage' => [
                'class' => '\App\Nova\Templates\Homepage',
                // 'unique' => true, // Whether more than one page can be created with this template
            ],
        ],
        'regions' => [
            // 'header' => [
            //     'class' => 'App\Nova\Templates\HeaderRegionTemplate',
            //     'unique' => true,
            // ],
        ],
    ],


    /*
    |--------------------------------------------------------------------------
    | SEO fields
    |--------------------------------------------------------------------------
    |
    | Set to true if you're happy with the default SEO fields:
    | Title, description, image
    |
    | If you want to show custom SEO fields on all pages, overwrite this
    | with a callable that returns an array of fields.
    |
    */

    'page_seo_fields' => true,


    /*
    |--------------------------------------------------------------------------
    | Locales
    |--------------------------------------------------------------------------
    |
    | Set all the available locales as [key => name] pairs.
    |
    | For example ['en_US' => 'English'].
    |
    */

    'locales' => ['en' => 'English'],



    /*
    |--------------------------------------------------------------------------
    | Resource and model overrides
    |--------------------------------------------------------------------------
    |
    | Add a custom implementation of Page and/or Region models/resources.
    |
    | Return false for any resource if you want to disable it
    | and hide the item from the navigation sidebar.
    |
    */

    'region_model' => \Outl1ne\PageManager\Models\Region::class,
    'region_resource' => \Outl1ne\PageManager\Nova\Resources\Region::class,
    'page_model' => \Outl1ne\PageManager\Models\Page::class,
    'page_resource' => \Outl1ne\PageManager\Nova\Resources\Page::class,



    /*
    |--------------------------------------------------------------------------
    | Base URL
    |--------------------------------------------------------------------------
    |
    | Define the base URL for your pages. Can be a string (ie https://webshop.com)
    | or a closure.
    |
    | If a closure is specified, the function is called with the $page as a
    | parameter. For example: fn($page) => config('app.url') . $page->path;
    |
    */

    
    'base_url' => 'https://weraisefunds.org/'
    // 'base_url' => function ($page) {
    //   return 'http://127.0.0.1:8000/'.$page;
    // }
];
