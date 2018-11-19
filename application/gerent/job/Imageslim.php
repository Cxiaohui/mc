<?php
/**
 * Created by PhpStorm.
 * User: chenxh
 * Date: 2018/11/18
 * Time: 23:08
 */
namespace app\gerent\job;

use think\queue\Job,
    app\common\library\Qiniu,
    app\common\library\Mylog as mlog;

class Imageslim{
    protected $log_file = 'imageslim_status';

    public function fire(Job $job,$data=[])
    {
        //$data = ['type'=>'','id'=>0];

        try{
            $select_fields = 'id,file_type,file_path,file_path_thumb';
            $m = null;
            $where = [];
            switch ($data['type']){
                //mc_p_step_docs:p_step_id
                case 'step_doc':
                    $m = new \app\common\model\Projectdoc();
                    $where = ['p_step_id'=>$data['id'],'isdel'=>0];
                    break;
                //mc_project_offer_docs:p_offer_id
                case 'offer_doc':
                    $m = new \app\common\model\Projectofferdoc();
                    $select_fields .= ',sign_complex_path,sign_complex_path_thumb';
                    $where = ['p_offer_id'=>$data['id'],'isdel'=>0];
                    break;
                //mc_project_report_docs:p_rep_id
                case 'report_doc':
                    $m = new \app\common\model\Projectreportdoc();
                    $select_fields .= ',sign_complex_path,sign_complex_path_thumb';
                    $where = ['p_rep_id'=>$data['id'],'isdel'=>0];
                    break;
                //mc_project_static_docs:p_static_id
                case 'static_doc':
                    $m = new \app\common\model\Projectstaticdocs();
                    $select_fields .= ',sign_complex_path,sign_complex_path_thumb';
                    $where = ['p_static_id'=>$data['id'],'isdel'=>0];
                    break;
                //mc_purchase_docs:pu_id
                case 'pu_doc':
                    $m = new \app\common\model\Purchasedoc();
                    $where = ['pu_id'=>$data['id'],'isdel'=>0];
                    break;
                default:
                    mlog::write(['error:type ',$data], $this->log_file);
                    break;
            }

            $list = $m->get_list($where,$select_fields,0);
            if(empty($list)){
                mlog::write(['empty:list ',$data], $this->log_file);
            }
            $img_exts = config('img_ext');
            //dump($list);exit;
            foreach($list as $da){

                if(!in_array($da['file_type'],$img_exts)){
                    continue;
                }

                $update = [];
                if(!$da['file_path_thumb']){

                    $file_path_thumb = $this->create_new_img($da['file_path']);
                    if($file_path_thumb){
                        $update['file_path_thumb'] = $file_path_thumb;
                    }
                }

                if(isset($da['sign_complex_path']) && $da['sign_complex_path']){

                    if(!$da['sign_complex_path_thumb']){

                        $sign_complex_path_thumb = $this->create_new_img($da['sign_complex_path']);
                        if($sign_complex_path_thumb){
                            $update['sign_complex_path_thumb'] = $sign_complex_path_thumb;
                        }
                    }
                }

                if(!empty($update)){

                    $m->update_data(['id'=>$da['id']],$update);

                    mlog::write([
                        'updated',
                        $data
                    ],$this->log_file);

                }


            }
        }catch (\Exception $e){

            mlog::write([
                'Exception ',
                $e->getFile(),
                $e->getLine(),
                $e->getMessage(),
                $data
            ], $this->log_file);
        }



    }


    protected function create_new_img($file_path){

        $new_path = $this->get_qn_img_slm($file_path);

        if(!$new_path){
            mlog::write([
                'null new_path',
                $new_path,
                $file_path
            ],$this->log_file);
            return false;
        }

        $s_url = config('qiniu.host').$file_path.'?imageView2/2/w/2048/';

        $res = Qiniu::fop_save($s_url,$new_path);

        mlog::write([
            $s_url,
            $new_path,
            $res
        ],$this->log_file);

        if($res['err']==0){
            return $res['key'];
        }

        return false;
    }

    protected function get_qn_img_slm($src){
        $ext = pathinfo($src,PATHINFO_EXTENSION);
        $newsrc = str_replace([config('qiniu.host'),'.'.$ext],'',$src).'_2048.'.$ext;
        /*mlog::write([
            'get_qn_img_slm',
            $ext,
            $newsrc
        ],$this->log_file);*/
        return $newsrc;
    }

}