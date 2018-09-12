/**
 * Created by xiaohui on 2015/9/2.
 */
$(function(){
    //添加地区
    $('a.openlayerwin').on('click',function(){
        var url = $(this).attr('data-url');
        if(!url){
            return false;
        }
        layeriframe(url, '添加/编辑车牌信息', 600, 400);
    });

    if($('#citybox').length>0){
        $("#citybox").cateSelect({
            url:'area.js',
            nodata:"none"
        });
    }

    //删除
    $('a.delbtn').on('click',function(){
        var _id = $(this).attr('data-id');
        if(!_id){
            return false;
        }

        layerconfirm('删除后将无法恢复，确定要删除吗？', 2, '操作确认', function () {
            var data = {
                'act':'delplate','id':_id,'ajax':1
            };
            $.post('/cate/plateoper',data,function(d){
                if(d.err!='0'){
                    layeralert(d.mesg,0,'操作提示');
                    return false;
                }else{
                    layermsgpos(d.mesg,2,1);
                    setTimeout(function(){
                        location.reload();
                    },1800);
                }

            },'json');
            closelayer();
        }, function () {
            closelayer();
        });




    });

    //保存检查
    $('#savedata').on('click',function(){
        var area = $('select[name="lv1id"]'),
            name = $('input[name="name"]');

        if(!checkInputEmpty(name)){
            pageMesg.show('请填写简称',0);
            name.focus();
            return false;
        }
        if(!checkSelectEmpty(area)){
            pageMesg.show('请选择地区',0);
            area.focus();
            return false;
        }

        $('#postform').submit();
    });
});