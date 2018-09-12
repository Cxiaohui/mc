/**
 * Created by xiaohui on 2015/7/15.
 */
$(function(){

    //分类
    /*$("#areasbox").cateSelect({
        url:'area.js',
        nodata:"none"
    });*/

    //头像上传
    if($('#filePicker').length>0){
        var confg = {
            'btntext': '<i class="icon-upload-alt"></i> 上传头像',
            'server': up_url,
            'progress': $('#progressbar'),
            'statbar': $('.statusBar'),
            'viewimg': '',
            'inneralert':false,
            'isiframe':false,
            'formData': {'uptype':'admin_heads','typeid':0},
            'afterSuccess': function (file, response) {
                //console.log(response);
                var timenow = new Date().getTime();
                $('#logoimg').attr('src', response['data'] + '?' + timenow);
                $('#logoinput').val(response['info']);
            }

        };
        //插件初始化
        sUpd.init(confg);
    }

    /*formValid.showErr($('input[name="name"]'));
    setTimeout(function(){
        formValid.showSuccess($('input[name="name"]'));
    },1000);*/
    $('#subbtn').on('click',function(){
        var log = $('input[name="log"]'),
            pwd = $('input[name="pwd"]'),
            name = $('input[name="name"]'),
            mobile = $('input[name="mobile"]'),
            role = $('select[name="role"]'),
            cpid = $('select[name="cpid"]'),
            department = $('select[name="department"]'),
            is_join_team = $('select[name="is_join_team"]'),
            post = $('input[name="post"]'),
            post_desc = $('input[name="post_desc"]'),
            post_duties = $('input[name="post_duties"]');
            //console.log();return false;
        if(!checkInputEmpty(log)){
            formValid.showErr(log,'请填写账号');
            return false;
        }else{
            formValid.showSuccess(log);
        }
        if(!checkInputEmpty(pwd)){
            formValid.showErr(pwd,'请填写密码');
            return false;
        }else{
            formValid.showSuccess(pwd);
        }
        if(!checkInputEmpty(name)){
            formValid.showErr(name,'请填写姓名');
            return false;
        }else{
            formValid.showSuccess(name);
        }
        if(!checkInputEmpty(mobile)){
            formValid.showErr(mobile,'请填写手机号码');
            return false;
        }else{
            formValid.showSuccess(mobile);
        }
        if(!isPhone(mobile)){
            formValid.showErr(mobile,'手机号码不正确');
            return false;
        }else{
            formValid.showSuccess(mobile);
        }
        if(!checkSelectEmpty(role)){
            formValid.showErr(role,'请选择权限角色');
            return false;
        }else{
            formValid.showSuccess(role);
        }
        if(!checkSelectEmpty(cpid)){
            formValid.showErr(cpid,'请选择所属公司');
            return false;
        }else{
            formValid.showSuccess(cpid);
        }
        if(!checkSelectEmpty(department)){
            formValid.showErr(department,'请选择部门');
            return false;
        }else{
            formValid.showSuccess(department);
        }
        if(is_join_team.val()>0){
            if(!checkInputEmpty(post)){
                formValid.showErr(post,'请填写岗位信息');
                return false;
            }else{
                formValid.showSuccess(post);
            }
            if(!checkInputEmpty(post_desc)){
                formValid.showErr(post_desc,'请填写岗位描述');
                return false;
            }else{
                formValid.showSuccess(post_desc);
            }
            if(!checkInputEmpty(post_duties)){
                formValid.showErr(post_duties,'请填写岗位职责');
                return false;
            }else{
                formValid.showSuccess(post_duties);
            }
        }

    });
});