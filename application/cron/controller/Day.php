<?php
/**
 * Created by PhpStorm.
 * User: chenxh
 * Date: 2018/9/29
 * Time: 19:29
 */
namespace app\cron\controller;

class Day extends Common{
    private $log_file = 'cron_day';


    public function gasi(){

        \think\Queue::later(2,'app\gerent\job\Stepstatus',[]);

    }
}