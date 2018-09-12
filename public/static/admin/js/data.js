/**
 * Created by xiaohui on 2015/8/15.
 */
$(function(){

    $('select[name="tableselect"]').on('change',function(){
        var val = $(this).val(),_url = location.href;

        location.href = _url.replace(/tb\/[a-z_0-9]+/i,'tb/'+val);
    });

    //优化表
    if($('a.optbtn').length > 0){
        $('a.optbtn').on('click',function(){

            var me = $(this),_t = me.attr('data-t');
            if(!_t || _t==''){
                return false;
            }
        post.data = {'act':'optable','t':_t,'ajax':1};
            post.aftsend = function(d){
                if(d.err!='0'){
                    layeralert(d.mesg,0,'操作提示');
                    return false;
                }else{
                    layermsgpos(d.mesg,2,1);
                    setTimeout(function(){
                        location.reload();
                    },1500);
                }
            };
        post.send();
        });
    }
    //保存表/字段注释
    if($('input.comtinput').length > 0){
        $('input.comtinput').on('keyup',function(event){
            if(event.keyCode==13){
                var tb = $('var').attr('data-tb'),
                    me = $(this),
                    name = me.attr('name'),
                    val = me.val();
                post.data = {'act':'savecomment','tb':tb,'name':name,'val':val,'ajax':1};
                post.aftsend = function(d){
                    if(d.err!='0'){
                        layeralert(d.mesg,0,'操作提示');
                        return false;
                    }else{
                        //layermsgpos(d.mesg,2,1);
                        me.next('span').removeClass('hide');
                    }
                };
                post.send();
            }
        });
    }


});
var post = {
    'url':'/data/tableopers',
    'data':{},
    'aftsend':function(d){},
    send:function(){

        if(!this.data || isEmptyObj(this.data)){
            return false;
        }

        $.post(this.url,this.data,function(d){
            post.aftsend(d);
        },'json');

    }
};