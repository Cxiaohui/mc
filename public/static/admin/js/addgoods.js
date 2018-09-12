/**
 * Created by xiaohui on 2015/7/28.
 */
$(function(){


    //logo上传
    var confg = {
        'btntext': '<i class="icon-upload-alt"></i> 上传商品Logo图片',
        'server': uppath,
        'progress': $('#progressbar'),
        'statbar': $('.statusBar'),
        'viewimg': '',
        'inneralert':false,
        'isiframe':false,
        'formData': {'uptype':'goodslogo','typeid':0},
        'afterSuccess': function (file, response) {
            //console.log(response);
            var timenow = new Date().getTime();
            $('#logoimg').attr('src', response['data'] + '?' + timenow);
            $('#logoinput').val(response['info']);
        }

    };
    //插件初始化
    sUpd.init(confg);
    //多图片上传
    var mconfg = {
        'btntext': '<i class="icon-upload-alt"></i> 选择要上传的展示图片',
        'server': uppath,
        'inneralert':false,
        'isiframe':false,
        'formData': {'uptype':'goodsimgs','typeid':0},
        'afterSuccess': function (file, response) {
            appendInputVal($('#imgsinput'),response['info']);
        }
    };
    mUpd.init(mconfg);
    if($('#showmupbox').length>0){
        $('#showmupbox').on('click',function(){
            var upbox = $('#uploader'),
                dis = upbox.css('display'),
                me = $(this);
            if(dis=='none'){
                upbox.show();
                me.text('取消');
            }else{
                upbox.hide();
                me.text('上传更多图片');
            }
        });
    }

    //商品分类
    $("#goodsbox").cateSelect({
        url:'goods.js',
        nodata:"none"
    });
    //配件分类
    $("#partsbox").cateSelect({
        url:'parts.js',
        nodata:"none"
    });

    //特价
    $('input[name="istejia"]').on('change',function(){
        var val = $(this).val();
        if(val==1){
            $('.teijiarow').show();
        }else{
            $('.teijiarow').hide();
        }
    });

    //搜索店铺
    soshop.init();

    //车系选择
    carsel.init();
    valid.init();
    delFile.init();
    mFile.init();
});
//搜索店铺
var soshop={
    'idinput':'#sidinput',
    'sobtn':'#soshopname',
    'namebox':'#shopnamebox',
    'aurl':'/shop/shopopers',
    'data':{},
    init:function(){
        this._bindEvent();
    },
    _bindEvent:function(){
        $(this.sobtn).on('click',function(){
            var ele = $(soshop.idinput);
            if(!checkInputEmpty(ele)){
                layeralert('请输入店铺id',0,'操作提示');
                ele.val('');
                return false;
            }
            if(ele.val()<=0){
                layeralert('店铺id不正确',0,'操作提示');
                ele.val('');
                return false;
            }
            soshop.data = {'act':'findname','ajax':1,'sid':ele.val()};
            soshop._post();
        });
    },
    _post:function(){

        if(!this.data || isEmptyObj(this.data)){
            return false;
        }

        $.post(this.aurl,this.data,function(d){
            if(d.err!='0'){
                layeralert(d.mesg,0,'操作提示');
                return false;
            }else{
                $(soshop.namebox).show().find('input').val(d.info);
            }
        },'json');
    }
};

