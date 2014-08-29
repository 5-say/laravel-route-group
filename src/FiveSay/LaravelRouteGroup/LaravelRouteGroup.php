<?php namespace FiveSay\LaravelRouteGroup;

use Illuminate\Support\Facades\Route;

class LaravelRouteGroup
{
    /**
     * \FiveSay\LaravelRouteGroup\LaravelRoute
     * @var object
     */
    private $route;
    private $allCaches = array();

    /**
     * 构造分组路由并设置路由前缀
     * @param  string $prefix 路由前缀
     * @return self
     */
    public function make($prefix = '/')
    {
        if ($this->route)
            $this->allCaches = array_merge($this->allCaches, $this->route->routeCaches);
        $this->route = new LaravelRoute;
        $this->route->prefix = rtrim($prefix, '/');
        return $this;
    }

    /**
     * 设置别名前缀
     * @param  string $as 别名前缀
     * @return self
     */
    public function MyAs($as)
    {
        $this->route->as  = $as.'.';
        $this->route->has = 'hasAccess:'.$as.'.';
        return $this;
    }

    /**
     * 未定义方法处理
     * @param  string $name      请求的方法名称
     * @param  array  $arguments 传入的参数
     * @return mixed
     */
    public function __call($name, $arguments) 
    {
        if ($name === 'as')  // 绕过关键字冲突
            return $this->MyAs($arguments[0]);
    }

    /**
     * 设置本组路由使用的控制器
     * @param  string $uses 控制器类名
     * @return self
     */
    public function controller($uses)
    {
        $this->route->uses = $uses.'@';
        return $this;
    }

    /**
     * 设置本组路由使用的公共前置过滤器
     * @param  string $before 前置过滤器
     * @return self
     */
    public function before($before)
    {
        $this->route->before = $before;
        return $this;
    }

    /**
     * 路由 has 参数
     * @param string $has
     */
    public function has($has)
    {
        // 
    }

    /**
     * 更多需要注册进分组的路由
     * @param  Closure $moreRouteCallback 匿名回调函数
     * @return self
     */
    public function go(\Closure $moreRouteCallback)
    {
        call_user_func($moreRouteCallback, $this->route);

        foreach ($this->route->routeCaches as $key => $value) {
            list($method, $uri) = explode('@', $key);

            if (isset($value['as'])) {
                $route = Route::$method($uri, array(
                    'as'   => $value['as'],
                    'uses' => $value['uses'],
                ));
            } else {
                $route = Route::$method($uri, $value['uses']);
            }

            // 前置过滤器
            $beforeArr   = array();
            if (! isset($value['isOnlyBefore'])) $beforeArr[] = $this->route->before;
            if (isset($value['before'])) $beforeArr[] = $value['before'];
            $before      = implode('|', array_filter($beforeArr));
            if (! empty($value['before'])) $route->before($before);
            $before      = '';
        }

        return $this;
    }

    /**
     * 断点调试，输出当前实例注册的路由
     * @return void
     */
    public function dd()
    {
        $this->echoTable($this->route->routeCaches);
        die;
    }

    /**
     * 断点调试，输出所有注册的路由
     * @return void
     */
    public function ddAll()
    {
        $this->echoTable(array_merge($this->allCaches, $this->route->routeCaches));
        die;
    }

    /**
     * 输出调试，输出当前实例注册的路由
     * @return void
     */
    public function echoTable($routes)
    {
        echo '<style> th, td { background-color: #B8D0EB; padding: 5px; } </style>';
        echo '<table><tr><th>#</th><th>Method</th><th>URI</th><th>AS</th><th>Uses</th><th>Before</th></tr>';
        $i = 0;
        foreach ($routes as $key => $value) {
            list($method, $uri) = explode('@', $key);
            echo '<tr>';
            echo '<td>'.++$i.'</td>';
            echo '<td>'.$method.'</td>';
            echo '<td>'.$uri.'</td>';
            if (isset($value['as'])) {
                echo '<td>'.$value['as'].'</td>';
            } else {
                echo '<td>&nbsp;</td>';
            }
            echo '<td>'.$value['uses'].'</td>';

            # Before ---
            $beforeArr   = array();
            if (! isset($value['isOnlyBefore'])) $beforeArr[] = $this->route->before;
            if (isset($value['before'])) $beforeArr[] = $value['before'];
            $before      = implode('|', array_filter($beforeArr));
            if (! empty($before)) {
                echo '<td>'.$before.'</td>';
            } else {
                echo '<td>&nbsp;</td>';
            }
            $before      = '';
            # Before ---

            echo '</tr>';
        }
        echo '</table>';
    }


}
