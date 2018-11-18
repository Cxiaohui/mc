<?php
/**
 * Created by PhpStorm.
 * User: chenxh
 * Date: 2018/11/18
 * Time: 23:08
 */
namespace app\gerent\job;

use think\queue\Job;

class Imageslim{
    protected $log_file = 'imageslim_status';

    public function fire(Job $job,$data=[])
    {

    }
}