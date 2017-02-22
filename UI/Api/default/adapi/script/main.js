/**
 * Created by Administrator on 2016/1/13.
 */
//加载更多
//点击div时，跳转链接
$('.Index_Content').bind('click',function(){
    //获取当前点击元素的子元素
    var aid = $(this).find('.aid').val();
    var apid = $(this).find('.apid').val();
    ajax_hit(aid,apid);
    location.href = $(this).attr('data-href');
});

$('.ad_card').bind('click',function(){
    var aid = $(this).find('.aid').val();
    var apid = $(this).find('.apid').val();
    ajax_hit(aid,apid);
    location.href = $(this).attr('data-href');
});

$('.loadmore').click(function () {
    var limit = $('#limit').val();
    $.ajax({
        type: "post",
        url: "http://112.5.16.66:8098/index.php/api/ad/getList",
        dataType: "json",
        data:{"limit":limit},//参数
        success: function(data){
            if(data.status==0){
                $('.Index_Content').remove();
                $('#limit').remove();
                for(var i=0;i<data.lists.length;i++){
                    var url = data.lists[i].url;
                    var div = $('<div></div>').addClass('Index_Content').attr('data-href',url).bind('click',function(){
                        location.href = $(this).attr('data-href');
                    });
                    $(div).append(
                        "<img class='Content_Icon' src="+data.lists[i]['ad_thumb']+" />"+
                        "<div class='Index_Content_details'>"+
                        "<div class='Index_Content_AppName'>"+data.lists[i]['title']+"</div>"+
                        "<div class='list_app_info'>"+
                        "<div>"+
                        data.lists[i]['info']+
                        "</div>"+
                        "</div>"+
                        "</div>"
                    );
                    $('#lists').append(div);
                }
                $('#lists').append('<input type="hidden" id="limit" value="'+data.limit+'" />');
            }else{
                $('#lists').append("没有更多数据了");
                return false;
            }
        }
    });
});

var ajax_hit = function(aid,apid){
    $.ajax({
        type: "post",
        url: "http://112.5.16.66:8098/index.php/api/ad/adhit",
        //url: "http://localhost/index.php/api/ad/adhit",
        dataType: "json",
        data:{'aid':aid,'apid':apid},//参数
        success: function(){
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            alert(XMLHttpRequest.status);
            alert(XMLHttpRequest.readyState);
            alert(textStatus);
        },
    });
}
