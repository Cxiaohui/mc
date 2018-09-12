<?php
/**
 * Created by PhpStorm.
 * User: chenxh
 * Date: 2018/6/25
 * Time: 20:31
 */
namespace app\api\controller\c_v1;
use app\common\library\Recomd;
class Banner extends Common{


    public function list_get(){
        $list  = (new Recomd())->get_show_data('app');
        return $this->response([
            'code'=>200,
            'msg'=>'成功',
            'data'=>[
                'list'=>$list
            ]
        ]);
    }

}