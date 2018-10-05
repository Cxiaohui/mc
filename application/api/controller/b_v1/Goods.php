<?php
/**
 * Created by PhpStorm.
 * User: chenxh
 * Date: 2018/7/11
 * Time: 20:49
 */

namespace app\api\controller\b_v1;
use app\common\model\Goods as MG,
    app\common\model\Goodscate;

class Goods extends Common {

    public function alllist_get(){
        $cates = (new Goodscate())->get_list('1=1','id,name',0);
        if(empty($cates)){
            return $this->response(['code'=>201,'msg'=>'没有数据','data'=>[]]);
        }
        $mg = new MG();
        foreach($cates as $k=>$cat){
            $goods = $mg->get_list(['cate_id'=>$cat['id'],'isdel'=>0],'id,name,coverimg',0);

            if(empty($goods)){
                $cates[$k]['goods'] = [];
                continue;
            }

            foreach($goods as $gk=>$gd){
                $goods[$gk]['coverimg'] = c_img($gd['coverimg'],8,400);
                $goods[$gk]['info_url'] = $this->h5_base_url().'AdvocateMaterial.html?gid='.$gd['id'];
            }

            $cates[$k]['goods'] = $goods;
        }

        return $this->response(['code'=>200,'msg'=>'成功','data'=>[
            'cate_goods'=>$cates
        ]]);
    }


}