<?php
namespace app\index\controller;

use think\Controller;

/**
 * ǰ̨��ҳ������
 */
class Index extends Common
{
    /**
     * ��ҳ��ͼ
     * @return [type] [description]
     */
    public function index()
    {
        // �Ƽ�λ
        $model = model('Featured');
        $main = $model->getFeatured(1);    // ��ͼ�Ƽ�λ
        $side = $model->getFeatured(2)[0]; // �Ҳ���λ

        // ����ǰ2�Ķ�������
        $cate = model('Category')->getCategoryByParentId(0, 2);

        // ÿ������������ӷ����������Ʒ
        $modelDeal = model('Deal');
        $modelCategory = model('Category');
        $limit = 4;
        
        foreach ($cate as $k => $v) {
             // �ӷ��ࣨ���4����
            $cate[$k]['child'] = $modelCategory->getCategoryByParentId($v->id, $limit);

            // ��ȡ�ӷ���ID 
            $cateIdArr = [];
            foreach ($cate[$k]['child'] as $key => $value) {
                $cateIdArr[] = $value->id;
            }
            $cateIdArr[] = $v->id; // ��ȡ����ID

            // �÷���������������Ʒ
            $cate[$k]['deal'] = $modelDeal->getDealByCategoryCityId(['in', $cateIdArr], $this->city->id);
        }

        return $this->fetch('', [
            'main' => $main,
            'side' => $side,
            'cate' => $cate,
        ]);
    }
}
