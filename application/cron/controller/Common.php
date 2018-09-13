<?php
/**
 * Created by PhpStorm.
 * User: chenxh
 * Date: 2018/9/11
 * Time: 10:07
 */
namespace app\cron\controller;
use app\common\controller\Base;
use think\Request;

class Common extends Base{

    private $user = [
        'uname'=>'mcabc',
        'upaswd'=>'mctest'
    ];

    public function _initialize(){
        $this->checkAuth();
    }

    protected function checkAuth(){
        if (!session('ls_admin_login')) {

            if (!isset($_SERVER['PHP_AUTH_USER'])) {
                header('WWW-Authenticate: Basic realm="LS Event Reg Datas"');
                header('HTTP/1.0 401 Unauthorized');
                echo("Please enter a valid username and password");
                exit();
            } else if (($_SERVER['PHP_AUTH_USER'] == $this->user['uname']) && ($_SERVER['PHP_AUTH_PW'] == $this->user['upaswd'])) {
                session('ls_admin_login',1);
            } else {
                header('WWW-Authenticate: Basic realm="LS Event Reg Datas"');
                header('HTTP/1.0 401 Unauthorized');
                echo("Please enter a valid username and password");
                exit();
            }
        }
    }

    protected function doShell($cmd,$cwd=null){
        $descriptorspec = array(
            0 => array("pipe", "r"),    // stdin
            1 => array("pipe", "w"),    // stdout
            2 => array("pipe", "w")     // stderr
        );

//        $cmd = './test.sh';  // 替换为你要执行的shell脚本
        /*
         * 返回值
         *     返回表示进程的资源类型， 当使用完毕之后，请调用 proc_close() 函数来关闭此资源。 如果失败，返回 FALSE。
         * cmd 要执行的命令
         * descriptorspec 一个索引数组。
         *      数组的键表示描述符， 0 表示标准输入（stdin），1 表示标准输出（stdout），2 表示标准错误（stderr）
         *      数组元素值表示 PHP 如何将这些描述符传送至子进程。
         *                  pipe （第二个元素可以是： r 向进程传送该管道的读取端，w 向进程传送该管道的写入端），
         *                    以及 file（第二个元素为文件名）。
         * pipes
         *      将被置为索引数组， 其中的元素是被执行程序创建的管道对应到 PHP 这一端的文件指针。
         * cwd
         *      要执行命令的初始工作目录。 必须是 绝对 路径， 设置此参数为 NULL 表示使用默认值（当前 PHP 进程的工作目录）
         * env
         *      要执行的命令所使用的环境变量。 设置此参数为 NULL 表示使用和当前 PHP 进程相同的环境变量。
         *
         * */
        $proc = proc_open($cmd, $descriptorspec, $pipes, $cwd, null);
        // $proc为false，表明命令执行失败
        if ($proc == false) {
            return false;
            // do sth with HTTP response
        } else {
            $stdout = stream_get_contents($pipes[1]);
            fclose($pipes[1]);
            $stderr = stream_get_contents($pipes[2]);
            fclose($pipes[2]);
            $status = proc_close($proc);  // 释放proc
        }
        $data = array(
            'stdout' => $stdout,
            'stderr' => $stderr,
            'retval' => $status
        );

        return $data;
    }

}