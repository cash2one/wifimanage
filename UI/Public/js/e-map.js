// 路径配置
require.config({
    paths: {
        echarts: 'http://112.5.16.66:8098/UI//Public/js/dist'
    }
});
//添加数据
var e = [];
$.ajax({
    url: "http://any.all-wifi.cn/index.php?m=Home&c=api&a=areaHot1",
    dataType: 'json',
    success: function(data){
        for (i=0;i<data.length;i++) {
            e.push({name:data[i].name,value:parseInt(data[i].value)})
        }

        require(
            [
                'echarts',
                'echarts/chart/map',
                'echarts/theme/macarons'
            ],
            function(ec) {
                var myChart = ec.init(document.getElementById('main'), 'macarons');
                require('echarts/util/mapData/params').params.QZ = {
                    getGeoJson: function(callback) {
                        $.getJSON('http://112.5.16.66:8098/UI//Public/js/350500.js', callback);
                    }
                }
                option = {
                    backgroundColor: '#fff',
                    title: {
                        text: 'All-Wifi 各区在线人数',
                        x: 'center'
                    },
                    tooltip: {
                        trigger: 'item',
                        formatter: '{b}<br/>{c}人 (在线)'
                    },
                    //toolbox: {
                    //    show: true,
                    //    orient: 'vertical',
                    //    x: 'right',
                    //    y: 'center',
                    //    feature: {
                    //        mark: {
                    //            show: true
                    //        },
                    //        dataView: {
                    //            show: true,
                    //            readOnly: false
                    //        },
                    //        restore: {
                    //            show: true
                    //        },
                    //        saveAsImage: {
                    //            show: true
                    //        }
                    //    }
                    //},
                    dataRange: {
                        min: 0,
                        max: 2000000,
                        text: ['高', '低'],
                        realtime: false,
                        calculable: true,
                        color: ['orangered', 'yellow', 'lightskyblue']
                    },
                    series: [{
                        name: 'All-Wifi 各区在线人数',
                        type: 'map',
                        mapType: 'QZ', // 自定义扩展图表类型
                        mapLocation:{
                            x : 'center',
                            y : 'bottom'
                            // height : 600
                        },
                        itemStyle: {
                            normal: {
                                label: {
                                    show: true,
                                    textStyle: {
                                        "color": "#333"
                                    }
                                },
                                borderColor: '#efefef'
                            },
                            emphasis: {
                                label: {
                                    show: true
                                }
                            }
                        },
                        data: [{
                            name: '鲤城',
                            value: '-'
                        }, {
                            name: '丰泽',
                            value: '-'
                        }, {
                            name: '惠安县',
                            value: '-'
                        }, {
                            name: '泉港区',
                            value: '-'
                        }, {
                            name: '洛江',
                            value: '-'
                        }, {
                            name: '德化县',
                            value: '-'
                        }, {
                            name: '永春县',
                            value: '-'
                        }, {
                            name: '安溪县',
                            value: '-'
                        }, {
                            name: '南安市',
                            value: '-'
                        }, {
                            name: '晋江市',
                            value: '-'
                        }, {
                            name: '石狮市',
                            value: '-'
                        }],
                        // 文本位置修正
                        textFixed: {
                            '晋江市': [-10, 0],
                            '丰泽': [0, -7],
                            '洛江': [-2,0],
                            '惠安县': [-5,0]
                        },
                        // 文本直接经纬度定位
                        geoCoord: {
                            'Islands': [113.95, 22.26]
                        }
                    }]
                };
                option.series[0].data = e;
                myChart.setOption(option);
                window.onresize = myChart.resize;

            });
    }
});