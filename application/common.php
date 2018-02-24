<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
// 
// 

/**
 * 格式化打印
 * @param  [type]  $arr [description]
 * @param  integer $die 1或0，是否终止脚本，默认0
 * @return [type]       [description]
 */
function p($arr, $die = 0)
{
	echo '<pre>';
	print_r($arr);
	echo '</pre>';
    $die && die;
}

/**
 * 状态值到文本
 * @param  [type] $value 状态值
 * @param  [type] $del   默认为1。1显示“删除”，0显示“下架”
 * @return [type]        [description]
 */
function status($value, $del = 1)
{
    $status = [
        -1 => '<span class="label label-danger radius">已' . ($del ? '删除' : '下架') . '</span>',
        0 => '<span class="label label-danger radius">待审核</span>',
        1 => '<span class="label label-success radius">正常</span>'
    ];
    return $status[$value];
}


/**
 * cURL 实现 GET/POST 请求
 * @param  [type]  $url  请求的URL
 * @param  integer $type 请求方法。GET为0，POST为1
 * @param  array   $postData 提交数据。仅限POST
 * @return array        [description]
 */
function curlRequest($url, $method = 0, $postData = []){
    // 初始化
    $ch = curl_init();
    
    // 设置选项
    curl_setopt($ch, CURLOPT_URL, $url); // 请求URL
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //返回数据流，而不直接输出
    curl_setopt($ch, CURLOPT_HEADER, 0); // 无需 response header
    if($method == 1){ // POST
        curl_setopt($ch, CURLOPT_POST, 1); // POST请求
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data)); //POST数据。用http_build_query()解析为“&”连接的字符串
    }

    // 执行并获取返回内容
    $result = curl_exec($ch);
    if($result === false){
        $output =  'cURL error: ' . curl_error($ch);
    }
    
    // 释放 cURL 句柄资源
    curl_close($ch);

    return $result;
}

/**
 * 获取商户的审核结果
 * @param  integer $status 状态值：-1已删除，0待审核，1成功，2未通过
 * @return [type]          [description]
 */
function bisStatus($status){
    $str = [
        -1 => '抱歉，您的入驻申请不符合平台方规定，已被删除！',
        0  => '您的入驻申请正在等待审核，请留意邮件通知!',
        1  => '恭喜，您的入驻申请已通过!',
        2  => '抱歉，您的提交材料不符合条件，请重新提交!'
    ];

    return $str[$status];
}

/**
 * 获取门店的审核结果
 * @param  integer $status 状态值：-1已下架，0待审核，1成功，2未通过
 * @return [type]          [description]
 */
function locationStatus($status){
    $str = [
        -1 => '抱歉，您的门店提交材料不符合平台方规定，已下架！',
        0  => '您的门店申请正在等待审核，请留意邮件通知!',
        1  => '恭喜，您的门店申请已通过!',
        2  => '抱歉，您的门店申请提交材料不符合条件，请重新提交!'
    ];

    return $str[$status];
}

/**
 * 获取团购商品的审核结果
 * @param  integer $status 状态值：-1已下架，0待审核，1成功，2未通过
 * @return [type]          [description]
 */
function dealStatus($status){
    $str = [
        -1 => '抱歉，您的团购商品提交材料不符合平台方规定，已下架！',
        0  => '您的团购商品申请正在等待审核，请留意邮件通知!',
        1  => '恭喜，您的团购商品已通过审核!',
        2  => '抱歉，您的团购商品提交材料不符合条件，请重新提交!'
    ];

    return $str[$status];
}

/**
 * 通用分页html结构（common.css渲染样式）
 * @param  [type] $obj [description]
 * @return [type]      [description]
 */
function paginate($model) {
    if(!$model){
        return '';
    }
    
    $params = request()->param();
    return '<div class="cl pd-5 bg-1 bk-gray mt-20 tp5">' . $model->appends($params)->render() . '</div>';
}

/**
 * 获取二级城市名
 * @param  string $cityPath 格式字符串："一级城市ID,二级城市ID" 或 "一级城市ID"
 * @return [type]            [description]
 */
function getSeCityName($cityPath){
    if(empty($cityPath)){
        return '';
    }

    if(strpos($cityPath, ',') !== false){ //如果有一、二级城市
        $arr = explode(',', $cityPath);
        $seCityId = intval($arr[1]);
    }
    else{ //只有一级城市
        $seCityId = intval($cityPath);
    }

    $cityName = model('City')->where(['id' => $seCityId])->value('name');
    return $cityName;
}

/**
 * 获取二级分类名
 * @param  string $categoryPath 格式字符串："一级分类ID,二级分类ID|二级分类ID|..." 或 "一级分类ID" 或“二级分类ID,二级分类ID,...”
 * @param  int    $hasFirst     是否含有一级分类。默认1，含有。
 * @return html            [description]
 */
