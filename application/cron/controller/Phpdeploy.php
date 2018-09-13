<?php
/**
 * Created by PhpStorm.
 * User: chenxh
 * Date: 2018/9/13
 * Time: 11:02
 */
namespace app\cron\controller;

class Phpdeploy extends Common{


    public function gasi(){

        $command = "sudo /home/script/php_deploy.sh";
        $results = $this->doShell($command);
        echo '<pre>';
        print_r($results);
        echo '</pre>';
    }

}