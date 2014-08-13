<?php namespace FiveSay\LaravelRouteGroup;

use Illuminate\Support\Facades\Route;

class LaravelRouteGroup
{
    private $allRouteList = array(
        'index',
        'create',
        'store',
        'show',
        'edit',
        'update',
        'destroy',
        'recycle',
        'restore',
        'remove',
    );

    private $prefix = '';
    private $as     = '';
    private $uses   = '';
    private $has    = '';

    private $routeNow    = '';
    private $routeCaches = array();
    private $allCaches   = array();

    /**
     * 构造分组路由并设置路由前缀
     * @param  string $prefix 路由前缀
     * @return self
     */
    public function make($prefix = '/')
    {
        $this->prefix = rtrim($prefix, '/');
        return $this;
    }

    /**
     * 设置别名前缀
     * @param  string $as 别名前缀
     * @return self
     */
    public function asPrefix($as)
    {
        $this->as  = $as.'.';
        $this->has = 'hasAccess:'.$as.'.';
        return $this;
    }

    /**
     * 设置本组路由使用的控制器
     * @param  string $uses 控制器类名
     * @return self
     */
    public function controller($uses)
    {
        $this->uses = $uses.'@';
        return $this;
    }

    /**
     * 更多需要注册进分组的路由
     * @param  Closure $moreRouteCallback 匿名回调函数
     * @return self
     */
    public function go(\Closure $moreRouteCallback)
    {
        $that = $this;
        $this->allCaches   = array_merge($this->allCaches, $this->routeCaches);
        $this->routeCaches = array();
        call_user_func($moreRouteCallback, $that);

        foreach ($this->routeCaches as $key => $value) {
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
            if (isset($value['before']) && ! empty($value['before']))
                $route->before($value['before']);
        }

        return $this;
    }

    /**
     * 断点调试，输出当前实例注册的路由
     * @return void
     */
    public function dd()
    {
        $this->echoTable($this->routeCaches);
        die;
    }

    /**
     * 断点调试，输出所有注册的路由
     * @return void
     */
    public function ddAll()
    {
        $this->echoTable(array_merge($this->allCaches, $this->routeCaches));
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
            if (isset($value['before']) && ! empty($value['before'])) {
                echo '<td>'.$value['uses'].'</td>';
            } else {
                echo '<td>&nbsp;</td>';
            }
            echo '</tr>';
        }
        echo '</table>';
    }

    /**
     * GET 路由
     * @param  string $uri URI 路径
     * @return self
     */
    public function get($uri)
    {
        return $this->addroute('get', $uri);
    }

    /**
     * POST 路由
     * @param  string $uri URI 路径
     * @return self
     */
    public function post($uri)
    {
        return $this->addroute('post', $uri);
    }

    /**
     * PUT 路由
     * @param  string $uri URI 路径
     * @return self
     */
    public function put($uri)
    {
        return $this->addroute('put', $uri);
    }

    /**
     * PATCH 路由
     * @param  string $uri URI 路径
     * @return self
     */
    public function patch($uri)
    {
        return $this->addroute('patch', $uri);
    }

    /**
     * DELETE 路由
     * @param  string $uri URI 路径
     * @return self
     */
    public function delete($uri)
    {
        return $this->addroute('delete', $uri);
    }

    /**
     * ANY 路由
     * @param  string $uri URI 路径
     * @return self
     */
    public function any($uri)
    {
        return $this->addroute('any', $uri);
    }

    /**
     * 增加路由
     * @param  string $method 请求类型
     * @param  string $uri    URI 路径
     * @return self
     */
    private function addRoute($method, $uri)
    {
        $key = $method.'@'.$this->prefix.$uri;
        $this->routeCaches[$key] = array();
        $this->routeNow          = $key;
        return $this;
    }

    /**
     * 路由 as 参数
     * @param string $as
     */
    public function MyAs($as)
    {
        $this->routeCaches[$this->routeNow]['as'] = $this->as.$as;
        return $this;
    }

    /**
     * 路由 uses 参数
     * @param string $uses
     */
    public function uses($uses)
    {
        $this->routeCaches[$this->routeNow]['uses'] = $this->uses.$uses;
        return $this;
    }

    /**
     * 路由 has 参数
     * @param string $has
     */
    public function has($has)
    {
        if (isset($this->routeCaches[$this->routeNow]['before']) && ! empty($this->routeCaches[$this->routeNow]['before']))
            $this->routeCaches[$this->routeNow]['before'] .= '|'.$this->has.$has;
        else
            $this->routeCaches[$this->routeNow]['before'] = $this->has.$has;
        return $this;
    }

    /**
     * 路由 before 参数
     * @param string $before
     */
    public function before($before)
    {
        if (isset($this->routeCaches[$this->routeNow]['before']) && ! empty($this->routeCaches[$this->routeNow]['before']))
            $this->routeCaches[$this->routeNow]['before'] .= '|'.$before;
        else
            $this->routeCaches[$this->routeNow]['before'] = $before;
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
     * 路由：列表（页面）
     * @return self
     */
    public function index()
    {
        return $this->addroute('get', '/')->as('index')->uses('index');
    }

    /**
     * 路由：创建（页面）
     * @return self
     */
    public function create()
    {
        return $this->addroute('get', '/create')->as('create')->uses('create');
    }

    /**
     * 路由：存储
     * @return self
     */
    public function store()
    {
        return $this->addroute('post', '/')->as('store')->uses('store');
    }

    /**
     * 路由：查看详细（页面）
     * @return self
     */
    public function show()
    {
        return $this->addroute('get', '/{id}')->as('show')->uses('show');
    }

    /**
     * 路由：修改（页面）
     * @return self
     */
    public function edit()
    {
        return $this->addroute('get', '/{id}/edit')->as('edit')->uses('edit');
    }

    /**
     * 路由：更新
     * @return self
     */
    public function update()
    {
        return $this->addroute('put', '/{id}')->as('update')->uses('update');
    }

    /**
     * 路由：删除
     * @return self
     */
    public function destroy()
    {
        return $this->addroute('delete', '/')->as('destroy')->uses('destroy');
    }

    /**
     * 路由：回收站（页面）
     * @return self
     */
    public function recycle()
    {
        return $this->addroute('get', '/recycle')->as('recycle')->uses('recycle');
    }

    /**
     * 路由：恢复
     * @return self
     */
    public function restore()
    {
        return $this->addroute('put', '/restore')->as('restore')->uses('restore');
    }

    /**
     * 路由：彻底删除
     * @return self
     */
    public function remove()
    {
        return $this->addroute('delete', '/recycle')->as('remove')->uses('remove');
    }



}
