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

    <div id="pad-wrapper">
        <div class="row head" style=" margin-left:20px;">
            <div class="col-md-12">
                <h4>AP列表</h4>
            </div>
        </div>
        <div class="row-fluid form-wrapper" style="margin-left:20px;">
            <form method="post">

                <div class="span6 column" style="width:100%;">
                    <div>
                        <div class="span6 field-box" style="width:auto;">
                            <label class="span4" style="width:70px;">APMAC:</label>
                            <input class="span8" type="text" name="mac"  style="width:150px;">
                        </div>
                        <div class="span6 field-box" style="width:auto; margin-left:10px;">
                            <label class="span4" style="width:70px;">AP名称:</label>
                            <input class="span8" type="text" name="sname" style="width:150px;">
                        </div>
                        <!--<div class="span6 field-box">
                                 <label class="span4">代理商:</label>
                               <input class="span8" type="text" name="agent">
                        </div>-->
                    </div>
                    <!--<div>
                        <div class="span6 field-box">
                            <label class="span4">商户名称:</label>
                            <input class="span8" type="text" name="sname">
                        </div>
                         <div class="span6 field-box">
                                <label class="span4">商户帐号:</label>
                                <input class="span8" type="text" name="slogin">
                         </div>
                    </div>-->

                    <div class="filter-block">

                        <input type="submit" class="btn-flat success " value="查询" style="margin-left:20px;">

                    </div>

                </div>



            </form>
        </div>
    </div>

    <div id="pad-wrapper">
        <!-- orders table -->
        <div class="table-wrapper orders-table section">




            <div class="">
                <table class="table table-hover">
                    <thead>
                    <tr>
                        <th class="col-md-1">
                            编号
                        </th>
                        <th class="col-md-2">
                            <span class="line"></span>
                            添加日期
                        </th>
                        </th>
                        </th>
                        <th class="col-md-2">
                            <span class="line"></span>
                            AP名称
                        </th>
                        <th class="col-md-1">
                            <span class="line"></span>
                            APMAC
                        </th>
                        <th class="col-md-2">
                            <span class="line"></span>
                            区域
                        </th>
                        <th class="col-md-2">
                            <span class="line"></span>
                            行业
                        </th>
                        <th class="col-md-1">
                            <span class="line"></span>
                           地址
                        </th>
                        <th class="col-md-1">
                            <span class="line"></span>
                            操作
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if(is_array($lists)): $i = 0; $__LIST__ = $lists;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><!-- row -->
                        <tr class="<?php if(($i) == "1"): ?>first<?php endif; ?>">
                        <td>
                            <?php echo ($i); ?>
                        </td>
                        <td>
                            <?php echo ($vo["add_time"]); ?>
                        </td>
                        <td>
                            <?php echo ($vo["routename"]); ?>
                        </td>
                        <td>
                            <?php echo ($vo["gw_id"]); ?>
                        </td>
                        <td>
                            <?php echo ($vo["area"]); ?>
                        </td>
                        <td>
                            <?php echo ($vo["trade"]); ?>
                        </td>
                        <td>
                            <?php echo ($vo["gw_address"]); ?>
                        </td>
                        <td>
                            <a href="<?php echo U('edit',array('id'=>$vo['id']));?>">编辑</a>
                            |
                            <a href="javascript:drop_confirm('您确定要删除吗?','<?php echo U('del',array('id'=>$vo['id']));?>');">删除</a>
                            |
                            <a href="<?php echo U('info',array('id'=>$vo['id']));?>">备注</a>
                        </td>
                        </tr><?php endforeach; endif; else: echo "" ;endif; ?>

                    </tbody>
                </table>
            </div>

            <div class="pagination pull-right">
                <?php echo ($page); ?>
            </div>
        </div>
        <!-- end orders table -->
    </div>
</div>


<!-- scripts -->
<script src="<?php echo ($Theme['P']['js']); ?>/jquery.js"></script>
<script src="<?php echo ($Theme['P']['root']); ?>/bootadmin/js/bootstrap.min.js"></script>
<script src="<?php echo ($Theme['P']['root']); ?>/bootadmin/js/theme.js"></script>
<script src="<?php echo ($Theme['P']['root']); ?>/bootadmin/js/common.js"></script>

</body>
</html>