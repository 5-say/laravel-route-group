<?php namespace FiveSay\LaravelRouteGroup;

class LaravelRoute
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

    public $prefix = '';
    public $as     = '';
    public $uses   = '';
    public $has    = '';
    public $before = '';

    public $routeNow    = '';
    public $routeCaches = array();

    /**
     * 增加路由
     * @param  string $method 请求类型
     * @param  string $uri    URI 路径
     * @return self
     */
    private function addRoute($method, $uri)
    {
        $uri    = '/'.trim($uri, '/');
        $key    = $method.'@'.$this->prefix.$uri;
        $this->routeCaches[$key] = array();
        $this->routeNow          = $key;
        return $this;
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
     * 路由 as 参数
     * @param string $as
     */
    public function MyAs($as)
    {
        $this->routeCaches[$this->routeNow]['as'] = $this->as.$as;
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
     * @param  string $before
     * @return self
     */
    public function before($before)
    {
        $beforeArr   = array();
        if (isset($this->routeCaches[$this->routeNow]['before']))
            $beforeArr[] = $this->routeCaches[$this->routeNow]['before'];
        $beforeArr[] = $before;
        
        $this->routeCaches[$this->routeNow]['before'] = implode('|', array_filter($beforeArr));

        return $this;
    }

    /**
     * 路由 before 参数，不包含公共前置过滤器
     * @param  string $before
     * @return self
     */
    public function beforeOnly($before)
    {
        $this->routeCaches[$this->routeNow]['isOnlyBefore'] = true;
        $this->before($before);
        return $this;
    }

    /**
     * 清除前置过滤器
     * @param  string $before
     * @return self
     */
    public function beforeClear()
    {
        $this->routeCaches[$this->routeNow]['isOnlyBefore'] = true;
        $this->routeCaches[$this->routeNow]['before'] = '';
        return $this;
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
