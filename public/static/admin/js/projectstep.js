/**
 * Created by chenxh on 2018/6/19.
 */
var steps = {
    'options':{
        'ul_select':''
    },

    init:function(options){
        var _this = this;
        _this.options = $.extend(this.options,options);

        $('.add_main_step').on('click',function(){
            var _ol = $(this).prev(_this.options.ul_select);
            _this.add_main_step(_ol);
        });

        $(document).on('click','.add_sub_step',function(){
            //console.log('sdds');
            $(this).closest('li').find('.sub_step_ul').append(_this.sub_step_tpl());
        });

        $(document).on('click','.del_main_step',function(){
            var _thi = $(this);
            layerconfirm('其中的子阶段信息也会删除，确定要删除吗？', 2, '确认', function () {
                var _id = _thi.closest('li').attr('data-id');
                //console.log(_id);
                if(!_id || _id<=0){
                    _thi.closest('li').remove();
                    closelayer();
                    return false;
                }

                _this.del_step(_id,function(d){
                    _thi.closest('li').remove();
                    closelayer();
                });

            }, function () {

            });
            //
        });
        $(document).on('click','.del_sub_step',function(){
            var _thi = $(this);
            var _id = _thi.closest('li').attr('data-id');
            console.log(_id);
            if(!_id || _id<=0){
                _thi.closest('li').remove();
                return false;
            }
            //_this.closest('li').remove();
            _this.del_step(_id,function(d){
                _thi.closest('li').remove();
            });
        });
        $(document).on('click','.move_up',function(){
            var li = $(this).closest('li'),
                ul = li.closest('ol');
            var _index = li.index();
            if(_index<=0){
                return false;
            }
            var clone_li = li.clone(true);
            //console.log(_index);
            ul.children('li').eq(_index-1).before(clone_li);
            li.remove();
        });
        $(document).on('click','.move_down',function(){
            var li = $(this).closest('li'),
                ul = li.closest('ol');
            var _index = li.index();
            if(_index>=ul.children('li').length-1){
                return false;
            }
            var clone_li = li.clone(true);
            //console.log(_index);
            ul.children('li').eq(_index+1).after(clone_li);
            li.remove();
        });
    },

    add_li:function(){
        var tpl = this.main_step_tpl();
        $(this.options.ul_select).children('li').eq(1).before(tpl);
    },
    del_step:function(id,after_act){
        $.post(del_url,{'id':id},function(d){
            if(d.err==0){
                after_act && after_act(d);
            }else{
                layeralert(d.mesg,4,'提示');
            }
        },'json');
    },
    add_main_step:function(_ol){
        var tpl = this.main_step_tpl();
        _ol.append(tpl);
        //this.init_dragsort();
    },

    main_step_tpl:function(){

        var tpl = '<li class="mb10" data-id="">'
            +'<p class="input-append">'
            +'<a href="javascript:;" class="add-on text-error del_main_step"><i class="icon-trash"></i></a>'
            +'<span class="add-on">阶段名称</span><input type="text" class="stepv step_m_name" value=""/>'
            +'<span class="add-on">计划时间</span><input type="date" min="'+mindate+'" class="stepv step_m_time1" value="'+mindate+'"/>'
            +'<span class="add-on">-</span><input type="date" min="'+mindate+'" class="stepv step_m_time2" value="'+mindate+'"/>'
            +'<span class="add-on"><i class="icon-circle-arrow-up move_up"></i> </span>'
            +'<span class="add-on"><i class="icon-circle-arrow-down move_down"></i> </span>'

            +'</p><ol class="mt10 sub_step_ul">'
            //+'<li class="mb10"></li>'
            +'</ol><p><a href="javascript:;" class="btn btn-primary mini add_sub_step" style="margin-left: 20%;"><i class="icon-plus-sign"></i>子阶段</a></p>' +
            '</li>';

        return tpl;
    },
    sub_step_tpl:function(){
        var tpl = '<li class="mb10" data-id="">'
            +'<p class="input-append">'
            +'<a href="javascript:;" class="add-on text-error del_sub_step"><i class="icon-trash"></i></a>'
            +'<span class="add-on">阶段名称</span><input type="text"  class="stepv step_s_name"/>'
            +'<span class="add-on">计划时间</span><input type="date" min="'+mindate+'" class="stepv step_s_time1" value="'+mindate+'"/>'
            +'<span class="add-on">-</span><input type="date" min="'+mindate+'" class="stepv step_s_time2" value="'+mindate+'"/>'
            +'<span class="add-on"><i class="icon-circle-arrow-up move_up"></i> </span>'
            +'<span class="add-on"><i class="icon-circle-arrow-down move_down"></i> </span>'

            +'</p></li>';
        return tpl;
    }

};

