<?php
namespace app\index\controller;

use app\common\library\Notice;
use think\image\Exception,
    think\Db,
    think\queue\Job,
    app\common\model\Projectoffer,
    app\common\model\Projectofferdoc,
    app\common\model\Projectreport,
    app\common\model\Projectreportdoc,
    app\common\library\Qiniu,
    app\gerent\model\Pushruntime;

class Index extends \app\common\controller\Base
{
    public function index()
    {
        return '^_^';
    }

    public function test_im(){
        $yim = new \app\common\library\YunIM();
        $res = $yim->imobj()->updateUserToken('b_5');
        var_dump($res);
    }

    public function test_qu(){
        $bucket1 = config('qiniu.bucket1');
        $token = \app\common\library\Qiniu::get_uptoken($bucket1);
        echo $token;
    }

    public function test_pushq(){
        $info = (new Pushruntime())->get_info(['id'=>3]);
        $data = \app\common\library\Notice::addNoticeFromPush($info);
        if($data['err']==1){
            print_r($data);
            return false;
        }
        \think\Queue::later(2,'app\gerent\job\Pushqueue',$data['data']);
    }

    public function push_test(){
        $uid = input('get.uid',0,'int');
        $key = input('get.key',0,'int');
        if(!$uid){
            return 'empty uid';
        }
        /**
         *    type   说明            跳转
        1    首页             0
        2    项目设计阶段详情   阶段id
        3    项目施工阶段详情   阶段id
        4    项目付款信息      项目id
        5    预约             预约消息详情id
        6    施工预算          某个方案id
        7    验收方案          某个方案id
        8    事务提醒详情       提醒id
         */
        $test_data = [
            'https://mokchuen.iytime.com/mch5/mochuan/DetailsPage.html?id=16',
            'mochuan://com.aysd.mochuan?type=1',
            'mochuan://com.aysd.mochuan?type=2&p_id=1&id=12',
            'mochuan://com.aysd.mochuan?type=3&p_id=1&id=17',
            'mochuan://com.aysd.mochuan?type=4&p_id=1',
            'https://mokchuen.iytime.com/mch5/mochuan/PaymentSchedule.html?p_id=1',
            'mochuan://com.aysd.mochuan?type=5&id=3',
            'mochuan://com.aysd.mochuan?type=6&p_id=1&id=1',
            'mochuan://com.aysd.mochuan?type=7&p_id=1&id=1',
            'mochuan://com.aysd.mochuan?type=8&p_id=1&id=1',
        ];
        $data = [
            'to_user_type'=>'c',
            'to_user_id'=>$uid,
            'message'=>'莫川测试推送',
            'extras'=>[
                'url'=>$test_data[$key]
            ]
        ];
        /* try{
             \app\common\library\Jpush::send('c',$uid,'莫川测试推送',['url'=>$test_data[$key]]);
         }catch (Exception $e){
             print_r($e);
         }*/

        \think\Queue::later(2,'app\gerent\job\Pushqueue',$data);
        echo $test_data[$key];
    }
    public function timg(){
        echo '<img src="http://t.cn/EPS8YuD"/>';
    }
    public function test_imgmegre(){
        $type = input('get.type','');
        $id = input('get.id',0);
        $p_id = input('get.p_id',0);
        $ss = input('get.ss',0);
        //$data = ['type'=>'report','id'=>4];
        $data = ['type'=>$type,'id'=>$id,'p_id'=>$p_id];
        //$Compleximg = new \app\gerent\job\Compleximg();
        //$Compleximg->do_job($data);
        //$Compleximg->fire(,$data);

        //\think\Queue::later(2,'app\gerent\job\Compleximg',$data);
        if($ss){
            $Compleximg = new \app\gerent\job\Compleximg();
            $Compleximg->do_job($data);
        }else{
            $res = \think\Queue::later(1,'app\gerent\job\Compleximg',$data);
            var_dump($res);
        }

    }

