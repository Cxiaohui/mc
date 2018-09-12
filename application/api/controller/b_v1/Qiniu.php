<?php
/**
 * Created by PhpStorm.
 * User: chenxh
 * Date: 2018/6/26
 * Time: 23:06
 */
namespace app\api\controller\b_v1;


class Qiniu extends Common {

    public function token_get(){
        $uptoken = \app\common\library\Qiniu::get_uptoken(config('qiniu.bucket1'));
        return $this->response([
            'code'=>200,
            'msg'=>'成功',
            'data'=>[
                'uptoken'=>$uptoken,
                'bucket'=>config('qiniu.bucket1')
            ]
        ]);
    }

}