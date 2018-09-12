/**
 * Created by xiaohui on 2015/7/24.
 */
// 百度地图API功能
// 创建Map实例
var map = new BMap.Map("baidumap");
// 初始化地图,设置中心点坐标和地图级别。
if(mx && my){
    var point = new BMap.Point(mx, my);
    map.centerAndZoom(point, 15);
    //设置一个标记
    var marker = new BMap.Marker(point);
    map.addOverlay(marker);
}else{
    map.centerAndZoom("深圳",12);
}

//启用滚轮放大缩小
map.enableScrollWheelZoom();
//缩放平移控件
map.addControl(new BMap.NavigationControl());

//点击获取坐标
map.addEventListener("click", function(e){
    document.getElementById("localinput").value = e.point.lng + "," + e.point.lat;
});

//智能提示搜索---------------
var ac = new BMap.Autocomplete(    //建立一个自动完成的对象
    { "input" : "localinput","location" : map} );

ac.addEventListener("onhighlight", function(e) {  //鼠标放在下拉列表上的事件
    var str = "";
    var _value = e.fromitem.value;
    var value = "";
    if (e.fromitem.index > -1) {
        value = _value.province +  _value.city +  _value.district +  _value.street +  _value.business;
    }
    str = "FromItem<br />index = " + e.fromitem.index + "<br />value = " + value;

    value = "";
    if (e.toitem.index > -1) {
        _value = e.toitem.value;
        value = _value.province +  _value.city +  _value.district +  _value.street +  _value.business;
    }
    str += "<br />ToItem<br />index = " + e.toitem.index + "<br />value = " + value;
    document.getElementById("searchResultPanel").innerHTML = str;
});

var myValue;
ac.addEventListener("onconfirm", function(e) {    //鼠标点击下拉列表后的事件
    var _value = e.item.value;
    myValue = _value.province +  _value.city +  _value.district +  _value.street +  _value.business;
    document.getElementById("searchResultPanel").innerHTML ="onconfirm<br />index = " + e.item.index + "<br />myValue = " + myValue;

    setPlace();
});

function setPlace(){
    map.clearOverlays();    //清除地图上所有覆盖物
    function myFun(){
        var pp = local.getResults().getPoi(0).point;    //获取第一个智能搜索的结果
        map.centerAndZoom(pp, 18);
        map.addOverlay(new BMap.Marker(pp));    //添加标注
    }
    var local = new BMap.LocalSearch(map, { //智能搜索
        onSearchComplete: myFun
    });
    local.search(myValue);
}
setTimeout(function(){
    if(mx && my){
        document.getElementById("localinput").value = mx + "," + my;
    }
},1000);


