<?php
class AdnewAction extends BaseApiAction
{

	/**
	 * 点击广告时，点击数+1
	 */
	public function adHit()
	{
		$portalInfo = I();
		$portalInfo = session('portalInfo');
		$apmac = $portalInfo['apmac'];
		$apid = $this->getApid($apmac);
		$time = time();
		$date = date('Y-m-d');
		$hour = date('G');
		$browser = get_browser();
		$ip = get_client_ip();	

		$url = urldecode(I('url'));
		$uid = I('uid');
		$mode = I('mode');
		$addtime = I('addtime');
		$result = M('ad')->where("add_time='$addtime'")->find();
		$aid = $result['id'];
		
		//查询是否已经存在记录，没有则新增，有则增加次数
		$check = M('adcount')->where(array('apid'=>$apid,'aid'=>$aid,'add_hour'=>$hour))->find();
		if(empty($check)){
			//新增数据
			$data['aid'] = $aid;
			$data['apid'] = $apid;
			$data['add_time'] = $time;
			$data['add_date'] = $date;
			$data['add_hour'] = $hour;
			$data['ip'] = $ip;
			$data['browser'] = $browser;
			$data['uid'] = $uid;
			$data['mode'] = $mode;	
			M('adcount')->add($data);
		}else{
			//增加点击次数1
			M('adcount')->where(array('id'=>$check['id']))->setInc('hit');
			Header("Location:$url");
		}
		//$this->ajaxReturn();
	}

	/**
	 * portal页面接口
	 * $apmac apmac地址
	 * $ad_type 广告位名称
	 * $telephone 手机号码
	 * @return 返回json $lists
	 */
	public function portal()
	{
		$con = mysql_connect("localhost","root","112516");
	    if (!$con){
	       die('Could not connect: ' . mysql_error());
	    }
     	mysql_select_db("wifi", $con);


		$portalInfo = I();
		session('portalInfo',$portalInfo);
		$apmac = I('apmac');
		$name = I('name');
		$ip = get_client_ip();
		mysql_query("set names utf8",$con);
		
		$sql = "SELECT  `show_limit` FROM  `wifi_advertise` WHERE(`name` = '$name')";
		$result = mysql_query($sql,$con);
		$row = mysql_fetch_row($result,MYSQL_ASSOC);
		$show_limit = $row['show_limit'];
		$limit = $show_limit+1;
		$time = time();
		if($apmac){
			$sql1 = "SELECT * FROM ( SELECT ad.id AS aid,ad.ad_thumb AS ad_thumb,ad.url AS url,ad.description AS description,ad.title AS title,ad.info AS info,ad.ad_sort AS ad_sort,routemap_ad.apid AS apid FROM wifi_ad ad  JOIN wifi_advertise advertise ON ad.ad_pos = advertise.id  JOIN wifi_routemap_ad routemap_ad ON ad.id = routemap_ad.adid  JOIN wifi_routemap routemap ON routemap_ad.apid = routemap.id WHERE ( `startdate` <= $time ) AND ( `enddate` >= $time ) AND ( ad.default_status = 0 ) AND ( LEFT(routemap.gw_id,17) = '$apmac' ) AND ( advertise.name = '$name' ) ORDER BY ad.ad_sort ASC,ad.id DESC,ad.add_time DESC LIMIT 0,$limit) a GROUP BY ad_sort ORDER BY ad_sort ASC
";
     		$result1 = mysql_query($sql1,$con);
     		while($row = mysql_fetch_array($result1,MYSQL_ASSOC)){
     			/*$lists['aid'] = $row['aid'];
     			$lists['ad_thumb'] = $row['ad_thumb'];
     			$lists['url'] = $row['url'];
     			$lists['description'] = $row['description'];
     			$lists['title'] = $row['title'];
     			$lists['info'] = $row['info'];
     			$lists['ad_sort'] = $row['ad_sort'];
     			$lists['apid'] = $row['apid'];*/
     			$lists[] = $row;
     		}
     		
     		
		}
		
		if(empty($lists)){

			$sql = "SELECT * FROM ( SELECT ad.id AS aid,ad.uid AS uid,ad.mode AS MODE,ad.ad_sort AS ad_sort,`ad_thumb`,`url`,`description`,`title`,`info`,`name` FROM wifi_ad ad,wifi_advertise advertise WHERE ( `name` = '$name' ) AND ( `startdate` <= $time ) AND ( `enddate` >= $time ) AND ( ad.default_status = 1 ) AND ( ad.ad_pos = advertise.id ) ORDER BY ad.ad_sort ASC,aid DESC,ad.add_time DESC LIMIT 0,$limit) a GROUP BY ad_sort ;
";
     		$result = mysql_query($sql,$con);
     		while($row = mysql_fetch_array($result,MYSQL_ASSOC)){
     			/*$lists['aid'] = $row['aid'];
     			$lists['uid'] = $row['uid'];
     			$lists['MODE'] = $row['MODE'];
     			$lists['url'] = $row['url'];
     			$lists['description'] = $row['description'];
     			$lists['title'] = $row['title'];
     			$lists['info'] = $row['info'];
     			$lists['ad_sort'] = $row['ad_sort'];
     			$lists['ad_thumb'] = $row['ad_thumb'];
     			$lists['name'] = $row['name'];*/
     			$lists[] = $row;
     		}
		}
		
		$time = time();
		$date = date('Y-m-d');
		$hour = date('G');
		$browser = get_browser();

		$sql = "SELECT `id` FROM `wifi_routemap` WHERE ( `gw_id` = '$apmac' )";
     	$result = mysql_query($sql,$con);
     	$row = mysql_fetch_row($result,MYSQL_ASSOC);
		$apid = $row['id'];
		$this->adcount($lists,$portalInfo,$apid,$ip,$time,$date,$hour,$browser);
		//$this->adHit($lists,$portalInfo,$apid,$ip,$time,$date,$hour,$browser);
			
		$jsonpcallback = I('jsonpcallback');
		
		echo "$jsonpcallback(".json_encode($lists).")";
	}

