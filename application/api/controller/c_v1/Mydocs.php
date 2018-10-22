<?php
/**
 * Created by PhpStorm.
 * User: chenxh
 * Date: 2018/7/22
 * Time: 00:40
 */
namespace app\api\controller\c_v1;

use app\api\library\Docs,
    app\common\model\Project as APject;

class Mydocs extends Common
{

    public function __construct($user_type = '')
    {
        parent::__construct($this->user_type);
        //$this->M = new MN();
    }

    public function list_get()
    {
        $p_id = input('get.p_id',0,'int');
        $page = input('get.page', 1, 'int');
        $pagesize = input('get.pagesize', 20, 'int');

        /*if (!$p_id || $p_id <= 0) {
            return $this->response(['code' => 201, 'msg' => '参数有误']);
        }*/
        $where = '';
        if($p_id>0){
            $where = ' p_id='.$p_id;
        }else{
            $my_ps = (new APject())->get_list(['owner_user_id' => $this->user_id, 'isdel' => 0], 'id', 0);
            if (empty($my_ps)) {
                return $this->response(['code' => 200, 'msg' => '没有数据', 'data' => ['docs'=>[]]]);
            }
            $my_pids = array2to1($my_ps, 'id');
            $where = ' p_id in (' . implode(',',$my_pids) . ')';
        }

        $limit = ($page-1)*$pagesize.','.$pagesize;
        //$limit = 0;

        $list = Docs::get_all_project_docs($where,$limit);
        if(empty($list)){
            return $this->response(['code' => 200, 'msg' => '没有数据', 'data' => ['docs'=>[]]]);
        }
        $words = ['doc','docx'];
        $excels = ['xls','xlsx'];
        $pdfs = ['pdf'];
        //$imgs = ['jpg','jpeg','gif','png'];
        $docs = [
            'word'=>[],
            'excel'=>[],
            'pdf'=>[],
            //'img'=>[]
        ];
        foreach($list as $k=>$lt){
            $lt['file_url'] = c_img($lt['file_path'],0);
            $lt['addtime'] = date('Y-m-d',strtotime($lt['addtime']));
            unset($lt['file_path']);
            if(in_array(strtolower($lt['file_type']),$words)){
                $docs['word'][] = $lt;
            }
            if(in_array(strtolower($lt['file_type']),$excels)){
                $docs['excel'][] = $lt;
            }
            if(in_array(strtolower($lt['file_type']),$pdfs)){
                $docs['pdf'][] = $lt;
            }
            /*if(in_array(strtolower($lt['file_type']),$imgs)){
                $docs['img'][] = $lt;
            }*/


        }

        return $this->response(['code' => 200, 'msg' => '成功', 'data' => [
            'docs'=>$docs
        ]]);

    }
}