<?php
/**
 * Created by PhpStorm.
 * User: chenxh
 * Date: 2018/9/5
 * Time: 18:13
 */
namespace app\gerent\controller;

use app\gerent\model\Project,
    app\common\library\Jpush as Jpusher,
    app\gerent\model\Push as mPush,
    app\common\library\Notice,
    app\gerent\model\Pushruntime,
    app\gerent\library\Pusher as lpush,
    app\common\model\Buser;

class Pusher extends Common{

    /**
     * @var mPush
     */
    private $m;

    public function _initialize($check_login=true)
    {
        parent::_initialize($check_login);
        $this->m = new mPush();
    }

    public function index(){
        $where = ['isdel'=>0];
        $count = $this->m->get_count($where);

        $data = $page = [];
        if($count>0){
            $page = $this->_pagenav($count);
            //$field = '*';
            $data = $this->m->get_list($where,'*',$page['offset'].','.$page['limit']);

        }

        $js = $this->loadJsCss(array('p:common/common'), 'js', 'admin');
        $this->assign('footjs', $js);

        $this->assign('pagenav',$page);
        $this->assign('data',$data);
        $this->assign('run_types',$this->run_types());

        return $this->fetch('index');
    }

    public function info($id=0){
        if(!$id || $id<=0){
            $this->error('访问错误');
        }

        $info = $this->m->get_info(['id'=>$id,'isdel'=>0]);
        if(!$info){
            $this->error('该消息设置不存在');
        }

        if($info['type']==1){
            $this->error('立即发送的没有定时任务详情');
        }

        $get_users = explode(',',$info['geter_users']);
        $get_userids = explode(',',$info['geter_user_ids']);
        $get_user_map = [];
        foreach($get_userids as $k=>$v){
            $get_user_map[$v] = $get_users[$k];
        }

        $run_time = (new Pushruntime())->get_order_list(['not_id'=>$id,'isdel'=>0],'id,jpush_user_id,runtime,donetime,result',['id'=>'asc'],0);

        //print_r($runtime);
        $this->assign('info',$info);
        $this->assign('get_user_map',$get_user_map);
        $this->assign('run_time',$run_time);
        $this->assign('runtimes',$this->run_times());
        $this->assign('oncetimes',$this->once_times());
        $this->assign('types',$this->types());
        $this->assign('run_types',$this->run_types());
        return $this->fetch('info');
    }

    public function add($id=0){

        if($this->request->isPost()){
            return $this->save_push_data();
        }

        $p_id = input('get.p_id',0,'int');
        $type = input('get.type',0,'int');
        $type_id = input('get.type_id',0,'int');
        $ref = get_ref();
        $info = [];
        $all_projects = [];
        $project = [];
        $target_end_time = '';
        $run_rate_time = '09:00';
        $title = $message = '';

        if($id>0){
            $info = $this->m->get_info(['id'=>$id,'isdel'=>0]);
            if(!$info){
                $this->error('该消息设置不存在');
            }
            $title = $info['title'];
            $message = $info['message'];
            $p_id = $info['p_id'];
            $type = $info['type'];
            $type_id = $info['type_id'];
            $run_rate_time = $info['run_rate_time'];
            if($info['once_limit_time']>0){
                $target_end_time = $info['once_limit_time'];
            }
        }

        $lpush = new lpush($p_id,$type,$type_id);
        if(empty($info)) {
            $default = $lpush->get_title_message();
            if ($default['err'] == 1) {
                $this->error($default['msg']);
            }
            $title = $default['title'];
            $message = $default['message'];
            if ($default['limittime']) {
                $target_end_time = $default['limittime'];
            }
        }
        $can_send_one = !$lpush->has_send_one();
        //var_dump($can_send_one);
        if($p_id>0){
            $project =(new Project())->get_info(['id'=>$p_id],'id,name');
            if(empty($info)){
                $title = str_replace('PNAME',$project['name'],$title);
                $message = str_replace('PNAME',$project['name'],$message);
            }

        }else{
            $w = ['isdel' => 0];
            if(session('cp_power_tag')!=1){
                $w['cpid'] = session('cpid');
            }
            $all_projects = (new Project())->get_order_list($w,'id,name',['id'=>'desc'],0);
        }

        //$can_send_one = false;
        $js = $this->loadJsCss(array('p:common/common','pusher_add'), 'js', 'admin');

        $this->assign('footjs', $js);
        $this->assign('ref',$ref);
        $this->assign('p_id',$p_id);
        $this->assign('type',$type);
        $this->assign('type_id',$type_id);
        $this->assign('info',$info);
        $this->assign('can_send_one',$can_send_one);
        $this->assign('project',$project);
        $this->assign('title',$title);
        $this->assign('message',$message);
        $this->assign('all_projects',$all_projects);
        $this->assign('run_rate_time',$run_rate_time);
        $this->assign('target_end_time',$target_end_time);
        $this->assign('get_member_url',url('Pusher/get_project_members'));
        $this->assign('runtimes',$this->run_times());
        $this->assign('oncetimes',$this->once_times());
        $this->assign('types',$this->types());

        return $this->fetch('add');
    }