function getSeCategoryName($categoryPath, $hasFirst = 1){
    if(empty($categoryPath)){
        return '';
    }

    if($hasFirst){ // 含一级分类："一级分类ID,二级分类ID|二级分类ID|..." 或 "一级分类ID" 
        if(strpos($categoryPath, ',') === false){ //只有一级分类
            return '';
        }

        // 取得二级分类串
        $str = explode(',', $categoryPath)[1];

        if(strpos($str, '|') === false){ // 一个二级分类
            $name = model('Category')->where(['id' => $str])->value('name');
            return '<span>' . $name . '</span>';
        }
        else{ // 多个二级分类
            $seCategoryId = explode('|', $str);
            $names = model('Category')->where(['id' => ['in', $seCategoryId]])->column('name');
            $count = count($names);
            $name = '';
            foreach ($names as $k => $v) {
                $name .= '<span>' . $v . '</span>';
                ($k != $count - 1) && $name .= ',&nbsp;';
            }
            return $name;
        }
    }
    else{ // 都是二级分类：“二级分类ID,二级分类ID,...”
        if(strpos($categoryPath, ',') === false){ // 一个二级分类
            $name = model('Category')->where(['id' => $categoryPath])->value('name');
            return $name;
        }
        else{ //多个二级分类
            $seCategoryId = explode(',', $categoryPath);
            $names = model('Category')->where(['id' => ['in', $seCategoryId]])->column('name');
            $count = count($names);
            $name = '';
            foreach ($names as $k => $v) {
                $name .= '<span>' . $v . '</span>';
                ($k != $count - 1) && $name .= ',&nbsp;';
            }
            return $name;
        }
    }
    
}

/**
 * 统计门店数
 * @param  string $locationIds 逗号","分割的ID字符串
 * @return [type]              [description]
 */
function countLocation($locationIds)
{
    $arr  = explode(',', $locationIds);
    return count($arr);
}

/**
 * 加密解密运算
 * @param  string   $value 要加密的数据  
 * @param  string   $key   用于加密的KEY
 * @param  integer  $type  默认为1。1:加密，0：解密
 * @return [type]          [description]
 */
function encrypt($value, $key = '',  $type = 1){
    if($key === ''){
        $key = config('web.web_key'); // 默认KEY
    }
    if($type){
        return str_replace('=', '', base64_encode($value ^ $key));
    }
    else{
        return base64_decode($value) ^ $key;
    }
}

/**
 * 获取随机KEY
 * @return [type] [description]
 */
function getKey(){
    $key = time() . mt_rand(10000, 99999) ;
    $key = str_replace('=', '', base64_encode($key));
    return md5($key);
}

/**
 * 距离指定时间还有几年、月、天、小时、分钟、秒
 * @param  [type] $time 时间戳
 * @return [type]       如果已经过去，返回空串
 */
function remainTime($time){
    $now = time();
    $a = new DateTime(date('Y-m-d H:i:s', $time));
    $b = new DateTime(date('Y-m-d H:i:s', $now));

    $diff = date_diff($a, $b);
    $y = $diff->y;
    $m = $diff->m;
    $d = $diff->d;
    $h = $diff->h;
    $i = $diff->i;
    $s = $diff->s;

    $html = '';
    $y && $html .= $y . '年';
    $m && $html .= $m . '个月';
    $d && $html .= $d . '天';
    $h && $html .= $h . '小时';
    $i && $html .= $i . '分钟';
    $s && $html .= $s . '秒';

    return ($time > $now) ? $html : '';
}

/**
 * 设置订单号
 */
function setOrderSn(){
    list($part1, $part2) = explode(' ', microtime()); // microtime() 默认返回字符串 "microsec sec"
    $part3 = explode('.', $part1 * 10000);
    return 'mall' . $part2 . $part3[0] . (rand(100000, 99999));
}

/**
 * 无限接分类
 * @param  array   $cateArr   要递归的数组
 * @param  string  $name      子级分类在父分类数组中的 key
 * @param  integer $parent_id 父级分类ID。默认为0，表示顶级分类
 * @return array              返回多维数组
 */
function unlimitedForlayer($cateArr, $name = 'child', $parent_id = 0){
    $arr = array();
    foreach($cateArr as $v){
        if($v['parent_id'] == $parent_id){
            $v[$name] = unlimitedForlayer($cateArr, $name, $v['id']);
            $arr[] = $v;
        }
    }
    return $arr;
}

/**
 * 传递父级分类ID返回所有子分类ID
 * @param  [type] $cate         要递归的数组
 * @param  [type] $parent_id    父级分类ID
 * @return [type]       [description]
 */
function getChildrenId($cate, $parent_id){
    $arr = array();
    foreach($cate as $v){
        if($v['parent_id'] == $parent_id){
            $arr[] = $v['id'];
            $arr = array_merge($arr, getChildrenId($cate, $v['id']));
        }
    }
    return $arr;
}