var step_values = {};

var step_tpls={
    "tpl1":{
        "sheji":[
            {
                "id":0,
                "name":"平面设计",
                "subs":[

                ]
            },
            {
                "id":0,
                "name":"效果图设计",
                "subs":[

                ]
            },
            {
                "id":0,
                "name":"施工图设计",
                "subs":[

                ]
            }
        ],
        "shigong":[
            {
                "id":0,
                "name":"基础项目",
                "subs":[
                    {"id":0,"name":"现场形象保护，标语等制作","90_new":1,"90_old":2,"90_150_new":2,"90_150_old":2,"150_200_new":2,"150_200_old":3},
                    {"id":0,"name":"拆墙体，砌墙，批荡，建楼面","90_new":7,"90_old":16,"90_150_new":10,"90_150_old":27,"150_200_new":17,"150_200_old":27},
                    {"id":0,"name":"铲白灰","90_new":1,"90_old":2,"90_150_new":2,"90_150_old":3,"150_200_new":2,"150_200_old":4},
                    {"id":0,"name":"水、电主体工程","90_new":7,"90_old":12,"90_150_new":13,"90_150_old":17,"150_200_new":18,"150_200_old":21},
                    {"id":0,"name":"防水工程、陶粒回填","90_new":4,"90_old":4,"90_150_new":5,"90_150_old":5,"150_200_new":6,"150_200_old":6}
                ]
            },
            {
                "id":0,
                "name":"装饰项目",
                "subs":[
                    {"id":0,"name":"瓷片铺贴","90_new":10,"90_old":11,"90_150_new":12,"90_150_old":12,"150_200_new":13,"150_200_old":14},
                    {"id":0,"name":"地砖铺贴","90_new":5,"90_old":6,"90_150_new":6,"90_150_old":6,"150_200_new":7,"150_200_old":7},
                    {"id":0,"name":"天花吊顶制作","90_new":5,"90_old":7,"90_150_new":8,"90_150_old":8,"150_200_new":13,"150_200_old":20},
                    {"id":0,"name":"窗套、门套制作","90_new":7,"90_old":8,"90_150_new":8,"90_150_old":8,"150_200_new":7,"150_200_old":7},
                    {"id":0,"name":"傢俬制作","90_new":5,"90_old":6,"90_150_new":8,"90_150_old":11,"150_200_new":13,"150_200_old":15},
                    {"id":0,"name":"铝扣板天花","90_new":1,"90_old":1,"90_150_new":2,"90_150_old":2,"150_200_new":2,"150_200_old":1}
                ]
            },
            {
                "id":0,
                "name":"油漆项目",
                "subs":[
                    {"id":0,"name":"傢俬油漆","90_new":10,"90_old":13,"90_150_new":13,"90_150_old":14,"150_200_new":15,"150_200_old":17},
                    {"id":0,"name":"墙身、天花灰底","90_new":10,"90_old":13,"90_150_new":12,"90_150_old":13,"150_200_new":14,"150_200_old":15},
                    {"id":0,"name":"墙身、天花乳胶漆","90_new":5,"90_old":6,"90_150_new":7,"90_150_old":7,"150_200_new":7,"150_200_old":7},
                    {"id":0,"name":"墙纸","90_new":2,"90_old":2,"90_150_new":2,"90_150_old":2,"150_200_new":2,"150_200_old":3}
                ]
            },
            {
                "id":0,
                "name":"安装项目",
                "subs":[
                    {"id":0,"name":"木地板安装","90_new":2,"90_old":3,"90_150_new":3,"90_150_old":3,"150_200_new":2,"150_200_old":2},
                    {"id":0,"name":"灯具、插座安装","90_new":2,"90_old":2,"90_150_new":2,"90_150_old":3,"150_200_new":3,"150_200_old":3},
                    {"id":0,"name":"门锁、五金配件","90_new":2,"90_old":2,"90_150_new":2,"90_150_old":3,"150_200_new":3,"150_200_old":3},
                    {"id":0,"name":"洁具安装","90_new":3,"90_old":3,"90_150_new":2,"90_150_old":3,"150_200_new":2,"150_200_old":2}
                ]
            },
            {
                "id":0,
                "name":"其它",
                "subs":[
                    {"id":0,"name":"清洁卫生","90_new":1,"90_old":1,"90_150_new":1,"90_150_old":1,"150_200_new":2,"150_200_old":2}
                ]
            }
        ]
    },
    "fill_default":function(){
        var data = this.tpl1;
        $('#sheji').html(this.tpl_fill(data.sheji));
        $('#shigong').html(this.tpl_fill(data.shigong));
    },
    "tpl_fill":function(data){
        var len = data.length,tpl = '';
        for(var i=0;i<len;i++){
            tpl += '<li class="mb10" data-id=""><p class="input-append">'
                +'<a href="javascript:;" class="add-on text-error del_main_step"><i class="icon-trash"></i></a>'
                +'<span class="add-on">阶段名称</span><input type="text" class="stepv step_m_name" value="'+data[i]['name']+'">'
                +'<span class="add-on">计划时间</span><input type="date" class="stepv step_m_time1" value="">'
                +'<span class="add-on">-</span><input type="date" class="stepv step_m_time2" value="">'
                +'<span class="add-on"><i class="icon-circle-arrow-up move_up"></i> </span>'
                +'<span class="add-on"><i class="icon-circle-arrow-down move_down"></i> </span></p>'

                +'<ol class="mt10 sub_step_ul">';
            var sub_len = data[i]['subs'].length;

            if(sub_len>0){
                for(var si=0;si<sub_len;si++){
                    tpl += '<li class="mb10" data-id=""><p class="input-append">'
                        +'<a href="javascript:;" class="add-on text-error del_sub_step"><i class="icon-trash"></i></a>'
                        +'<span class="add-on">阶段名称</span><input type="text" class="stepv step_s_name" value="'+data[i]['subs'][si]['name']+'">'
                        +'<span class="add-on">计划时间</span><input type="date" class="stepv step_s_time1" value="">'
                        +'<span class="add-on">-</span><input type="date" class="stepv step_s_time2" value="">'
                        +'<span class="add-on"><i class="icon-circle-arrow-up move_up"></i> </span>'
                        +'<span class="add-on"><i class="icon-circle-arrow-down move_down"></i> </span></p></li>';
                }

            }

            tpl += '</ol><p><a href="javascript:;" class="btn btn-primary mini add_sub_step" style="margin-left: 20%;"><i class="icon-plus-sign"></i>子阶段</a></p></li>';
        }
        return tpl;
    }
};