//车系选择
var carsel = {
    'selele':'#carsselele',
    'subbox':'#subbox',
    'tagsbox':'#cartagsbox',
    'carsjosn':{},
    'lv1id':0,
    'lv1txt':'',
    'lv2id':0,
    'lv2txt':'',
    'carjsurl':'/public/attachment/cate/cars.js',
    init:function(){
        this._getCarJSON();

        setTimeout(function(){
            carsel._bindEvent();
        },800);
    },
    //删除已选标签
    deltag:function(obj){
        $(obj).parent('span').remove();
    },
    //选择子系
    changesel:function(obj){
        var chkd = obj.checked,val = $(obj).val(),
            txt = $(obj).parent('label').text();

        this._changeseltags(val,txt,chkd);
    },
    //品牌
    selallsub: function (obj) {
      var me = $(obj),act = true;
        if(me.hasClass('btn-success')){
            act = false;
            me.removeClass('btn-success');
        }else{
            me.addClass('btn-success');
        }
        this._changeseltags(this.lv1id,this.lv1txt,act);
        /*$(this.subbox).find('input[type="checkbox"]').each(function(i){
            carsel._changecheckval($(this),act);
        });*/

    },
    //子品牌
    selallsubsub:function(obj){
        var me = $(obj),cheked = obj.checked,val = me.val(),txt = $.trim(me.parent('label').text().replace('：',''));
        console.log('sssssss');
        this._changeseltags(val,txt,cheked);
    },

    //事件绑定
    _bindEvent:function(){
        $(this.selele).chosen();
        //车系选择
        $(this.selele).on('change',function(){
            var me = $(this),sidx = me.get(0).selectedIndex,
                val = me.val(),
                sub;
            carsel.lv1id = val;
            carsel.lv1txt = $.trim(me.find('option:selected').text());
            //console.log(carsel.lv1txt);
            if(val=='0'){
                carsel._changeseltags(val,'全部车系',true);
                return false;
            }
            subs = carsel.carsjosn.catelist[sidx-2];
            if(!subs){
                $(this.subbox).html('');
                return false;
            }
            carsel._putoutsubs(subs);
        });
    },
    //改变子系勾选状态
    _changecheckval:function(obj,act){
        var val = obj.val(),txt = obj.parent('label').text();
        obj.attr('checked',act);
        this._changeseltags(val,txt,act);
    },
    //改变已选的标签
    _changeseltags:function(val,txt,act){
        if(act && $('#cartag'+val).length<=0){
            $(this.tagsbox).append(this._tagtpl(val,txt));
        }else if(!act){
            $('#cartag'+val).remove();
        }
    },
    //已选标签模板
    _tagtpl:function(val,txt){
        return '<span class="tag cartags" id="cartag'+val+'" data-id="'+val+'"><span>'+txt+'&nbsp;&nbsp;</span><a href="javascript:;" title="删除" onclick="carsel.deltag(this);">x</a></span>';
    },

    //输出子系内容
    _putoutsubs:function(data){
        var len = data.c.length,i= 0,tpl='<a href="javascript:;" class="btn mini" data-id="'+this.lv1id+'" onclick="carsel.selallsub(this);">全选</a>&nbsp;&nbsp;';
        for(;i<len;i++){
            //是否还有子系
            if(data['c'][i]['a'] && data['c'][i]['a'].length>0){
                carsel.lv2id = data['c'][i]['i'];
                tpl += '<div class="row-fluid"><p class="b" style="width:100px;float:left;margin-right: 15px;">'
                    +'<label class="label label-info">'
                    +'<input type="checkbox" value="'+carsel.lv1id+'-'+carsel.lv2id+'" onchange="carsel.selallsubsub(this);"/> '+data['c'][i]['n']
                    +'：</label>'
                    +'</p><p class="span10 ml0">'+this._putoutsubsubs(data['c'][i]['a'])+'</p></div>';
            }else{
                tpl += this._subtpl(data['c'][i]);
            }

        }
        $(this.subbox).html(tpl);
    },
    //输出子系中的子系内容
    _putoutsubsubs:function(data){
        var len = data.length,i= 0,tpl='';
        for(;i<len;i++){
            tpl += this._subsubtpl(data[i]);
        }
        return tpl;
    },
    //子系模板
    _subtpl:function(da){
        return '<label class="label"><input type="checkbox" value="'+this.lv1id+'-'+da.i+'" onchange="carsel.changesel(this);"/>'+da.n+' </label>&nbsp;&nbsp;';
    },
    //子系中的子系模板
    _subsubtpl:function(da){
        return '<label class="label"><input type="checkbox" value="'+this.lv1id+'-'+this.lv2id+'-'+da.i+'" onchange="carsel.changesel(this);"/>'+da.s+' </label>&nbsp;&nbsp;';
    },

    //获取车系数据
    _getCarJSON: function () {
        $.getJSON(this.carjsurl,function(json){
            //console.log(json);
            carsel.carsjosn = json;
        });
    }
};

