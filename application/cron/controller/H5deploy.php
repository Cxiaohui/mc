<?php
/**
 * Created by PhpStorm.
 * User: chenxh
 * Date: 2018/9/11
 * Time: 10:09
 */
namespace app\cron\controller;

class H5deploy extends Common{


    public function kaishi(){

        $command = "sudo /home/script/h5deploy.sh";
        $results = $this->doShell($command);
        echo '<pre>';
        print_r($results);
        echo '</pre>';
    }

}