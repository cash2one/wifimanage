<?php
class ShopAction extends  AdminAction{
	public function index(){
		$this->doLoadID(300);
		import('@.ORG.AdminPage');
		$db=D('Shop');
		if(IS_POST){
			if(isset($_POST['sname'])&&$_POST['sname']!=""){
					$map['sname']=$_POST['sname'];
					$where.=" and a.shopname like '%%%s%%'";	
			}
			if(isset($_POST['slogin'])&&$_POST['slogin']!=""){
					$map['slogin']=$_POST['slogin'];
					$where.=" and a.account like '%%%s%%'";
			}
			if(isset($_POST['phone'])&&$_POST['phone']!=""){
					$map['phone']=$_POST['phone'];
					$where.=" and a.phone like '%%%s%%'";
			}
			if(isset($_POST['agent'])&&$_POST['agent']!=""){
					$map['agent']=$_POST['agent'];
					$where.=" and b.name like '%%%s%%'";
			}
			
			$_GET['p']=0;
		}else{
			if(isset($_GET['sname'])&&$_GET['sname']!=""){
					$map['sname']=$_GET['sname'];
					$where.=" and a.shopname like '%%%s%%'";
					
			}
			if(isset($_GET['slogin'])&&$_GET['slogin']!=""){
					$map['slogin']=$_GET['slogin'];
					$where.=" and a.account like '%%%s%%'";
			}
			if(isset($_GET['phone'])&&$$_GET['phone']!=""){
					$map['phone']=$_GET['phone'];
					$where.=" and a.phone like '%%%s%%'";
			}
			if(isset($_GET['agent'])&&$_GET['agent']!=""){
					$map['agent']=$_GET['agent'];
					$where.=" and b.name like '%%%s%%'";
			}
		}
		
		$sqlcount=" select count(*) as ct from ". C('DB_PREFIX')."shop a left join ". C('DB_PREFIX')."agent b on a.pid=b.id ";
		if(!empty($where)){
			$sqlcount.=" where true ".$where;
		}
		$rs=$db->query($sqlcount,$map);

		$count=$rs[0]['ct'];
		$page=new AdminPage($count,C('ADMINPAGE'));
		foreach($map as $k=>$v){
			$page->parameter.=" $k=".urlencode($v)."&";//赋值给Page";
		}
		
		$sql=" select a.id,a.shopname,a.add_time,a.linker,a.phone,a.account,a.maxcount,a.linkflag,b.name as agname from ". C('DB_PREFIX')."shop a left join ". C('DB_PREFIX')."agent b on a.pid=b.id ";
		if(!empty($where)){
			$sql.=" where true ".$where;
		}
		$sql.=" order by a.add_time desc limit ".$page->firstRow.','.$page->listRows." ";
		
      
		$result = $db->query($sql,$map);
     	//dump($db->getLastSql());
        
        $this->assign('page',$page->show());
        $this->assign('lists',$result);
		$this->display();
	}
	
	public function editshop()
	{
		$this->doLoadID(300);
		if(IS_POST){
			$user = D('Shop');
			$id=I('post.id','0','int');
	        $where['id']=$id;
	        $info=$user->where($where)->find();
	        if(!$info){
	        	//无此用户信息
	        	$data['error']=1;
	    		$data['msg']="服务器忙，请稍候再试";
	    		return $this->ajaxReturn($data);
	        }
	        
	       $_POST['update_time']=time();
	        if($user->create($_POST,2)){
	        	if($user->where($where)->save($_POST)){
	        		$data['error']=0;
		    		$data['url']=U('index');
		    		return $this->ajaxReturn($data);
	        	}else{
	        		$data['error']=1;
		    		$data['msg']=$user->getError();
		    		
		    		return $this->ajaxReturn($data);
	        	}
	        }else{
	        		$data['error']=1;
		    		$data['msg']=$user->getError();
		    		return $this->ajaxReturn($data);
	        }
		}else{
			$id=I('get.id','0','int');
			$where['id']=$id;
			$db=D('Shop');
			$info=$db->where($where)->find();
			if(!$info){
				$this->error("参数不正确");
			}
			$this->assign("shop",$info);
			include CONF_PATH.'enum/enum.php';//$enumdata

	        $this->assign('enumdata',$enumdata);
	        $this->display();
		}
		
	}