if(stepact=='add'){
    step_tpls.fill_default();
}


steps.init({
    'ul_select':'.project_steps'
});

$(function(){
    $('#subbtn').on('click',function(){
        var check_res = true;
        $('.stepv ').each(function(i){
            var _this = $(this);
            if(!checkInputEmpty(_this)){
                layeralert('请完成信息',4,'提示');
                _this.focus();
                check_res = false;
                return false;
            }
        });

        if(!check_res){
            layeralert('请完成信息',4,'提示');
            return false;
        }

        $('.project_steps').each(function(i){

            var _thisol = $(this),
                type = _thisol.attr('data-type');
            //step_values.push();
            step_values[i] = {'type':type,'li':[]};

            _thisol.children('li').each(function(j){

                var _thisli = $(this);
                step_values[i]['li'][j] = {
                    'id':_thisli.attr('data-id'),
                    'name':_thisli.find('input.step_m_name').val(),
                    'time1':_thisli.find('input.step_m_time1').val(),
                    'time2':_thisli.find('input.step_m_time2').val(),
                    'subs':[]
                }

                var subs = _thisli.find('ol.sub_step_ul li');

                if(subs.length>0){

                    subs.each(function(k){
                        var sub = $(this);
                        step_values[i]['li'][j]['subs'][k] = {
                            'id':sub.attr('data-id'),
                            'name':sub.find('input.step_s_name').val(),
                            'time1':sub.find('input.step_s_time1').val(),
                            'time2':sub.find('input.step_s_time2').val()
                        };

                    });
                }

            });

        });

        console.log(step_values);

        if(isEmptyObj(step_values)){
            layeralert('请完成信息',4,'提示');
            return false;
        }
        $.post(location.href,{'data':step_values},function(d){
            console.log(d);
            if(d.err==0){
                layermsg('保存成功',1);
                setTimeout(function(){
                    history.back();
                },1500);

            }else{
                layeralert(d.mesg,4,'提示');
            }
        },'json');
    });
});