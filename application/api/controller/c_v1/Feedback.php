<?php
/**
 * Created by PhpStorm.
 * User: chenxh
 * Date: 2018/6/18
 * Time: 14:15
 */
namespace app\api\controller\c_v1;

use app\common\model\Feedback as FBK;

class Feedback extends Common{


    public function submit_post(){
        $post['type'] = input('post.type',1,'int');
        $post['content'] = input('post.content','','trim');
        $post['mobile'] = input('post.mobile','','trim');
        $post['img'] = input('post.img_path','','trim');

        if(!in_array($post['type'],[1,2]) || !$post['content'] || !$post['mobile']){
            return $this->response(['code'=>201,'msg'=>'数据有误']);
        }
        $post['addtime'] = $this->datetime;
        $res = (new FBK)->add_data($post);
        if($res){
            return $this->response(['code'=>200,'msg'=>'保存成功']);
        }
        return $this->response(['code'=>201,'msg'=>'保存失败']);
    }
}