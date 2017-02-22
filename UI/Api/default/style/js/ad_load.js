/**
 * Created by jasonwoo on 15/11/5.
 */
var wrap = document.getElementById('wrap');
var container = document.getElementById('container');
var sec = document.getElementById('sec');
var loadingprequeue = 0;
var slideNum = $('.swiper-slide').length;

var slip = Slip(wrap, 'x').slider().webapp();

//轮播
var now_page = -1;
function NextPlay(){
        slip.end(function() {
            for (x=0;x<(slideNum);x++){
                if (x==(this.page)){
                    $('.nav-point span').eq(x).addClass('cur');
                }else{
                    $('.nav-point span').eq(x).removeClass();
                }
            }
            now_page = this.page;
        });
        now_page++;
        slip.jump(now_page);
        setTimeout(NextPlay,5000);
}

//计时器
function count_down(){
    var i = 20;
    function addsec(){
        if(i>0){
            $('#sec').text('正在为您连接.. '+i+' 秒');
            setTimeout(addsec,1000);
            i--;
        }else{
            //$('#sec').text('开始上网').click(login);
            login();
        }
    }
    addsec();
}

//login
function login(){
    ik.core.isReady(function(){
        var ip		=	ikClientIp();
        var apmac	=	ikRouterGwid();
        var mac		=	ikClientMac();
        $.ajax({
            url: '/index.php/api/UserAuth/smslogin',
            type: "post",
            data:{
                'mac':mac,
                'act':'noauth',
                'user':'11111111111',
                'smscode':'p',
                'routeid':apmac,
                'login_ip':ip,
                '__hash__':$('input[name="__hash__"]').val()
            },
            dataType:'jsonp',
            success:function(){
                ikLogin();
            },
            error: function() {

            }
        })
    })
}
//自适应
$(window).resize(function(){
    for (i=0;i<$("#wrap p").length;i++){
        var Str = '#wrap p:eq(' + i +')';
        var Style = $(Str).attr('style');
        var pat = /width.*px;/;
        var m_str = Style.replace(pat,'width: '+$(window).width() + 'px;' );
        console.log(m_str);
        $('#wrap p').eq(i).attr('style',m_str);
    };
    slip.height($(window).height());
    slip.width($(window).width());
});

//导航点
function addPoint(){
    var nav = $("<div class='nav-point'></div>");
    $('#container').append(nav);
    for (i=0;i<slideNum;i++){
        nav.append('<span></span>');
    }
}

//加载完成
function preloadend(){
    $('#warp').show();
    $('.load-num, .load-line').transit({'opacity': 0});
    $('.load-logo-1').transit({'y': '30%'},800);
    $('.load-logo-2').transit({'y': '-100%'},800);
    $('.loader').delay(1000).transit({'opacity':0},800, function() {
        addPoint();
        $('.loader').hide();
        count_down();
        NextPlay();
    });
}

//图片加载
function loadPic(){
    for(var i=0;i<slideNum;i++)
    {
        var img = new Image();
        var backImg = $('.swiper-slide').eq(i).css('backgroundImage');
        img.src = backImg.substring(4, backImg.length - 1);
        if(img.complete){
            loadingprequeue++;
        }
        else{
            img.onload = function () {
                loadingprequeue++;
            }
        }
    }
}

//判断加载进程
var i = 0;
var loadingFun = function() {
    if(slideNum == 0) return;
    // console.log(imgNum + ',' + loadingprequeue + ',' + i);

    if(i < loadingprequeue/slideNum*100) {
        $('.load-num span').text(i);
        $('.load-line-bg').css({'width': i + '%'});
        i++;
    }
    if(i == 100){
        preloadend();
        clearInterval(loadingTimer);
    }
}

//加载开始
var loadingTimer = setInterval(loadingFun, 10);
loadPic();