    public function test_qn_donw(){
        $w_url='http://pa5ijfg62.bkt.clouddn.com/reports/mcdocs-3b82b6e2e9f62bcaac7bb7dfe0be9c18.jpg?watermark/1/image/aHR0cDovL3BhNWlqZmc2Mi5ia3QuY2xvdWRkbi5jb20vL3NpZ25pbWcvc2lnbl9pbWcuZ2lm/dissolve/99/gravity/SouthEast/dx/15/dy/15/ws/0.8/wst/0';
        try{
            \app\common\library\Qiniu::download_upload_watermark($w_url);
        }catch (\Exception $e){
            print_r([
                $e->getMessage()
            ]);
        }
    }

    public function test_watermark(){
        //$pic = 'http://content.iytime.com/app/20180720/mcdocs-15320765630.jpg';
        $pic = 'http://content.iytime.com/reports/mcdocs-9c21713ff766e5b994692d19ad63a489.jpg';
        //$logo = 'http://content.iytime.com/project/1/mcdocs-20180923155318.png';
        $logo = 'http://content.iytime.com/project/20180923/11/8/mcdocs-1537689265.png';


        $pic_info = \extend\Http::curl_get($pic.'?imageInfo');
        if(!$pic_info){
            echo 'false';
        }
        $pic_info = json_decode($pic_info,1);

        //print_r($pic_info);exit;
        $ws = 0.11;
        if($pic_info['width']>200){
            $ws = sprintf("%.2f",180/$pic_info['width']);
        }
        //echo $ws;

        $w_url = \app\common\library\Qiniu::watermark_url($pic,$logo,['ws'=>$ws,'dissolve'=>80,'wst'=>2]);
        echo $w_url;
        exit;
        try{
            $q_key = \app\common\library\Qiniu::download_upload_watermark($w_url);
            echo 'http://content.iytime.com/'.$q_key;
        }catch (\Exception $e){
            print_r([
                $e->getMessage()
            ]);
        }
    }

    public function test_comp(){
        $data = ['type'=>'report','id'=>4];

        $types = ['offer','report'];
        if(!in_array($data['type'],$types) && !$data['id']){
            return false;
        }
        $m=null;
        $mdoc = null;
        $w = [];
        switch($data['type']){
            case 'offer':
                $m = new Projectoffer();
                $mdoc = new Projectofferdoc();
                $w = ['p_offer_id'=>$data['id']];
                break;
            case 'report':
                $m = new Projectreport();
                $mdoc = new Projectreportdoc();
                $w = ['p_rep_id'=>$data['id']];
                break;
        }

        $info = $m->get_info(['id'=>$data['id']],'id,sign_img');
        if(!$info || !$info['sign_img']){
            print_r('info not found');
            return false;
        }
        //mlog::write($info,$this->log_file);
        $w['isdel']  = 0;
        $docs = $mdoc->get_list($w,'id,file_type,file_path',0);

        if(empty($docs)){
            print_r('empty doc');
            return false;
        }
        try{
            $img_exts = config('img_ext');
            $img_exts[] = 'pdf';
            foreach($docs as $doc){
                if(!in_array($doc['file_type'],$img_exts)){
                    print_r('not image or pdf;file_type='.$doc['file_type'].';'.json_encode($data).';doc id='.$doc['id']);
                    continue;
                }
                print_r([$doc,$info]);
                $w_url = Qiniu::watermark_url($doc['file_path'],$info['sign_img']);
                if(!$w_url){
                    print_r('watermark_url=>false;data:'.json_encode($data).';doc id='.$doc['id']);
                    continue;
                }
                print_r('$w_url='.$w_url);
                exit;
                $q_key = Qiniu::download_upload_watermark($w_url);
                if(!$q_key){
                    print_r('download_upload_watermark=>false;data:'.json_encode($data).';doc id='.$doc['id']);
                    continue;
                }
                print_r('$q_key='.$q_key);
                echo 'SUCCESS';
                //$mdoc->update_data(['id'=>$doc['id']],['sign_complex_path'=>$q_key]);
            }
        }catch(\Exception $e){
            throw new \Exception($e);

        }
    }

    public function test_not(){
        //通知业主
        $ndata = [
            'p_id'=>11,
            'type'=>1,
            'target_id'=>86,
            'user_type'=>2,
            'user_id'=>5,//业主
            'title'=>'再测试',
            'content'=>'再测试下提醒功能'
        ];
        Notice::add($ndata);
    }

