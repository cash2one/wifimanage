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
		$rs=$shop->table('wifi_shop,wifi_advertise,wifi_routemap')->where($where)->where('wifi_shop.shop_pos = wifi_advertise.id and wifi_shop.id = wifi_routemap.shopid')->count();
		$page=new AdminPage($rs,5);
		foreach($map as $k=>$v){
			$page->parameter.=" $k=".urlencode($v)."&";//赋值给Page";
		}
		$result = $shop->table('wifi_shop,wifi_advertise,wifi_routemap')->where($where)->where('wifi_shop.shop_pos = wifi_advertise.id and wifi_shop.id = wifi_routemap.shopid')->order('wifi_shop.id asc')->limit($page->firstRow,$page->listRows)->select();
		if(IS_POST && empty($_POST['search'])){
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
    			$sql=" select t,CONCAT(CURDATE(),' ',t,'点') as showdate, COALESCE(showup,0)  as showup, COALESCE(hit,0)  as hit,COALESCE(hit/showup*100,0) as rt from ".C('DB_PREFIX')."hours a left JOIN ";
				$sql.="(select thour, sum(showup)as showup,sum(hit) as hit,  ";
				$sql.="sum(case when mode=99 then showup else 0 end) as pshowup, ";
				$sql.="sum(case when mode=50 then showup else 0 end) as ashowup, ";
				$sql.="sum(case when mode=99 then hit else 0 end) as phit, ";
				$sql.="sum(case when mode=99 then showup else 0 end) as ahit from";
				$sql.="  (select  FROM_UNIXTIME(add_time,\"%H\") as thour,showup ,hit,mode from ".C('DB_PREFIX')."adcount";

				$sql.=" where add_date='".date("Y-m-d")."' and (mode=99 or mode=50) ";
				$sql.=" )a group by thour ) c ";
				$sql.="  on a.t=c.thour ";

    			break;
    		case "yestoday":
    			
    			$sql=" select t,CONCAT(CURDATE(),' ',t,'点') as showdate, COALESCE(showup,0)  as showup, COALESCE(hit,0)  as hit,COALESCE(hit/showup*100,0) as rt from ".C('DB_PREFIX')."hours a left JOIN ";
				$sql.="(select thour, sum(showup)as showup,sum(hit) as hit,  ";
				$sql.="sum(case when mode=99 then showup else 0 end) as pshowup, ";
				$sql.="sum(case when mode=50 then showup else 0 end) as ashowup, ";
				$sql.="sum(case when mode=99 then hit else 0 end) as phit, ";
				$sql.="sum(case when mode=99 then showup else 0 end) as ahit from";
				$sql.="(select  FROM_UNIXTIME(add_time,\"%H\") as thour,showup ,hit from ".C('DB_PREFIX')."adcount";

				$sql.=" where add_date=DATE_ADD(CURDATE() ,INTERVAL -1 DAY) and (mode=99 or mode=50) ";
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
				$sql.=" where   add_date between DATE_ADD(CURDATE() ,INTERVAL -6 DAY) and CURDATE() and (mode=99 or mode=50) GROUP BY  add_date";
				$sql.=" ) b on a.td=b.add_date ";
				
    			break;
    		case "month":
    			$t=date("t");
    			$sql=" select tname as showdate,tname as t, COALESCE(showup,0)  as showup, COALESCE(hit,0)  as hit,COALESCE(hit/showup*100,0) as rt from ".C('DB_PREFIX')."day  a left JOIN";
				$sql.="( select right(add_date,2) as td ,sum(showup) as showup ,sum(hit) as hit  from ".C('DB_PREFIX')."adcount  ";
				$sql.=" where   add_date >= '".date("Y-m-01")."' and (mode=99 or mode=50) GROUP BY  add_date";
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
    			$sql=" select td as showdate,right(td,5) as td,datediff(td,CURDATE()) as t,COALESCE(showup,0)  as showup, COALESCE(hit,0)  as hit,COALESCE(hit/showup*100,0) as rt from ";
    			$sql.=" ( select '$sdate' as td ";
    			for($i=0;$i<=$leftday;$i++){
    				$sql.="  UNION all select DATE_ADD('$sdate' ,INTERVAL $i DAY) ";
    			}
    			$sql.=" ) a left join ";
    			
    
				$sql.="( select add_date,sum(showup) as showup ,sum(hit) as hit  from ".C('DB_PREFIX')."adcount ";
				$sql.=" where  add_date between '$sdate' and '$edate'  and (mode=99 or mode=50) GROUP BY  add_date";
				$sql.=" ) b on a.td=b.add_date ";

			
    			break;
    	}
    	
    	$db=D('Adcount');
    	$rs=$db->query($sql);
		file_put_contents('sql.txt',"sql".$sql);
    	$this->ajaxReturn(json_encode($rs));
    }
	
	
}