<?php
/**
 * Created by PhpStorm.
 * User: chenxh
 * Date: 2018/6/12
 * Time: 09:21
 */
namespace app\gerent\validate;
use think\Validate;

class Users extends Validate{

    protected $rule = [
        'cpid'  =>  'require',//|unique:admin
        'uname'  =>  'require',//|unique:admin
        'mobile'=>'require|unique:users',
        'lgpwd' =>  'require'
    ];

    protected $message = [
        'cpid.require'  =>  '请选择客户所属公司',
        'uname.require'  =>  '用户名必须',
        //'uname.alphaDash'  =>  '用户名只能为字母和数字，下划线_及破折号-',
        'mobile.require' =>  '请填写手机号码',
        'mobile.unique' =>  '该手机号码已存在',
        'lgpwd.require' =>  '请设置密码'
    ];

    protected $scene = [
        'add'=>['cpid','uname','mobile','lgpwd'],
        'edit'  =>  ['cpid','uname']
    ];
}