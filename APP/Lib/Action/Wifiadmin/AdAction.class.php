<?php
class AdAction extends AdminAction{
	protected function _initialize(){
		parent::_initialize();
		$this->doLoadID(500);
	}

	public function index(){
		import('@.ORG.AdminPage');

		$db=D('Ad');

		if(IS_POST){
			$sname = $_POST['sname'];
			if(isset($sname) && !empty($sname)){
				// $result = M('Advertise')->field('name')->where("id='$sname'")->find();
				// $where.=" and c.name='{$result['name']}'";
				$where = "a.ad_pos={$sname}";
				$this->assign('sname',$sname);
			}
			if(isset($_POST['sstartdate']) && $_POST['sstartdate']!=""){
				$map['startdate'] = strtotime($_POST['sstartdate']);
				$where .= " and a.startdate >= '{$map['startdate']}'";
				$this->assign('startdate',date('Y-m-d',$map['startdate']));
			}
			if(isset($_POST['senddate'])&&$_POST['senddate']!=""){
				$map['enddate']=strtotime($_POST['senddate']);
				$where .= " and a.enddate <= '{$map['enddate']}'";
				$this->assign('senddate',date('Y-m-d',$map['enddate']));
			}
			$pos = M('Advertise');
			$poslist = $pos->select();
			$this->assign('pos',$poslist);
			$_GET['p']=0;
		}else{
			if(isset($_GET['sname']) && !empty($_GET['sname'])){
				$where = "a.ad_pos={$sname}";
				$this->assign('sname',$sname);
			}
			if(isset($_GET['sstartdate']) && $_GET['sstartdate']!= ""){
				$map['startdate']=strtotime($_GET['sstartdate']);
				$where.=" and a.startdate >= '{$map['startdate']}'";
			}
			if(isset($_GET['senddate']) && $_GET['senddate']!=""){
				$map['enddate']=strtotime($_GET['senddate']);
				$where.=" and a.enddate <= '{$map['enddate']}'";
			}
			if(isset($_GET['advid']) && $_GET['advid']!=""){
				$map['ad_pos']=$_GET['advid'];
				$where.=" and a.ad_pos = %d";
			}
			
			$pos = M('Advertise');
			$poslist = $pos->select();
			$this->assign('pos',$poslist);
		}

	
		$chkAduser = M('role')->where("id = '".session('roleid')."'")->find();
		if($chkAduser['id'] == 2){
			$map['f.id']=session('adminid');
			$where.=" and f.id = %d";
		}


		if(!empty($where)){
			$where = 'WHERE '.$where;
		}


		$prefix = C('DB_PREFIX');
	
		$count_sql = "SELECT  COUNT(a.id) AS count FROM ".$prefix."ad a $where LIMIT 0,1";

	
	
		$rs = $db->query($count_sql,$map);

		$count = $rs[0]['count'];



		$page = new AdminPage($count,C('ADMINPAGE'));
		foreach($map as $k=>$v){
			$page->parameter.=" $k=".urlencode($v)."&";//赋值给Page";
		}

	

		$sql = "SELECT DISTINCT 
					a.id,a.title,a.default_status, 
					a.ad_pos,a.ad_thumb,a.ad_sort,a.mode,
					a.add_time,a.update_time,a.startdate,
					a.enddate,
					-- d.routename,d.trade,d.area,d.gw_id,
					-- c.name,
					f.user 
				FROM 
					{$prefix}ad a 
				-- LEFT JOIN 
				-- 	{$prefix}advertise c ON a.ad_pos=c.id 
				-- LEFT JOIN 
				-- 	{$prefix}routemap_ad b ON a.id=b.adid 
				-- LEFT JOIN 
				-- 	{$prefix}routemap d ON b.apid=d.id
				LEFT JOIN 
					{$prefix}admin f ON a.uid = f.id 
				$where
				ORDER BY 
					a.id DESC 
				LIMIT 
					".$page->firstRow.','.$page->listRows;
		
	
		$result = $db->query($sql,$map);

	

		$this->assign('page',$page->show());
		$this->assign('lists',$result);
		$this->display();
	}

