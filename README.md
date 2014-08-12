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
        # 列表页
        Route::get(   '/'          , array('as' => 'user.index'  , 'uses' => 'UserController@index'  ))->before('hasAccess:user.show'   );
        # 创建页
        Route::get(   '/create'    , array('as' => 'user.create' , 'uses' => 'UserController@create' ))->before('hasAccess:user.create' );
        # 存储
        Route::post(  '/'          , array('as' => 'user.store'  , 'uses' => 'UserController@store'  ))->before('hasAccess:user.create' );
        # 详情页
        Route::get(   '/{id}'      , array('as' => 'user.show'   , 'uses' => 'UserController@show'   ))->before('hasAccess:user.show'   );
        # 修改页
        Route::get(   '/{id}/edit' , array('as' => 'user.edit'   , 'uses' => 'UserController@edit'   ))->before('hasAccess:user.edit'   );
        # 更新
        Route::put(   '/{id}'      , array('as' => 'user.update' , 'uses' => 'UserController@update' ))->before('hasAccess:user.edit'   );
        # 删除
        Route::delete('/'          , array('as' => 'user.destroy', 'uses' => 'UserController@destroy'))->before('hasAccess:user.destroy');
        # 禁用
        Route::get(   '/{id}/ban'  , array('as' => 'user.ban'    , 'uses' => 'UserController@ban'    ))->before('hasAccess:user.ban'    );
        # 解除禁用
        Route::get(   '/{id}/unban', array('as' => 'user.unban'  , 'uses' => 'UserController@unban'  ))->before('hasAccess:user.ban'    );
    });

> **注意** 需配合“路由权限过滤器”使用

    Route::filter('hasAccess', function ($route, $request, $permission) {
        if (! user()->hasAccess($permission)) {
            App::abort(403);
        }
    });

上例中的 `user()->hasAccess($permission)` 只是一个例子，作用是“获取当前用户实例，并判断用户是否有给定的操作权限”，需根据实际情况自行调整。