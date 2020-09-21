<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if(app()->isLocal()){
            // 本地开发模式, 注册SudoSu
            $this->app->register(\VIACreative\SudoSu\ServiceProvider::class);
        }

        // 注册 api 异常处理
        \Dingo\Api\Facade\API::error(function (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $notFoundHttpException) {
            abort(404);
        });

        \Dingo\Api\Facade\API::error(function (\Illuminate\Auth\Access\AuthorizationException $authorizationException) {
            abort(403, $authorizationException->getMessage());
        });


    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
	{
		\App\Models\User::observe(\App\Observers\UserObserver::class);
		\App\Models\Reply::observe(\App\Observers\ReplyObserver::class);

        // 注册 TopicObserver , 启动服务
        \App\Models\Topic::observe(\App\Observers\TopicObserver::class);

        \App\Models\Link::observe(\App\Observers\LinkObserver::class);
    }
}
