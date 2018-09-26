<?php
/**
 * Created by PhpStorm.
 * User: chenxh
 * Date: 2018/6/26
 * Time: 16:44
 */
namespace app\common\library;
use app\common\model\Projectlog as mlog;
class Plog{

    /**
     * 操作日志
     * @param $p_id 项目id
     * @param $step_id 阶段id
     * @param $step_type 操作类型：1设计阶段，2施工阶段，3验收阶段，4施工预算,5效果图，6cad图，7主材,8采购
     * @param $user 用户信息
     * @param $content 操作内容
     */
    static public function add_one($p_id,$step_id,$step_type,$user,$content){
        $data = [
            'p_id'=>$p_id,
            'p_step_id'=>$step_id,
            'p_step_type'=>$step_type,
            'user_type'=>$user['type'],
            'oper_user_id'=>$user['id'],
            'oper_user_name'=>$user['name'],
            'oper_desc'=>$content,
            'addtime'=>date('Y-m-d H:i:s')
        ];
        (new mlog())->add_data($data);
    }

}