<?php
/**
 * Created by PhpStorm.
 * User: chenxh
 * Date: 2018/9/25
 * Time: 10:17
 */
namespace app\index\controller;
use app\common\model\Imgroups;

class T extends \app\common\controller\Base{

    public function dwz(){
        $url = 'http://pa5ijfg62.bkt.clouddn.com/reports/mcdocs-3b82b6e2e9f62bcaac7bb7dfe0be9c18.jpg?watermark/1/image/aHR0cDovL3BhNWlqZmc2Mi5ia3QuY2xvdWRkbi5jb20vc2lnbmltZy90ZXN0c2lnbi5qcGc=/dissolve/99/gravity/SouthEast/dx/15/dy/15/ws/0.8/wst/0';
        //$url = 'http://content.iytime.com/projectstatics/mcdocs-8e0f3bef97a266f168213223258d77f8.jpg';
        $res = \app\common\library\Shorturl::sina_create($url);

        print_r($res);
    }

    public function imuserinfo(){
        $accid = input('get.accid','','trim');
        if(!$accid){
            exit('accid error');
        }
        $im = new \app\common\library\YunIM();
        $res = $im->imobj()->getUinfos([$accid]);
        print_r($res);
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