	public function delshop(){
		$db = D('Shop');
		$info = D('Shop_info');
		$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
		$where['id']=$id;
		$r = $db->where($where)->find();
		if($r==false){
			$this->error('没有此商户信息');
		}else{
			if($db->where($where)->delete() && $info->where("shopid = ".$id)->delete()){
				$this->success('删除成功');
			}else{
				$this->error('删除失败');
			}
			echo $db->getLastSql();
			echo $info->getLastSql();
		}
	}

	public function addshop(){
		$this->doLoadID(300);
		if(IS_POST){
			$user=D('Shop');
			$now=time();
			$_POST['pid']=0;
			$_POST['authmode']='#0#';

	        if($user->create($_POST,1)){
	        	$id=$user->add();
	        	if($id>0){
	        		$data['error']=0;
		    		$data['url']=U('index');
		    		return $this->ajaxReturn($data);
	        	}else{
	        		$data['error']=1;
		    		$data['msg']=$user->getDbError();
		    		return $this->ajaxReturn($data);
	        	}
	        }else{
	        		$data['error']=1;
		    		$data['msg']=$user->getError();
		    		return $this->ajaxReturn($data);
	        }
		}else{
			include CONF_PATH.'enum/enum.php';//$enumdata
	        $this->assign('enumdata',$enumdata);
	        $this->display();      
		}  
	}

	/**
	 * 商户附属信息
	 */
	public function shopinfo(){
		$db = D('Shop_info');
		$this->doLoadID(300);
		if(IS_POST){
			$id = I('post.id');
			$where['id']=$id;
			$info=$db->where($where)->find();
			if (!$info) {
				//无此信息
				unset($_POST['id']);
				if($db->add($_POST)) {
					$this->success("编辑成功");
					redirect(U('index'));
				}else{
					$this->error("编辑失败");
				}
			} else {
				if ($db->where($where)->save($_POST)) {
					echo $db->getLastSql();
					$this->success("编辑成功");
					redirect(U('index'));
				} else {
					$this->error("编辑失败");
				}
			}
		}else{
			$shop=D('Shop');
			$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
			$where['shopid']=$id;
			$info=$shop->where('id = '.$id)->find();
			if(!$info){
				$this->error("参数不正确");
			}
			$shopinfo=$db->where($where)->find();
			if(empty($shopinfo['top_hotspot_name'])) {
				$shopinfo['top_hotspot_name'] = $info['shopname'];
			}
			$shopinfo['shopid'] = $id;
			$this->assign('shopinfo',$shopinfo);
			$this->display();
		}
	}

	public  function addroute(){
		$this->doLoadID(300);
		if(IS_POST){
			$db=D('Routemap');
			if($db->create()){
				if($db->add()){
					$this->success('添加成功',U('index'));
				}else{
					$this->error("操作失败");
				}
			}else{
				$this->error($db->getError());
			
			}
		}else{
			$id=I('get.id','0','int');
			
			if(empty($id)){
				$this->error("参数不正确");
			}
			$where['id']=$id;
			$db=D('Shop');
			$info=$db->where($where)->field('id,shopname')->find();
			if(!$info){
				$this->error("参数不正确");
			}
			$this->assign("shop",$info);
			$this->display();        
		}		
	}
	
	public  function routelist(){
			$this->doLoadID(300);
			$id=I('get.id','0','int');

			if(empty($id)){
				$this->error("参数不正确");
			}
			import('@.ORG.AdminPage');
			$db=D('Routemap');
			$where['shopid']=$id;
			$count=$db->where($where)->count();
			$page=new AdminPage($count,C('ADMINPAGE'));
		
			$sql=" select a.* ,b.shopname from ". C('DB_PREFIX')."routemap a left join ". C('DB_PREFIX')."shop b on a.shopid=b.id where a.shopid=".$id." order by a.id desc limit ".$page->firstRow.','.$page->listRows." ";
			
			//$sql=" select a.id,a.shopname,a.add_time,a.linker,a.phone,a.account,a.maxcount,a.linkflag,b.name as agname from ". C('DB_PREFIX')."shop a left join ". C('DB_PREFIX')."agent b on a.pid=b.id order by a.add_time desc limit ".$page->firstRow.','.$page->listRows." ";
	        
			$result = $db->query($sql);
	     
	        
	        $this->assign('page',$page->show());
	        $this->assign('lists',$result);
			$this->display();        
		
		
		
		
	}

