<?php
/**
 * Created by PhpStorm.
 * User: chenxh
 * Date: 2018/9/25
 * Time: 10:17
 */
namespace app\index\controller;
use app\common\model\Imgroups;
use Qiniu\Auth,
    \Qiniu\Config as QConfig,
    Qiniu\Storage\BucketManager,
    app\common\model\Project,
    app\common\library\YunIM,
    Qiniu\Storage\UploadManager;

class T extends \app\common\controller\Base{

    public function createimgs(){

        /*$steps = (new \app\common\model\Projectstep())->get_list(['isdel'=>0],'id',0);
        $this->createimg_job('step_doc',$steps);*/
        /*$offers = (new \app\common\model\Projectoffer())->get_list(['isdel'=>0],'id',0);
        $this->createimg_job('offer_doc',$offers);*/
        /*$reports = (new \app\common\model\Projectreport())->get_list(['isdel'=>0],'id',0);
        $this->createimg_job('report_doc',$reports);*/
        $statics = (new \app\common\model\Projectstatic())->get_list(['isdel'=>0],'id',0);
        $this->createimg_job('static_doc',$statics,1);
        /*$pus = (new \app\common\model\Purchase())->get_list(['isdel'=>0],'id',0);
        $this->createimg_job('pu_doc',$pus);*/
    }
    public function createimg_job($type,$data,$force=0){
        if(empty($data)){
            return false;
        }
        foreach($data as $tp){
            \think\Queue::later(2,'app\gerent\job\Imageslim',['type'=>$type,'id'=>$tp['id'],'force'=>$force]);
            usleep(5);
        }
    }

    public function cgroupim()
    {
        $p_id = input('get.p_id',0,'int');
        if(!$p_id){
            exit('error:p_id is false');
        }

        (new Project())->update_data(['id'=>$p_id],['imgroup_id'=>0]);

        $yim = new YunIM();

        $res = $yim->createGroupByProject($p_id);

        print_r($res);
    }

    public function dwz(){
        $url = 'http://pa5ijfg62.bkt.clouddn.com/reports/mcdocs-3b82b6e2e9f62bcaac7bb7dfe0be9c18.jpg?watermark/1/image/aHR0cDovL3BhNWlqZmc2Mi5ia3QuY2xvdWRkbi5jb20vc2lnbmltZy90ZXN0c2lnbi5qcGc=/dissolve/99/gravity/SouthEast/dx/15/dy/15/ws/0.8/wst/0';
        //$url = 'http://content.iytime.com/projectstatics/mcdocs-8e0f3bef97a266f168213223258d77f8.jpg';
        $res = \app\common\library\Shorturl::sina_create($url);

        print_r($res);
    }

    public function savecompimg(){
        //$q = new \app\common\library\Qiniu();

        //$url = 'http://content.iytime.com/projectstatics/mcdocs-c09e57fbf9d4569ba2448c04b9f2cbaa.png?watermark/1/image/aHR0cDovL2NvbnRlbnQuaXl0aW1lLmNvbS9wcm9qZWN0LzIwMTgwOTIzLzExLzgvbWNkb2NzLTE1Mzc2OTQ4MjcucG5n/dissolve/80/gravity/SouthEast/dx/15/dy/15/ws/0.12/wst/2';
        //$url = 'http://content.iytime.com/projectstatics/mcdocs-32477b9e4abfddc67181f46bb401285a.jpg?watermark/1/image/aHR0cDovL2NvbnRlbnQuaXl0aW1lLmNvbS9wcm9qZWN0LzIwMTgwOTI2LzExLzkvbWNkb2NzLTE1Mzc5NzQwNTEucG5n/dissolve/80/gravity/SouthEast/dx/15/dy/15/ws/0.24/wst/2';

        //$url = 'http://content.iytime.com/projectstatics/mcdocs-0b1ef120ebf1eacfd504407cf29bb573.png?watermark/1/image/aHR0cDovL2NvbnRlbnQuaXl0aW1lLmNvbS9wcm9qZWN0LzIwMTgwOTI2LzExLzkvbWNkb2NzLTE1Mzc5NzQwNTEucG5n/dissolve/80/gravity/SouthEast/dx/15/dy/15/ws/0.24/wst/2';
        $url = 'http://content.iytime.com/reports/mcdocs-0fef2f34920fa230ee55c88cb41408d5.png?watermark/1/image/aHR0cDovL2NvbnRlbnQuaXl0aW1lLmNvbS9wcm9qZWN0LzIwMTgxMDA2LzE0LzEyL21jZG9jcy0xNTM4ODAxOTEyLnBuZw==/dissolve/80/gravity/SouthEast/dx/15/dy/15/ws/0.17/wst/2';

        $res = \app\common\library\Qiniu::save_new_img($url);
        print_r($res);
    }

