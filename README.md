laravel-route-group
===================

对象化分组路由辅助工具
2.* 系列的版本将单独使用一个分支，为 laravel 4.2 做出支持

---

- [使用前的准备](#1)
- [使用方法](#2)
- [实际用例](#3)

<a name="1"></a>
## 使用前的准备

在 composer.json 文件中申明依赖：

```json
"five-say/laravel-route-group": "2.*"
```

在 `/app/config/app.php` 中设置“服务提供者”与“别名”

```php
'providers' => array(
    ...
    'FiveSay\LaravelRouteGroup\ServiceProvider',
),
'aliases' => array(
    ...
    'RouteGroup' => 'FiveSay\LaravelRouteGroup\Facade',
),
```

<a name="2"></a>
## 使用方法

```php
RouteGroup::make()->controller('AdminController')->go(function ($route) {
    $route->get('/')->as('admin')->uses('getIndex');
});
```

> 想知道它为我们注册了什么路由？我们来加一个小尾巴 `->dd()` 就像下面这样：

```php
RouteGroup::make()->controller('AdminController')->go(function ($route) {
    $route->get('/')->as('admin')->uses('getIndex');
})->dd();
```

![](/public/image/1.png)

> 虽然对象化了，但总觉的代码量反而更多了是不是？没关系，让我们来个霸气点的：

```php
RouteGroup::make('admin')->as('admin')->before('auth')
    ->controller('AdminController')->go(function ($route) {
        $route->index(  )
              ->create( )
              ->store(  )
              ->edit(   )
              ->update( )
              ->destroy();
    })->dd();
```

![](/public/image/2.png)

> 现在有点感觉了？我们还可以做到更多：

```php
RouteGroup::make('admin')->as('admin')->before('auth')
    ->controller('AdminController')->go(function ($route) {
        $route->index(  )
              ->create( )->beforeClear()
              ->store(  )->before('more')->before('more2|more3')
              ->edit(   )->beforeOnly('myself')
              ->update( );
        $route->delete('{id}')->as('destroy')->uses('destroy');
    })->dd();
```

![](/public/image/3.png)

> 哦，对了，这里还有个大尾巴 `->ddAll()` 它将输出在此之前，由此辅助注册的所有路由信息。

<a name="3"></a>
## 实际用例

请参考此项目 [5-say/laravel-4.1-simple-blog](https://github.com/5-say/laravel-4.1-simple-blog/blob/master/app/routes.php)。