/**
 * Created by xiaohui on 2015/7/16.
 */
$(function(){
    $('#editbtn').on('click', function () {
        var pwd = $('input[name="pwd"]'),
            pwd1 = $('input[name="pwd1"]');
        if(!checkInputEmpty(pwd)){
            formValid.showErr(pwd,'请填写新密码');
            return false;
        }else{
            formValid.showSuccess(pwd);
        }
        if(!checkInputEmpty(pwd1)){
            formValid.showErr(pwd1,'请确认密码');
            return false;
        }else{
            formValid.showSuccess(pwd1);
        }
        if($.trim(pwd.val())!= $.trim(pwd1.val())){
            formValid.showErr(pwd1,'二次密码不一致，请确认');
            return false;
        }else{
            formValid.showSuccess(pwd1);
        }

    });
});