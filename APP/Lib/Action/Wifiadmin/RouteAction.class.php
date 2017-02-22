<?php
class RouteAction extends AdminAction{
	protected function _initialize(){
		parent::_initialize();
		$this->doLoadID(900);
	}
	public function index(){
		import('@.ORG.AdminPage');
		$db=D('Routemap');
		if(IS_POST){
			if(isset($_POST['sname'])&&$_POST['sname']!=""){
				$map['sname']=$_POST['sname'];
				$where.=" and routename like '%%%s%%'";
			}
			/*if(isset($_POST['slogin'])&&$_POST['slogin']!=""){
				$map['slogin']=$_POST['slogin'];
				$where.=" and b.account like '%%%s%%'";
			}*/
			if(isset($_POST['mac'])&&$_POST['mac']!=""){
				$map['mac']=$_POST['mac'];
				$where.=" and a.gw_id like '%%%s%%'";
			}
			/*if(isset($_POST['agent'])&&$_POST['agent']!=""){
				$map['agent']=$_POST['agent'];
				$where.=" and c.name like '%%%s%%'";
			}*/
			$_GET['p']=0;
		}else{
			if(isset($_GET['sname'])&&$_GET['sname']!=""){
				$map['sname']=$_GET['sname'];
				$where.=" and routename like '%%%s%%'";

			}
			/*if(isset($_GET['slogin'])&&$_GET['slogin']!=""){
				$map['slogin']=$_GET['slogin'];
				$where.=" and b.account like '%%%s%%'";
			}*/
			if(isset($_GET['mac'])&&$$_GET['mac']!=""){
				$map['phone']=$_GET['mac'];
				$where.=" and a.gw_id like '%%%s%%'";
			}
			/*if(isset($_GET['agent'])&&$_GET['agent']!=""){
				$map['agent']=$_GET['agent'];
				$where.=" and c.name like '%%%s%%'";
			}*/
		}

		$sqlcount=" select count(*) as ct from ". C('DB_PREFIX')."routemap";
		if(!empty($where)){
			$sqlcount.=" where true ".$where;
		}
		$rs=$db->query($sqlcount,$map);

		$count=$rs[0]['ct'];

		$page=new AdminPage($count,C('ADMINPAGE'));
		foreach($map as $k=>$v){
			$page->parameter.=" $k=".urlencode($v)."&";//赋值给Page";
		}
		$sql=" select * from ". C('DB_PREFIX')."routemap";
		if(!empty($where)){
			$sql.=" where true ".$where;
		}
		$sql.=" order by id desc limit ".$page->firstRow.','.$page->listRows." ";
		$result = $db->query($sql,$map);

		$this->assign('page',$page->show());
		$this->assign('lists',$result);
		$this->display();

	}

	public function add(){
		if(IS_POST){
			$db=D('Routemap');
			$_POST['gw_id'] = strtolower($_POST['gw_id']);
			if($db->create()){
				if($db->add()){
					$data['error']=0;
					$data['url']=U('index');
					$this->ajaxReturn($data);
				}else{
					$data['error']=1;
					$data['msg']=$db->getError();
					$this->ajaxReturn($data);
				}
			}else{
				$data['error']=1;
				$data['msg']=$db->getError();
				$this->ajaxReturn($data);

			}
		}else{
			include CONF_PATH.'enum/enum.php';//$enumdata
			$this->assign('enumdata',$enumdata);
			$this->display();
		}
	}
	public function edit(){
		if(IS_POST){
			$db= D('Routemap');
			$_POST['gw_id'] = strtolower($_POST['gw_id']);
			$id = I('post.id','0','int');
			$where['id']=$id;

			$result =$db
				->where($where)
				->field('id')
				->find();

			if($result==false){
				$data['error']=1;
				$data['msg']='没有此路由信息';
				$this->ajaxReturn($data);
				exit;
			}

			if($db->create()){
				if($db->where($where)->save()){
					$data['error']=0;
					$data['url']=U('index');
					$this->ajaxReturn($data);
				}else{
					$data['error']=1;
					$data['msg']='操作失败';
					$this->ajaxReturn($data);
				}
			}else{
				$data['error']=1;
				$data['msg']=$db->getError();
				$this->ajaxReturn($data);
			}
		}else{
			$id=I('get.id','0','int');

			$where['id']=$id;
			$db=D('Routemap');
			$info=$db->where($where)->find();
			if(!$info){
				$this->error("参数不正确");
			}
			include CONF_PATH.'enum/enum.php';//$enumdata
			$this->assign('enumdata',$enumdata);
			$this->assign("info",$info);

			$this->display();
		}
	}

	/**
	 * AP备注信息
	 */
	public function info(){
		$db = D('Routemap_info');
		$this->doLoadID(300);
		if(IS_POST){
			$id = I('post.id');
			$where['id']=$id;
			$_POST['starttime'] = date('Y-m-d',time());
			$info=$db->where($where)->find();
			if (!$info) {
				//无此信息
				unset($_POST['id']);
				if($db->add($_POST)) {
					echo $db->getLastSql();
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
			$route=D('Routemap');
			$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
			$where['routeid']=$id;
			$info=$route->where('id = '.$id)->find();
			if(!$info){
				$this->error("参数不正确");
			}
			$routeinfo=$db->where($where)->find();
			$routeinfo['routename'] = $info['routename'];
			$routeinfo['routeid'] = $id;
			$this->assign('routeinfo',$routeinfo);
			$this->display();
		}
	}


	public function del(){
		$db = D('Routemap');
		$info = D('Routemap_info');
		$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
		$where['id']=$id;
		$r = $db->where($where)->find();

		if($r==false){
			$this->error('没有此路由信息');
		}else{
			if($db->where($where)->delete()  && $info->where("routeid = ".$id)->delete()){
				$this->success('删除成功');
			}else{
				$this->error('删除失败');
			}

		}
	}

}