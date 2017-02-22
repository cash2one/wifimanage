<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
	<title><?php echo C('sitename');?>-管理后台</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" /></head>
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<!-- bootstrap -->
    <link href="<?php echo ($Theme['P']['root']); ?>/bootadmin/css/bootstrap/bootstrap.css" rel="stylesheet" />
       <link href="<?php echo ($Theme['P']['root']); ?>/bootadmin/css/bootstrap/bootstrap-responsive.css" rel="stylesheet" />
    <link href="<?php echo ($Theme['P']['root']); ?>/bootadmin/css/bootstrap/bootstrap-overrides.css" type="text/css" rel="stylesheet" />
    <!-- libraries -->
    <link href="<?php echo ($Theme['P']['root']); ?>/bootadmin/css/lib/font-awesome.css" type="text/css" rel="stylesheet" />

    <!-- global styles -->
    <link rel="stylesheet" type="text/css" href="<?php echo ($Theme['P']['root']); ?>/bootadmin/css/layout.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo ($Theme['P']['root']); ?>/bootadmin/css/elements.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo ($Theme['P']['root']); ?>/bootadmin/css/icons.css" />


    <!-- open sans font -->
      <link href='<?php echo ($Theme['P']['root']); ?>/font/italic.css' rel='stylesheet' type='text/css' />

    <!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->

   
	  <link rel="stylesheet" href="<?php echo ($Theme['P']['root']); ?>/bootadmin/css/compiled/index.css" type="text/css" media="screen" />    
	
<body>


    <!-- navbar -->
    <div class="navbar navbar-inverse">
        <div class="navbar-inner">
            <button type="button" class="btn btn-navbar visible-phone" id="menu-toggler">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            
            <a class="brand" href="<?php echo U('index');?>" style=" padding-top:20px;"><img src="<?php echo ($Theme['P']['img']); ?>/wifilogo-mini.png" /></a>
            

            <ul class="nav pull-right">                
               
                <li class=" hidden-phone">
                    	<a href="javascript:void(0);">登录帐号:(<?php echo (session('adminmame')); ?>)</a>
                </li>
                 <li class=" hidden-phone">
                    	<a href="<?php echo U('Index/pwd');?>">修改密码</a>
                </li>
                <li class="settings hidden-phone">
                    <a href="<?php echo U('login/loginout');?>" role="button">
                        <i class="icon-share-alt"></i>
                    </a>
                </li>
            </ul>            
        </div>
    </div>
    <!-- end navbar -->
    <div class="footerbar" style="width:100%; line-height:20px; font-size:10px; background-color:#F7F7F7; position:absolute; left:0; bottom:-40px;; z-index:999; text-align:center;">厦门思可达信息科技有限公司 提供技术支持</div>
   <!-- sidebar -->

<div id="sidebar-nav" style="padding-top:0;">
<ul id="dashboard-menu">
<?php if(is_array($nav)): $i = 0; $__LIST__ = $nav;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i; if($vo['pid'] == 1): if(in_array($vo['id'],$navids)): if($vo['single'] == 1): if((strtolower($nownav['m']) == strtolower($vo['m']) ) && strtolower($nownav['a']) == strtolower($vo['a'])): ?><li class="active">
    <div class="pointer">
      <div class="arrow"></div>
      <div class="arrow_border"></div>
    </div>
    <?php else: ?>
  <li><?php endif; ?>
<a href="<?php echo U(''.$vo['m'].'/'.$vo['a'].'');?>"  > <i class="<?php echo ($vo["ico"]); ?>"></i> <span><?php echo ($vo["title"]); ?></span> </a>
</li>
<?php else: ?>
<?php if($nownav['a'] == $vo['id']): ?><li class="active">
    <div class="pointer">
      <div class="arrow"></div>
      <div class="arrow_border"></div>
    </div>
    <?php else: ?>
  <li><?php endif; ?>
<a class="dropdown-toggle" href="#" > <i class="<?php echo ($vo["ico"]); ?>"></i> <span><?php echo ($vo["title"]); ?></span> <i class="icon-chevron-down"></i> </a>
<?php if($nownav['a'] == $vo['id']): ?><ul class="active submenu">
  <?php else: ?>
  <ul class="submenu"><?php endif; ?>
    <?php if(is_array($nav)): $i = 0; $__LIST__ = $nav;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$sonnode): $mod = ($i % 2 );++$i; if($sonnode['pid'] == $vo['id']): if(in_array($sonnode['id'],$navids)): ?><li> <a href="<?php echo U(''.$sonnode['m'].'/'.$sonnode['a'].'');?>"><?php echo ($sonnode['title']); ?></a> </li><?php endif; endif; endforeach; endif; else: echo "" ;endif; ?>
  </ul>
  </li><?php endif; endif; endif; endforeach; endif; else: echo "" ;endif; ?>
