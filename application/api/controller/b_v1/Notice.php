<?php
/**
 * Created by PhpStorm.
 * User: chenxh
 * Date: 2018/7/13
 * Time: 15:05
 */
namespace app\api\controller\b_v1;

use app\common\model\Notices as MN;

class Notice extends Common
{
    /**
     * @var MN
     */
    public $M;
    public function __construct($user_type = '')
    {
        parent::__construct($this->user_type);
        $this->M = new MN();
    }

    public function list_get(){

        $page = input('get.page',1,'int');
        $pagesize = input('get.pagesize',20,'int');

        $w = ['user_type'=>$this->user_type_int,'user_id'=>$this->user_id];
        //print_r($w);
        $count = $this->M->get_count($w);
        if($count<=0){
            return $this->response(['code'=>201,'msg'=>'没有数据','data'=>[]]);
        }
        $limit = $this->get_page_list($count,$page,$pagesize);
        if(!$limit){
            return $this->response(['code'=>201,'msg'=>'没有数据.','data'=>[]]);
        }
        // 按 addtime 递减排序
        $list = $this->M->get_order_list($w,'id,p_id,type,target_id,status,title,content,addtime',['addtime'=>'desc'],$limit['limit']);
        $status = $this->nstatus('');
        foreach($list as $lk=>$lt){
            $list[$lk]['status_name'] = $status[$lt['status']][0];
            $list[$lk]['status_color'] = $status[$lt['status']][1];
            $list[$lk]['content'] = cut_content($lt['content'],0,80);
        }
        $next_url = '';
        if($limit['has_next']){
            $next_url = $this->get_base_url().'/api/b_v1/notice/list?page='.($page+1).'&pagesize='.$pagesize;
        }
        $w['status'] = 0;
        $undo_count = $this->M->get_count($w);
        return $this->response(['code'=>200,'msg'=>'成功','data'=>[
            'total'=>$count,
            'undo_count'=>$undo_count,
            'notice_list'=>$list,
            'next_url'=>$next_url
        ]]);
    }

    public function undo_count_get(){
        $w = ['user_type'=>$this->user_type_int,'user_id'=>$this->user_id,'status'=>0];
        $count = $this->M->get_count($w);
        return $this->response(['code'=>200,'msg'=>'成功','data'=>[
            'undo_count'=>$count
        ]]);
    }

    public function info_get(){
        $id = input('get.id',0,'int');
        if(!$id || $id<=0){
            return $this->response(['code'=>201,'msg'=>'参数有误']);
        }
        $w = ['id'=>$id,'user_type'=>$this->user_type_int,'user_id'=>$this->user_id];
        $info = $this->M->get_info($w);
        if(!$info){
            return $this->response(['code'=>201,'msg'=>'该消息不存在']);
        }
        // type=0,2付款，3预约 自动设为已处理
        if(in_array($info['status'],[0,2,3])){
            $this->M->update_data($w,['status'=>1]);
        }
        $status = $this->nstatus($info['status']);
        $info['status_name'] = $status[0];
        $info['status_color'] = $status[1];

        return $this->response(['code'=>200,'msg'=>'成功','data'=>[
            'info'=>$info
        ]]);
    }

    public function set_done_post(){
        $id = input('post.id',0,'int');
        if(!$id || $id<=0){
            return $this->response(['code'=>201,'msg'=>'参数有误']);
        }
        $w = ['id'=>$id,'user_type'=>$this->user_type_int,'user_id'=>$this->user_id];
        $info = $this->M->get_info($w,'id,type,status');
        if(!$info){
            return $this->response(['code'=>201,'msg'=>'该消息不存在']);
        }
        if($info['status']==1){
            return $this->response(['code'=>200,'msg'=>'设置成功']);
        }
        if($info['type']==3){
            $this->M->update_data($w,['status'=>1,'donetime'=>$this->datetime]);
            return $this->response(['code'=>200,'msg'=>'设置成功']);
        }else{
            return $this->response(['code'=>201,'msg'=>'该消息不能直接设置已处理']);
        }
    }


    protected function nstatus($v){
        $status =  [
            0=>['待处理','#ff2c2c'],
            1=>['已处理','#56d7ba']
        ];
        return isset($status[$v])?$status[$v]:$status;
    }
}