    public function t220(){
        (new Pushruntime())->update_data(
            ['id'=>2],
            [
                //'metas'=>['exp','CONCAT(metas,9)']
                'metas'=>Db::raw('CONCAT(metas,9)')
            ]
        );
        exit;
        //strtotime('')
        echo '2018-09-20前3天：',date("Y-m-d",(strtotime("2018-09-20") - 3*3600*24));
        $save_data = [
            'begin_time'=>'2018-09-11',
            'end_time'=>'2018-09-20',
            'run_rate_day'=>3
        ];
        $for_begin = strtotime($save_data['begin_time']);
        $for_end = strtotime($save_data['end_time']);
        for($i=$for_begin;$i<=$for_end;){
            echo date('Y-m-d',$i).PHP_EOL;

            $i += $save_data['run_rate_day']*24*3600;
        }
    }

    public function t1(){
        $rs = \app\common\library\Sms::verify_send('15811835212');
        //$rs = \app\common\library\Sms::send('15811835212','你好，可以认识一下吗？');
        //$sms = new \extend\Sms_mc();
        //$rs = $sms->send('15811835212','你好，可以认识一下吗？');
        var_dump($rs);
    }

    public function t2(){
        $file = './data/image/cusers/20180612/06dd1cd0061ed27616e0f316eba63a00.jpg';

        $q_key = 'mcdocs-'.md5($file);
        \app\common\library\Qiniu::upload_file(config('qiniu.bucket1'),$file,$q_key);

    }

    public function t3(){
        $file = '20180612/06dd1cd0061ed27616e0f316eba63a00.jpg';
        $dir_key = 2;
        \app\common\library\Qiniu::upload_mc_file($file,$dir_key);
    }

    public function t4(){
        $key = 'mcdocs-cusers/9b2ea27e03d96cfe8412e168dbda2a17.jpg';
        //\app\common\library\Qiniu::download_file($key);
        $url = 'http://pa5ijfg62.bkt.clouddn.com/mcdocs-cusers/9b2ea27e03d96cfe8412e168dbda2a17.jpg';
        $save_name = './data/'.$key;

        $path = pathinfo($save_name);
        //print_r($path);
        if(!is_dir($path['dirname'])){
            mkdir($path['dirname']);
        }
        //file_put_contents($save_name,$url);
        $res = \extend\Http::curl_get($url);
        //echo $res;
        file_put_contents($save_name,$res);
        //echo '<img src="'.$res.'"/>';
    }

    public function t6(){
        $key = 'mcdocs-cusers/9b2ea27e03d96cfe8412e168dbda2a17.jpg';
        var_dump(pathinfo($key)['extension']);
    }

    public function qiniu_js(){

        $uptoken = \app\common\library\Qiniu::get_uptoken(config('qiniu.bucket1'));
        $this->assign('uptoken', $uptoken);
        return $this->fetch('qiniu_js');
    }

    public function t5(){
            echo request()->host(true);
        }

    public function t7(){
        $id = input('get.id',0);
            $im = new \app\common\library\YunIM();
            //$res = $im->createGroupByProject(1);
            //$im->removeGroup(1,560880533);
            $res = $im->queryGroup([$id]);
            print_r($res);
        }

    public function t8(){
            $pic_list = [
                'http://img104.job1001.com/upload/faceimg/20140305/5176438df39012880af6da07c725d91f_1394001874.jpeg',
                'http://img104.job1001.com/upload/faceimg/20131121/90d8df2365743b0830f57ed3090c3311_1385026102.gif',
                //'http://pa5ijfg62.bkt.clouddn.com/mcdocs-8698b32fc2aff0affadb8533a2936bc5.jpeg',
                'http://img104.job1001.com/upload/faceimg/20130820/ec2135080510a11fd163d1ebc487ea84_1376968031.png',
                'http://img104.job1001.com/upload/faceimg/20130322/427f52f63193a2ffe2ef8f4e9130c74a_1363919801.jpeg',
                /*'http://img104.job1001.com/upload/faceimg/20130916/65ae25bf4cf82eae8ba26d1f9e67b3ae_1379298441.jpeg',
                'http://img104.job1001.com/upload/faceimg/20131126/71c2cff7d0105602513f74568c1967ab_1385448526.gif',
                'http://img104.job1001.com/upload/faceimg/20131121/375d6cf0ce7bd3b21a48eb8e6bafa2c8_1385026044.gif',
                'http://img104.job1001.com/upload/faceimg/20131121/d5f4380f337f0b0a96592f80f83d20e5_1385026012.gif'*/
            ];
            $img = new \extend\Imgjoin($pic_list,'./data/imgjoin115.jpg');
            $img->do_work();
        }

