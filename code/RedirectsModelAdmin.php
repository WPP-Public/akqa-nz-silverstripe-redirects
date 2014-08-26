<?php

/**
 * @package Heyday\Redirects
 */
class RedirectsModelAdmin extends ModelAdmin
{
    /**
     * @var array
     */
    private static $managed_models = [
        'RedirectUrl'
    ];

    /**
     * @var string
     */
    private static $url_segment = 'redirects-management';

    /**
     * @var string
     */
    private static $menu_title  = 'Redirects';
}