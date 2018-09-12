/**
 * Created by chenxh on 2018/7/5.
 */
$(function(){

    $('.checkbtn').on('click',function(){

        var _this = $(this),
            id = _this.attr('data-id'),
            v = _this.attr('data-v');
        if(!id || id<=0 || (v!=1 && v!=2)){

            return false;
        }
        layerconfirm('你确定该验收报告可以通过吗？',2,'提示',function(){
            $.post(check_url,{'act':'checkreport','id':id,'value':v},function(d){
                if(d.err==0){
                    layermsg(d.mesg,1);
                    setTimeout(function(){
                        location.reload();
                    },1500);
                }else{
                    layeralert(d.mesg,4,'提示');
                }
            },'json');
        },function(){

        });

    });


});