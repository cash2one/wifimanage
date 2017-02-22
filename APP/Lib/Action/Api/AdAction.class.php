<?php
class AdAction extends BaseApiAction
{
	/**
	 * 广告页首页
	 * 获取ad表的图片地址以及对应的url
	 * $apmac ap的mac地址
	 */
	public function index()
	{
		if(!session('apmac')){
			$apmac = I('apmac');
			session('apmac',$apmac);
		}
		$where['advertise.name'] = 'Banner';
		$this->getList($where);
	}

	/**
	 * 游戏列表
	 * $apmac ap的mac地址
	 */
	public function gameList()
	{
		$where['advertise.name'] = '游戏列表';
		$this->getList($where);
	}

	/**
	 * app列表
	 */
	public function appList()
	{
		$where['advertise.name'] = 'app列表';
		$this->getList($where);
	}

	/**
	 * 视频列表
	 */
	public function videoList()
	{
		$where['advertise.name'] = '视频列表';
		$this->getList($where);
	}

	/**
	 * 列表详情
	 */
	public function listsInfo()
	{
		$lists = M('ad')->find(I('id'));

		$this->assign('lists',$lists);
		$this->display();
	}

	/**
	 * 点击广告时，点击数+1
	 */
	public function adHit()
	{

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
		$check = M('adcount')->where(array('aid'=>$aid,'add_hour'=>$hour,'add_date'=>$date))->find();
		


		# ---累计点击用户 文件缓存 --- #
		$this->filecache('adhit',$date,$hour);


		if(empty($check)){
			//新增数据

			$data['aid'] = $aid;
			$data['apid'] = 14;
			$data['add_time'] = $time;
			$data['add_date'] = $date;
			$data['add_hour'] = $hour;
			$data['ip'] = $ip;
			$data['browser'] = $browser;
			$data['uid'] = $uid;
			$data['mode'] = $mode;
			$data['hit'] = 1;	
			M('adcount')->add($data);
	


			# ---累计投放广告 文件缓存 --- #
			$this->filecache('adcount',$date,$hour);

	

			Header("Location:$url");
		}else{
			//增加点击次数1
			M('adcount')->where(array('id'=>$check['id']))->setInc('hit');


			Header("Location:$url");
		}
		//$this->ajaxReturn();
	}



	/**
	* 文件缓存
	* @param resultname 统计字段名称
	* @param date 日期
	* @param hour 时段
	**/
	private function filecache($resultname,$date,$hour){
		//新增一条投放记录
		$newArr = [];
		$newArr['date'] = $date;
		$newArr['hour'] = intval($hour);
		$newArr[$resultname] = 1;

		if (!is_dir('./filecache')) mkdir('./filecache',0700); //判断目录是否存在
		$filepath = './filecache/'.$resultname.'.txt';
		$exist = file_exists($filepath);
		//文件不存在
		if(!$exist){
			file_put_contents($filepath,'['.json_encode($newArr).']');	//单个数组要加[]
		}else{
			//文件已存在
			$data = file_get_contents($filepath);
			$dataArr = json_decode($data,true);
			$index = count($dataArr)-1;
			//累加时间段计数
			if(($dataArr[$index]['date'] == $newArr['date']) && ($dataArr[$index]['hour'] == $newArr['hour'])){
				$dataArr[$index][$resultname] += 1;  
			}else{
				//时间段最新一条记录
				$dataArr[] = $newArr;
			}
			
			//文件处理
			$str = json_encode($dataArr);
			$str = str_replace("},","},\r\n",$str);
			file_put_contents($filepath,$str);
			
		}
	}




	/**
	 * 获取数据列表
	 *$apmac 获取的apmac地址
	 * @param array $where 游戏/app/视频
	 * @return array $lists
	 */
	public function getList($where=null)
	{
		$portalInfo = session('portalInfo');
		$apmac = $portalInfo['apmac'];
		$name = $portalInfo['name'];
		$ip = get_client_ip();
		
		$show_limit = M('advertise')->getFieldByName($name,'show_limit');
		$limit = I('limit');
		if(empty($limit)){
			$limit = 20;
		}else{
			$limit += 20;
		}
		$this->assign('limit',$limit);

		$where['startdate'] = array('ELT',time());
		$where['enddate'] = array('EGT',time());
		if($apmac){
			$where['ad.default_status'] = 0;
			$lists = D('AdView')->field('aid,uid,ad_thumb,url,description,title,info,apid,`name`')->where($where)->where("left(gw_id,17) = '{$apmac}'")->order('ad_sort asc,aid asc,name asc,ad.add_time asc')->limit(0,$show_limit)->select();
		}
		if(empty($lists)){
			$where['ad.default_status'] = 1;
			$lists = M()->table('wifi_ad ad,wifi_advertise advertise')->field('ad.id as aid,ad_thumb,url,description,title,info,`name`')->where($where)->where("ad.ad_pos = advertise.id")->order('ad.ad_sort asc,aid asc,advertise.name asc,ad.add_time asc')->limit(0,$show_limit)->select();
		}
		
		$time = time();
		$date = date('Y-m-d');
		$hour = date('G');
		$browser = get_browser();
		$apid = $this->getApid($apmac);		
		$this->adcount($lists,$portalInfo,$apid,$ip,$time,$date,$hour,$browser);//统计页面浏览次数
		$this->assign('lists',$lists);

		//判断是否数据已全部加载完,如果没有则显示加载更多
		$count = count($lists);
		if($count > $limit){
			$more = 1;
			$this->assign('more',$more);
		}

		//点击加载更多
		if($_POST){
			$data['status'] = 0;
			$data['message'] = "加载中..";
			$data['lists'] = $lists;
			$data['limit'] = $limit;
			return $this->ajaxReturn($data);
		}

		$this->display();
	}


