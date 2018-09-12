<?php
/**
 * Created by PhpStorm.
 * User: chenxh
 * Date: 2018/6/12
 * Time: 22:53
 */
namespace app\common\library;

use \extend\netease\ServerAPI,
    app\gerent\model\User,
    app\gerent\model\Systable,
    app\common\model\Project,
    app\common\model\Projectim,
    app\common\library\Imgjoin,
    app\common\model\IM as mIM;

class YunIM
{
    /**
     * @var ServerAPI
     */
    protected $im_obj = null;

    //按项目来创建群
    /*
     * {
  "code":200,
  "tid":"11001",
  "faccid":{
         "accid":["a","b","c"],
         "msg":"team count exceed"
     }
}*/

    public function updateGroupByProject($p_id){
        $fields = 'id,imgroup_id,name,address,customer_manager_user_id,desgin_user_id,desgin_assistant_user_id,manager_user_id,supervision_user_id,decorate_butler_user_id,owner_user_id';
        $pject = new Project();
        $p_info = $pject->get_info(['id'=>$p_id,'isdel'=>0],$fields);
        if(!$p_info){
            return ['err' => 1, 'msg' => '项目不存在'];
        }
        if(!$p_info['imgroup_id']){
            return ['err' => 1, 'msg' => '该项目暂无建群'];
        }

        /**
         * Array
        (
            [tinfos] => Array
            (
                [0] => Array
                (
                    [icon] =>
                    [announcement] => 暂无公告
                    [updatetime] => 1530514330838
                    [muteType] => 0
                    [uptinfomode] => 0
                    [maxusers] => 200
                    [intro] => 深圳宝安西乡[2018-06-26 19:01]
                    [size] => 5
                    [createtime] => 1530010906886
                    [upcustommode] => 0
                    [owner] => p_1
                    [tname] => 中粮三期装修
                    [beinvitemode] => 0
                    [joinmode] => 0
                    [tid] => 560928224
                    [members] => Array
                    (
                        [0] => c_2
                        [1] => b_3
                        [2] => c_3
                        [3] => c_1
                    )

                    [invitemode] => 0
                    [mute] =>
                )

            )

            [code] => 200
        )
         */
        $res = $this->imobj()->queryGroup([$p_info['imgroup_id']]);
        if($res['code']!= 200){
            return ['err' => 1, 'msg' => '获取群信息失败'];
        }
        $old_members = $res['tinfos'][0]['members'];

        $members_B = [
            $p_info['manager_user_id'],//项目经理
            $p_info['customer_manager_user_id'],//客户经理
            $p_info['desgin_user_id'],//设计师
            $p_info['desgin_assistant_user_id'],//设计师助理
            $p_info['supervision_user_id'],//监理,项目质检
            $p_info['decorate_butler_user_id']//装修管家
        ];
        $members_C = [$p_info['owner_user_id']];//业主id

        $new_members = $this->create_members($members_B,$members_C);

        $in = array_intersect($old_members,$new_members);
        $out = array_diff($old_members,$in);
        $new_join = array_diff($new_members,$in);
        //踢人
        if(!empty($out)){
            foreach($out as $ot){
                $this->imobj()->kickFromGroup($p_info['imgroup_id'],$res['tinfos'][0]['owner'],$ot);
            }
        }
        //加人
        if(!empty($new_join)){
            $this->imobj()->addIntoGroup($p_info['imgroup_id'],$res['tinfos'][0]['owner'],$new_join);
            //发送入群消息
            $this->imobj()->sendMsg($res['tinfos'][0]['owner'],1,$p_info['imgroup_id'],0,['msg'=>'有'.count($new_join).'人加入群聊']);
        }
        if(!empty($out)){
            //Imgjoin::create_group_icon($members_B,$members_C,$p_info['imgroup_id']);
        }
        $this->save_im_list($p_id,$p_info['imgroup_id'],$res['tinfos'][0]['tname'],$members_B,$members_C);
    }

