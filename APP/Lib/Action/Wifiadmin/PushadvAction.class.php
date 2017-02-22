<?php
class PushadvAction extends AdminAction{
protected function _initialize(){
		parent::_initialize();
		$this->doLoadID(800);
	}

	public function index(){
		import('@.ORG.AdminPage');
		$db = D('ad');
		$pos = D('Advertise');
		$shop = D('shop');
		if(IS_POST){
			//$where['city'] = $_POST['city'];
			$where['area'] = $_POST['area'];
			//$where['province'] = $_POST['province'];
			$where['shopname'] = array('like','%'.$_POST['shopname'].'%');
			//$map['name'] = I('post.shopname');
			$where['trade'] = array('like','%'.$_POST['trade'].'%');
			if($where['city']=='市辖区' && $where['area']=='东城区' && $where['province']=='北京市'){
				unset($where['city']);
				unset($where['area']);
				unset($where['province']);
			}
			if(empty($_POST['area'] ))
			{
				unset($where['area']);
			}
			if(empty($_POST['trade'] ))
			{
				unset($where['trade']);
			}
			if(empty($_POST['shopname'] ))
			{
				unset($where['shopname']);
			}
			$_GET['p']=0;
		}

		if(IS_POST && $where){
			session('add',$where);
		}elseif(IS_POST && empty($where) && $_GET['p'] > 0){
			$where = session('add');
		}
		//$rs=$shop->table('wifi_shop,wifi_routemap')->where($where)->where('wifi_shop.id = wifi_routemap.shopid')->count();
		$rs = M('routemap')->where($where)->count();
		$page=new AdminPage($rs,20);
		/*
		foreach($map as $k=>$v){
			$page->parameter.=" $k=".urlencode($v)."&";//赋值给Page";
		}
		$this->assign('map',$map['name']);
		*/
		$this->assign('page',$page->show());

		//$result = $shop->table('wifi_shop,wifi_routemap')->where($where)->where('wifi_shop.id = wifi_routemap.shopid')->order('wifi_shop.id asc')->limit($page->firstRow,$page->listRows)->select();
		$result = M('routemap')->where($where)->order('id asc')->limit($page->firstRow,$page->listRows)->select();
		$this->assign('lists',$result);
		//echo M('routemap')->getlastsql();
		//print_r($result);
		//exit;
		$trade = M('trade')->select();
		$this->assign('trade',$trade);

		$poslist = $pos->order('id asc')->select();
		$this->assign('pos',$poslist);

		$chkAduser = M('role')->where("id = '".session('roleid')."'")->find();
		if($chkAduser['id'] == 2){
			$ad_where['uid'] = session('adminid');
		}
		$ad_where['ad_pos'] = $poslist[0]['id'];
		$ad_where['default_status'] = 0;
		$adlist = $db->where($ad_where)->order('title asc')->select();

		$this->assign('adlist',$adlist);
		$this->assign('area',$_POST['area']);
		$this->assign('seltrade',$_POST['trade']);

		$this->display();
	}

