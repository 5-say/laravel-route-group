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

### 初级

    RouteGroup::make()->controller('UserController')->go(function ($route) {
        $route->get('/')->uses('index');
    });

> 想知道它为我们注册了什么路由？我们来加一个小尾巴 `->dd()` 就像下面这样：

    RouteGroup::make()->controller('UserController')->go(function ($route) {
        $route->get('/')->uses('index');
    })->dd();

> 虽然对象化了，但总觉的代码量反而更多了是不是？没关系，让我们来个霸气点的：

    RouteGroup::make('user')->asPrefix('user')->controller('UserController')
    ->go(function ($route) {
        $route->index()->create()->store()->show()->edit()->update()->destroy();
    })->dd();

> 现在有点感觉了？噢不，官方貌似也提供了一个方法 `Route::resource('user', 'UserController');`！完败？不，这里可以做到更多：

    RouteGroup::make('user')->asPrefix('user')->controller('UserController')
        ->go(function ($route) {
            $route
                ->index(  )
                ->create( )->before('allowCreate')
                ->store(  )->before('allowCreate')
                ->show(   )
                ->edit(   )->before('allowEdit')
                ->update( )->before('allowEdit')
                ->destroy()->before('allowDelete');
            # 禁用
            $route->get('/{id}/ban'  )->as('ban'  )->uses('ban'  )->before('auth')->has('ban');
            # 解除禁用
            $route->get('/{id}/unban')->as('unban')->uses('unban')->before('auth')->has('ban');
        })->dd();

> 看看我们都完成了哪些路由的注册：

![Alt text](/public/image/1.jpg "Optional title")

> **注意** `has()` 需配合“路由权限过滤器”使用

    Route::filter('hasAccess', function ($route, $request, $permission) {
        if (! user()->hasAccess($permission)) {
            App::abort(403);
        }
    });

上例中的 `user()->hasAccess($permission)` 只是一个例子，作用是“获取当前用户实例，并判断用户是否有给定的操作权限”，需根据实际情况自行调整。