</ul>
</div>
<!-- end sidebar --> 


	<!-- main container -->
    <div class="content" style="height: 100%;">
    	 <div class="container-fluid">
    	 <!-- upper main stats -->
            <div id="main-stats">
                <div class="row-fluid stats-row">
                    <div class="span3 stat">
                        <div class="data">
                        		展示中广告
                           <a href="../ad/online"> <span class="number" id="sumauth"><?php echo ($adonline); ?></span></a>
                            		
                        </div>
                        <span class="date"><?php echo (date('Y-m-d g:i a',time())); ?>
                        </span>
                    </div>
                    <div class="span3 stat">
                        <div class="data">
                       	 累计投放广告
                            <span class="number" id="sumuser"><?php echo ($adcount); ?></span>
                           	 次
                        </div>
                        <span class="date"><?php echo (date('Y-m-d g:i a',time())); ?></span>
                    </div>
                    <div class="span3 stat">
                        <div class="data">
                       		 累计浏览用户
                            <span class="number" id="sumshopad"><?php echo ($adshowup); ?></span>
                            次
                        </div>
                        <span class="date"><?php echo (date('Y-m-d g:i a',time())); ?></span>
                    </div>
                    <div class="span3 stat last">
                        <div class="data">
                        		累计点击用户
                            <span class="number" id="sumad"><?php echo ($adhit); ?></span>
                            人
                        </div>
                        <span class="date"><?php echo (date('Y-m-d g:i a',time())); ?></span>
                    </div>
                </div>
            </div>
            <!-- end upper main stats -->
    	   <div id="pad-wrapper">
				<div class="row-fluid">
					<div class="alert hide fade in span11">
		            <a class="close" data-dismiss="alert" href="#">×</a>
		            <div id="vermsg"></div>
		          </div>
					
					
					

				</div>
				
                <!-- statistics chart built with jQuery Flot -->
                <div class="row-fluid chart">
                	<div class="span6">
                		<h4>
                        	投放广告统计
                       	<a id="authbtn" class="hidden"></a>
	                    </h4>
	                    <div class="span12">
	                        <div id="adchart"></div>
	                    </div>
                	</div>
                    
                </div>

    	 
    	 </div>
    
          </div>
          <div class="span8">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>历史统计</th>
                <th>广告展示次数</th>
                <th>访问次数</th>
                <th>点击次数</th>
                <th>点击率</th>
              </tr>
            </thead>
            <tbody id="gridbox">
            </tbody>
          </table>
        </div>

</div>
	<!-- scripts -->
 <script src="<?php echo ($Theme['P']['js']); ?>/jquery.js"></script>
    <script src="<?php echo ($Theme['P']['root']); ?>/bootadmin/js/bootstrap.min.js"></script>
    <script src="<?php echo ($Theme['P']['root']); ?>/bootadmin/js/theme.js"></script>
<script src="<?php echo ($Theme['P']['js']); ?>/flot/jquery.flot.js"></script> 
<!--[if lte IE 8]><script language="javascript" type="text/javascript" src="<?php echo ($Theme['P']['js']); ?>/flot/excanvas.min.js"></script><![endif]-->
<script src="<?php echo ($Theme['T']['js']); ?>/index.js"></script> 
<script>

$(function(){
	//authchart('<?php echo U('pub/getauthrpt');?>');
	//mchart('<?php echo U('pub/getuserchart');?>');
	//shopad('<?php echo U('pub/getadrpt');?>');
	//adchar('<?php echo U('pub/getpubadrpt');?>');
    $.ajax({
            url: '<?php echo U('pub/getpubadrpt');?>',
            type: "get",
            data:{
                'mode':'history',
                },
            dataType:'json',
            error:function(){
                    AlertTips("服务器忙，请稍候再试",2000);
                    },
            success:function(data){
                    var bt=[];
                    var bt_hit=[];
                    var bt_count=[];
                    data=eval(data);
                    for(var vo in data){
                        
                        bt.push([data[vo].t,data[vo].showup]);
                        bt_count.push([data[vo].t,data[vo].count]);
                        bt_hit.push([data[vo].t,data[vo].hit]);
                        
                    }
                    var dataset=[{label:"广告展示",data:bt},{label:"访问人数",data:bt_count},{label:"广告点击",data:bt_hit}];
                     $.plot($("#adchart"), dataset, {grid: { hoverable: true, clickable: true, borderColor:'#000',borderWidth:1},xaxis:{ticks:hourlist},series:{lines:{fill:true, show: true}, points:
                        { show: true,
                              }}});
                    rendertable(data);
            }
        });
	//getversion(<?php echo getvsersion();?>);
    var previousPoint = null;
    $("#adchart").bind("plothover", function (event, pos, item) {
        if (item) {
              if (previousPoint != item.dataIndex) {
                  previousPoint = item.dataIndex;
                  
                  $('#tooltip').fadeOut(200,function(){
                        $(this).remove();
                    });
                  var x = item.datapoint[0].toFixed(2),
                        y = item.datapoint[1].toFixed(2);
                      
                  maruti.flot_tooltip(item.pageX, item.pageY, y+"次");
              }
              
          } else {
                $('#tooltip').fadeOut(200,function(){
                        $(this).remove();
                    });
              previousPoint = null;           
        }   
    });   
    maruti = {
            // === Tooltip for flot charts === //
            flot_tooltip: function(x, y, contents) {
                
                $('<div id="tooltip">' + contents + '</div>').css( {
                    top: y + 5,
                    left: x + 5
                }).appendTo("body").fadeIn(200);
            }
    }

});

function rendertable(data){
    
    $("#gridbox").empty();
    var trHtml="";
    var sumshow=0;
    var sumhit=0;
    var sumcount=0;
    for(var vo in data){
        sumshow+=parseFloat( data[vo].showup);
        sumhit+=parseFloat(data[vo].hit);
        sumcount+=parseFloat(data[vo].count);
    }
    trHtml+="<tr>";
    trHtml+="<td>合计：</td>";
    trHtml+="<td>"+sumshow+"次</td>";
    trHtml+="<td>"+sumcount+"次</td>";
    trHtml+="<td>"+sumhit+"次</td>";
    trHtml+="<td>"+(sumhit/sumshow)+"%</td>";
    trHtml+="</tr>";
    $("#gridbox").append(trHtml);
}
</script>
</body>
</html>