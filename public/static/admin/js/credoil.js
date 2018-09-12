/**
 * Created by xiaohui on 2015/8/18.
 */
$(function(){

    $('a.operbtn').on('click',function(){
        var me = $(this),
            _act = me.attr('data-act'),
            _id = me.attr('data-id');
        if(!_act || !_id){
            return false;
        }

        if(_act=='setstat'){
            post.data = {'act':_act,'id':_id,'ajax':1};
            post.send();
            return false;
        }else if(_act=='resetc'){
            var _uid = me.attr('data-uid');
            if(!_uid){
                return false;
            }
            layerconfirm('恢复积分后不可还原，是否继续？', 2, '操作确认', function () {
                post.data = {'act':_act,'id':_id,'uid':_uid,'ajax':1};
                post.send();
                return false;
            }, function () {
                closelayer();
                return false;
            });
        }



    });

});

var post = {
    'url':'/credoil/listopers',
    'data':{},
    send:function(){
        if(!this.data || isEmptyObj(this.data)){
            return false;
        }
        $.post(this.url,this.data,function(d){
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
    }
};