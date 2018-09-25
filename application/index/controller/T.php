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