	public function online(){
		import('@.ORG.AdminPage');
		$db=D('Ad');
		$time = strtotime(date('Y-m-d',time()));
		$pos = M('Ad');
		$poslist = $pos->where("enddate>='$time'")->select();
		$count=count($poslist);
		$page=new AdminPage($count,C('ADMINPAGE'));
		$where = " where enddate>='$time'";
		$sql="select DISTINCT a.id,a.title,a.default_status, a.ad_pos,ad_thumb,ad_sort,a.mode,a.add_time,a.update_time,a.startdate,a.enddate from ".C('DB_PREFIX')."ad a";
		$sql.=$where;
		$sql.=" order by a.id desc limit ".$page->firstRow.','.$page->listRows;
		$result = $db->query($sql);
		file_put_contents('n.txt',$db->getLastSql());
		$this->assign('lists',$result);
		$this->assign('page',$page->show());
		$this->display();
	}

	
	public function addAd(){
		$db=M('Ad');
		if(IS_POST){
			$_POST['add_time'] = time();
			$_POST['startdate']=strtotime($_POST['startdate']);
			$_POST['enddate']=strtotime($_POST['enddate']);
			$uid = $_POST['uid'];
			$mode = $_POST['mode'];
			$ad_pos = $_POST['ad_pos'];
			$url = urlencode($_POST['url']);
			$addtime = $_POST['add_time'];
			$_POST['url'] = "http://112.5.16.66:8098/index.php/api/ad/adhit?url=$url&uid=$uid&addtime=$addtime&mode=$mode";
			import('ORG.Net.UploadFile');

			$upload             = new UploadFile();
			$upload->maxSize    = C('AD_SIZE');
			$upload->allowExts  = C('AD_IMGEXT');
			$upload->savePath   =  C('AD_SAVE');

			if (!is_null($_FILES['img']['name'])&& $_FILES['img']['name']!="") {

				if(!$upload->upload()) {
					$this->error($upload->getErrorMsg());
				}else{
					$info =  $upload->getUploadFileInfo();
					$_POST['ad_thumb'] = trim( $info[0]['savepath'],'.').$info[0]['savename'];
				}
			}

			if($db->create()){
				if($db->add()){
					$this->success("操作成功",U('index'));
				}else{
					$this->error("操作失败");
				}
			}else{
				$this->error($db->getError());
			}


		}else{
			$pos = D('Advertise');
			$shop = D('Shop');
			$poslist = $pos->select();
			$shoplist = $shop->select();

			$where['state&status'] = 1;
			$where['role.name'] = "广告主";
			$aduser= D('RoleView')->where($where)->select();
			if($aduser){
				$this->assign('aduser',$aduser);
			}
			$this->assign('pos',$poslist);
			$this->assign('shoplist',$shoplist);
			$this->display();
		}
	}

