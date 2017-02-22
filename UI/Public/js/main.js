/**
 * 界面
*/
var loadingprequeue = 0;
var slideNum = $('.swiper-slide').length;    

//nav
for (var i = 0; i < slideNum; i++) {
    $('.swiper-nav-vertical').append('<span></span>');
};
var navHeight = $('.swiper-nav-vertical').height();
$('.swiper-nav-vertical').css('marginTop', - navHeight / 2 + 'px').show();
$('.swiper-nav-vertical span').eq(0).addClass('active');
$('.swiper-slide').addClass('swiper-no-swiping'); 

function startFun() {
	//滑动
	var mySwiper = $('.swiper-container').swiper({
	    speed: 800,
	    mode: 'vertical',
	    autoplayStopOnLast: true,
	    autoplay: 5000,
	    noSwiping: true,
	    autoplayDisableOnInteraction: false,
	    onSlideChangeEnd: function() {
	        var index = mySwiper.activeIndex;
	        $('.swiper-nav-vertical span').removeClass('active');
	        $('.swiper-nav-vertical span').eq(index).addClass('active');
	        if(index == slideNum-1) {
				setTimeout(function() {
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
						});
//            			ikLogin();
            		});
				}, 3000); 
	        }
	    }
	});
	
	//倒数
	var cuntdownNow = 20;
	function cuntdown() {
	    cuntdownNow--;
	    $('.cuntdown span').text(cuntdownNow);
	    if(cuntdownNow == 0) { clearInterval(cuntdownTime);
		}
	}  
	var cuntdownTime = setInterval(cuntdown, 1000); 
}


/**
 * 页面载入
*/

//加载完成
function preloadend()
{   
	$('.swiperSlide').show();
	$('.load-num, .load-line').transit({'opacity': 0});
    $('.load-logo-1').transit({'y': '30%'},800);
    $('.load-logo-2').transit({'y': '-100%'},800);
    $('.loader').delay(1000).transit({'opacity':0},800, function() {
    	$('.loader').hide();
    	startFun();
    });
    // $('.loader').transit({'opacity':0},800);
}

//图片加载
function loadPic(){
    for(var i=0;i<slideNum;i++)
    {          
        var img = new Image();
        var backImg = $('.swiper-slide').eq(i).css('backgroundImage');
        img.src = backImg.substring(4, backImg.length - 1);
        console.log(img.src);
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
        // $('.onload-page .load-r').css({'height': 150 * i / 100 + 'px'});
        $('.load-num span').text(i);
        $('.load-line-bg').css({'width': i + '%'});
        i++; 
        // console.log(i);
    }
    if(i == 100){
        preloadend();
        clearInterval(loadingTimer);
    }
}

//加载开始
var loadingTimer = setInterval(loadingFun, 10); 
loadPic();