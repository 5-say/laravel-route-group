<?php namespace FiveSay\LaravelRouteGroup;

class Facade extends \Illuminate\Support\Facades\Facade
{

    /**
     * {@inheritDoc}
     */
    protected static function getFacadeAccessor() { return 'laravel-route-group'; }

}
