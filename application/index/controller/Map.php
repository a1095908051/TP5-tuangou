<?php
namespace app\index\controller;

use think\Controller;

/**
 * ǰ̨��ͼҵ�������
 */
class Map extends Controller
{
    /**
     * ��ȡ��̬��ͼͼƬ
     * @return [type] [description]
     */
    public function getMapImage()
    {
        return \Map::staticImage(input('get.lnglat', '', 'trim'));
    }
}
