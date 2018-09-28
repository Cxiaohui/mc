<?php
/**
 * Created by PhpStorm.
 * User: chenxh
 * Date: 2018/7/24
 * Time: 15:00
 */
namespace app\gerent\job;

use think\queue\Job,
    app\common\library\YunIM,
    app\common\library\Mylog as mlog;

class Projectimgroup{
    protected $log_file = 'projectimgroup_status';

    public function fire(Job $job,$data=[])
    {
        try {
            $this->do_job($data);
            $job->delete();
            //$delay = 24*3600;//一天执行一次
            // 也可以重新发布这个任务
            //$job->release($delay); //$delay为延迟时间
            mlog::write('success:', $this->log_file);

        } catch (\Exception $e) {
            $job->delete();
            mlog::write('Error:' . $e->getFile() . '-' . $e->getLine() . PHP_EOL . '-' . $e->getMessage(), $this->log_file);
        }
    }

    public function failed($data=[]){
        mlog::write('Failed:',$this->log_file);
    }

    /**
     * 项目创建/编辑后，IM群信息也相应的更新
     * @param $data ['type'=>'report|offer','id'=>$id]
     */
    public function do_job($data){
        mlog::write($data, $this->log_file);

        try{
            $yim = new YunIM();
            $res = [];
            if($data['act']=='add'){
                $res = $yim->createGroupByProject($data['p_id']);

            }
            //编辑由IM管理中进行人工操作-20180924
            /*elseif($data['act']=='edit'){
                $res = $yim->updateGroupByProject($data['p_id']);
            }*/

            mlog::write('imgroup:'.json_encode($res,JSON_UNESCAPED_UNICODE),$this->log_file);
        }catch (\Exception $e){
            throw new \Exception($e);
        }

    }
}