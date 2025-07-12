<?php

namespace App\Policies;

use Sereny\NovaPermissions\Policies\BasePolicy;

class ContactPolicy extends BasePolicy
{
    /**
     * The Permission key the Policy corresponds to.
     *
     * @var string
     */
    public $key = 'contact';
}