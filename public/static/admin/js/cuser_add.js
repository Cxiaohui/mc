/**
 * Created by chenxh on 2018/6/12.
 */
$(function(){

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
            'formData': {'uptype':'cuser_heads','typeid':0},
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
        var cpid = $('select[name="cpid"]'),
            uname = $('input[name="uname"]'),
            mobile = $('input[name="mobile"]'),
            lgpwd = $('input[name="lgpwd"]');
        //console.log();return false;
        if(!checkSelectEmpty(cpid)){
            formValid.showErr(cpid,'请选择所属公司');
            return false;
        }else{
            formValid.showSuccess(cpid);
        }
        if(!checkInputEmpty(uname)){
            formValid.showErr(uname,'请填写姓名');
            return false;
        }else{
            formValid.showSuccess(uname);
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

        // if(!checkInputEmpty(lgpwd)){
        //     formValid.showErr(lgpwd,'请填写密码');
        //     return false;
        // }else{
        //     formValid.showSuccess(lgpwd);
        // }

    });
});