    public function createGroupByProject($p_id)
    {
        $fields = 'id,imgroup_id,name,address,customer_manager_user_id,desgin_user_id,desgin_assistant_user_id,manager_user_id,supervision_user_id,decorate_butler_user_id,owner_user_id';
        $pject = new Project();
        $p_info = $pject->get_info(['id'=>$p_id,'isdel'=>0],$fields);
        if(!$p_info){
            return ['err' => 1, 'msg' => '项目不存在'];
        }

        //群名--项目名称
        //群主--虚拟一个用户
        //成员--业主，B端的人员

        $gname = $p_info['name'];
        $ower_user_id = $p_info['id'];
        $members_B = [
            $p_info['manager_user_id'],//项目经理
            $p_info['customer_manager_user_id'],//客户经理
            $p_info['desgin_user_id'],//设计师
            $p_info['desgin_assistant_user_id'],//设计师助理
            $p_info['supervision_user_id'],//监理,项目质检
            $p_info['decorate_butler_user_id']//装修管家
        ];
        $members_C = [$p_info['owner_user_id']];//业主id

        //创建之前，先创建 云信ID
        $rs = $this->createPUserid($ower_user_id);
        if ($rs['err'] == 1) {
            return $rs;
        }

        $ower_user = $this->build_im_userid($ower_user_id, 'P');
        $members = $this->create_members($members_B,$members_C);
        //创建成功后，获取tid,并将每个人与tid保存到IM列表信息中


        //群公告，最大长度1024字节
        $announcement = '暂无公告';
        //群描述，最大长度512字节
        $intro = $p_info['address'].'['.date('Y-m-d H:i').']';
        //邀请发送的文字，最大长度150字节
        $msg = '邀请你加入群聊['.$gname.']';
        $rs = $this->imobj()->createGroup($gname, $ower_user, $members,$announcement,$intro,$msg,'0','0','');

        if ($rs['code'] != 200) {
            return ['err' => 1, 'msg' => '创建失败', 'code' => $rs['code']];
        }
        $tid = $rs['tid'];
        $pject->update_data(['id'=>$p_id],['imgroup_id'=>$tid]);


        $this->save_im_list($p_id,$tid,$gname,$members_B,$members_C);
        (new Projectim())->save_data([
            'p_id'=>$p_info['id'],
            'tid'=>$tid
        ]);
        //Imgjoin::create_group_icon($members_B,$members_C,$tid);
        //发个群信息
        $this->imobj()->sendMsg($ower_user,1,$tid,0,['msg'=>'群:['.$gname.']创建成功']);
        return ['err' => 0, 'msg' => 'success'];
    }

    public function save_im_list($p_id,$tid,$gname,$members_B,$members_C){
        $im = new mIM();
        $members_B = array_unique($members_B);
        foreach ($members_B as $bmer) {
            $data = [
                'p_id'=>$p_id,
                'user_type' => 1,
                'user_id' => $bmer,
                'im_type' => 2,
                'target_type'=>0,
                'target_tag' => $tid,
                'name' => $gname
            ];
            $im->save_data($data);
        }
        foreach ($members_C as $cmer) {
            $data = [
                'p_id'=>$p_id,
                'user_type' => 2,
                'user_id' => $cmer,
                'im_type' => 2,
                'target_type'=>0,
                'target_tag' => $tid,
                'name' => $gname
            ];

            $im->save_data($data);
        }
    }

    public function create_members($members_B,$members_C){
        $members = [];
        $members_B = array_unique($members_B);
        foreach ($members_B as $bmer) {
            $rs = $this->createBUserID($bmer);
            if ($rs['err'] == 1) {
                return $rs;
            }
            $members[] = $this->build_im_userid($bmer, 'B');
        }

        foreach ($members_C as $cmer) {
            $rs = $this->createCUserID($cmer);
            if ($rs['err'] == 1) {
                return $rs;
            }
            $members[] = $this->build_im_userid($cmer, 'C');
        }

        return array_unique($members);
    }

    public function sendTestMsg($from,$to,$type,$message){
        return $this->imobj()->sendMsg($from,$type,$to,0,['msg'=>$message]);
    }

    public function queryGroup($tids){
      $ginfo =    $this->imobj()->queryGroup($tids,1);
      print_r($ginfo);
    }

