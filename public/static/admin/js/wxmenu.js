/**
 * Created by chenxh on 2017/7/11.
 */
$(function () {

    //添加/编辑菜单
    $('a.openlayerwin').on('click',function(){
        var url = $(this).attr('data-url');
        if(!url){
            return false;
        }
        layeriframe(url, '菜单信息', 400, 400);
    });

    //保存检查
    $('#savedata').on('click',function(){
        var name = $('input[name="name"]');
        if(!checkInputEmpty(name)){
            pageMesg.show('请填写菜单名称',0);
            name.focus();
            return false;
        }
        $('#postform').submit();
    });

    $('.sort_input').on('blur',function(){

        var v = $(this).val(),
            _id = $(this).attr('data-id');
        //console.log(v);
        if(!_id){
            return false;
        }
        var data = {
            'act':'sort',
            'id':_id,
            'sort':v
        };
        post_data(data);
    });

    $('#sub_menu').on('click',function(){

        var mindex = layerconfirm('确定要更新菜单到公众号吗？',1,'操作确认',function(){
           post_data({'act':'sub_menu'});
            layer.close(mindex);
        },function () {
            layer.close(mindex);
        });

    });

    function post_data(data){
        if(!data){
            return false;
        }
        $.post(post_url,data,function(d){
            if(d.err){
                layeralert(d.mesg);
            }else{
                playermsg(d.mesg);
                setTimeout(function(){
                    location.reload();
                },1500);
            }
            return false;
        },'json');
    }

});