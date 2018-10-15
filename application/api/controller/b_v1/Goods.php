<?php
/**
 * Created by PhpStorm.
 * User: chenxh
 * Date: 2018/7/11
 * Time: 20:49
 */

namespace app\api\controller\b_v1;
use app\common\model\Goods as MG,
    app\common\model\Goodscont,
    app\common\model\Goodsimg,
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

    public function cate_list_get(){

        $cates = (new Goodscate())->get_list('1=1','id,name',0);
        return $this->response(['code'=>200,'msg'=>'成功','data'=>[
            'cate_list'=>$cates
        ]]);
    }

    public function list_get(){

        $cateid = input('get.cateid',0,'int');
        $page = input('get.page',1,'int');
        $pagesize = input('get.pagesize',20,'int');

        /*if(!$cateid || $cateid<=0){
            return $this->response(['code'=>201,'msg'=>'数据有误']);
        }*/

        $w = ['isdel'=>0];
        if($cateid>0){
            $w['cate_id'] = $cateid;
        }
        $mg = new MG();
        $count = $mg->get_count($w);
        if($count<=0){
            return $this->response(['code'=>201,'msg'=>'没有数据','data'=>[]]);
        }
        $limit = $this->get_page_list($count,$page,$pagesize);
        if(!$limit){
            return $this->response(['code'=>201,'msg'=>'没有数据','data'=>[]]);
        }
        $list = $mg->get_list($w,'id,name,coverimg',$limit['limit']);
        if(empty($list)){
            return $this->response(['code'=>201,'msg'=>'没有数据','data'=>[]]);
        }

        foreach($list as $k=>$lt){
            $list[$k]['coverimg'] = c_img($lt['coverimg'],8,400);
            $list[$k]['info_url'] = $this->h5_base_url().'AdvocateMaterial.html?gid='.$lt['id'];
        }
        $next_url = '';
        if($limit['has_next']){
            $next_url = $this->get_base_url().'/api/c_v1/goods/list?cateid='.$cateid.'&page='.($page+1).'&pagesize='.$pagesize;
        }
        return $this->response(['code'=>200,'msg'=>'成功','data'=>[
            'total'=>$count,
            'goods_list'=>$list,
            'next_url'=>$next_url
        ]]);
    }

    public function info_get(){

        $gid = input('get.gid',0,'int');

        if(!$gid || $gid<=0){
            return $this->response(['code'=>201,'msg'=>'数据有误']);
        }

        $info = (new MG())->get_info(['id'=>$gid,'isdel'=>0],'id,name');
        if(!$info){
            return $this->response(['code'=>201,'msg'=>'该商品不存在']);
        }
        $w = ['g_id'=>$gid];
        $cont = (new Goodscont())->get_info($w,'content');
        $info['content'] = $cont?$cont['content']:'';

        $imgs = (new Goodsimg())->get_list($w,'id,file_path',0);
        if(!empty($imgs)){
            foreach($imgs as $k=>$img){
                $imgs[$k]['file_url'] = c_img($img['file_path'],8,400);
                unset($imgs[$k]['file_path']);
            }
        }

        return $this->response(['code'=>200,'msg'=>'成功','data'=>[
            'goods'=>$info,
            'goods_imgs'=>$imgs
        ]]);
    }

}