	public function oddrpt(){
		if(IS_POST){
			$way=$_POST['mode'];
			$aid = isset($_POST['id']) ? intval($_POST['id']) : 0;
				switch(strtolower($way)){
					case "today":
						$sql=" select t,CONCAT(CURDATE(),' ',t,'点') as showdate, COALESCE(showup,0)  as showup, COALESCE(hit,0)  as hit,COALESCE(hit/showup*100,0) as rt from ".C('DB_PREFIX')."hours a left JOIN ";
						$sql.="(select thour, sum(showup)as showup,sum(hit) as hit from ";
						$sql.="(select  FROM_UNIXTIME(add_time,\"%H\") as thour,showup ,hit from ".C('DB_PREFIX')."adcount";

						$sql.=" where add_date='".date("Y-m-d")."' and mode=1 and aid=".$aid;
						$sql.=" )a group by thour ) c ";
						$sql.="  on a.t=c.thour ";

						break;
					case "yestoday":

						$sql=" select t,CONCAT(DATE_ADD(CURDATE() ,INTERVAL -1 DAY),' ',t,'点') as showdate, COALESCE(showup,0)  as showup, COALESCE(hit,0)  as hit,COALESCE(hit/showup*100,0) as rt from ".C('DB_PREFIX')."hours a left JOIN ";
						$sql.="(select thour, sum(showup)as showup,sum(hit) as hit from ";
						$sql.="(select  FROM_UNIXTIME(add_time,\"%H\") as thour,showup ,hit from ".C('DB_PREFIX')."adcount";

						$sql.=" where add_date=DATE_ADD(CURDATE() ,INTERVAL -1 DAY) and mode=1 and aid=".$aid;
						$sql.=" )a group by thour ) c ";
						$sql.="  on a.t=c.thour ";

						break;
					case "week":
						$sql="  select td as showdate,right(td,5) as td,datediff(td,CURDATE()) as t, COALESCE(showup,0)  as showup, COALESCE(hit,0)  as hit ,COALESCE(hit/showup*100,0) as rt from ";
						$sql.=" ( select CURDATE() as td ";
						for($i=1;$i<7;$i++){
							$sql.="  UNION all select DATE_ADD(CURDATE() ,INTERVAL -$i DAY) ";
						}
						$sql.=" ORDER BY td ) a left join ";
						$sql.="( select add_date,sum(showup) as showup ,sum(hit) as hit from ".C('DB_PREFIX')."adcount";
						$sql.=" where   add_date between DATE_ADD(CURDATE() ,INTERVAL -6 DAY) and CURDATE() and mode=1 and aid=".$aid."  GROUP BY  add_date";
						$sql.=" ) b on a.td=b.add_date ";

						break;
					case "month":
						$t=date("t");
						$sql=" select tname as showdate,tname as t, COALESCE(showup,0)  as showup, COALESCE(hit,0)  as hit,COALESCE(hit/showup*100,0) as rt from ".C('DB_PREFIX')."day  a left JOIN";
						$sql.="( select right(add_date,2) as td ,sum(showup) as showup ,sum(hit) as hit  from ".C('DB_PREFIX')."adcount  ";
						$sql.=" where   add_date >= '".date("Y-m-01")."' and mode=1 and aid=".$aid." GROUP BY  add_date";
						$sql.=" ) b on a.tname=b.td ";
						$sql.=" where a.id between 1 and  $t";

						break;
					case "query":
						$sdate=$_POST['sdate'];
						$edate=$_POST['edate'];
						import("ORG.Util.Date");
						//$sdt=Date("Y-M-d",$sdate);
						//$edt=Date("Y-M-d",$edate);
						$dt=new Date($sdate);
						$leftday=$dt->dateDiff($edate,'d');
						$sql=" select td as showdate,right(td,5) as td,datediff(td,CURDATE()) as t,COALESCE(showup,0)  as showup, COALESCE(hit,0)  as hit,COALESCE(hit/showup*100,0) as rt from ";
						$sql.=" ( select '$sdate' as td ";
						for($i=0;$i<=$leftday;$i++){
							$sql.="  UNION all select DATE_ADD('$sdate' ,INTERVAL $i DAY) ";
						}
						$sql.=" ) a left join ";

						$sql.="( select add_date,sum(showup) as showup ,sum(hit) as hit  from ".C('DB_PREFIX')."adcount ";
						$sql.=" where  add_date between '$sdate' and '$edate'  and mode=1 and aid=".$aid ." GROUP BY  add_date";
						$sql.=" ) b on a.td=b.add_date";

						break;
				}

			$db=D('Adcount');
			$rs=$db->query($sql);
			$this->ajaxReturn(json_encode($rs));

		}else{
			$aid = isset($_GET['id']) ? intval($_GET['id']) : 0;

			$where['id']=$aid;
			$result = D('Ad')
				->where($where)
				->find();

			if($result){
				$this->assign('info',$result);
				$this->display();
			}else{
				$this->error('无此广告信息');
			}
		}
	}

