/**
 * Created by chenxh on 2017/12/20.
 */
$(function(){



    $('.refund_btn').on('click',function(){
        var _id = $(this).attr('data-id');
        var lindex = layerconfirm('确定退款吗？', 2, '确认', function () {

            //console.log(_id);
            if(!_id){
                layer.close(lindex);
                return false;
            }

            $.post(refund_url,{'oid':_id},function(d){
                if(d.err){
                    layeralert(d.mesg,4,'提示');
                }else{
                    layermsg(d.mesg,1);
                    setTimeout(function(){
                        location.reload();
                    },1500);
                }
            },'json');

        }, function () {
            layer.close(lindex);
        });

    });




});