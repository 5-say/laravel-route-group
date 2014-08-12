laravel-route-group
===================

对象化分组路由辅助工具

## 使用前的准备

在 composer.json 文件中申明依赖：

    "five-say/laravel-route-group": "1.*"

在 `/app/config/app.php` 中设置“服务提供者”与“别名”

    'providers' => array(
        ...
        'FiveSay\LaravelRouteGroup\ServiceProvider',
    ),
    'aliases' => array(
        ...
        'RouteGroup' => 'FiveSay\LaravelRouteGroup\Facade',
    ),

## 使用方法

    RouteGroup::make('user')->asPrefix('user')->controller('UserController')
        ->normal()
        ->more(function ($as, $uses, $has) {
            # 禁用
            Route::get('/{id}/ban'  , array('as' => $as.'ban'  , 'uses' => $uses.'ban'  ))->before($has.'ban');
            # 解除禁用
            Route::get('/{id}/unban', array('as' => $as.'unban', 'uses' => $uses.'unban'))->before($has.'ban');
        });

以上相当于

    Route::group(array('prefix' => 'user'), function () {
        # 禁用
        Route::get('/{id}/ban'  , array('as' => 'user.ban'  , 'uses' => 'UserController@ban'  ))->before('hasAccess:user.ban');
        # 解除禁用
        Route::get('/{id}/unban', array('as' => 'user.unban', 'uses' => 'UserController@unban'))->before('hasAccess:user.ban');
    });