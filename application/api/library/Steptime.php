<?php
/**
 * Created by PhpStorm.
 * User: chenxh
 * Date: 2018/7/21
 * Time: 23:46
 */
namespace app\api\library;

use app\common\model\Projectpay,
    app\common\model\Projectstep,
    app\common\model\Booking;

class Steptime{
    /**
     * @param $main_steps
     * @return mixed
     * Array
    (
    [0] => Array
    (
    [id] => 79
    [type] => 1
    [name] => 当前阶段：平面设计
    [realtime] =>
    [real_end_time] =>
    [active] => 1
    [plan_begin_date] => 2018-09-12
    [plan_end_date] => 2018-12-12
    [plan_date_str] => 09.12-12.12
    [color] => #a9e9fe
    )

    [1] => Array
    (
    [id] => 82
    [type] => 2
    [name] => 下一阶段：平面设计
    [realtime] =>
    [color] => #8ae7bc
    [active] => 0
    [real_end_time] =>
    [plan_begin_date] => 2019-09-12
    [plan_end_date] => 2018-12-31
    [plan_date_str] => 09.12-12.31
    )

    [2] => Array
    (
    [id] => 80
    [type] => 1
    [name] => 效果图设计
    [realtime] =>
    [real_end_time] =>
    [active] => 0
    [plan_begin_date] => 2018-09-12
    [plan_end_date] => 2018-12-31
    [plan_date_str] => 09.12-12.31
    )

    [3] => Array
    (
    [id] => 88
    [type] => 2
    [name] => 装饰项目
    [realtime] =>
    [real_end_time] =>
    [active] => 0
    [plan_begin_date] => 2018-09-12
    [plan_end_date] => 2018-09-12
    [plan_date_str] => 09.12-09.12
    )

    [4] => Array
    (
    [id] => 81
    [type] => 1
    [name] => 施工图设计
    [realtime] =>
    [real_end_time] =>
    [active] => 0
    [plan_begin_date] => 2018-09-12
    [plan_end_date] => 2018-12-31
    [plan_date_str] => 09.12-12.31
    )

    [5] => Array
    (
    [id] => 95
    [type] => 2
    [name] => 油漆项目
    [realtime] =>
    [real_end_time] =>
    [active] => 0
    [plan_begin_date] => 2018-09-12
    [plan_end_date] => 2018-09-12
    [plan_date_str] => 09.12-09.12
    )

    [6] => Array
    (
    [id] => 100
    [type] => 2
    [name] => 安装项目
    [realtime] =>
    [real_end_time] =>
    [active] => 0
    [plan_begin_date] => 2018-09-12
    [plan_end_date] => 2018-09-12
    [plan_date_str] => 09.12-09.12
    )

    [7] => Array
    (
    [id] => 105
    [type] => 2
    [name] => 其它
    [realtime] =>
    [real_end_time] =>
    [active] => 0
    [plan_begin_date] => 2018-09-12
    [plan_end_date] => 2018-09-12
    [plan_date_str] => 09.12-09.12
    )

    )
    时间有误
     */
    static public function get_mainstep_color($main_steps){
        $colors = self::step_colors();
        $now = date('Y-m-d');
        $has_cur = false;
        $i=0;
        foreach ($main_steps as $k => $mstp) {
            $times = explode('|', $mstp['plan_time']);
            $retimes = explode('|', $mstp['realtime']);
            $main_steps[$k]['real_end_time'] = '';
            if(count($retimes)==2 && $retimes[1]){
                $main_steps[$k]['real_end_time'] = $retimes[1];
            }
            unset($main_steps[$k]['plan_time']);
            $main_steps[$k]['active'] = 0;
            $main_steps[$k]['plan_begin_date'] = $times[0];
            $main_steps[$k]['plan_end_date'] = isset($times[1]) ? $times[1] : '';

            $plan_date_str = date('m.d',strtotime($times[0]));
            if(isset($times[1])){
                $plan_date_str .= '-'.date('m.d',strtotime($times[1]));
            }
            $main_steps[$k]['plan_date_str'] = $plan_date_str;

            $main_steps[$k]['color'] = $colors['before'];

            if($times[1]<$now){
                $main_steps[$k]['color'] = $colors['before'];
            }
            if($times[0]>$now){
                $main_steps[$k]['color'] = $colors['after'][$i%2];
            }
            if (!$has_cur && $times[0] <= $now && $times[1] >= $now) {
                $main_steps[$k]['name'] = '当前阶段：' . $mstp['name'];
                $main_steps[$k]['color'] = $colors['now'];
                $main_steps[$k]['active'] = 1;
                $has_cur = true;
                if (isset($main_steps[$k + 1])) {
                    $main_steps[$k + 1]['name'] = '下一阶段：' . $mstp['name'];
                    $main_steps[$k+1]['color'] = $colors['after'][$i%2];
                    $main_steps[$k+1]['active'] = 0;
                }
            }

            $i++;
        }

        return $main_steps;
    }



