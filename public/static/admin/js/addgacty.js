/**
 * Created by xiaohui on 2015/8/25.
 */
var tagsobj = null;
$(function(){

    $('select[name="type"]').on('change',function(){
        return false;
    });

    tagsobj = $('#idtags').tagsInput({
        width: '50%',
        height:'200px',
        //autosize:false,
        defaultText:'输入关联的id',
        /*onChange:function(obj,val){
            console.log('onchange'+val);
        },*/
        onRemoveTag:function(val){
            //console.log('onRemoveTag'+val);
            gettags.remove(val);
        },
        onAddTag:function(val,opts){
            //console.log('onAddTag'+val);
            gettags.putout(val);
        }
    });

    //封面上传
    var confg = {
        'btntext': '<i class="icon-upload-alt"></i> 上传活动封面',
        'server': '/up',
        'progress': $('#progressbar'),
        'statbar': $('.statusBar'),
        'viewimg': '',
        'inneralert':false,
        'isiframe':false,
        'formData': {'uptype':'gactyimg','typeid':0},
        'afterSuccess': function (file, response) {
            //console.log(response);
            var timenow = new Date().getTime();
            $('#coverimg').attr('src', response['data'] + '?' + timenow);
            $('#coverinput').val(response['info']);
        }

    };
    //插件初始化
    sUpd.init(confg);

    gettags.init(litags);
    listentype.init();
    valid.init();
});
//类型选择
var listentype = {
    'self':'select[name="type"]',
    'curval':'1',
    init:function(){
        this.curval = $(this.self).val();
        this._bindEvent();
    },
    _bindEvent:function(){
        $(this.self).on('change',function(){
            var me = $(this), htags = $('#idtags').val();
            if(!htags || htags==''){
                return false;
            }
            layerconfirm('切换类型后，关联id将清空，是否继续', 2, '操作确认', function () {
                listentype.curval = me.val();
                gettags.cleartags();
                closelayer();
            }, function () {
                me.val(listentype.curval);
                closelayer();
            });
        });
    }
};

//标签处理
var gettags = {
    'tagname':{},
    'url':'/gacty/gactyoper',
    'data':{},
    'listbox':'#listbox',
    'valinput':'input[name="resids"]',
    init:function(tags){
        this.tagname = tags;

    },
    cleartags:function(){
        this.tagname = {};
        $(this.valinput).val('');
        $('.tag',$('#idtags_tagsinput')).each(function(i){
            $(this).remove();
        });
        $(this.listbox).html('');
    },

    remove:function(id){
        if($('#li'+id).length >0){
            $('#li'+id).remove();
        }
    },
    putout:function(id){
        if(!id){
            return false;
        }
        if(this.tagname[id]){
            this._addlist(id,this.tagname[id]);
            return false;
        }

        var type = $('select[name="type"] option:selected').val();
        this.data = {'act':'getname','type':type,'sid':id,'ajax':1};
        this._post();
    },
    _post:function(){
        if(!this.data || isEmptyObj(this.data)){
            return false;
        }
        $.post(this.url,this.data,function(d){
            if(d.err!='0'){
                layeralert(d.mesg,0,'操作提示');
                gettags._removeTags();
                return false;
            }else{
                gettags._addlist(d.data.id, d.data.name);
            }
        },'json');
    },

    _removeTags:function(){
        var idsval = $(this.valinput).val().split(',');
        idsval.pop();
        $(this.valinput).val(idsval);
        $('.tag',$('#idtags_tagsinput')).last().remove();
    },

    _addlist:function(id,name){
        $(this.listbox).append('<li id="li'+id+'">【'+id+'】'+name+'</li>');
    }



};

//表单检查
var valid = {
    'subbtn':'#savebtn',
    'form':'#postform',

    init:function(){
        this._bindEvent();
    },
    _bindEvent:function(){
        $(this.subbtn).on('click',function(){
            if(!valid._check()){
                return false;
            }
            $(valid.form).submit();
        });

    },
    _check:function(){
        var title = $('input[name="title"]'),
            bdate = $('input[name="bedate"]'),
            bh = $('input[name="behs"]').val(),
            edate = $('input[name="endate"]'),
            eh = $('input[name="enhs"]').val(),
            cimg = $('input[name="coverimg"]'),
            resids = $('input[name="resids"]');



        if(!checkInputEmpty(title)){
            formValid.showErr(title,'请填写活动标题');
            return false;
        }else{
            formValid.showSuccess(title);
        }
        if(!checkInputEmpty(bdate)){
            formValid.showErr($('#datebox'),'请填写活动开始时间');
            return false;
        }else{
            formValid.showSuccess($('#datebox'));
        }
        if(!checkInputEmpty(edate)){
            formValid.showErr($('#datebox'),'请填写活动结束时间');
            return false;
        }else{
            formValid.showSuccess($('#datebox'));
        }
        if(!aGtB(edate.val()+' '+eh,bdate.val()+' '+bh)){
            formValid.showErr($('#datebox'),'开始时间不能大于结束时间');
            return false;
        }else{
            formValid.showSuccess($('#datebox'));
        }

        if(!checkInputEmpty(cimg)){
            formValid.showErr(cimg,'请上传活动封面');
            return false;
        }else{
            formValid.showSuccess(cimg);
        }

        if(!checkInputEmpty(resids)){
            formValid.showErr(resids,'请填写关联的id');
            return false;
        }else{
            formValid.showSuccess(resids);
        }

        if(!checkInputEmpty(resids)){
            formValid.showErr(resids,'请填写关联的id');
            return false;
        }else{
            formValid.showSuccess(resids);
        }

        return true;

    }

};