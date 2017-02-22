/**
 * Created by jasonwoo on 15/10/27.
 */
$(document).ready(function (){
    var map = new BMap.Map('map', {minZoom: 10, maxZoom: 19}); //初始化数据
    map.enableScrollWheelZoom();                            // 启用滚轮放大缩小 map.enableContinuousZoom();
    map.centerAndZoom("泉州", 12); // 初始化地图,设置中心点坐标和地图级别
    map.addControl(new BMap.NavigationControl({anchor: BMAP_ANCHOR_TOP_RIGHT,type:BMAP_NAVIGATION_CONTROL_ZOOM}));  //添加默认缩放平移控件

    if (document.createElement('canvas').getContext) {  // 判断当前浏览器是否支持绘制海量点
        var points = [];  // 添加海量点数据
        var points_info = data.data;
        for (var i = 0; i < data.data.length; i++) {
            points.push(new BMap.Point(data.data[i].lng, data.data[i].lat));
            //points_info.push(data.data[i].lng,data.data[i].lat,data.data[i].title,data.data[i].address);
        }
        var options = {
            size: BMAP_POINT_SIZE_SMALL,
            shape: BMAP_POINT_SHAPE_WATERDROP,
            color: 'red'
        }

        var pointCollection = new BMap.PointCollection(points, options);  // 初始化PointCollection
        pointCollection.addEventListener('click', function (e) {
            for (var i = 0;i<points_info.length;i++) {
                if (points_info[i].lng == e.point.lng && points_info[i].lat == e.point.lat){
                    var infoWindow = new BMap.InfoWindow();
                    infoWindow.setWidth(230);
                    infoWindow.setTitle("<div class='info_title'>" + points_info[i].shopName + "</div>");
                    infoWindow.setContent("<div class='info_content'>" + "<div class='info_logo'></div>" +
                        "<p class='info_address clearFix'><label>地址:</label><span>" + points_info[i].address + "</span></p>" +
                        "</div>");
                    var point = new BMap.Point(e.point.lng, e.point.lat);
                    map.openInfoWindow(infoWindow,point);
                    return;
                }
            }
        });
        map.addOverlay(pointCollection);  // 添加Overlay
    } else {
        alert('请在chrome、safari、IE8+以上浏览器查看本示例');
    }

    function search() {
        var local = new BMap.LocalSearch("泉州", {
            renderOptions: {map: map, panel: "s-result"},
            pageCapacity: 5
        });
        local.searchInBounds($("#input-search").val(), map.getBounds());
    }

    $("#search").click(function(){search();});

    $("#input-search").bind('keypress',function(event){
        if(event.keyCode == "13")
        {search();}
    });
});
