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
            throw new \Symfony\Component\HttpKernel\Exception\HttpException(404,  '404 Not Found');
        });

        \API::error(function (\Illuminate\Database\Eloquent\ModelNotFoundException $exception) {
            abort(404);
        });

        \Dingo\Api\Facade\API::error(function (\Illuminate\Auth\Access\AuthorizationException $exception) {
            abort(403, $exception->getMessage());
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