    public function edit($id=0){
        if(!$id || $id<=0){
            $this->error('访问错误');
        }


        return $this->add($id);
    }

    public function del($id=0){
        if(!$id || $id<=0){
            $this->error('访问错误');
        }
        $info = $this->m->get_info(['id'=>$id,'isdel'=>0],'id,p_id,type,type_id');
        if(!$info){
            $this->error('访问错误2');
        }

        if($info['p_id']>0 && $info['type']>0 && $info['type_id']>0){
            $lpush = new lpush($info['p_id'],$info['type'],$info['type_id']);
            $lpush->update_type_id(0);
        }
        $this->m->update_data(['id'=>$id],['isdel'=>1]);
        (new Pushruntime())->update_data(['not_id'=>$id],['isdel'=>1]);
        $this->success('删除成功');
    }
    // ajax 获取项目中的人员信息
    public function get_project_members(){
        if(!$this->request->isAjax()){
            return ['err'=>1,'msg'=>'参数有误'];
        }
        //return ['err'=>1,'msg'=>'参数有误'];
        $p_id = input("get.p_id",0,'int');
        $pushid = input("get.pushid",0,'int');
        if(!$p_id){
            return ['err'=>1,'msg'=>'参数有误'];
        }
        $p = new Project();
        $info = $p->get_info(['id'=>$p_id,'isdel'=>0],'customer_manager_user_id,desgin_user_id,desgin_assistant_user_id,manager_user_id,supervision_user_id,decorate_butler_user_id,owner_user_id,owner_name');

        if(!$info){
            return ['err'=>1,'msg'=>'参数有误'];
        }
        $userids = [];
        if($pushid>0){
            $userids = $this->m->get_info(['id'=>$pushid],'geter_user_ids');
            if($userids){
                $userids = explode(',',$userids['geter_user_ids']);
            }

        }
        //print_r($userids);
        $data = [
            ['id'=>$info['customer_manager_user_id'],'role'=>'客户经理','name'=>''],
            ['id'=>$info['desgin_user_id'],'role'=>'设计师','name'=>''],
            ['id'=>$info['desgin_assistant_user_id'],'role'=>'设计师助理','name'=>''],
            ['id'=>$info['manager_user_id'],'role'=>'项目经理','name'=>''],
            ['id'=>$info['supervision_user_id'],'role'=>'项目监理,质检','name'=>''],
            ['id'=>$info['decorate_butler_user_id'],'role'=>'装修管家','name'=>''],
        ];
        $buser_ids = array2to1($data,'id');
        $busers = (new Buser())->get_list(['id'=>['in',$buser_ids]],'id,name');
        foreach($busers as $bu){
            foreach ($data as $k=>$da){

                if($da['name']==''){

                    if($bu['id']==$da['id']){//$userids
                        $jpushid = Jpusher::create_alias($bu['id'],'b');
                        $data[$k]['id'] = $jpushid;
                        $data[$k]['name'] = $bu['name'];
                        if(in_array($jpushid,$userids)){
                            $data[$k]['checked'] = 1;
                        }else{
                            $data[$k]['checked'] = 0;
                        }
                    }
                }else{
                    continue;
                }

            }
        }
        $jpushid = Jpusher::create_alias($info['owner_user_id'],'c');
        $checked = in_array($jpushid,$userids) ? 1 : 0;

        $data[] = ['id'=>$jpushid,'role'=>'业主','name'=>$info['owner_name'],'checked'=>$checked];

        return ['err'=>0,'msg'=>'success','data'=>$data];
    }

    //=================================

