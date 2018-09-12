/**
 * Created by xiaohui on 2015/8/17.
 */
$(function () {

    //添加/编辑代金券
    $('a.openlayerwin').on('click',function(){
        var url = $(this).attr('data-url');
        if(!url){
            return false;
        }
        layeriframe(url, '添加/编辑代金券', 440, 440);
    });

    // 添加检查
    $('#savepost').on('click',function(){
        var cname = $('input[name="name_zh"]'),
            ename = $('input[name="name_en"]');
        if(!checkInputEmpty(cname)){
            pageMesg.show('请填写名称(中)',0);
            cname.focus();
            return false;
        }
        if(ename.length>0 && !checkInputEmpty(ename)){
            pageMesg.show('请填写名称(英)',0);
            ename.focus();
            return false;
        }
        $('#postform').submit();
    });
});