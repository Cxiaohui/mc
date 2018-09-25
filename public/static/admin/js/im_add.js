/**
 * Created by chenxh on 2018/9/25.
 */


$(function(){

    $('#subform').on('click',function(){


        $('#postform').submit();
    });

    $('.kikout').on('click',function(){

        var _this = $(this),
            accid = _this.attr('data-id');
        if(!accid ||!imid){
            return false;
        }

        layerconfirm("真的要踢出群吗？",2,"确认",function(){
            $.get(out_url,{'accid':accid,'id':imid},function(d){
                console.log(d);
                if(d.err=='0'){
                    layermsg(d.msg,1);
                    _this.closest('tr').remove();
                }else{
                    layeralert(d.msg,4);
                    return false;
                }

            },'json');
        },function(){
            layer.closeAll();
        });

    });

    $('#search_member').on('click',function(){
        var mobile = $('#mobile').val();

        if(!mobile){
            layeralert('请输入手机号码',4);
            return false;
        }
        $.get(so_buser,{'mobile':mobile},function (d) {
            //console.log(d);
            if(d.err=='0'){
                fill_search(d.data);
            }else{

                layeralert(d.msg,4);
                return false;
            }
        },'json');

    });

    function fill_search(data){
        var len = data.length,
            tpl = '',
            i=0;
        for(;i<len;i++){
            tpl = '<p>'+data[i].department+'，'+data[i].post+'，'+data[i].name+'('+data[i].en_name+') <a href="javascript:;" data-id="'+data[i].id+'" onclick="addintogroup(this);">加入群聊</a></p>';
        }

        $('#search_result').html(tpl);
    }

});

function addintogroup(o){
    var uid = $(o).attr('data-id');
    if(!uid ||!imid){
        return false;
    }

    $.get(add_url,{'id':imid,'uid':uid},function(d){
        console.log(d);
        if(d.err=='0'){
            layermsg(d.msg,1);
            //刷新页面
            setTimeout(function(){
                //location.reload();
            },1400);

        }else{
            layeralert(d.msg,4);
            return false;
        }
    },'json');
}