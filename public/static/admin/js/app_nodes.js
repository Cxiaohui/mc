/**
 * Created by chenxh on 2018/6/12.
 */

$(function () {

    //添加/编辑
    $('a.openlayerwin').on('click',function(){
        var url = $(this).attr('data-url');
        if(!url){
            return false;
        }
        layeriframe(url, 'APP端操作', 400, 400);
    });

    //保存检查
    $('#savedata').on('click',function(){
        var ios_name = $('input[name="ios_name"]'),
            android_name = $('input[name="android_name"]'),
            remark = $('input[name="remark"]');
        if(!checkInputEmpty(ios_name) && !checkInputEmpty(android_name)){
            pageMesg.show('请填写操作名称',0);
            ios_name.focus();
            return false;
        }

        if(!checkInputEmpty(remark)){
            pageMesg.show('请填写操作说明',0);
            remark.focus();
            return false;
        }
        $('#postform').submit();
    });

});