    //加上竣工时间及事项 在 $times 中加上状态
    // 项目应付款项
    // 预约与被预约事项

    static public function get_color_days($times,$p_id=0,$user_type=1){
        //print_r($times);exit;
        $time_range_begin = strtotime($times[0]['plan_begin_date']);
        $time_range_end = strtotime($times[count($times)-1]['plan_end_date']);
        $one_day_time = 24*3600;
        $today = strtotime(date('Y-m-d'));

        $real_end_time = $times[count($times)-1]['real_end_time'];
        if($real_end_time && $real_end_time>$time_range_end){
            $time_range_end = $real_end_time;
        }

        //项目的应付款项信息
        $paylist = (new Projectpay())->get_list(['p_id'=>$p_id,'paied_time'=>0,'isdel'=>0],'id,name,payable,payable_time',0);

        // 预约信息  $user_type
        $bookings = (new Booking())->get_list(['p_id'=>$p_id],'id,to_users,booking_time,booking_content');

        $days = [];
        $colors = self::step_colors();
        $j=0;
        /*if($time_range_begin<=$time_range_end){
            return false;
        }*/
        //todo 显示子节点信息 20180929
        //$projectstep = new Projectstep();
        for($i=$time_range_begin;$i<=$time_range_end;){
            //echo date('Y-m-d',$i),PHP_EOL;
            $is_sp_po = false;
            foreach($times as $tm){
                //获取子阶段信息
                /*$w = ['pid'=>$tm['id'],'isdel'=>0];
                $has_sub = $projectstep->get_count($w);
                if($has_sub){
                    $subs = $projectstep->get_list($w,'id,type,name,plan_time,realtime');
                    $subs = self::get_mainstep_color($subs);
                }*/

                if($i>=strtotime($tm['plan_begin_date']) && $i<= strtotime($tm['plan_end_date'])){
                    //$tmp = [];
                    $days[$j]['date'] = date('Y-m-d',$i);
                    $days[$j]['color'] = $tm['color'];
                    if($i==$today){
                        $days[$j]['color'] = $colors['now'];
                    }
                    /*else if($i>$today){
                        $days[$j]['color'] = '#b8f7d9';
                    }*/
                    //找出竣工时间及事项
                    if($tm['real_end_time'] && $i==strtotime($tm['real_end_time'])){
                        $days[$j]['steps'][] = [
                            'title'=>$tm['name'].' 实际竣工日',
                            'content'=>'',
                            'date_time'=>date('Y-m-d',$i)
                        ];
                        $is_sp_po = true;
                    }

                    $days[$j]['steps'][] = ['id'=>$tm['id'],'title'=>$tm['name'],'content'=>'','date_time'=>date('Y-m-d',$i)];

                    if(count($days[$j]['steps'])>1){
                        $days[$j]['color'] = $colors['t_po'];
                    }
                    if($i<$today){
                        $days[$j]['color'] = $colors['before'];
                    }
                }
            }

            if(!empty($paylist)){
                foreach($paylist as $pay){
                    if($i==strtotime($pay['payable_time'])){
                        $days[$j]['steps'][] = [
                            'title'=>'项目款应付日',
                            'content'=>'应付金额'.$pay['payable'],
                            'date_time'=>date('Y-m-d',$i)
                        ];
                        if(!$is_sp_po){
                            $is_sp_po = true;
                        }

                    }
                }
            }

            if(!empty($bookings)){

                foreach($bookings as $bok){
                    $bk_time = strtotime(substr($bok['booking_time'],0,10));
                    if($i==$bk_time){
                        $days[$j]['steps'][] = [
                            'title'=>'预约看工地',
                            'content'=>$bok['booking_content'],
                            'date_time'=>$bok['booking_time']
                        ];
                        if(!$is_sp_po){
                            $is_sp_po = true;
                        }
                    }
                }
            }

            if($is_sp_po){
                $days[$j]['color'] = $colors['d_po'];
            }
            $j++;
            $i += $one_day_time;
        }

        return array_values($days);
    }

    static public  function step_colors(){
        return [
            'before'=>'#f6f6f6',//过去节点
            'now'=>'#a9e9fe',//正在进行节点
            'after'=>['#b8f7d9','#8ae7bc'],//未来节点
            't_po'=>'#a4cffe',//重叠节点
            'd_po'=>'#ff9292',//特殊节点颜色
        ];
    }
}