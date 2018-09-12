/*
settings 参数说明
-----
url:数据josn文件路径
prov:默认一级
city:默认二级
dist:默认三级
nodata:无数据状态
required:必选项
------------------------------ */
(function($){
	$.fn.cateSelect=function(settings){
		if(this.length<1){return;};

		// 默认值
		settings=$.extend({
			base:'/static/plugin/cate/',
			url:"js/city.min.js",
			prov:null,
			city:null,
			dist:null,
			lv4:null,
			nodata:null,
			required:false
		},settings);

		var box_obj=this,
		prov_obj=box_obj.find(".lv1select"),
		city_obj=box_obj.find(".lv2select"),
		dist_obj=box_obj.find(".lv3select"),
		lv4_obj=box_obj.find(".lv4select");

		var prov_val=prov_obj.attr('data-val'),
		city_val=city_obj.attr('data-val'),
		dist_val=dist_obj.attr('data-val'),
		lv4_val=lv4_obj.attr('data-val');
		var select_prehtml=(settings.required) ? "" : "<option value=''>请选择</option>";
		var city_json;

		// 赋值市级函数
		var cityStart=function(){
			var prov_id=prov_obj.get(0).selectedIndex;
			if(!settings.required){
				prov_id--;
			};
			city_obj.empty().attr("disabled",true);
			dist_obj.empty().attr("disabled",true);
			lv4_obj.empty().attr("disabled",true);

			if(prov_id<0||typeof(city_json.catelist[prov_id].c)=="undefined"){
				if(settings.nodata=="none"){
					city_obj.css("display","none");
					dist_obj.css("display","none");
					lv4_obj.css("display","none");
				}else if(settings.nodata=="hidden"){
					city_obj.css("visibility","hidden");
					dist_obj.css("visibility","hidden");
					lv4_obj.css("visibility","hidden");
				};
				return;
			};
			
			// 遍历赋值市级下拉列表
			temp_html=select_prehtml;
			$.each(city_json.catelist[prov_id].c,function(idx,city){
				temp_html+="<option value='"+city.i+"'>"+city.n+"</option>";
			});
			city_obj.html(temp_html).attr("disabled",false).css({"display":"","visibility":""});
			distStart();
		};

		// 赋值地区（县）函数
		var distStart=function(){
			var prov_id=prov_obj.get(0).selectedIndex;
			var city_id=city_obj.get(0).selectedIndex;
			if(!settings.required){
				prov_id--;
				city_id--;
			};
			dist_obj.empty().attr("disabled",true);
			lv4_obj.empty().attr("disabled",true);

			if(prov_id<0||city_id<0||typeof(city_json.catelist[prov_id].c[city_id].a)=="undefined"){
				if(settings.nodata=="none"){
					dist_obj.css("display","none");
					lv4_obj.css("display","none");
				}else if(settings.nodata=="hidden"){
					dist_obj.css("visibility","hidden");
					lv4_obj.css("visibility","hidden");
				};
				return;
			};
			
			// 遍历赋值市级下拉列表
			temp_html=select_prehtml;
			$.each(city_json.catelist[prov_id].c[city_id].a,function(idx,dist){
				temp_html+="<option value='"+dist.i+"'>"+dist.s+"</option>";
			});
			dist_obj.html(temp_html).attr("disabled",false).css({"display":"","visibility":""});
			lv4Start();
		};

		var lv4Start = function(){
			var prov_id=prov_obj.get(0).selectedIndex,
				city_id=city_obj.get(0).selectedIndex,
				dist_id=dist_obj.get(0).selectedIndex;
			if(!settings.required){
				prov_id--;
				city_id--;
				dist_id--;
			};
			lv4_obj.empty().attr("disabled",true);
			if(prov_id<0||city_id<0 || dist_id<0 ||typeof(city_json.catelist[prov_id].c[city_id].a[dist_id].a)=="undefined"){
				if(settings.nodata=="none"){
					lv4_obj.css("display","none");
				}else if(settings.nodata=="hidden"){
					lv4_obj.css("visibility","hidden");
				};
				return;
			};
			// 遍历赋值市级下拉列表
			temp_html=select_prehtml;
			$.each(city_json.catelist[prov_id].c[city_id].a[dist_id].a,function(idx,lv4){
				temp_html+="<option value='"+lv4.i+"'>"+lv4.s+"</option>";
			});
			lv4_obj.html(temp_html).attr("disabled",false).css({"display":"","visibility":""});
		};

		var init=function(){

			// 遍历赋值省份下拉列表
			temp_html=select_prehtml;
			$.each(city_json.catelist,function(idx,prov){
				temp_html+="<option value='"+prov.i+"'>"+prov.p+"</option>";
			});
			prov_obj.html(temp_html);

			// 若有传入省份与市级的值，则选中。（setTimeout为兼容IE6而设置）
			setTimeout(function(){
				if(prov_val!=null){
					prov_obj.val(prov_val);
					cityStart();
					setTimeout(function(){
						if(city_val!=null){
							city_obj.val(city_val);
							distStart();
							setTimeout(function(){
								if(dist_val!=null){
									dist_obj.val(dist_val);
									lv4Start();
									setTimeout(function(){
										if(lv4_val!=null){
											lv4_obj.val(lv4_val);
										}
									},1);
								};
							},1);
						};
					},1);
				};
			},1);

			// 选择省份时发生事件
			prov_obj.bind("change",function(){
				cityStart();
			});

			// 选择市级时发生事件
			city_obj.bind("change",function(){
				distStart();
			});
			// 选择区时发生事件
			dist_obj.bind("change",function(){
				lv4Start();
			});
		};

		// 设置省市json数据
		if(typeof(settings.url)=="string"){
			$.getJSON(settings.base+settings.url,function(json){
				city_json=json;
				init();
			});
		}else{
			city_json=settings.url;
			init();
		};

	};
})(jQuery);