    public function t9(){
        $buserids= [2,3,4];
        $cuserids=[1];
        $tid=434954385;
        \app\common\library\Imgjoin::create_group_icon($buserids,$cuserids,$tid);
    }
    // IM

    public function tt8(){
        $type = input('get.type','c');
        $id = input('get.id',0);
        $yim = new \app\common\library\YunIM();
        $accid = $yim->build_im_userid($id,$type);
        $info = $yim->imobj()->getUinfos([$accid]);
        print_r($info);
    }

    public function tt9(){
        $type = input('get.type','c');
        $id = input('get.id',0);
        $yim = new \app\common\library\YunIM();
        $accid = $yim->build_im_userid($id,$type);
        $info = [];
        if($type=='c'){
            $info = (new \app\common\model\Cuser())->get_info(['id'=>$id],'id,uname,gender,head_pic,mobile,im_token');
        }else if($type=='b'){
            $info = (new \app\common\model\Buser())->get_info(['id'=>$id],'id,name as uname,sex as gender,mobile,head_pic,im_token');
        }
        if(!$info){
            exit('no user');
        }
        $icon = 'http://content.iytime.com/app/1/20180718122251';
        $addon = [
            //'icon'=>$info['head_pic']?:$icon,
            'icon'=>$icon,
            'gender'=>$info['gender'],
            'mobile'=>$info['mobile']
        ];
        //$res = $yim->imobj()->createUserId($accid,$info['uname'],'{}',$icon,$info['im_token']);
        $res = $yim->imobj()->updateUserId($accid,$info['uname'],'{}',$addon,$info['im_token']);
        var_dump($res);
    }

    public function t10(){
        $yim = new \app\common\library\YunIM();
        $res = $yim->createCUserID(2);
        print_r($res);
    }

    public function t11(){
        $yim = new \app\common\library\YunIM();

        $res = $yim->addIntoGroup('624712007','p_4',['b_2','b_3','b_4','b_5','b_6']);
        print_r($res);
    }


    function tqq(){
        $type = input("get.type",'','trim');
        $id = input("get.id",0,'int');
        \think\Queue::push('app\gerent\job\Imageslim',['type'=>$type,'id'=>$id]);
        //$job = new \think\queue\Job();
        //$islim = new \app\gerent\job\Imageslim();
        //$islim->fire(null,['type'=>'static_doc','id'=>134]);
        /*$file_path = 'projectstatics/mcdocs-63b89c4e921de4184acba69cc8a467c2.jpg';
        $new_path = get_qn_img_slm($file_path);

        //$res = \app\common\library\Http::curl_get('http://content.iytime.com/projectstatics/mcdocs-63b8e4184acba69cc8a467c2.jpg?imageInfo');
        dump([
            config('qiniu.host').$file_path.'?imageView2/2/w/2048/',
            $new_path
        ]);*/
    }

    function t12(){
        $yim = new \app\common\library\YunIM();
        $res = $yim->queryGroupMsg('560928224','p_1');
        print_r($res);
    }

    function t13(){
        $yim = new \app\common\library\YunIM();
        $res = $yim->sendTestMsg('p_23','1459721559',1,'今天是'.date('Y-m-d H:i'));
        //http://pa5ijfg62.bkt.clouddn.com/doc/testestesteststtte.docx
        //$res = $yim->sendTestMsg('p_1','560928224',1,'今天是'.date('Y-m-d H:i'));
        print_r($res);
    }