    public function imuserinfo(){
        $accid = input('get.accid','','trim');
        if(!$accid){
            exit('accid error');
        }
        $im = new \app\common\library\YunIM();
        $res = $im->imobj()->getUinfos([$accid]);
        dump($res);
    }

    public function imuser(){
        $accid = input('get.accid','','trim');
        if(!$accid){
            exit('accid error');
        }
        $res = [];
        $im = new \app\common\library\YunIM();
        list($type,$uid) = explode('_',$accid);
        if($type=='b'){
            $res = $im->updateBUserinfo($uid);
        }elseif($type=='c'){
            $res = $im->updateCUserinfo($uid);
        }
        print_r($res);

        $res = $im->imobj()->getUinfos([$accid]);
        print_r($res);
    }

    public function up_group_ower_icon(){
        $accid = input('get.accid','','trim');
        if(!$accid){
            exit('accid error');
        }
        $icon = "http://content.iytime.com/im/group_icon.png";
        //
        $data = [
            'name'=>'莫川设计',
            'icon'=>$icon,
            'sign'=>'',
            'email'=>'',
            'birth'=>'',
            'mobile'=>'',
            'gender'=>0,
            'ex'=>[
                'ename'=>'',
                'comp'=>'',
                'depart'=>'',
                'post'=>''
            ]
        ];
        $im = new \app\common\library\YunIM();

        $res = $im->updateUserInfo($accid,$data);
        dump($res);
    }

    public function createimgroup(){
        $p_id = input('get.p_id',0,'int');
        if(!$p_id){
            exit('tid error');
        }
        \think\Queue::later(2,'app\gerent\job\Projectimgroup',['p_id'=>$p_id,'act'=>'add']);
    }

    public function init_im_groups(){

        $tid = input('get.tid',0,'int');
        $p_id = input('get.p_id',0,'int');
        if(!$tid || !$p_id){
            exit('tid error');
        }

        $im = new \app\common\library\YunIM();
        $res = $im->queryGroup([$tid]);
        if($res['code']!='200'){
            print_r($res);
            exit();
        }
        $data = $res['tinfos'][0];
        $data['icon'] = $data['icon']?:'';
        $data['members'] = implode(',',$data['members']);
        $data['updatetime'] = date('Y-m-d H:i:s',ceil($data['updatetime']/1000));
        $data['createtime'] = date('Y-m-d H:i:s',ceil($data['createtime']/1000));
        $data['p_id'] = $p_id;



        //print_r($data);
        (new Imgroups())->save_groups($data);
        echo 'OK';
        /**
         * Array
        (
        [tinfos] => Array
        (
        [0] => Array
        (
        [icon] =>
        [announcement] => 暂无公告
        [updatetime] => 1537775080332
        [muteType] => 0
        [uptinfomode] => 0
        [maxusers] => 200
        [intro] => 深圳市福田区深南路阳光花园 F栋五房两厅[2018-07-29 16:48]
        [size] => 3
        [createtime] => 1532854117842
        [upcustommode] => 0
        [owner] => p_4
        [tname] => 阳光花园 F栋 五房两厅两卫
        [beinvitemode] => 0
        [joinmode] => 0
        [tid] => 624712007
        [members] => Array
        (
        [0] => c_2
        [1] => b_5
        )

        [invitemode] => 0
        [mute] =>
        )

        )

        [code] => 200
        )
         */

    }

}