	public function editad(){
		if(IS_POST){
			$id = I('post.id','0','int');
			$where['id']=$id;

			$db=D('Ad');
			$result =$db
				->where($where)
				->field('id')
				->find();
			if($result==false){
				$this->error('无此广告信息');
				exit;
			}

			import('ORG.Net.UploadFile');

			$upload             = new UploadFile();
			$upload->maxSize    = C('AD_SIZE');
			$upload->allowExts  = C('AD_IMGEXT');
			$upload->savePath   =  C('AD_SAVE');

			if (!is_null($_FILES['img']['name'])&& $_FILES['img']['name']!="") {

				if(!$upload->upload()) {
					$this->error($upload->getErrorMsg());
				}else{
					$info =  $upload->getUploadFileInfo();
					$_POST['ad_thumb'] = trim( $info[0]['savepath'],'.').$info[0]['savename'];
				}
			}
			$_POST['update_time']=time();
			//9/14 开始时间和结束时间
			$_POST['startdate']=strtotime($_POST['startdate']);
			$_POST['enddate']=strtotime($_POST['enddate']);

			if($result)
			{
				if($db->create()){
					if($db->where($where)->save()){

						$this->success('修改成功',U('index'));
					}else{
						$this->error('操作出错');
					}
				}else{
					$this->error($db->getError());
				}

			}
		}else{
			$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

			$where['id']=$id;
			$result = D('Ad')
				->where($where)
				->find();


			$pos = D('Advertise');
			$result1 = $pos->select();
			if($result){
				$this->assign('info',$result);
				$this->assign('pos',$result1);
				$this->display();
			}else{
				$this->error('无此广告信息');
			}

		}
	}

	public function delad(){
		$id = isset($_GET['id']) ? intval($_GET[id]) : 0;

		if($id)
		{
			$thumb = D('ad')->where("id={$id}")->field("ad_thumb")->select();
			if(D('ad')->delete($id))
			{
				if(file_exists( ".{$thumb[0]['ad_thumb']}"))
				{
					unlink(".{$thumb[0]['ad_thumb']}");
				}

				$this->success('删除成功',U('index'));
			}else{
				$this->error('操作出错');
			}
		}
	}
	public function rpt(){
		$way=I('get.mode');
		if(!empty($way)){
			$this->getadrpt();
			exit;
		}
		$tradelist = M('trade')->select();
		$this->assign('tradelist',$tradelist);

		$arealist = M('routemap')->field('area')->distinct(true)->select();
		$this->assign('arealist',$arealist);

		$aduserlist = M()->table('wifi_admin a,wifi_role_user b,wifi_role c')->field('a.id as aid,a.user as user')->where("a.id = b.user_id and b.role_id = c.id and c.id = 2")->select();
		$this->assign('aduserlist',$aduserlist);
		
		$nowtime = time();
		$adlist = M('ad')->where(" startdate < $nowtime and enddate > $nowtime")->select();
		$this->assign('adlist',$adlist);

		$this->display();
	}

