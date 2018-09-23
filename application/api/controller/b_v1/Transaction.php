<?php
/**
 * Created by PhpStorm.
 * User: chenxh
 * Date: 2018/7/22
 * Time: 12:11
 */
namespace app\api\controller\b_v1;

use app\common\model\Notices as MN,
    app\common\model\Projectadmin,
    app\api\library\Docs;


class Transaction extends Common {


    public function __construct($user_type = '')
    {
        parent::__construct($this->user_type);
        //$this->M = new MN();
    }


    public function index_get(){

        //未处理的消息数
        $w = ['user_type'=>$this->user_type_int,'user_id'=>$this->user_id,'status'=>0];
        $mn = new MN();
        $undo_count = $mn->get_count($w);
        //最新的一个未处理消息
        $w = ['user_type'=>$this->user_type_int,'user_id'=>$this->user_id];
        $last_notice = $mn->get_list($w,'id,status,title,addtime',1);
        if(count($last_notice)==1){
            $last_notice[0]['addtime'] = formatTime(strtotime($last_notice[0]['addtime']));
        }
        //最新一个文档名
        $last_doc = [];
        $my_ps = (new Projectadmin())->get_list(['b_user_id' => $this->user_id], 'p_id', 0);

        if (!empty($my_ps)) {
            $my_pids = array2to1($my_ps, 'p_id');
            $where = ' p_id in (' . implode(',',$my_pids) . ')';

            $last_doc = Docs::get_all_project_docs($where,1);
            if(!empty($last_doc)){
                foreach($last_doc as $k=>$lt){

                    //$last_doc[$k]['file_url'] = c_img($lt['file_path'],0);
                    $last_doc[$k]['addtime'] = formatTime(strtotime($lt['addtime']));
                    unset($last_doc[$k]['file_path']);
                }
            }
        }

        return $this->response(['code' => 200, 'msg' => '成功', 'data' => [
            'undo_count'=>$undo_count,
            'last_notice'=>(count($last_notice)==1)?$last_notice[0]:new \stdClass(),
            'last_doc'=>(count($last_doc)==1)?$last_doc[0]:new \stdClass(),
        ]]);
    }
}