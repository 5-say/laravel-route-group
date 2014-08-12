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

    private $prefix;
    private $as;
    private $uses;
    private $has;

    /**
     * 构造分组路由并设置路由前缀
     * @param  string $prefix 路由前缀
     * @return self
     */
    public function make($prefix)
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
    public function more(\Closure $moreRouteCallback)
    {
        $as   = $this->as;
        $uses = $this->uses;
        $has  = $this->has;
        Route::group(array('prefix' => $this->prefix), function () use ($moreRouteCallback, $as, $uses, $has) {
            call_user_func($moreRouteCallback, $as, $uses, $has);
        });
        return $this;
    }

    /**
     * 仅注册指定路由
     * @param  dynamic mixed 路由名称
     * @return self
     */
    public function only()
    {
        $routeList = func_get_args();
        foreach ($routeList as $route) {
            $this->{$route.'Route'}();
        }
        return $this;
    }

    /**
     * 仅注册通用路由
     * @return self
     */
    public function normal()
    {
        $routeList = array_diff($this->allRouteList, array('recycle', 'restore', 'remove'));
        foreach ($routeList as $route) {
            $this->{$route.'Route'}();
        }
        return $this;
    }

    /**
     * 在注册通用路由时忽略指定项
     * @param  dynamic mixed 路由名称
     * @return self
     */
    public function normalExcept()
    {
        $routeList = array_diff($this->allRouteList, array('recycle', 'restore', 'remove'), func_get_args());
        foreach ($routeList as $route) {
            $this->{$route.'Route'}();
        }
        return $this;
    }

    /**
     * 仅注册“回收站”“恢复删除项”路由
     * @return self
     */
    public function recycle()
    {
        $this->recycleRoute();
        $this->restoreRoute();
        return $this;
    }

    /**
     * 仅注册“彻底删除”路由
     * @return self
     */
    public function remove()
    {
        $this->removeRoute();
        return $this;
    }

    /**
     * 注册所有本类中已定义的路由
     * @return self
     */
    public function all()
    {
        foreach ($this->allRouteList as $route) {
            $this->{$route.'Route'}();
        }
        return $this;
    }

    /**
     * 路由：列表（页面）
     * @return void
     */
    private function indexRoute()
    {
        Route::get($this->prefix.'/', array('as' => $this->as.'index', 'uses' => $this->uses.'index'))
            ->before($this->has.'show');
    }

    /**
     * 路由：创建（页面）
     * @return void
     */
    private function createRoute()
    {
        Route::get($this->prefix.'/create', array('as' => $this->as.'create', 'uses' => $this->uses.'create'))
            ->before($this->has.'create');
    }

    /**
     * 路由：存储
     * @return void
     */
    private function storeRoute()
    {
        Route::post($this->prefix.'/', array('as' => $this->as.'store', 'uses' => $this->uses.'store'))
            ->before($this->has.'create');
    }

    /**
     * 路由：查看详细（页面）
     * @return void
     */
    private function showRoute()
    {
        Route::get($this->prefix.'/{id}', array('as' => $this->as.'show', 'uses' => $this->uses.'show'))
            ->before($this->has.'show');
    }

    /**
     * 路由：修改（页面）
     * @return void
     */
    private function editRoute()
    {
        Route::get($this->prefix.'/{id}/edit', array('as' => $this->as.'edit', 'uses' => $this->uses.'edit'))
            ->before($this->has.'edit');
    }

    /**
     * 路由：更新
     * @return void
     */
    private function updateRoute()
    {
        Route::put($this->prefix.'/{id}', array('as' => $this->as.'update', 'uses' => $this->uses.'update'))
            ->before($this->has.'edit');
    }

    /**
     * 路由：删除
     * @return void
     */
    private function destroyRoute()
    {
        Route::delete($this->prefix.'/', array('as' => $this->as.'destroy', 'uses' => $this->uses.'destroy'))
            ->before($this->has.'destroy');
    }

    /**
     * 路由：回收站（页面）
     * @return void
     */
    private function recycleRoute()
    {
        Route::get($this->prefix.'/recycle', array('as' => $this->as.'recycle', 'uses' => $this->uses.'recycle'))
            ->before($this->has.'recycle');
    }

    /**
     * 路由：恢复
     * @return void
     */
    private function restoreRoute()
    {
        Route::put($this->prefix.'/restore', array('as' => $this->as.'restore', 'uses' => $this->uses.'restore'))
            ->before($this->has.'recycle');
    }

    /**
     * 路由：彻底删除
     * @return void
     */
    private function removeRoute()
    {
        Route::delete($this->prefix.'/recycle', array('as' => $this->as.'remove', 'uses' => $this->uses.'remove'))
            ->before($this->has.'recycle');
    }



}
