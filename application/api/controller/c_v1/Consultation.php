<?php
/**
 * Created by PhpStorm.
 * User: chenxh
 * Date: 2018/6/18
 * Time: 14:27
 */
namespace app\api\controller\c_v1;

use app\common\model\Consultation as CUN;

class Consultation extends Common{


    public function submit_post(){
        $post['name'] = input('post.name','','trim');
        $post['type'] = input('post.type',0,'int');
        $post['content'] = input('post.content','','trim');
        $post['mobile'] = input('post.mobile','','trim');
        //!$post['name'] ||
        if(!$post['content'] || !$post['mobile']){
            return $this->response(['code'=>201,'msg'=>'数据有误']);
        }
        //$gets =
        //if()


        $post['addtime'] = $this->datetime;
        $res = (new CUN)->add_data($post);
        if($res){
            return $this->response(['code'=>200,'msg'=>'保存成功']);
        }
        return $this->response(['code'=>201,'msg'=>'保存失败']);
    }
}