	/**
	 * 记录广告浏览详情和次数
	 * @param array $lists
	 */
	public function adcount($lists,$portalInfo,$apid,$ip,$time,$date,$hour,$browser){
		$con = mysql_connect("localhost","root","112516");
	    if (!$con){
	       die('Could not connect: ' . mysql_error());
	    }
     	mysql_select_db("wifi", $con);
     	mysql_query("set names utf8",$con);
		foreach($lists as $value){

			$adid = $value['aid'];
			$apid = $apid;
			$userip = $portalInfo['userip'];
			$apmac = $portalInfo['apmac'];
			$acname = $portalInfo['acname'];
			$nasid = $portalInfo['nasid'];
			$tel_version = $portalInfo['tel_version'];
			//$username = $portalInfo['telephone'];
			$usermac = $portalInfo['usermac'];
			$add_time = $time;
			$add_date = $date;
			$sql = "INSERT INTO `wifi_adrecode` (`adid`,`apid`,`userip`,`apmac`,`acname`,`nasid`,`tel_version`,`usermac`,`add_time`,`add_date`) VALUES ($adid,$apid,'$userip','$apmac','$acname','$nasid','$tel_version','$usermac',$add_time,'$add_date')";
			mysql_query($sql,$con);
			
			//查询是否已经存在记录，没有则新增，有则增加次数
			$sql1 = "SELECT * FROM `wifi_adcount` WHERE ( `apid` = $apid ) AND ( `aid` = $adid ) AND ( `add_hour` = $hour ) AND (`add_date` = '$date')";
			$result1 = mysql_query($sql1,$con);
			
			$check = mysql_fetch_array($result1,MYSQL_ASSOC);
			if(!$check){

				$aid = $value['aid'];
				$apid = $apid;
				$add_time = $time;
				$add_date = $date;
				$add_hour = $hour;
				$ip = $ip;
				$browser = $browser;
				$uid = $value['uid'];
				$mode = $value['MODE'];
				$sql = "INSERT INTO `wifi_adcount` (`aid`,`apid`,`add_time`,`add_date`,`add_hour`,`ip`,`browser`,`uid`,`mode`) VALUES ($aid,$apid,$add_time,'$add_date',$add_hour,'$ip','$browser',$uid,$mode)";
				mysql_query($sql,$con);

			}else{
				//增加浏览次数1
				$id = $check['id'];
				$sql = "UPDATE `wifi_adcount` SET `showup`=showup+1 WHERE ( `id` = $id )";
				mysql_query($sql,$con);
				
			}
			
		}
			mysql_close($con);
	}
}