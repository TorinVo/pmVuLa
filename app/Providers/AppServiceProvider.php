<?php

namespace App\Providers;
use Illuminate\Support\ServiceProvider;

use Validator;
use Illuminate\Http\Request;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Validator::extend('event_repeat_limit', function($attribute, $value, $parameters, $validator) {
            if(!empty($parameters[0]) && !empty($parameters[1] && $parameters[1] == '1' && empty($value)))
                return false;
            return true;
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
