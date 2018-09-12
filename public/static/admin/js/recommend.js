/**
 * Created by xiaohui on 2015/8/14.
 */
$(function(){

    $('a.openlayerwin').on('click',function(){
        var url = $(this).attr('data-url');
        if(!url){
            return false;
        }
        layeriframe(url, '推荐设置', 600, 550);
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