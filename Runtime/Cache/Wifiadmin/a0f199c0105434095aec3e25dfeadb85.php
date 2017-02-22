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
<link  href="<?php echo ($Theme['P']['root']); ?>/kindeditor/themes/default/default.css" type="text/css" rel="stylesheet">
<link rel="stylesheet" href="<?php echo ($Theme['P']['root']); ?>/bootadmin/css/compiled/form-showcase.css" type="text/css" media="screen" />
<link href="<?php echo ($Theme['P']['root']); ?>/bootadmin/css/lib/uniform.default.css" type="text/css" rel="stylesheet" />
<link href="<?php echo ($Theme['P']['root']); ?>/bootadmin/css/lib/select2.css" type="text/css" rel="stylesheet" />
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


<form name="doad" action="__URL__/index" method="post" enctype="multpart/form-data" id="search_form">
    <!-- main container -->
    <div class="content" style="height: 100%;">
        <div id="pad-wrapper">
        	<div class="row-fluid ">
                <h4 class="title">
                   AP列表 <small></small>
                </h4>
                <div id="pad-wrapper" style="margin-top:10px;">
                    <div class="span8 " style="width:100%;">
                        <div class="field-box rptSearchBar">
                            <label class="span1" style="width: 70px;">选择区域:</label>
                            <select class="" name="area">
                                <option value="">请选择</option>
                                <option value="丰泽区" <?php if(($area) == "丰泽区"): ?>selected<?php endif; ?>>丰泽区</option>
                                <option value="晋江市" <?php if(($area) == "晋江市"): ?>selected<?php endif; ?>>晋江市</option>
                                <option value="泉港区" <?php if(($area) == "泉港区"): ?>selected<?php endif; ?>>泉港区</option>
                                <option value="石狮市" <?php if(($area) == "石狮市"): ?>selected<?php endif; ?>>石狮市</option>
                                <option value="安溪县" <?php if(($area) == "安溪县"): ?>selected<?php endif; ?>>安溪县</option>
                                <option value="鲤城区" <?php if(($area) == "鲤城区"): ?>selected<?php endif; ?>>鲤城区</option>
                                <option value="永春县" <?php if(($area) == "永春县"): ?>selected<?php endif; ?>>永春县</option>
                                <option value="南安市" <?php if(($area) == "南安市"): ?>selected<?php endif; ?>>南安市</option>
                                <option value="惠安县" <?php if(($area) == "惠安县"): ?>selected<?php endif; ?>>惠安县</option>
                                <option value="德化县" <?php if(($area) == "德化县"): ?>selected<?php endif; ?>>德化县</option>
                            </select>
                            <label class="span1" style="width: 70px;">选择行业:</label>
                            <select class="" name="trade">
                                <option value="">请选择</option>
                                <?php if(is_array($trade)): $i = 0; $__LIST__ = $trade;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo["trade"]); ?>" <?php if(($vo["trade"]) == "{$seltrade}"): ?>selected<?php endif; ?> /> <?php echo ($vo["trade"]); endforeach; endif; else: echo "" ;endif; ?>
                            </select>
                            <label class="span1" style="width: 80px;">热点名称:</label>
                            <input type="text" class="inputSty" name="shopname"/>
                            <a class="btn btn-success span1  pull-left" id="query" name="query">查询</a>&nbsp;
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="pad-wrapper">
            <!-- orders table -->
            <div class="table-wrapper orders-table section">
                <div class="">
                    <table class="table table-hover">
                        <thead>
                        <tr>
                            <th class="col-md-1" style="width:10px;"><input type="checkbox" id='all' class="input_hide" value=""></th>
                            <th class="col-md-2"> <span class="line"></span> ap名称 </th>
                            <th class="col-md-2"> <span class="line"></span> 热点名称 </th>
                            <th class="col-md-2"> <span class="line"></span> 区域 </th>
                            <th class="col-md-2"> <span class="line"></span> 行业 </th>
                            <th class="col-md-2"> <span class="line"></span> APMAC </th>
                            <th class="col-md-2"> <span class="line"></span> AP坐标 </th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if(is_array($lists)): $i = 0; $__LIST__ = $lists;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><td><input type="checkbox" value = "<?php echo ($vo["id"]); ?>" name="sid[]" class="apid"/></td>
                            <td><?php echo ($vo['routename']); ?></td>
                            <td><?php echo ($vo['shopname']); ?></td>
                            <td><?php echo ($vo['area']); ?></td>
                            <td><?php echo ($vo['trade']); ?></td>
                            <td><?php echo ($vo['gw_id']); ?></td>
                            <td><?php echo ($vo['lat']); ?>,<?php echo ($vo['lng']); ?></td>
                            </tr><?php endforeach; endif; else: echo "" ;endif; ?>
                        </tbody>
                    </table>
                	<div class="pagination pull-right"> <?php echo ($page); ?> </div>
                    <div class="filter-block">
                        <div class="span8 " style="width:100%; margin-left:0;">
                            <div class="field-box rptSearchBar">
                                <label class="span1" style="width: 70px;">广告位置:</label>
                                <select class="" name="ad_pos" id="ad_pos">
                                	<option value="">请选择</option>
                                    <?php if(is_array($pos)): $i = 0; $__LIST__ = $pos;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo["id"]); ?>" /> <?php echo ($vo["name"]); endforeach; endif; else: echo "" ;endif; ?>
                                </select>
                                <label class="span1" style="width: 70px;">广告名:</label>
                                <select class="" id="adlist" name="adid">
                                	<option value="">请选择</option>
                                    <?php if(is_array($adlist)): $i = 0; $__LIST__ = $adlist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$ad): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo["id"]); ?>"><?php echo ($ad['title']); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                                </select>
                                <input type="button" class="btn-flat success" value="推送当前条件所有AP" id="allpush" style="width:150px; margin-left: 20px;">&nbsp;
                                <input type="button" class="btn-flat success" value="推送选中AP">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end orders table -->
        </div>
    </div>