    public function t14(){
        $tes = [
            'aa'=>12,
            "bb"=>'sdfsdfsd',
            'cc'=>'34',
            'name'=>"陈小ipql",
            'sub'=>[
                'aa'=>90,
                'sdf'=>'34',
                'sub'=>[
                    'asfsd'=>54,
                    'fjsd'=>'sfjsdofjosd'
                ]
            ]
        ];
        $tes = tostring($tes);
        echo json_encode($tes);
    }

    public function t15(){

        //$pinfo = (new \app\common\model\Projectadmin())->get_list(['p_id'=>1,'type'=>['in',[3,1]]],'id,b_user_id');
        //print_r($pinfo);
        try{
            $yim = new \app\common\library\YunIM();
            $res = $yim->createGroupByProject(4);
            print_r($res);
        }catch (\Exception $e){
            echo $e->getMessage();
        }

    }

    public function t16(){
        //\app\common\library\Mylog::write(date('Y-m-d H:i:s'),'mytest');
        //$data = ['id'=>124,'name'=>date('Y-m-d H:i:s').'这是什么东西'];
        //$data = ['bid'=>22,'user_id'=>5,'transaction_id'=>'4005752001201706308157752886'];
        try{
            //\app\common\library\Mylog::write(date('Y-m-d H:i:s'),'mytest');
            \think\Queue::push('app\gerent\job\Mytest',[]);
            //\think\Queue::later(2,'app\gerent\job\Projectimgroup',['p_id'=>4,'act'=>'add']);
            //\think\Queue::push('app\gerent\job\Stepstatus',[]);
            //\think\Queue::later(5,'app\gerent\job\Projectpaycheck',[]);
            //\think\Queue::later(8,'app\gerent\job\Booking',[]);
            //\think\Queue::deleteReserved(null,'app\cool_admin2\job\Livestatus');
            //\think\Queue::push('app\cool_admin\job\Queryrefund',$data);
            //\think\Queue::later(60,'app\cool_admin\job\Queryrefund',$data);
            //\think\Queue::later(6,'app\cool_admin\job\Borderstatus',[]);
        }catch (\Exception $e){
            echo $e->getMessage();
        }
    }

    public function t17(){
        $str = 'aHR0cDovL2hrLmxvdmVzdHJ1Y2suY29tLy9kaXNjb3Zlci9hbGwv';
        echo base64_decode($str);
        echo date('Y-m-d H:i:s',strtotime("+7 days"));
    }

    public function t18(){
        //echo date('Y-m-d 20:00:00');
        echo time()-strtotime(date('Y-m-d 20:00:00'));
        echo '|',strtotime(date('H:i')),'===',strtotime(date('20:00'));

    }