	/**
	 * 获取单条广告
	 *$apmac 获取的apmac地址
	 * @param array $where 游戏/app/视频
	 * @return array $lists
	 */
	public function getAD()
	{
		$adid = I('adid');
		$jsonpcallback = I('jsonpcallback');
		$adinfo = M("ad")->field("title,info")->find($adid);
		//点击加载更多
		if($adinfo){
			echo "$jsonpcallback(".json_encode($adinfo).")";
		}
	}


	/**
	 * portal页面接口
	 * $apmac apmac地址
	 * $ad_type 广告位名称
	 * $telephone 手机号码
	 * @return 返回json $lists
	 */
	public function portal(){
		
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
			$sql1 = "SELECT * FROM ( 
							SELECT 
								ad.id AS aid,
								ad.ad_thumb AS ad_thumb,
								ad.url AS url,
								ad.description AS description,
								ad.title AS title,
								ad.info AS info,
								ad.ad_sort AS ad_sort,
								routemap_ad.apid AS apid 
							FROM 
								wifi_ad ad  
							JOIN 
								wifi_advertise advertise 
							ON ad.ad_pos = advertise.id  
							JOIN 
								wifi_routemap_ad routemap_ad 
							ON 
								ad.id = routemap_ad.adid  
							JOIN 
								wifi_routemap routemap 
							ON 
								routemap_ad.apid = routemap.id 
							WHERE 
								( `startdate` <= $time ) 
							AND 
								( `enddate` >= $time ) 
							AND 
								( ad.default_status = 0 ) 
							AND 
								( LEFT(routemap.gw_id,17) = '$apmac' ) 
							AND 
								( advertise.name = '$name' ) 
							ORDER BY 
								ad.ad_sort ASC,ad.id DESC,ad.add_time DESC 
							LIMIT 
								0,$limit) a 
							GROUP BY 
								ad_sort 
							ORDER BY ad_sort ASC";


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

			$sql = "SELECT *
							FROM
								(
									SELECT
										ad.id AS aid,
										ad.uid AS uid,
										ad. MODE AS MODE,
										ad.ad_sort AS ad_sort,
										`ad_thumb`,
										`url`,
										`description`,
										`title`,
										`info`,
										`name`
									FROM
										wifi_ad ad,
										wifi_advertise advertise
									WHERE
										(`name` = '$name')
									AND (`startdate` <= $time)
									AND (`enddate` >= $time)
									AND (ad.default_status = 1)
									AND (ad.ad_pos = advertise.id)
									ORDER BY
										ad.ad_sort ASC,
										aid DESC,
										ad.add_time DESC
									LIMIT 0,
									$limit
								) a
							GROUP BY
								ad_sort
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
	 * 用户上网，通过apmac查找广告列表
	 */

	public function getAdList(){
		$apmac = I('get.apmac','','trim');
		if(empty($apmac)) $this->ajaxReturn('');
		



		$map['gw_id'] = $apmac;
		$apid = M('Routemap')->where($map)->getField('id');
		if(empty($apid)) $this->ajaxReturn('');


		#--- 查询静态文件数据
		$fileData = file_get_contents('./files/mac_ad/'.$apid);
		if(empty($fileData)) $this->ajaxReturn([]);
		$time = time();
		$fileData = json_decode($fileData,true);
		foreach ($fileData as $key => $value) {
			if($time < $value['enddate']){
				$list[] = $fileData[$key];
			}
		}
		$this->ajaxReturn($list);
		exit;
		#!---



		$time = time();
		$adMap['map_ad.apid'] = $apid;
		$adMap['ad.startdate'] = array('ELT',$time);
		$adMap['ad.enddate'] = array('GT',$time);
		$list = M('Ad')->alias('ad')
					->distinct(true)
					->field('ad.id AS aid,uid,ad.mode AS MODE,ad_sort,ad.ad_thumb,ad.url,ad.description,ad.title,ad.info,ad_pos.name')
					->join('LEFT JOIN wifi_routemap_ad map_ad ON map_ad.adid=ad.id')
					->join('LEFT JOIN wifi_advertise ad_pos ON ad_pos.id=ad.ad_pos')
					->where($adMap)
					->select();

		if(empty($list)) $this->ajaxReturn('');


		$this->ajaxReturn($list);

	}



	function insertMuti($listarr){			
		date_default_timezone_set ("Asia/beijing");
		$time = date('YmdGi');
		//(1,'admin','21232f297a57a5a743894a0e4a801fc3',0,NULL,'1389750196','1440666262',1,'183.250.219.160','1458531254'),
	}
	
	
	public function getApid($apid=null){
		if(!isset($apid)){
			$portalInfo = session('portalInfo');
			$apid = M('routemap')->getFieldByGw_id($portalInfo['apmac'],'id');
		}
		return empty($apid)?0:$apid;
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
			
			# ---累计浏览用户 文件缓存 --- #
			$this->filecache('adshowup',$date,$hour);


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