	public function getadrpt(){

		$way=I('get.mode');
		//$where=" where shopid=".session('uid');
		$chkAduser = M('role')->where("id = '".session('roleid')."'")->find();
		if($chkAduser['id'] == 2){
			$where=" and f.id = '".session('adminid')."' ";
		}

		$area = trim(I('get.area'));
		$trade = trim(I('get.trade'));
		$aduser = trim(I('get.aduser'));
		$ad = trim(I('get.ad'));
		if($area){
			$swhere = " and (e.area = {$aduser}";
		}elseif($trade){
			$swhere = " and (e.trade = {$trade}";
		}elseif($aduser){
			$swhere = " and f.id = {$aduser}";
		}elseif($ad){
			$swhere = " and d.aid = {$ad}";
		}

		switch(strtolower($way)){
			case "today":
				$sql=" select t,CONCAT(CURDATE(),' ',t,'点') as showdate, COALESCE(showup,0)  as showup, COALESCE(count,0)  as count,COALESCE(hit,0)  as hit,COALESCE(hit/showup*100,0) as rt,trade,`area` from ".C('DB_PREFIX')."hours a left JOIN ";
				$sql.="(select thour, sum(showup)as showup,count(apid) as count,sum(hit) as hit,trade,`area` from ";
				$sql.="(select  d.add_hour as thour,showup,apid,hit,trade,`area` from ".C('DB_PREFIX')."adcount d,".C('DB_PREFIX')."routemap e,".C('DB_PREFIX')."admin f,".C('DB_PREFIX')."role_user g,".C('DB_PREFIX')."role h";

				$sql.=" where d.apid = e.id and d.uid = f.id and f.id = g.user_id and g.role_id = h.id and d.add_date='".date("Y-m-d")."'";
				$sql.= $where;
				$sql.= $swhere;
				$sql.=" )a group by thour ) c ";
				$sql.="  on a.t=c.thour ";

				break;
			case "yestoday":

				$sql=" select t,CONCAT(DATE_ADD(CURDATE() ,INTERVAL -1 DAY),' ',t,'点') as showdate, COALESCE(showup,0)  as showup,COALESCE(count,0)  as count, COALESCE(hit,0)  as hit,COALESCE(hit/showup*100,0) as rt,trade,`area` from ".C('DB_PREFIX')."hours a left JOIN ";
				$sql.="(select thour, sum(showup)as showup,count(apid) as count,sum(hit) as hit,trade,`area` from ";
				$sql.="(select  d.add_hour as thour,showup,apid,hit,trade,`area` from ".C('DB_PREFIX')."adcount d,".C('DB_PREFIX')."routemap e,".C('DB_PREFIX')."admin f,".C('DB_PREFIX')."role_user g,".C('DB_PREFIX')."role h";

				$sql.=" where d.apid = e.id and d.uid = f.id and f.id = g.user_id and g.role_id = h.id and d.add_date=DATE_ADD(CURDATE() ,INTERVAL -1 DAY) ";
				$sql.= $where;
				$sql.= $swhere;
				$sql.=" )a group by thour ) c ";
				$sql.="  on a.t=c.thour ";

				break;
			case "week":
				$sql="  select td as showdate,right(td,5) as td,datediff(td,CURDATE()) as t, COALESCE(showup,0)  as showup,COALESCE(count,0)  as count, COALESCE(hit,0)  as hit ,COALESCE(hit/showup*100,0) as rt,trade,area from ";
				$sql.=" ( select CURDATE() as td ";
				for($i=1;$i<7;$i++){
					$sql.="  UNION all select DATE_ADD(CURDATE() ,INTERVAL -$i DAY) ";
				}
				$sql.=" ORDER BY td ) a left join ";
				//$sql.="( select add_date,sum(showup) as showup ,sum(hit) as hit from ".C('DB_PREFIX')."adcount";
				$sql.="(select d.add_hour as thour,sum(showup) as showup ,count(apid) as count,sum(hit) as hit,trade,`area`,add_date from ".C('DB_PREFIX')."adcount d,".C('DB_PREFIX')."routemap e,".C('DB_PREFIX')."admin f,".C('DB_PREFIX')."role_user g,".C('DB_PREFIX')."role h";
				$sql.=" where  d.apid = e.id and d.uid = f.id and f.id = g.user_id and g.role_id = h.id and d.add_date between DATE_ADD(CURDATE() ,INTERVAL -6 DAY) and CURDATE() GROUP BY  d.add_date";
				$sql.= $where;
				$sql.= $swhere;
				$sql.=" ) b on a.td=b.add_date ";

				break;
			case "month":
				$t=date("t");
				$sql=" select tname as showdate,tname as t, COALESCE(showup,0)  as showup,COALESCE(count,0) as count,COALESCE(hit,0)  as hit,COALESCE(hit/showup*100,0) as rt,trade,`area` from ".C('DB_PREFIX')."day  a left JOIN";
				//$sql.="( select right(add_date,2) as td ,sum(showup) as showup ,sum(hit) as hit  from ".C('DB_PREFIX')."adcount  ";
				$sql.="(select right(d.add_date,2) as td ,sum(showup) as showup ,count(apid) as count,sum(hit) as hit,trade,`area` from ".C('DB_PREFIX')."adcount d,".C('DB_PREFIX')."routemap e,".C('DB_PREFIX')."admin f,".C('DB_PREFIX')."role_user g,".C('DB_PREFIX')."role h";
				$sql.=" where d.apid = e.id and d.uid = f.id and f.id = g.user_id and g.role_id = h.id and d.add_date >= '".date("Y-m-01")."' GROUP BY  d.add_date";
				$sql.= $where;
				$sql.= $swhere;
				$sql.=" ) b on a.tname=b.td ";
				$sql.=" where a.id between 1 and $t";

				break;
			case "query":
				$sdate=trim(I('get.sdate'));
				$edate=trim(I('get.edate'));
				import("ORG.Util.Date");
				//$sdt=Date("Y-M-d",$sdate);
				//$edt=Date("Y-M-d",$edate);
				$dt=new Date($sdate);
				$leftday=$dt->dateDiff($edate,'d');
				$sql=" select td as showdate,right(td,5) as td,datediff(td,CURDATE()) as t,COALESCE(showup,0) as showup,COALESCE(count,0)  as count, COALESCE(hit,0)  as hit,COALESCE(hit/showup*100,0) as rt,trade,`area` from ";
				$sql.=" ( select '$sdate' as td ";
				for($i=0;$i<=$leftday;$i++){
					$sql.="  UNION all select DATE_ADD('$sdate' ,INTERVAL $i DAY) ";
				}
				$sql.=" ) a left join ";

				//$sql.="( select add_date,sum(showup) as showup ,sum(hit) as hit,trade,area  from ".C('DB_PREFIX')."adcount ";
				$sql.="(select d.add_date,sum(showup) as showup ,count(apid) as count,sum(hit) as hit,trade,`area` from ".C('DB_PREFIX')."adcount d,".C('DB_PREFIX')."routemap e,".C('DB_PREFIX')."admin f,".C('DB_PREFIX')."role_user g,".C('DB_PREFIX')."role h";
				$sql.=" where d.apid = e.id and d.uid = f.id and f.id = g.user_id and g.role_id = h.id and d.add_date between '$sdate' and '$edate' ";
				$sql.= $where;
				$sql.= $swhere;
				$sql.=" GROUP BY d.add_date ) b on a.td=b.add_date GROUP BY a.td ";
				break;
		}

		$db=D('Adcount');
		$rs=$db->query($sql);
		//echo $swhere;
		//exit($ad);
		//echo $db->getLastSql();
		//file_put_contents('sql1.txt',$db->getLastSql(),1);
		$this->ajaxReturn(json_encode($rs));
	}