    public function t19(){

        $times = [
            [
                "id"=>"1",
                "type"=>"1",
                "name"=>"平面设计1",
                "plan_begin_date"=>"2018-06-16",
                "plan_end_date"=>"2018-06-22",
            ],
            [
                "id"=>"2",
                "type"=>"1",
                "name"=>"平面设计2",
                "plan_begin_date"=>"2018-06-20",
                "plan_end_date"=>"2018-06-29",
            ],
            [
                "id"=>"3",
                "type"=>"2",
                "name"=>"平面设计3",
                "plan_begin_date"=>"2018-07-01",
                "plan_end_date"=>"2018-07-09",
            ],
            [
                "id"=>"4",
                "type"=>"2",
                "name"=>"平面设计4",
                "plan_begin_date"=>"2018-07-06",
                "plan_end_date"=>"2018-07-26",
            ],
            [
                "id"=>"5",
                "type"=>"2",
                "name"=>"平面设计5",
                "plan_begin_date"=>"2018-07-25",
                "plan_end_date"=>"2018-07-29",
            ]
        ];
        $time_range_begin = strtotime($times[0]['plan_begin_date']);
        $time_range_end = strtotime($times[count($times)-1]['plan_end_date']);
        $one_day_time = 24*3600;
        $today = strtotime(date('Y-m-d'));

        $days = [];
        $colors = [
            'before'=>'#eeeeee',
            'now'=>'#a9e9fe',
            'after'=>'#b8f7d9',
            't_po'=>'#ff9292',
            'd_po'=>'#a4cffe'
        ];
        $j=0;
        $stepids = [];
        for($i=$time_range_begin;$i<=$time_range_end;){
            //echo date('Y-m-d',$i),PHP_EOL;

            foreach($times as $tm){
                if($i>=strtotime($tm['plan_begin_date']) && $i<= strtotime($tm['plan_end_date'])){
                    $days[$j]['date'] = date('Y-m-d',$i);

                    if($i==$today){
                        $days[$j]['color'] = '#a9e9fe';
                    }else if($i>$today){
                        $days[$j]['color'] = '#b8f7d9';
                    }
                    /*if(!isset($days[$j]['steps'])){
                        $days[$j]['steps'] = [];
                    }*/
                    $days[$j]['steps'][] = ['id'=>$tm['id'],'name'=>$tm['name']];
                    /*if(!in_array($tm['id'],$stepids)){
                        echo 'fsdjfsldjfl';

                        $stepids[] = $tm['id'];
                    }*/
                    if(count($days[$j]['steps'])>1){
                        $days[$j]['color'] = '#ff9292';
                    }
                    if($i<$today){
                        $days[$j]['color'] = '#eeeeee';
                    }

                    $days[$j]['html'] = '<span style="background:'.$days[$j]['color'].';">'.date('Y-m-d',$i).'</span>';
                }
            }
            $j++;


            $i += $one_day_time;
        }
        echo '<pre>';

        echo print_r(array_values($days),1);
        echo '</pre>';
    }

    /**
     * watermark/1
    /image/<encodedImageURL>
    /dissolve/<dissolve>
    /gravity/<gravity>
    /dx/<distanceX>
    /dy/<distanceY>
    /ws/<watermarkScale>
    /wst/<watermarkScaleType>
     */
    public function t20(){
        $s_img = 'http://pa5ijfg62.bkt.clouddn.com/app/20180720/mcdocs-15320765635.jpg';
        $w_img = 'http://pa5ijfg62.bkt.clouddn.com/mcdocs-06c2445980e3a5033a59927f999412cb.jpg';

        /*$input_parms = [
            'dx'=>11,
            'dy'=>16,
            'ws'=>'1',
            'wst'=>1
        ];
        $def_parms = [
            'dissolve'=>99,
            'gravity'=>'SouthEast',
            'dx'=>15,
            'dy'=>15,
            'ws'=>'0.8',
            'wst'=>0
        ];
        $parms = array_merge($def_parms,$input_parms);
        print_r($parms);*/
        $wparms = [
            '?watermark/1',
            '/image/'.base64_encode($w_img),
            '/dissolve/99',
            '/gravity/SouthEast',
            '/dx/15',
            '/dy/15',
            '/ws/0.8',
            '/wst/0'
        ];

        $new_url = $s_img.implode('',$wparms);
        //echo $new_url;
        //print_r(pathinfo($new_url));
        //$path = str_replace(config('qiniu.host'),'',substr($new_url,0,strrpos($new_url,'?')));
        //echo $path;
        //print_r(pathinfo('./data/'.$path));
        \app\common\library\Qiniu::download_upload_watermark($new_url);
    }

    public function t21(){

        $old = [
            'c_2',
            'c_1',
            'c_3',
            'b_2',
            'b_3',
            'b_6',
        ];

        $new = [
            'c_2',
            'b_2',
            'b_3',
            'b_6',
            'b_7',
        ];
        print_r($old);
        print_r($new);

        $in = array_intersect($old,$new);
        print_r($in);
        $out = array_diff($old,$in);
        print_r($out);
        $nin = array_diff($new,$in);
        print_r($nin);
    }

    public function t22(){
        $log_data = [
            'url'=>'baidu.com',
            'data'=>[
                'a'=>'name',
                'b'=>'title'
            ]
        ];
        file_put_contents('/Users/chenxh/Documents/www/we7.log',print_r($log_data,1),FILE_APPEND);
    }

    public function t23(){
        \extend\Mylog::write('is a test ','compleximg_status');
        sleep(1);
        \app\common\library\Mylog::write('is a test too','compleximg_status');
    }
}