//表单验证
var valid = {
    'subbtn':'#subbtn',
    'form':'#postform',
    'carselbox':'#cartagsbox',
    init: function () {
        this._bindEvent();
    },
    _bindEvent:function(){
        $(this.subbtn).on('click',function(){
            valid._checkPost();
        });
    },
    _checkPost:function(){
        var name = $('input[name="name"]'),
            desn = $('input[name="desn"]'),
            sid = $('input[name="sid"]'),
            p = $('input[name="price"]'),
            op = $('input[name="oprice"]'),
            gcate1 = $('select[name="gcatelv1id"]'),
            logo = $('#logoinput'),
            imgs = $('#imgsinput'),
            server = $('textarea[name="server"]'),
            about = $('textarea[name="about"]'),
            istj = $('input[name="istejia"]:checked').val(),
            tprice = $('input[name="tprice"]'),
            tbtime = $('input[name="tbtime"]'),
            tetime = $('input[name="tetime"]');

        if(!checkInputEmpty(sid)){
            formValid.showErr(sid,'请填写所属店铺');
            return false;
        }else{
            formValid.showSuccess(sid);
        }

        if(!checkInputEmpty(name)){
            formValid.showErr(name,'请填写商品名称');
            return false;
        }else{
            formValid.showSuccess(name);
        }
        if(!checkInputEmpty(desn) || desn.val().length>150){
            formValid.showErr(desn,'请填写商品副标题/确保字数在150字以内');
            return false;
        }else{
            formValid.showSuccess(desn);
        }

        if(!checkInputEmpty(p)){
            formValid.showErr(p,'请填写商品现价');
            return false;
        }else{
            formValid.showSuccess(p);
        }
        if(!checkInputEmpty(op)){
            formValid.showErr(op,'请填写商品原价');
            return false;
        }else{
            formValid.showSuccess(op);
        }

        if(!checkSelectEmpty(gcate1)){
            formValid.showErr(gcate1,'请选择商品分类');
            return false;
        }else{
            formValid.showSuccess(gcate1);
        }

        if(!this._checkCars()){
            formValid.showErr($(this.carselbox),'请选择商品所适车系');
            return false;
        }else{
            formValid.showSuccess($(this.carselbox));
        }
        if(!checkInputEmpty(logo)){
            formValid.showErr($('#logomesg'),'请上传商品logo');
            return false;
        }else{
            formValid.showSuccess($('#logomesg'));
        }
        if(!checkInputEmpty(imgs)){
            formValid.showErr($('#imgsmesg'),'请上传商品展示图片');
            return false;
        }else{
            formValid.showSuccess($('#imgsmesg'));
        }
        if(server.val()){
            if(server.val().length>400){
                formValid.showErr(server,'服务保障内容最多400字');
                return false;
            }else{
                formValid.showSuccess(server);
            }
        }
        if(about.val()){
            if(server.val().length>5000){
                formValid.showErr(about,'说明内容最多5000字');
                return false;
            }else{
                formValid.showSuccess(about);
            }
        }

        //特价检查
        if(istj==1){

            if(!checkInputEmpty(tprice)){
                formValid.showErr($('#tperr'),'请填写商品特价价格');
                return false;
            }else{
                formValid.showSuccess($('#tperr'));
            }

            if(!checkInputEmpty(tbtime)){
                formValid.showErr($('#ttimeerr'),'请填写商品特价时间');
                return false;
            }else{
                formValid.showSuccess($('#ttimeerr'));
            }
            if(!checkInputEmpty(tetime)){
                formValid.showErr($('#ttimeerr'),'请填写商品特价时间');
                return false;
            }else{
                formValid.showSuccess($('#ttimeerr'));
            }

        }

        $(this.form).submit();

    },
    _checkCars:function(){
        var tags = $('#cartagsbox .cartags'),data = [];
        if(tags.length<=0){
            return false;
        }
        tags.each(function(i){
            data.push($(this).attr('data-id'));
        });
        $('input[name="selcars"]').val(data.join('|'));
        return true;
    }
};