 public function delroute()
    {
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        $where['id']=$id;
        $r = D('Routemap')->where($where)->find();
        if($r==false){
        	$this->error('没有此路由信息');
        }else{
          if(D('Routemap')->where($where)->delete()){
          	$this->success('删除成功');
          }else{
          	$this->error('删除失败');
          }
        	
        }
    }
	
	public function editroute(){
		if(IS_POST){
			$db= D('Routemap');
        	
        	$id = I('post.id','0','int');
	        $where['id']=$id;

         	$result =$db
                    ->where($where)
                    ->field('id')
                    ->find();
                   
                    
	         if($result==false){
	         	$this->error('没有此路由信息');
	         	exit;
	         }
	        unset($_POST['shopid']);
        	if($db->create()){
        			if($db->where($where)->save()){
	        		   $this->success('更新成功',U('index'));
	        		}else{
	        			$this->error("操作失败");
	        		}
        	}else{
        		$this->error($db->getError());
        	}
		}else{
			
			$id=I('get.id','0','int');
			
			$where['id']=$id;

			$db=D('Routemap');
			$info=$db->where($where)->find();
			if(!$info){
				$this->error("参数不正确");
			}
			$this->assign("info",$info);
			$whereshop['id']=$info['shopid'];
			$db=D('Shop');
			$shopinfo=$db->where($where)->field('id,shopname')->find();
	        $this->assign("shop",$shopinfo);
	        
	        $this->display();    
		}
	}

	public function markshop(){
		/*if(IS_POST){
			$db = D('shop');
			$search['city'] = $_POST['city'];
			$search['area'] = $_POST['area'];
			//$search['area'] = $_POST['area'];
			$search['province'] = $_POST['province'];
			$search['address'] = array('like','%'.$_POST['address'].'%');
			$search['shopname'] = array('like','%'.$_POST['shopname'].'%');
			$search['gw_id'] = $_POST['gw_id'];
			$search['trade'] = array('like','%'.$_POST['trade'].'%');
			$search1['address'] = $_POST['address'];
			$search1['shopname'] = $_POST['shopname'];
			$search1['gw_id'] = $_POST['gw_id'];
			$search1['trade'] = $_POST['trade'];

			if($search['city']=='市辖区'&&$search['area']=='东城区'&&$search['province']=='北京市'){
				unset($search['city']);
				unset($search['area']);
				unset($search['province']);
			}
			elseif($search['area']=='市辖区')
			{
				unset($search['area']);
			}
			elseif(empty($search['trade'] ))
			{
				unset($search['trade']);
			}
			elseif(empty($search['shopname'] ))
			{
				unset($search['shopname']);
			}
			$count = $db->where($search)->where('lng <>"" and lat <>""')->count();
			$info = $db->where($search)->where('lng <>"" and lat <>""')->select();
			if($search['gw_id']){
				$sql=" select a.gw_id ,b.* from ". C('DB_PREFIX')."routemap a left join ". C('DB_PREFIX')."shop b on a.shopid=b.id where a.shopid=b.id and a.gw_id = ".$search['gw_id']."";
				$count = $db->execute($sql);
				$info = $db->query($sql);
			}
			for($i=0;$i<$count;$i++){
				preg_match('/^\d+(\.\d+)?/',$info[$i]['lng'],$rlng);
				preg_match('/^\d+(\.\d+)?/',$info[$i]['lat'],$rlat);
				$mapdata[] = array($rlng[0],$rlat[0]);
			}
			$check = 'ok';
			include CONF_PATH.'enum/enum.php';//$enumdata
			$this->assign('enumdata',$enumdata);
			$this->assign('shop',$search);
			$this->assign('shop1',$search1);
			$this->assign('check',$check);
			$this->assign('mapdata',json_encode($mapdata));//数组转成JSON格式的字符串方便后面传入模板
			$this->display();
		}
		else
		{
			include CONF_PATH.'enum/enum.php';//$enumdata
			$this->assign('enumdata',$enumdata);
			$this->display();
		}*/
		$db = D('shop');
		$sql=" select * from wifi_shop";
		$info = $db->query($sql);
		$count = $db->execute($sql);
		for($i=0;$i<$count;$i++){
			//$data.= "[".$info[$i]['lng'].",".$info[$i]['lat']."]".",";
			//$data.= "lng: ".$info[$i]['lng'].",lat: ".$info[$i]['lat'].",shopName: ".$info[$i]['shopname'].",address: ".$info[$i]['address'].",";
			$data.= '{"lng": "'.$info[$i]['lng'].'","lat": "'.$info[$i]['lat'].'","shopName": "'.$info[$i]['shopname'].'","address": "'.$info[$i]['address'].'"},';
		}

		$file = './map-data.js';
		$data = 'var data = {"data":['.$data.']}';
		file_put_contents($file, $data, LOCK_EX);

	}

