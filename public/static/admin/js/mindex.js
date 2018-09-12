/**
 * Created by xiaohui on 2015/7/21.
 */
$(function () {
    //会员列表页筛选操作
    if($('#carbox').length>0){
        $("#carbox").cateSelect({
            url:'cars.js',
            nodata:"none"
        });
        $('#carbox select').on('change', function () {
            var val = $(this).val(),url = location.href;
            if(url.indexOf('band')>-1) {
                url = url.replace(/band\/\d+/i, 'band/' + val);
            }else{
                url = url.replace('.html','/band/'+val);
            }
            location.href = url;
        });
    }
    if($('#areabox').length>0){
        $("#areabox").cateSelect({
            url:'area.js',
            nodata:"none"
        });

        $('#areabox select.lv1select').on('change', function () {
            var val = $(this).val(),url = location.href;
            if(url.indexOf('lv1id')>-1){
                url = url.replace(/lv1id\/\d+/i,'lv1id/'+val);
            }else{
                url = url.replace('.html','/lv1id/'+val+'.html');
            }


            location.href = url;
        });
        $('#areabox select.lv2select').on('change', function () {
            var val = $(this).val(),url = location.href;
            if(url.indexOf('lv2id')>-1){
                url = url.replace(/lv2id\/\d+/i,'lv2id/'+val);
            }else{
                url = url.replace('.html','/lv2id/'+val+'.html');
            }
            //console.log(url);
            location.href = url;
        });
    }
    //执行搜索
    $('#sobtn').on('click', function () {
        var sok = $('select[name="sok"]'),
            soval = $('input[name="soval"]');
        if(!checkSelectEmpty(sok)){
            return false;
        }
        if(!checkInputEmpty(soval)){
            return false;
        }
        $('#sofrom').submit();
    });

    //汽车信息弹窗
    $('a.openlayerwin').on('click',function(){
        var url = $(this).attr('data-url');
        if(!url){
            return false;
        }
        layeriframe(url, '汽车信息', 400, 300);
    });

    //bbs数据
    $('a.bbsbtn').on('click',function(){
        var _id = $(this).attr('data-uid');
        if(!_id || _id<=0){
            return false;
        }
        post.data = {'id':_id,'act':'createbbs','ajax':1};
        post.aftsend = function(d){
            if(d.err=='1'){
                layeralert(d.mesg);
                return false;
            }else{
                layermsg('创建成功！',1);
                setTimeout(function(){
                    location.reload();
                },1800);
            }
        };
        post.send();

    });

    //会员详情页
    editphone.init();


});
var post = {
    'url':'/member/mopers',
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
//修改手机
var editphone ={
    ebtn:'#editbtn',
    ebox:'#editphonebox',
    vbox:'#viewbox',
    subbtn:'#subedit',
    canbtn:'#canceledit',
    data:{},
    url:'',
    init:function(){
        this._bindEvent();
        this.url = '/Member/mopers';
    },

    _bindEvent:function(){
        $(this.ebtn).on('click',function(){
            editphone._showEditbox();
        });
        $(this.canbtn).on('click',function(){
            editphone._hideEditbox();
        });
        $(this.subbtn).on('click',function(){
            var _id = $(this).attr('data-id'),
                m = $(this).prev('input[name="mobile"]');
            if(!_id){
                return false;
            }
            if(!checkInputEmpty(m)){
                layeralert('请填写手机号码');
                m.focus();
                return false;
            }
            if(!isPhone(m)){
                layeralert('手机号码格式不正确');
                m.focus();
                return false;
            }
            editphone.data = {'act':'editmobile','uid':_id,'mobile': m.val(),'ajax':1};
            editphone._subPost();
        });
    },
    _subPost:function(){
        if(isEmptyObj(this.data)){
            return false;
        }
        $.post(this.url,this.data,function(d){
            if(d.err=='1'){
                layeralert(d.mesg);
                return false;
            }else{
                layermsg('修改成功！',1);
                setTimeout(function(){
                    location.reload();
                },1800);
            }
        },'json');

    },
    _showEditbox: function () {
        $(this.ebox).removeClass('hide');
        $(this.vbox).addClass('hide');
    },
    _hideEditbox:function(){
        $(this.vbox).removeClass('hide');
        $(this.ebox).addClass('hide');
    }

};