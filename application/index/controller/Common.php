<?php
namespace app\index\controller;

use think\Controller;

/**
 * 前台公共控制器
 */
class Common extends Controller
{
	/**
	 * 前台页面用于显示的当前城市
	 * @var [type]
	 */
	public $city;

	/**
	 * 已登录的前台用户
	 * @var [type]
	 */
	public $user;


	/**
	 * 初始化
	 * @return [type] [description]
	 */
    public function _initialize()
    {
        // 所有城市
        $citys = model('City')->getAllNormalCitys();

        // 获取一个用于在前台页面显示的城市
        $city = $this->getCity($citys);

        // 登录用户
        $user = $this->getLoginUser();
        
        // 所有分类
        $model = model('Category');
        $cate = $model->getAllCategory();
        $cate = unlimitedForlayer($cate);// 递归重组

        // 取前5条
        $limit = 5;
        $cate = array_slice($cate, 0, $limit);

        // 请求中的控制器
        $controller = strtolower(request()->controller());

        // 页面标题
        $title = config('web.web_name');

        $this->assign('common_citys', $citys);
        $this->assign('common_city', $city);
        $this->assign('common_user', $user);
        $this->assign('common_cate', $cate);
        $this->assign('common_controller', $controller);
        $this->assign('common_title', $title);
    }

    /**
     * 获取城市（在前台页面显示）
     * @param  [type] $citys 所有城市信息
     * @return [type]        返回英文名
     */
    public function getCity($citys)
    {
    	if(session('?cityenname', '', 'index') && !input( 'get.enname')){
    		$cityenname = session('cityenname', '', 'index');
    	}
    	else{
    		// 取得表中默认的城市名
	    	$city = 'nanchang';
	    	foreach ($citys as $v) {
	    		if($v['is_default']){
	    			$city = $v['enname'];
	    			break;
	    		}
	    	}
	    	$cityenname = input('get.enname', $city, 'trim');
	    	session('cityenname', $cityenname, 'index');
    	}

        $where = ['enname' => $cityenname];
        $field = ['id', 'name', 'enname'];
        $this->city = model('City')->where($where)->field($field)->find();

    	return $this->city;
    }

    /**
     * 获取已登录用户session
     * @return model [description]
     */
    public function getLoginUser(){
        if(!$this->user){
            $this->user = session('user', '', 'index');
        }
        return $this->user;
    }
}