	public function markroute(){
		if(IS_POST){
			$db = D('shop');
			$search['wifi_shop.city'] = $_POST['city'];
			$search['wifi_shop.area'] = $_POST['area'];
			$search['wifi_shop.province'] = $_POST['province'];
			$search['wifi_shop.address'] = array('like','%'.$_POST['address'].'%');
			$search['wifi_shop.shopname'] = array('like','%'.$_POST['shopname'].'%');
			$search['wifi_routemap.gw_id'] = $_POST['gw_id'];
			$search['wifi_shop.trade'] = array('like','%'.$_POST['trade'].'%');
			if($search['wifi_shop.city']=='市辖区'&&$search['wifi_shop.area']=='东城区'&&$search['wifi_shop.province']=='北京市'){
				unset($search['wifi_shop.city']);
				unset($search['wifi_shop.area']);
				unset($search['wifi_shop.province']);
				unset($search['wifi_shop.address']);
			}
			elseif(empty($search['wifi_shop.trade'] ))
			{
				unset($search['wifi_shop.trade']);
			}
			elseif(empty($search['wifi_shop.shopname'] ))
			{
				unset($search['wifi_shop.shopname']);
			}
			if(empty($search['wifi_routemap.gw_id']) )
			{
				unset($search['wifi_shop.gw_id']);
			}
			$info = $db->table('wifi_shop,wifi_routemap')->where($search)->where('wifi_shop.id = wifi_routemap.shopid')->field('wifi_routemap.lng,wifi_routemap.lat')->select();
			$count = $db->where($search)->count();
			/*if($search['wifi_shop.gw_id']){
				$sql=" select a.gw_id ,b.* from ". C('DB_PREFIX')."routemap a left join ". C('DB_PREFIX')."shop b on a.shopid=b.id where a.shopid=b.id and a.gw_id = ".$search['wifi_shop.gw_id']."";
				$info = $db->query($sql);
				$count = $db->execute($sql);
			}*/
			for($i=0;$i<$count;$i++){
				$data.= "[".$info[$i]['lng'].",".$info[$i]['lat']."]".",";
			}
			$file = './UI/Public/js/map-data.js';
			$data = 'var data = {"data":['.$data.']}';
			file_put_contents($file, $data, LOCK_EX);
			include CONF_PATH.'enum/enum.php';//$enumdata
			$this->assign('enumdata',$enumdata);
			$this->display();
		}
		else
		{
			include CONF_PATH.'enum/enum.php';//$enumdata
			$db = D('routemap');
			$info = $db->select();
			$count = $db->count();
			for($i=0;$i<$count;$i++){
				$data.= "[".$info[$i]['lng'].",".$info[$i]['lat']."]".",";
			}
			$file = './UI/Public/js/map-data.js';
			$data = 'var data = {"data":['.$data.']}';
			file_put_contents($file, $data, LOCK_EX);
			$this->assign('enumdata',$enumdata);
			$this->display();
		}

	}
}