	/**
	 * 新增广告位
	 */
	public function addAdvertise(){
		$db=D('Advertise');
		$data['name'] = I('post.name');
		$data['remark'] = I('post.remark');
		$data['add_time'] = time();
		$data['show_limit'] = I('post.show_limit');

		if($db->where("name = {$data['name']}")->find()){
			$this->error('广告位名称不得重复');
		}

		if(IS_POST){
			if($db->create()){
				if($db->add($data)){
					$this->success("操作成功",U('addAdvertise'));
				}else{
					$this->error("操作失败");
				}
			}else{
				$this->error($db->getError());
			}


		}else{
			$this->display();
		}
	}

	/**
	 * 广告位列表
	 */
	/*
	public function advertise(){
		import('@.ORG.AdminPage');
		$db = D('Advertise');
		if(IS_POST){
			if(isset($_POST['sname'])&&$_POST['sname']!=""){
				$map['sname']=$_POST['sname'];
				$where.=" and name like '%%%s%%'";
			}
			if(isset($_POST['sremark'])&&$_POST['sremark']!=""){
				$map['sremark']=$_POST['sremark'];
				$where.=" and remark like '%%%s%%'";
			}


			$_GET['p']=0;
		}else{
			if(isset($_GET['sname'])&&$_GET['sname']!=""){
				$map['sname']=$_GET['sname'];
				$where.=" and name like '%%%s%%'";

			}
			if(isset($_GET['sremark'])&&$_GET['sremark']!=""){
				$map['sremark']=$_GET['sremark'];
				$where.=" and remark like '%%%s%%'";
			}

		}

		$sql="select a.id, a.name, a.remark, a.add_time from ".C('DB_PREFIX')."advertise a, ".C('DB_PREFIX')."ad b";
		$chkAduser = M('role')->where("id = '".session('roleid')."'")->find();
		if($chkAduser['id'] == 2){
			$map['f.id']=session('adminid');
			$where.=" and b.uid = %d";
		}
		if(!empty($where)){
			$sql.=" where true and a.id = b.ad_pos".$where;
		}
		$sqlcount = $db->query($sql,$map);
		$count=count($sqlcount);
		$page=new AdminPage($count,C('ADMINPAGE'));
		foreach($map as $k=>$v){
			$page->parameter.=" $k=".urlencode($v)."&";//赋值给Page";
		}

		$sql.=" order by a.id asc limit ".$page->firstRow.','.$page->listRows;
		$result = $db->query($sql,$map);
		$this->assign('page',$page->show());
		$this->assign('lists',$result);
		$this->display();
	}
	*/

