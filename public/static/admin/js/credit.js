/**
 * Created by xiaohui on 2015/7/22.
 */

$(function(){

    //添加窗口
    $('a.openlayerwin').on('click',function(){
        var url = $(this).attr('data-url');
        if(!url){
            return false;
        }
        layeriframe(url, '添加积分项', 420, 440);
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
        if(!checkInputEmpty(ename)){
            pageMesg.show('请填写名称(英)',0);
            ename.focus();
            return false;
        }
        $('#postform').submit();
    });

    //行内保存
    $('.saveRow').on('click', function () {
        var me = $(this),
            _id = me.attr('data-id');
        if(!_id){
            return false;
        }
        var tr = me.parent('td').parent('tr'),
            zhname = tr.find('input[name="zhname"]'),
            val = tr.find('input[name="val"]'),
            maxn = tr.find('input[name="max"]'),
            data = {};

        if(!checkInputEmpty(zhname)){
            layeralert('请填写名称(中)');
            zhname.focus();
            return false;
        }

        data = {
            'id':_id,
            'name_zh':zhname.val(),
            'val':val.val(),
            'maxnum':maxn.val(),
            'ajax':1,
            'act':'rowedit'
        };
        $.post(oper_url,data,function(d){
            if(d.err=='1'){
                layeralert(d.mesg);
                return false;
            }else{
                layermsg(d.mesg);
            }
        },'json');
        return false;
    });

});
