<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>All-Wifi 数据中心</title>
	<title></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="<?php echo ($Theme['P']['root']); ?>/css/map.css"/>
    <script src="<?php echo ($Theme['P']['js']); ?>/jquery.js"></script>
    <script src="<?php echo ($Theme['P']['js']); ?>/main1.js"></script>
<!-- libraries -->
</head>
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

   
<body class="draw-body">
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

    <!-- <div class="navRow clearFix">
        <div class="navBar clearFix" style="width:auto;padding:0 100px;">
            <div class="navLogo">
                <a href="index.html"><img src="<?php echo ($Theme['P']['root']); ?>/img/relogo.png" alt=""/></a>
                <span>数据中心</span>
            </div>
            <ul class="navUL">
                <li><a href="javascript:void(0);">申请置点</a></li>
                <li><a href="javascript:void(0);">帮助</a></li>
                <li><a href="javascript:void(0);">关于</a></li>
            </ul>
        </div>
    </div> -->
    <div class="clearFix" id="mainContainer" style="width: 100%;height: 100%;margin-bottom:60px;margin-left:200px;">
        <div id="main">
            <div class="wait-load"><span>数据加载中...</span></div>
        </div>
        <div class="btn btn-Screen" id="fullScreen">全屏</div>
    </div>
    <script src="<?php echo ($Theme['P']['js']); ?>/dist/echarts.js"></script>
    <script src="<?php echo ($Theme['P']['js']); ?>/e-map.js"></script>
    
    <div class="footer">
        <div class="footer-copyright" style="padding:10px 0;">© 2015 all-WiFi <a href="http://www.miitbeian.gov.cn/state/outPortal/loginPortal.action" style=" color:#ccc; ">闽ICP备14001930号-2</a></div>
    </div>
<script src="<?php echo ($Theme['P']['js']); ?>/jquery.js"></script>
<script src="<?php echo ($Theme['P']['root']); ?>/bootadmin/js/bootstrap.min.js"></script>
<script src="<?php echo ($Theme['P']['root']); ?>/bootadmin/js/theme.js"></script>
<script src="<?php echo ($Theme['P']['root']); ?>/bootadmin/js/jquery.uniform.min.js"></script>
<script src="<?php echo ($Theme['P']['root']); ?>/bootadmin/js/common.js"></script>
<script src="<?php echo ($Theme['P']['root']); ?>/kindeditor/kindeditor-min.js" type="text/javascript"></script>
<script src="<?php echo ($Theme['P']['root']); ?>/kindeditor/lang/zh_CN.js" type="text/javascript"></script>
<script src="<?php echo ($Theme['P']['root']); ?>/bootadmin/js/bootstrap.datepicker.js"></script>
<script src="<?php echo ($Theme['P']['js']); ?>/region_select.js"></script>
    <script type="text/javascript">
    $('#fullScreen').bind('click',function(){
        toggleFullScreen(document.getElementById("mainContainer"));
    })

    </script>
</body>
</html>