</form>

<!-- scripts -->
<script src="<?php echo ($Theme['P']['js']); ?>/jquery.js"></script>
<script src="<?php echo ($Theme['P']['root']); ?>/bootadmin/js/bootstrap.min.js"></script>
<script src="<?php echo ($Theme['P']['root']); ?>/bootadmin/js/theme.js"></script>
<script src="<?php echo ($Theme['P']['root']); ?>/bootadmin/js/jquery.uniform.min.js"></script>
<script src="<?php echo ($Theme['P']['root']); ?>/bootadmin/js/common.js"></script>
<script src="<?php echo ($Theme['P']['root']); ?>/kindeditor/kindeditor-min.js" type="text/javascript"></script>
<script src="<?php echo ($Theme['P']['root']); ?>/kindeditor/lang/zh_CN.js" type="text/javascript"></script>
<script src="<?php echo ($Theme['P']['root']); ?>/bootadmin/js/bootstrap.datepicker.js"></script>
<script src="<?php echo ($Theme['P']['js']); ?>/region_select.js"></script>
<script>
    //new PCAS('province', 'city', 'area', '福建省', '泉州市', '');
    //a标签 submit
    $('#query').click(function () {
        $("#search_form").submit();
    });

    $('.btn-flat').bind("click", function () {
        var adid = $('#adlist').val();
        var ad_pos=$('#ad_pos').val();
        var apid = new Array();

        if($(this).attr('id')=="allpush"){
            appid = "";
        }else{
            $('input[name="sid[]"]:checked').each(function(x,y){
                apid[x] = $(this).val();
            });
        }

        $.ajax({
           url:"<?php echo U('push');?>",
            type:"post",
            data:{"adid":adid,"apid":apid,"ad_pos":ad_pos},
            dataType:"json",
            success: function (ajax) {
                if(ajax.error==0){
                    alert('推送成功');
                }else{
                    alert('推送失败');
                }
            }
        });
    });

    $('#ad_pos').bind("change", function () {
        var advid = $(this).val();

        $.ajax({
           url:"<?php echo U('adselect');?>",
            type:"post",
            data:{"advid":advid},
            dataType:"json",
            success: function (ajax) {
                if(ajax.error==0){
                    $('#adlist').find('option').remove();
                    for(i=0;i<ajax.msg.length;i++){
                        $('#adlist').append("<option value='"+ajax.msg[i].id+"'>"+ajax.msg[i].title+"</option>");
                    }
                }else{
                    $('#adlist').find('option').remove();
                }
            }
        });
    });

    KindEditor.ready(function(K) {
        K.create('textarea[name="info"]', {
            autoHeightMode : true,
            items:['source', '|', 'undo', 'redo', '|', 'preview', 'print', 'template',  'cut', 'copy', 'paste',
                'plainpaste', 'wordpaste', '|', 'justifyleft', 'justifycenter', 'justifyright',
                'justifyfull', 'insertorderedlist', 'insertunorderedlist', 'indent', 'outdent', 'subscript',
                'superscript', 'clearhtml', 'quickformat', 'selectall', '|', 'fullscreen', '/',
                'formatblock', 'fontname', 'fontsize', '|', 'forecolor', 'hilitecolor', 'bold',
                'italic', 'underline', 'strikethrough', 'lineheight', 'removeformat', '|', 'image',
                'flash', 'media', 'table', 'hr', 'emoticons', 'baidumap', 'pagebreak',
                'anchor', 'link', 'unlink', '|', 'about'],
            afterUpload:false,
            allowFlashUpload:false,
            allowFileUpload:false,
            allowImageUpload:false,
            allowMediaUpload:false,
        });
    });
</script>
<script type="text/javascript">
    $(function () {
        var shownow=new Date();
        shownow=shownow.getTime();
        $("#startdate").datepicker().on('changeDate',function(ev){
            var startTime = ev.date.valueOf();
            if(startTime<shownow){
                $("#startdate").focus();            }
        });
        $("#enddate").datepicker().on('changeDate',function(ev){
            var et = ev.date.valueOf();
            var t=$("#startdate").val();
            if($("#startdate").val()==""){
                $("#enddate").val("");
                alert("请先选择投放时间");
                return;

            }else{
                var dt=Date.parse($("#startdate").val());
                if(et<dt){
                    $("#enddate").val($("#startdate").val());
                    alert("广告结束时间不能小于开始时间");
                }
            }
        });
        // add uniform plugin styles to html elements
        $("input:checkbox, input:radio,input[type=file]").uniform();

        //全选/反全选
        $('#all').click(function () {
            $('.sid').attr("checked", this.checked);
        });
    });

</script>
</body>
</html>