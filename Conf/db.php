<?php
/**
 * 数据库配置文件
 */
return array(
     //'配置项'=>'配置值'
     'URL_CASE INSENSITIVE' =>true,
     "DB_DEPLOY_TYPE"=>1, //是否启用分布式
     'DB_RW_SEPARATE'=>true, //是否启用智能读写分离
     'DB_TYPE' => 'mysql', //数', 
     'DB_HOST'=>'localhost',     //服务器地址
     'DB_NAME' => 'wifimanager', //数据库名
     'DB_USER' => 'root', //用户名
     'DB_PWD' => 'root', //密码
     'DB_PREFIX' => 'wifi_', //数据库表前缀
	'DB_FIELDTYPE_CHECK' => 'true'
    );