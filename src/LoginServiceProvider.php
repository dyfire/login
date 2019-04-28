<?php

namespace Encore\Login;

use Illuminate\Support\ServiceProvider;

class LoginServiceProvider extends ServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function boot(Login $extension)
    {
        if (! Login::boot()) {
            return ;
        }

        if ($views = $extension->views()) {
            $this->loadViewsFrom($views, 'login');
        }

        if ($this->app->runningInConsole() && $assets = $extension->assets()) {
            $this->publishes(
                [$assets => public_path('vendor/laravel-admin-ext/login')],
                'login'
            );
        }

        $this->app->booted(function () {
            Login::routes(__DIR__.'/../routes/web.php');
        });
    }
}