	/**
	 * 广告推送到ap
	 */
	public function push(){
		if(IS_POST){
			$adid = I('post.adid');
			$apid = I('post.apid');
			$ad_pos=I('post.ad_pos');
			$time=time();
			if(empty($apid)){
				$apid = M('routemap')->field('id')->where(session('add'))->order('id asc')->select();
			}



			#--- 新增静态广告文件（对应连接apmac用户数据传输） ---
			//查询广告信息
			$adMap['id'] = $adid;
			$adInfo= M('Ad')->field('id AS aid,
								ad_thumb AS ad_thumb,
								url AS url,
								description AS description,
								title AS title,
								info AS info,
								ad_sort AS ad_sort,
								enddate')->where($adMap)->find();
			$jsonData = '['.json_encode($adInfo).']';

		

			//查询目录
			$files_path = './files';
			$mac_ad_path = './files/mac_ad';
			if(!is_dir($files_path)){
				mkdir($files_path,0777,true);
				mkdir($mac_ad_path,0777,true);
			}else if(!is_dir($mac_ad_path)){
				mkdir($mac_ad_path,0777,true);
			}

			
			//查询文件是否已存在（已有其他位置广告）
			foreach($apid as $val){

				if(is_array($val)){
					$ap_id = $val['id'];
				}else{
					$ap_id = $val;
					
				}
					
				$filename = $mac_ad_path.'/'.$ap_id;
				$fileData = file_get_contents($filename);


				//文件有数据时候，判断是否广告已新增
				if(!empty($fileData)){
					$fileData = json_decode($fileData,true);
					foreach ($fileData as $key => $value) {
						if($value['aid'] == $adid){
							$flag = 1;
							break;
						}
						
					}

					if($flag != 1) $fileData[] = $adInfo; //新的广告位
					$jsonData = json_encode($fileData);
				};
				
			
				file_put_contents($filename,$jsonData);

			

			}
		
			



		

			foreach($apid as $val){

				$data['apid'] = is_array($val)?$val['id']:$val;
				$data['adid'] = $adid;
				$data['ad_pos'] = $ad_pos;
				$data['times'] = $time;
				$result = M('routemap_ad')->add($data);
			}
			//file_put_contents("push.txt",M('routemap_ad')->getlastsql());			
			if($result !== false){
				$ajax['error'] = 0;
				$ajax['msg'] = "推送成功";
				$this->ajaxReturn($ajax);
			}else{
				$ajax['error'] = 1;
				$ajax['msg'] = "推送失败";
				$this->ajaxReturn($ajax);
			}
		}
	}

	/**
	 * 广告下拉列表
	 */
	public function adselect(){
		$where['ad_pos'] = I('post.advid');
		$adlist = M('ad')->field('id,title')->where($where)->order('title asc')->select();
		if($adlist){
			$ajax['error'] = 0;
			$ajax['msg'] = $adlist;
			$this->ajaxReturn($ajax);
		}else{
			$ajax['error'] = 1;
			$ajax['msg'] = "获取失败";
			$this->ajaxReturn($ajax);
		}
	}

	public function add(){
		import('@.ORG.AdminPage');
		$db = D('ad');
		$pos = D('Advertise');
		$shop = D('shop');
		if(IS_POST){
			$where['city'] = $_POST['city'];
			$where['area'] = $_POST['area'];
			$where['province'] = $_POST['province'];
			$where['shopname'] = array('like','%'.$_POST['shopname'].'%');
			$map['name'] = I('post.shopname');
			$where['trade'] = array('like','%'.$_POST['trade'].'%');
			if($where['city']=='市辖区' && $where['area']=='东城区' && $where['province']=='北京市'){
				unset($where['city']);
				unset($where['area']);
				unset($where['province']);
			}
			if(empty($_POST['trade'] ))
			{
				unset($where['trade']);
			}
			if(empty($_POST['shopname'] ))
			{
				unset($where['shopname']);
			}
			$_GET['p']=0;
		}

		if(IS_POST && $_POST['search']){
			session('add',$where);
		}elseif(IS_POST && empty($_POST['search'])){
			$where = session('add');
		}
		$rs=$shop->table('wifi_shop,wifi_routemap')->where($where)->where('wifi_shop.id = wifi_routemap.shopid')->count();
		$page=new AdminPage($rs,5);
		foreach($map as $k=>$v){
			$page->parameter.=" $k=".urlencode($v)."&";//赋值给Page";
		}

		$result = $shop->table('wifi_shop,wifi_routemap')->where($where)->where('wifi_shop.id = wifi_routemap.shopid')->order('wifi_shop.id asc')->limit($page->firstRow,$page->listRows)->select();
		if(IS_POST && empty($_POST['search'])){
			//$data['update_time'] = time();
			foreach($_POST['sid'] as $val){
				$shop1.=$val.",";
			}
			$data['shop_pos'] = $_POST['ad_pos'];
			$swhere['id'] = array('in',$shop1);
			$shop->where($swhere)->save($data);
		}
		//echo $shop->getLastSql();
		include CONF_PATH.'enum/enum.php';//行业
		$poslist = $pos->select();
		$adlist = $db->where('ad_pos='.$poslist[0]['id'])->select();
		$this->assign('pos',$poslist);
		$this->assign('page',$page->show());
		$this->assign('lists',$result);
		$this->assign('map',$map['name']);
		$this->assign('adlist',$adlist);
		$this->assign('enumdata',$enumdata);
		$this->display();
	}
	
	public function set(){
		if(IS_POST){
			$wt=$_POST['WAITSECONDS'];
			if(!isNumber($wt)){
				$this->error("广告展示时间以秒为单位,请输入展示的时间");
			}
			if($wt<3){
				$this->error("最低展示时间不能小于3秒");
			}
			$this->configsave();
		}else{
			$this->display();
		}
	}

	public function jl(){
		import('@.ORG.AdminPage');
		$model=M();
		//$sql='SELECT * FROM `wifi_ad` a,`wifi_routemap_ad` b,`wifi_routemap` c WHERE(b.`adid`=a.`id` AND b.`apid`=c.`id`)';
		//$result=$model->query($sql);
		//$result =$model->table("wifi_ad a,wifi_routemap_ad b,wifi_routemap c")->where("b.adid=a.id AND b.apid=c.id")->select();
		$count =$model->table("wifi_ad a,wifi_routemap_ad b,wifi_routemap c")->where("b.adid=a.id AND b.apid=c.id")->count();
		$page=new AdminPage($count,20);
		$result =$model->field("DISTINCT(b.`times`),b.`ad_pos`,c.`area`,c.`trade`,a.`title`")->table("wifi_ad a,wifi_routemap_ad b,wifi_routemap c")->where("b.ad_pos IS NOT NULL AND b.times IS NOT NULL AND b.adid=a.id AND b.apid=c.id")->limit($page->firstRow,$page->listRows)->GROUP("b.times")->select();
		$this->assign('result',$result);
		$this->assign('page',$page->show());
		$this->display();
}

	public function xx(){
		import('@.ORG.AdminPage');
		$time=$_GET['time'];
		$model=M();
		$count =$model->table("wifi_ad a,wifi_routemap_ad b,wifi_routemap c")
					->where("b.adid=a.id AND b.apid=c.id AND times='$time'")->count();
		$page=new AdminPage($count,20);
		$result =$model->table("wifi_ad a,wifi_routemap_ad b,wifi_routemap c")
				->where("b.ad_pos IS NOT NULL AND b.times IS NOT NULL AND b.adid=a.id AND b.apid=c.id AND times='$time'")
				->limit($page->firstRow,$page->listRows)->select();
		$this->assign('result',$result);
		$this->assign('page',$page->show());
		$this->display();
}
	private  function configsave(){
	
		
		$act=$this->_post('action');
		unset($_POST['files']);
		unset($_POST['action']);
		unset($_POST[C('TOKEN_NAME')]);
	
		if(update_config($_POST,CONF_PATH."adv.php")){
	
			$this->success('操作成功',U('Pushadv/'.$act));
	
		}else{
	
			$this->success('操作失败',U('Pushadv/'.$act));
	
		}
	
	}
	public function rpt(){
		$way=I('get.mode');
			if(!empty($way)){
				$this->getadrpt();
				exit;
			}
			$this->display();
	}
	private  function getadrpt(){
    	$way=I('get.mode');
    	$where=" where shopid=".session('uid');
    	switch(strtolower($way)){
    		case "today":
    			$sql=" select t,CONCAT(CURDATE(),' ',t,'点') as showdate, COALESCE(showup,0)  as showup, COALESCE(hit,0)  as hit,COALESCE(hit/showup*100,0) as rt,trade,`area` from ".C('DB_PREFIX')."hours a left JOIN ";
				$sql.="(select thour, sum(showup)as showup,sum(hit) as hit,  ";
				$sql.="sum(case when mode=99 then showup else 0 end) as pshowup, ";
				$sql.="sum(case when mode=50 then showup else 0 end) as ashowup, ";
				$sql.="sum(case when mode=99 then hit else 0 end) as phit, ";
				$sql.="sum(case when mode=99 then showup else 0 end) as ahit,trade,`area` from";
				$sql.="  (select  FROM_UNIXTIME(aa.add_time,\"%H\") as thour,showup ,hit,aa.mode,trade,area from ".C('DB_PREFIX')."adcount aa,".C('DB_PREFIX')."shop bb";
				$sql.=" where aa.shopid = bb.id and add_date>='".strtotime(date("Y-m-d"))."' and (aa.mode=99 or aa.mode=50) ";
				$sql.=" )a group by thour ) c ";
				$sql.="  on a.t=c.thour ";

    			break;
    		case "yestoday":
    			
    			$sql=" select t,CONCAT(CURDATE(),' ',t,'点') as showdate, COALESCE(showup,0)  as showup, COALESCE(hit,0)  as hit,COALESCE(hit/showup*100,0) as rt,trade,`area` from ".C('DB_PREFIX')."hours a left JOIN ";
				$sql.="(select thour, sum(showup)as showup,sum(hit) as hit,  ";
				$sql.="sum(case when mode=99 then showup else 0 end) as pshowup, ";
				$sql.="sum(case when mode=50 then showup else 0 end) as ashowup, ";
				$sql.="sum(case when mode=99 then hit else 0 end) as phit, ";
				$sql.="sum(case when mode=99 then showup else 0 end) as ahit,sum(trade) as trade,sum(area) as`area` from";
				$sql.="(select  FROM_UNIXTIME(aa.add_time,\"%H\") as thour,showup ,hit,trade,area from ".C('DB_PREFIX')."adcount aa,".C('DB_PREFIX')."shop bb where aa.shopid = bb.id";
				$sql.=" where aa.add_date=DATE_ADD(CURDATE() ,INTERVAL -1 DAY) and (aa.mode=99 or aa.mode=50) ";
				$sql.=" )a group by thour ) c ";
				$sql.="  on a.t=c.thour ";

    			break;
    		case "week":
    			$sql="  select td as showdate,right(td,5) as td,datediff(td,CURDATE()) as t, COALESCE(showup,0)  as showup, COALESCE(hit,0)  as hit ,COALESCE(hit/showup*100,0) as rt,trade,`area` from ";
    			$sql.=" ( select CURDATE() as td ";
    			for($i=1;$i<7;$i++){
    				$sql.="  UNION all select DATE_ADD(CURDATE() ,INTERVAL -$i DAY) ";
    			}
    			$sql.=" ORDER BY td ) a left join ";
    			$sql.="( select add_date,sum(showup) as showup ,sum(hit) as hit ,sum(trade) as trade,sum(area) as area from ".C('DB_PREFIX')."adcount aa,".C('DB_PREFIX')."shop bb where aa.shopid = bb.id";
				$sql.=" where   aa.add_date between DATE_ADD(CURDATE() ,INTERVAL -6 DAY) and CURDATE() and (aa.mode=99 or aa.mode=50) GROUP BY  add_date";
				$sql.=" ) b on a.td=b.add_date ";
				
    			break;
    		case "month":
    			$t=date("t");
    			$sql=" select tname as showdate,tname as t, COALESCE(showup,0)  as showup, COALESCE(hit,0)  as hit,COALESCE(hit/showup*100,0) as rt,trade,`area` from ".C('DB_PREFIX')."day  a left JOIN";
				$sql.="( select right(add_date,2) as td ,sum(showup) as showup ,sum(hit) as hit,sum(trade) as trade,sum(area) as area from ".C('DB_PREFIX')."adcount aa,".C('DB_PREFIX')."shop bb where aa.shopid = bb.id";
				$sql.=" where   aa.add_date >= '".date("Y-m-01")."' and (aa.mode=99 or aa.mode=50) GROUP BY  add_date";
				$sql.=" ) b on a.tname=b.td ";
				$sql.=" where a.id between 1 and  $t";
		
    			break;
    		case "query":
    			$sdate=I('get.sdate');
    			$edate=I('get.edate');
    			import("ORG.Util.Date");
    			//$sdt=Date("Y-M-d",$sdate);
    			//$edt=Date("Y-M-d",$edate);
    			$dt=new Date($sdate);
    			$leftday=$dt->dateDiff($edate,'d');
    			$sql=" select td as showdate,right(td,5) as td,datediff(td,CURDATE()) as t,COALESCE(showup,0)  as showup, COALESCE(hit,0)  as hit,COALESCE(hit/showup*100,0) as rt,trade,`area` from ";
    			$sql.=" ( select '$sdate' as td ";
    			for($i=0;$i<=$leftday;$i++){
    				$sql.="  UNION all select DATE_ADD('$sdate' ,INTERVAL $i DAY) ";
    			}
    			$sql.=" ) a left join ";
    			
    
				$sql.="( select add_date,sum(showup) as showup ,sum(hit) as hit,sum(trade) as trade,sum(area) as area from ".C('DB_PREFIX')."adcount aa,".C('DB_PREFIX')."shop bb where aa.shopid = bb.id";
				$sql.=" where  aa.add_date between '$sdate' and '$edate'  and (aa.mode=99 or aa.mode=50) GROUP BY  add_date";
				$sql.=" ) b on a.td=b.add_date ";

			
    			break;
    	}
    	
    	$db=D('Adcount');
    	$rs=$db->query($sql);
		//file_put_contents('sql.txt',"sql".$sql);
    	$this->ajaxReturn(json_encode($rs));
    }
	
	
}