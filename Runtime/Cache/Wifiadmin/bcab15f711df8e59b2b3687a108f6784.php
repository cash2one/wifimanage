<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
<title><?php echo C('sitename');?>-管理后台</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
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

   
<!-- libraries -->
<link href="<?php echo ($Theme['P']['root']); ?>/bootadmin/css/compiled/tables.css" type="text/css" rel="stylesheet"  />
<link rel="stylesheet" href="<?php echo ($Theme['P']['root']); ?>/bootadmin/css/lib/bootstrap.datepicker.css" />
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
    <div class="footerbar" style="width:100%; line-height:20px; font-size:10px; background-color:#F7F7F7; position:absolute; left:0; bottom:-40px;; z-index:999; text-align:center;">厦门思可达信息科技有限公司 提供技术支持</div> <!-- sidebar -->

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
    <div id="pad-wrapper">
      <div class="row-fluid ">
        <h4 class="title"> 广告投放统计 <small></small> </h4>
        <div id="pad-wrapper" style="margin-top:10px;">
          <form action="__URL__/rpt"  method="get">
            <div class="span8 " style="width:100%;">
              <div class="field-box rptSearchBar">
                <label class="span1">开始时间:</label>
                <input type="text" id="sdate" value="<?php echo date("Y-m-01") ?>
                " data-date-format="yyyy-mm-dd" class="pull-left datepicker" readonly="readonly">
                <label class="span1">结束时间:</label>
                <input type="text" id="edate" value="<?php echo date("Y-m-d") ?>
                " data-date-format="yyyy-mm-dd" class="pull-left datepicker" readonly="readonly">
                <label class="span1" style="width: 45px;">区域:</label>
                <select class="" id="area" name="area">
                  <option value="">请选择</option>
                  <?php if(is_array($arealist)): $i = 0; $__LIST__ = $arealist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$area): $mod = ($i % 2 );++$i;?><option value="<?php echo ($area["area"]); ?>"><?php echo ($area["area"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                </select>
                <label class="span1" style="width: 45px;">行业:</label>
                <select class="" id="trade"  name="trade">
                  <option value="">请选择</option>
                  <?php if(is_array($tradelist)): $i = 0; $__LIST__ = $tradelist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$trade): $mod = ($i % 2 );++$i;?><option value="<?php echo ($trade["trade"]); ?>"><?php echo ($trade["trade"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                </select>
              </div>
            </div>
            <div style="clear:both;"></div>
            <div class="span8 " style="width:100%; margin-left:0; margin-top:10px;">
              <div class="field-box rptSearchBar">
                <label class="span1" style="width: 50px;">广告主:</label>
                <select class="" style="width:150px;" id="aduser" name="aduser">
                  <option value="">请选择</option>
                  <?php if(is_array($aduserlist)): $i = 0; $__LIST__ = $aduserlist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$aduser): $mod = ($i % 2 );++$i;?><option value="<?php echo ($aduser["aid"]); ?>"><?php echo ($aduser["user"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                </select>
                <label class="span1" style="width: 50px;">广告:</label>
                <select id="ad" name="ad">
                  <option value="">请选择</option>
                  <?php if(is_array($adlist)): $i = 0; $__LIST__ = $adlist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$ad): $mod = ($i % 2 );++$i;?><option value="<?php echo ($ad["id"]); ?>"><?php echo ($ad["title"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                </select>
                <a class="btn btn-success span1  pull-left" id="query">查询</a>&nbsp; </div>
            </div>
            <div style="clear:both;"></div>
          </form>
          <div class="btn-group pull-right" style="float:left; margin:10px 7px 30px;">
            <button class="glow left " id="today">今天</button>
            <button class="glow middle " id="yestoday">昨天</button>
            <button class="glow middle " id="week">前七天</button>
            <button class="glow right" id="month">本月</button>
          </div>
        </div>
        <div class="row span11">
          <div id="placeholder"></div>
        </div>
      </div>
      <!-- orders table -->
      <div class="table-wrapper orders-table section">
        <div class="row head">
          <div class="col-md-12" style="margin:40px 30px 0;">
            <h4>统计列表</h4>
          </div>
        </div>
        <div class="row filter-block">
          <div class="pull-right"> </div>
        </div>
        <div class="span8">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>统计日期</th>
                <th>广告展示次数</th>
                <th>访问人数</th>
                <th>点击次数</th>
                <th>点击率</th>
              </tr>
            </thead>
            <tbody id="gridbox">
            </tbody>
          </table>
        </div>
        <div class=""> <?php echo ($page); ?> </div>
      </div>
      <!-- end orders table --> 
    </div>
  </div>
</div>

<!-- scripts --> 
<script src="<?php echo ($Theme['P']['js']); ?>/jquery.js"></script> 
<script src="<?php echo ($Theme['P']['root']); ?>/bootadmin/js/bootstrap.min.js"></script> 
<script src="<?php echo ($Theme['P']['root']); ?>/bootadmin/js/bootstrap.datepicker.js"></script> 
<script src="<?php echo ($Theme['P']['root']); ?>/bootadmin/js/theme.js"></script> 
<script src="<?php echo ($Theme['P']['root']); ?>/bootadmin/js/common.js"></script> 
<script src="<?php echo ($Theme['P']['js']); ?>/flot/jquery.flot.js"></script> 
<!--[if lte IE 8]><script language="javascript" type="text/javascript" src="<?php echo ($Theme['P']['js']); ?>/flot/excanvas.min.js"></script><![endif]--> 
<script>
	$('#query').click(function () {
		$("#search_form").submit();
	});
var daylist=[];
for(var i=1;i<=31;i++){
	if(i<10){
		daylist.push(["0"+i,i+"号"]);
	}else{
		daylist.push([i,i+"号"]);
	}
}
var hourlist=[];
for(var i=0;i<=24;i++){
	if(i<10){
		hourlist.push(["0"+i,i+"点"]);
	}else{
		hourlist.push([i,i+"点"]);
	}
}

var lines;
$(function () {
	 var stack = 0, bars = true, lines = false, steps = false;	
	    
	 $('.datepicker').datepicker();
  	  $("#today").bind("click",function(){
		  var st=new Date($("#sdate").val());	
		  var et=new Date($("#edate").val());	
		  if(st.getTime()>et.getTime()){
			  AlertTips("开始日期不能大于结束日期",2000);
				  return;
		  }
		  $.ajax({
			  	url: '<?php echo U('ad/rpt');?>',
		        type: "get",
				data:{
					'mode':'today',
					'sdate':$("#sdate").val(),
					'edate':$("#edate").val(),
					'trade':$("#trade").val(),
					'area':$("#area").val(),
					'ad':$("#ad").val(),
					'aduser':$("#aduser").val()
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
						 $.plot($("#placeholder"), dataset, {grid: { hoverable: true, clickable: true, borderColor:'#000',borderWidth:1},xaxis:{ticks:hourlist},series:{lines:{fill:true, show: true}, points:
						    { show: true,
						    	  }}});
						rendertable(data);
				}
			  });
  	  	  });

  	 $("#yestoday").bind("click",function(){
		  var st=new Date($("#sdate").val());	
		  var et=new Date($("#edate").val());	
		  if(st.getTime()>et.getTime()){
			  AlertTips("开始日期不能大于结束日期",2000);
				  return;
		  }
		  $.ajax({
			  url: '<?php echo U('ad/rpt');?>',
		        type: "get",
				data:{
					'mode':'yestoday',	
					'sdate':$("#sdate").val(),
					'edate':$("#edate").val(),
					'trade':$("#trade").val(),
					'area':$("#area").val(),
					'ad':$("#ad").val(),
					'aduser':$("#aduser").val()				
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
							bt_hit.push([data[vo].t,data[vo].hit]);
							bt_count.push([data[vo].t,data[vo].count]);
						}
						var dataset=[{label:"广告展示",data:bt},{label:"访问人数",data:bt_count},{label:"广告点击",data:bt_hit}];
						 $.plot($("#placeholder"), dataset, {grid: { hoverable: true, clickable: true, borderColor:'#000',borderWidth:1},xaxis:{ticks:hourlist},series:{lines:{fill:true, show: true}, points:
						    { show: true,
						    	  }}});
						 rendertable(data);						
				}
			  });
 	  	  });
	  	  
  		$("#week").bind("click",function(){
		  var st=new Date($("#sdate").val());	
		  var et=new Date($("#edate").val());	
		  if(st.getTime()>et.getTime()){
			  AlertTips("开始日期不能大于结束日期",2000);
				  return;
		  }
		  $.ajax({
			  url: '<?php echo U('ad/rpt');?>',
		        type: "get",
				data:{
					'mode':'week',
					'sdate':$("#sdate").val(),
					'edate':$("#edate").val(),
					'trade':$("#trade").val(),
					'area':$("#area").val(),
					'ad':$("#ad").val(),
					'aduser':$("#aduser").val()
					},
				dataType:'json',
				error:function(){
						AlertTips("服务器忙，请稍候再试",2000);
						},
				success:function(data){
						var bt=[];
						data=eval(data)  ;
						var templist=[];
						var bt_hit=[];
						var bt_count=[];
						for(var vo in data){
 							templist.push([data[vo].t,data[vo].td]);
							bt.push([data[vo].t,data[vo].showup]);
							bt_hit.push([data[vo].t,data[vo].hit]);
							bt_count.push([data[vo].t,data[vo].count]);
						}
						var dataset=[{label:"广告展示",data:bt},{label:"访问人数",data:bt_count},{label:"广告点击",data:bt_hit}];
						 $.plot($("#placeholder"), dataset, {grid: { hoverable: true, clickable: true, borderColor:'#000',borderWidth:1},xaxis:{ticks:templist},series:{lines:{fill:true, show: true}, points:
						    { show: true,
						    	  }}});
						 rendertable(data);
				}
			  });
	  	  });
  		$("#month").bind("click",function(){
		  var st=new Date($("#sdate").val());	
		  var et=new Date($("#edate").val());	
		  if(st.getTime()>et.getTime()){
			  AlertTips("开始日期不能大于结束日期",2000);
				  return;
		  }
  		  $.ajax({
  			  url: '<?php echo U('ad/rpt');?>',
  		        type: "get",
  				data:{
  					'mode':'month',
					'sdate':$("#sdate").val(),
					'edate':$("#edate").val(),
					'trade':$("#trade").val(),
					'area':$("#area").val(),
					'ad':$("#ad").val(),
					'aduser':$("#aduser").val()
  					},
  				dataType:'json',
  				error:function(){
  						AlertTips("服务器忙，请稍候再试",2000);
  						},
  				success:function(data){
  						var bt=[];
  						var bt_hit=[];
  						var bt_count=[];
  						data=eval(data)  ;
  						for(var vo in data){
  							bt.push([data[vo].t,data[vo].showup]);
  							bt_hit.push([data[vo].t,data[vo].hit]);
  							bt_count.push([data[vo].t,data[vo].count]);
  						}
  						var dataset=[{label:"广告展示",data:bt},{label:"访问人数",data:bt_count},{label:"广告点击",data:bt_hit}];
  						 $.plot($("#placeholder"), dataset, {grid: { hoverable: true, clickable: true, borderColor:'#000',borderWidth:1},xaxis:{ticks:daylist},series:{lines:{fill:true, show: true}, points:
  						    { show: true,
  						    	  }}});
  						rendertable(data);
  				}
  			  });
  	  	  });

  		$("#query").bind("click",function(){
			var st=new Date($("#sdate").val());	
			var et=new Date($("#edate").val());	
			if(st.getTime()>et.getTime()){
				AlertTips("开始日期不能大于结束日期",2000);
					return;
			}

			 $.ajax({
	  			  url: '<?php echo U('ad/rpt');?>',
	  		        type: "get",
	  				data:{
	  					'mode':'query',
	  					'sdate':$("#sdate").val(),
	  					'edate':$("#edate").val(),
	  					'trade':$("#trade").val(),
	  					'area':$("#area").val(),
	  					'ad':$("#ad").val(),
	  					'aduser':$("#aduser").val()
	  					},
	  				dataType:'json',
	  				error:function(){
	  						AlertTips("服务器忙，请稍候再试",2000);
	  						},
	  				success:function(data){
	  						var bt=[];
	  						var templist=[];
	  						var bt_hit=[];
	  						var bt_count=[];
	  						data=eval(data)  ;
	  						for(var vo in data){
	  							templist.push([data[vo].t,data[vo].td]);
	  							bt.push([data[vo].t,data[vo].showup]);
	  							bt_hit.push([data[vo].t,data[vo].hit]);
	  							bt_count.push([data[vo].t,data[vo].count]);
	  						}
	  						
	  						var dataset=[{label:"广告展示",data:bt},{label:"广告访问",data:bt_count},{label:"广告点击",data:bt_hit}];
	  						 var plot= $.plot($("#placeholder"), dataset, {xaxis:{ticks:templist},  grid: { hoverable: true, clickable: true, borderColor:'#000',borderWidth:1}, series:{lines:{fill:true, show: true}, points:
	  						    { show: true,
	  						    	  }}});
	  						rendertable(data);
	  				}
	  			  });
  	  	});
  		$("#today").trigger("click");
});

var previousPoint = null;
	$("#placeholder").bind("plothover", function (event, pos, item) {
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

function rendertable(data){
	
	$("#gridbox").empty();
	var trHtml="";
	var sumshow=0;
	var sumhit=0;
	var sumcount=0;
	for(var vo in data){
		trHtml+="<tr>";
		trHtml+="<td>"+data[vo].showdate+"</td>";
		trHtml+="<td>"+data[vo].showup+"次</td>";
		trHtml+="<td>"+data[vo].count+"次</td>";
		trHtml+="<td>"+data[vo].hit+"次</td>";
		trHtml+="<td>"+data[vo].rt+"%</td>";
		trHtml+="</tr>";
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