<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Overtrue\EasySms\EasySms;

class EasySmsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        // 注册 EasySms 服务，采用单例模式
        $this->app->singleton(EasySms::class, function ($app){
            return new EasySms(config('easysms'));
        });

        // 给服务取别名
        $this->app->alias(EasySms::class, 'easysms');

    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