    private function save_push_data(){
        $post = input('post.');

        $pinfo = (new Project())->get_info(['id'=>$post['p_id'],'isdel'=>0],'id,name');
        if(!$pinfo){
            $this->error('项目信息有误');
        }
        $geters = [
            'ids'=>[],
            'names'=>[]
        ];
        foreach($post['geterid'] as $geter){
            list($id,$name) = explode('|',$geter);
            $geters['ids'][] = $id;
            $geters['names'][] = $name;
        }
        $ref = $post['ref']?:url('Pusher/index');

        $save_data = [
            'p_id'=>$post['p_id'],
            'p_name'=>$pinfo['name'],
            'type'=>$post['type'],
            'type_id'=>$post['type_id'],
            'geter_users'=>implode(',',$geters['names']),
            'geter_user_ids'=>implode(',',$geters['ids']),
            'title'=>$post['title'],
            'message'=>$post['message'],
            'metas'=>'type=8&p_id='.$post['p_id'].'&id=',
            'run_type'=>$post['run_type'],
            'once_limit_time'=>$post['target_end_time'],
            'once_run_time_option'=>$post['once_run_time_option'],
            'begin_time'=>$post['begin_time'],
            'end_time'=>$post['end_time'],
            'run_rate_day'=>$post['run_rate_day'],
            'run_rate_time'=>$post['run_rate_time'],
            'uptime'=>$this->datetime
        ];

        //print_r($save_data);
        $n_id = 0;
        if(isset($post['id']) && $post['id']>0){
            $n_id = $post['id'];
            unset($post['id']);
            $this->m->update_data(['id'=>$n_id],$save_data);
        }else{
            $save_data['addtime'] = $this->datetime;
            $n_id = $this->m->add_data($save_data,true);
        }

        if(!$n_id){
            $this->error('保存数据失败');
        }
        
        if($save_data['p_id']>0 && $save_data['type']>0 && $save_data['type_id']>0){
            $lpush = new lpush($save_data['p_id'],$save_data['type'],$save_data['type_id']);
            $lpush->update_type_id($n_id);
            $lpush->tag_send_one();
        }
        
        
        
        // 立即发送
        if($save_data['run_type']==1){
            //...
            foreach($geters['ids'] as $ger){
                $send_data = Notice::addNoticeFromPush([
                    'not_id'=>$n_id,
                    'jpush_user_id'=>$ger,
                    'message'=>$save_data['title'],
                    'metas'=>$save_data['metas']
                ]);
                if($send_data['err']==1){
                    $this->error($send_data['msg']);
                }

                \think\Queue::later(1,'app\gerent\job\Pushqueue',$send_data['data']);
            }

            $this->success('消息已经发送发',$ref);
        }

        $pushruntime = new Pushruntime();

        $pushruntime->update_data(['not_id'=>$n_id],['isdel'=>1]);

        //单次任务
        if($save_data['run_type']==2 && $post['target_end_time']){
            $runtime = date("Y-m-d",(strtotime($post['target_end_time']) - $save_data['once_run_time_option']*3600*24));
            $runtime2 = [];
            foreach($geters['ids'] as $ger){
                $runtime2[] = [
                    'not_id'=>$n_id,
                    'jpush_user_id'=>$ger,
                    'message'=>$save_data['title'],
                    'metas'=>$save_data['metas'],
                    'runtime'=>$runtime.' 10:00:00',
                ];
            }
            $pushruntime->insert_all($runtime2);

            $this->success('消息设置成功',$ref);
        }

        //多次任务
        if($save_data['run_type']==3){

            $runtime3 = [];

            $for_begin = strtotime($save_data['begin_time']);
            $for_end = strtotime($save_data['end_time']);


            for($i=$for_begin;$i<=$for_end;){
                //echo date('Y-m-d',$i).PHP_EOL;
                foreach($geters['ids'] as $ger){
                    $runtime3[] = [
                        'not_id'=>$n_id,
                        'jpush_user_id'=>$ger,
                        'message'=>$save_data['title'],
                        'metas'=>$save_data['metas'],
                        'runtime'=>date('Y-m-d',$i).' '.$save_data['run_rate_time'].':00',
                    ];
                }

                $i += $save_data['run_rate_day']*24*3600;
            }

            //print_r($runtime3);
            $pushruntime->insert_all($runtime3);
            $this->success('消息设置成功',$ref);
        }

    }

    private function types(){
        return [
            0=>'自定义',
            1=>'项目阶段(设计)',
            2=>'项目付款',
            3=>'预约',
            4=>'施工预算',
            5=>'验收方案',
            6=>'项目阶段(施工)',
            7=>'效果图\CAD图\主材'
        ];
    }

    private function run_types(){
        return [
            1=>'立即提醒',
            '单次定时提醒',
            '多次定时提醒'
        ];
    }

    private function run_times(){
        $times = [];

        for($i=0;$i<=23;$i++){
            if($i<10){
                $times[] = '0'.$i.':00';
            }else{
                $times[] = $i.':00';
            }
        }

        return $times;
    }

    private function once_times(){
        return [
            1=>'提前1天提醒（上午10点）',
            '提前2天提醒（上午10点）',
            '提前3天提醒（上午10点）',
            '提前4天提醒（上午10点）',
            '提前5天提醒（上午10点）',
            '提前6天提醒（上午10点）',
            '提前7天提醒（上午10点）',
        ];
    }

}