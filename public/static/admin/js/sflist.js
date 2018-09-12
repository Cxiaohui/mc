/**
 * Created by xiaohui on 2015/7/27.
 */


$(function(){


    newfrecord.init();

});
var newfrecord = {
    'showbtn':'#addnew',
    'hidebtn':'#caladd',
    'addbox':'#addbox',
    'form':'#postform',
    'subbtn':'#subadd',
    init:function(){
        this.bindEvent();
    },
    bindEvent:function(){
        $(this.showbtn).on('click',function(){
            newfrecord.showaddbox();
        });

        $(this.hidebtn).on('click',function(){
            newfrecord.hideaddbox();
        });

        $(this.subbtn).on('click',function(){
            newfrecord.checkPost();
        });
    },
    checkPost:function(){
        var cont = $('textarea[name="fcont"]');
        if(!checkInputEmpty(cont)){
            pageMesg.show('请填写跟进内容',0);
            cont.focus();
            return false;
        }
        $(this.form).submit();
    },
    showaddbox:function(){
        $(this.addbox).show();
    },
    hideaddbox:function(){
        $(this.addbox).hide();
    }
};