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
  <div id="pad-wrapper">
    <div class="popImg"> <img src="" style="display:block; width:400px; height:auto;" /> </div>
    <!-- orders table -->
    <div class="table-wrapper orders-table section">
      <div class="row head" style=" margin-left:20px;">
        <div class="col-md-12">
          <h4>广告列表</h4>
        </div>
      </div>
      <div class="row filter-block" style="display:none;">
        <div class="pull-right"> </div>
      </div>
      <div class="row-fluid form-wrapper" style="margin-left:20px;">
        <form method="post">
          <div class="span6 column" style="width:100%;">
            <div>
              <div class="span4 field-box" style="width:auto;">
                <label class="span4" style="width:70px;">投放时间:</label>
                <input type="text" class="span7" value="<?php echo ($startdate); ?>" name="sstartdate" id="sstartdate"  data-date-format="yyyy-mm-dd" readonly style="width:150px;">
              </div>
              <div class="span4 field-box" style="width:auto; margin-left:10px;">
                <label class="span4" style="width:30px;">到:</label>
                <input type="text" class="span7"  value="<?php echo ($senddate); ?>" name="senddate" id="senddate"  data-date-format="yyyy-mm-dd" readonly style="width:150px;">
              </div>
              <div class="span3    field-box" style="width:auto;">
                <label class="span4" style="width:50px;">广告位:</label>
                <div>
                  <label class="span4">
                    <select style="height:30px;" name="sname" attr="<?php echo ($sname); ?>">
                      <?php if(is_array($pos)): $i = 0; $__LIST__ = $pos;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo["id"]); ?>" />
                        <?php echo ($vo["name"]); endforeach; endif; else: echo "" ;endif; ?>
                    </select>
                  </label>
                </div>
                <!--<input class="span8" type="text" name="sname" value="<?php echo ($sname); ?>" style="width:150px;">--> 
              </div>
            </div>
            <div class="filter-block">
              <input type="submit" class="btn-flat success " value="查询" style="margin-left:55px;">
            </div>
          </div>
        </form>
      </div>
      <div class="adListTable">
        <table class="table table-hover" style="text-align:center;">
          <thead>
            <tr>
              <th class="col-md-1"> 编号 </th>
              <th class="col-md-2"> 投放时间 </th>
              <!--<th class="col-md-2">
                                    	广告标题
                                </th>-->
              <th class="col-md-2"> 广告主 </th>
              <th class="col-md-2"> 广告位置 </th>
              <th class="col-md-2"> 广告名称 </th>
              <th class="col-md-2"> 默认广告 </th>
              <!-- <th class="col-md-2"> 区域 </th>
              <th class="col-md-2"> 行业 </th> -->
              <!--<th class="col-md-2">
                                    投放ap
                                </th>-->
              <th class="col-md-2"> 当前访问次数 </th>
              <!--<th class="col-md-1">
                                   		图片信息
                                </th>
                                <th class="col-md-1">
                                   	类型
                                </th>-->
                                <th class="col-md-2">
                                   		 排序
                                </th>
              <th class="col-md-1"> 预览 </th>
              <th class="col-md-1"> 操作 </th>
              <!--<th class="col-md-2">
                                    添加日期
                                </th>--> 
            </tr>
          </thead>
          <tbody>
            <?php if(is_array($lists)): $i = 0; $__LIST__ = $lists;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><!-- row -->
              <tr class="<?php if(($i) == "1"): ?>first<?php endif; ?>
                ">
                <td><?php echo ($vo["id"]); ?> </td>
                <td><?php echo (date('Y-m-d ',$vo["startdate"])); ?>到<?php echo (date('Y-m-d ',$vo["enddate"])); ?> </td>
                <!--<td><?php echo ($vo["shopname"]); ?></td>--> 
                <!--<td><?php echo getAdPos($vo['ad_pos']);?></td>-->                
                <td><?php echo ($vo['user']); ?></td>
                <td class="pos"></td>
                <td><?php echo ($vo['title']); ?> </td>
                <td><?php if($vo[default_status] == 1): ?>是
                    <?php else: ?>
                    否<?php endif; ?></td>
                <!-- <td><?php echo ($vo["area"]); ?></td>
                <td><?php echo ($vo["trade"]); ?></td> -->
                <!--<td><?php echo ($vo["gw_id"]); ?></td>-->
                <td><?php echo getShowup($vo['id']);?></td>
                <td><?php echo ($vo["ad_sort"]); ?></td>
                <td><img src="<?php echo ($vo["ad_thumb"]); ?>" class="thumb"></td>
                <!--<td><?php echo getAdMode($vo['mode']);?></td>-->
                <!--<td><?php echo (date('Y-m-d ',$vo["add_time"])); ?></td>-->
                <td><a href="<?php echo U('editad',array('id'=>$vo['id']));?>">编辑</a>| <a href="<?php echo U('delad',array('id'=>$vo['id']));?>">删除</a> | <a href="<?php echo U('oddrpt',array('id'=>$vo['id']));?>">查看统计</a></td>
              </tr><?php endforeach; endif; else: echo "" ;endif; ?>
          </tbody>
        </table>
      </div>
      <div class="pagination pull-right"> <?php echo ($page); ?> </div>
    </div>
    <!-- end orders table --> 
  </div>
</div>

<!-- scripts --> 
<script src="<?php echo ($Theme['P']['js']); ?>/jquery.js"></script> 
<script src="<?php echo ($Theme['P']['root']); ?>/bootadmin/js/bootstrap.min.js"></script> 
<script src="<?php echo ($Theme['P']['root']); ?>/bootadmin/js/theme.js"></script> 
<script src="<?php echo ($Theme['P']['root']); ?>/bootadmin/js/bootstrap.datepicker.js"></script> 
<script src="<?php echo ($Theme['P']['root']); ?>/bootadmin/js/common.js"></script> 
<script type="text/javascript">

    $(function () {
        var shownow=new Date();
        shownow=shownow.getTime();

        $("#sstartdate").datepicker().on('changeDate',function(ev){
            var startTime = ev.date.valueOf();

            if(startTime<shownow){

                $("#sstartdate").focus();
            }
        });
        $("#senddate").datepicker().on('changeDate',function(ev){
            var et = ev.date.valueOf();
            var t=$("#sstartdate").val();
            if($("#sstartdate").val()==""){
                $("#senddate").val("");
                alert("请先选择投放时间");
                return;

            }else{
                var dt=Date.parse($("#sstartdate").val());
                if(et<dt){
                    $("#senddate").val($("#sstartdate").val());
                    alert("广告结束时间不能小于开始时间");

                }
            }
        });
        
        $('img.thumb').click(function(){
        	var imgSrc = $(this).attr('src');
            $('.popImg img').attr("src", imgSrc);
            $('.popImg').show();
        });
        
        $('.popImg').click(function(){
            $('.popImg img').attr('src', '');
            $(this).hide();
        });
        
        // add uniform plugin styles to html elements
        /*$("input:checkbox, input:radio,input[type=file]").uniform();*/
        
        //首选
        selected('sname');
        //get赋值
        $('.pos').text($('select[name="sname"]').find('option:selected').text());
    });
</script>
</body>
</html>