	public function advertise(){
		import('@.ORG.AdminPage');
		$db = D('Advertise');
		if(IS_POST){
			if(isset($_POST['sname'])&&$_POST['sname']!=""){
				$map['sname']=$_POST['sname'];
				$where.=" and name like '%%%s%%'";
			}
			if(isset($_POST['sremark'])&&$_POST['sremark']!=""){
				$map['sremark']=$_POST['sremark'];
				$where.=" and remark like '%%%s%%'";
			}


			$_GET['p']=0;
		}else{
			if(isset($_GET['sname'])&&$_GET['sname']!=""){
				$map['sname']=$_GET['sname'];
				$where.=" and name like '%%%s%%'";

			}
			if(isset($_GET['sremark'])&&$_GET['sremark']!=""){
				$map['sremark']=$_GET['sremark'];
				$where.=" and remark like '%%%s%%'";
			}

		}

		/*$sql="select a.id, a.name, a.remark,a.show_limit, a.add_time from ".C('DB_PREFIX')."advertise a, ".C('DB_PREFIX')."ad b";*/
		$sql="select a.id, a.name, a.remark,a.show_limit, a.add_time from ".C('DB_PREFIX')."advertise a";
		$chkAduser = M('role')->where("id = '".session('roleid')."'")->find();
		if($chkAduser['id'] == 2){
			$map['f.id']=session('adminid');
			$where.=" and b.uid = %d";
		}
		if(!empty($where)){
			$sql.=" where true and a.id = b.ad_pos".$where;
		}
		$sqlcount = $db->query($sql,$map);
		$count=count($sqlcount);
		$page=new AdminPage($count,C('ADMINPAGE'));
		foreach($map as $k=>$v){
			$page->parameter.=" $k=".urlencode($v)."&";//赋值给Page";
		}

		$sql.=" group by a.id order by a.id asc limit ".$page->firstRow.','.$page->listRows;
		//echo $sql;
		$result = $db->query($sql,$map);
		$this->assign('page',$page->show());
		$this->assign('lists',$result);
		$this->display();
	}

	/**
	 * 删除广告位内容
	 */
	public function delAdvertise(){
		$id = isset($_GET['id']) ? intval($_GET[id]) : 0;

		if($id)
		{
			if(D('Advertise')->delete($id))
			{
				$this->success('删除成功',U('Advertise'));
			}else{
				$this->error('操作出错');
			}
		}
	}

	/**
	 * 修改广告位信息
	 */
	public function editAdvertise(){
		$db=D('Advertise');
		if(IS_POST){
			$id=I('post.id','0','int');
			$limit=I('post.show_limit');
			$where['id']=$id;
			$info=$db->where($where)->find();
			if($info!=false){
				$_POST['update_time'] = time();
				$_POST['show_limit']=$limit;
				if($db->create($_POST,2)){
					$db->where($where)->save();
					$this->success("操作成功",U('Advertise'));

				}else{
					$this->error($db->getError());
				}
			}else{
				$this->error("没有此广告位信息");
			}

		}else{
			$id=I('get.id','0','int');

			$where['id']=$id;
			$info=$db->where($where)->find();
			if($info!=false){
				$this->assign('info',$info);
				$this->display();
			}else{

				$this->error("没有此广告位信息");
			}
		}
	}

}