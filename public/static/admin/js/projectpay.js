/**
 * Created by chenxh on 2018/6/24.
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

        $(document).on('click','.del_main_step',function(){
            var _thi = $(this);
            var _id = _thi.closest('li').attr('data-id');
            //console.log(_id);
            if(!_id || _id<=0){
                _thi.closest('li').remove();

                return false;
            }

            _this.del_step(_id,function(d){
                _thi.closest('li').remove();
                closelayer();
            });
            //
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
            +'<span class="add-on">阶段</span><input type="text" class="stepv step_m_name" value=""/>'
            +'<span class="add-on">应付金额</span><input type="text"  class="stepv step_m_money" value=""/>'
            +'<span class="add-on">应付时间</span><input type="date" min="'+mindate+'" class="stepv step_m_time" value=""/>'
            +'<span class="add-on"><i class="icon-circle-arrow-up move_up"></i> </span>'
            +'<span class="add-on"><i class="icon-circle-arrow-down move_down"></i> </span>'

            +'</p></li>';

        return tpl;
    },


};

steps.init({
    'ul_select':'.project_steps'
});
var step_values = {};
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
                    'payable':_thisli.find('input.step_m_money').val(),
                    'time':_thisli.find('input.step_m_time').val()
                };

            });

        });

        console.log(step_values);
        //return false;
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