    public function removeGroup($p_id,$tid=0){
        $ower = $this->build_im_userid($p_id,'P');
        $rs = $this->imobj()->removeGroup($tid,$ower);
        if($rs['code']==200){
            return ['err' => 0, 'msg' => 'success'];
        }
        return ['err' => 1, 'msg' => '删除失败','code'=>$rs['code']];
    }

    public function queryGroupMsg($tid,$accid){
        $endtime = time();
        $begintime = $endtime - 10*24*3600;
        return $this->imobj()->queryGroupMsg($tid,$accid,$begintime,$endtime,20,2);
    }

    public function addIntoGroup($tid,$owner,$accid){
        return $this->imobj()->addIntoGroup($tid,$owner,$accid);
    }

    public function createCUserID($user_id)
    {
        $muser = new User();
        $uinfo = $muser->get_info(['id' => $user_id, 'isdel' => 0], 'id,uname,head_pic,im_token');
        if (!$uinfo) {
            return ['err' => 1, 'msg' => '用户不存在'];
        }
        if ($uinfo['im_token']) {

            return ['err' => 0, 'msg' => 'ok', 'name'=>$uinfo['uname'],'token' => $uinfo['im_token']];
        }
        $head_pic = c_img($uinfo['head_pic'], 2, 120);

        $res = $this->imobj()->createUserId($this->build_im_userid($uinfo['id'], 'C'), $uinfo['uname'], '{}', $head_pic);

        if ($res['code'] == 200) {

            $muser->update_data(['id' => $user_id], ['im_token' => $res['info']['token']]);
            return ['err' => 0, 'msg' => 'ok', 'name'=>$uinfo['uname'],'token' => $res['info']['token']];
        }

        return ['err' => 1, 'msg' => '创建失败c', 'code' => $res['code']];
    }

    public function createBUserID($user_id)
    {
        $m = new Systable();
        $uinfo = $m->get_admin_info(['id' => $user_id, 'isdel' => 0], 'id,name,head_pic,im_token');
        if (!$uinfo) {
            return ['err' => 1, 'msg' => '用户不存在'];
        }
        if ($uinfo['im_token']) {

            return ['err' => 0, 'msg' => 'ok', 'name'=>$uinfo['name'],'token' => $uinfo['im_token']];
        }
        $head_pic = c_img($uinfo['head_pic'], 3, 120);

        $res = $this->imobj()->createUserId($this->build_im_userid($uinfo['id'], 'B'), $uinfo['name'], '{}', $head_pic);

        if ($res['code'] == 200) {

            $m->update_admin(['id' => $user_id], ['im_token' => $res['info']['token']]);
            return ['err' => 0, 'msg' => 'ok', 'name'=>$uinfo['name'],'token' => $res['info']['token']];
        }

        return ['err' => 1, 'msg' => '创建失败b', 'code' => $res['code']];
    }

    public function createPUserid($p_id){
        $pm = new Projectim();
        $pim_info = $pm->get_info(['p_id'=>$p_id],'id,name,token');
        if($pim_info && $pim_info['token']){
            return ['err' => 0, 'msg' => 'ok', 'name'=>$pim_info['name'],'token' => $pim_info['token']];
        }
        $name = config('company.short_name');
        $res = $this->imobj()->createUserId($this->build_im_userid($p_id, 'P'), $name);
        if ($res['code'] == 200) {
            $pm->save_data([
                'p_id'=>$p_id,
                'im_userid'=>$this->build_im_userid($p_id, 'P'),
                'name'=>$name,
                'token'=>$res['info']['token']
            ]);
            //$m->update_admin(['id' => $user_id], ['im_token' => $res['info']['token']]);
            return ['err' => 0, 'msg' => 'ok', 'name'=>$name,'token' => $res['info']['token']];
        }

        return ['err' => 1, 'msg' => '创建失败p', 'code' => $res['code']];
    }

    public function build_im_userid($user_id, $type)
    {
        return strtolower($type) . '_' . $user_id;
    }
    protected function imobj()
    {
        if (is_null($this->im_obj)) {
            $config = config('yunxin');
            $this->im_obj = new ServerAPI($config['im_app_key'], $config['im_app_ecret'], 'curl');
        }
        return $this->im_obj;
    }

}