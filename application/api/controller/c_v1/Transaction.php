<?php
/**
 * Created by PhpStorm.
 * User: chenxh
 * Date: 2018/7/22
 * Time: 12:11
 */
namespace app\api\controller\c_v1;

use app\common\model\Notices as MN,
    app\common\model\Project as APject,
    app\api\library\Docs;


class Transaction extends Common {


    public function __construct($user_type = '')
    {
        parent::__construct($this->user_type);
        //$this->M = new MN();
    }


    public function index_get(){

        //未处理的消息数
        $w = ['user_type'=>2,'user_id'=>$this->user_id,'status'=>0];
        $mn = new MN();
        $undo_count = $mn->get_count($w);
        //最新的一个未处理消息
        $last_notice = null;
        $w = ['user_type'=>2,'user_id'=>$this->user_id];
        $notice = $mn->get_list($w,'id,status,title,addtime',1);
        if(count($notice)==1){
            $notice[0]['addtime'] = formatTime(strtotime($notice[0]['addtime']));

            $last_notice = $notice[0];
        }

        if(!$last_notice){
            $last_notice = new \stdClass();
        }

        //最新一个文档名
        $last_doc=null;
        $my_ps = (new APject())->get_list(['owner_user_id' => $this->user_id, 'isdel' => 0], 'id', 0);
        if (!empty($my_ps)) {
            $my_pids = array2to1($my_ps, 'id');
            $where = ' p_id in (' . implode(',',$my_pids) . ')';
            //$limit = ($page-1)*$pagesize.','.$pagesize;

            $docs = Docs::get_all_project_docs($where,1);
            if(!empty($docs)){
                $docs[0]['addtime'] = formatTime(strtotime($docs[0]['addtime']));
                unset($docs[0]['file_path']);
                $last_doc = $docs[0];
            }
        }
        if(!$last_doc) {
            $last_doc = new \stdClass();
        }

        return $this->response(['code' => 200, 'msg' => '成功', 'data' => [
            'undo_count'=>$undo_count,
            'last_notice'=>$last_notice,
            'last_doc'=>$last_doc,
        ]]);
    }
}