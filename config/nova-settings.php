<?php

return [
    'model' => \Stepanenko3\NovaSettings\Models\Settings::class,

    'resource' => \Stepanenko3\NovaSettings\Resources\Settings::class,

    'types' => [
        \Stepanenko3\NovaSettings\Types\General::class,
        \App\Nova\Settings\SettingTabs::class, // Add this line
    ],
];
