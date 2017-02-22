<?php
class AdvertisersAction extends AdminAction{
	protected function _initialize(){
		parent::_initialize();
		$this->doLoadID(100);
	}
	public function index(){
		$this->doLoadID(200);
		if(IS_POST){
			$this->sitesave();
		}else{
			$this->display();
		}



	}




	public function lists(){
		import('ORG.Util.Page');

		$db=M('Advertisers');
		$count=$db->count();
		$page=new Page($count,C('ADMINPAGE'));
		$info=$db->limit($page->firstRow.','.$page->listRows)->select();
		$this->assign('lists',$info);
		$this->assign('page',$page->show());
		$this->display();
	}
	public function edit(){
		$db=D('Advertisers');
		if(IS_POST){
			$id=I('post.id','0','int');
			$where['id']=$id;
			$info=$db->where($where)->find();
			if($info!=false){
				if($db->create($_POST,2)){
					$db->where($where)->save();
					$this->success("操作成功",U('lists'));

				}else{
					$this->error($db->getError());
				}
			}else{
				$this->error("没有此角色信息");
			}

		}else{
			$id=I('get.id','0','int');

			$where['id']=$id;
			$info=$db->where($where)->find();
			if($info!=false){
				$this->assign('info',$info);
				$this->display();
			}else{

				$this->error("没有此角色信息");
			}
		}


	}

	public  function del(){
		$db=D('Advertisers');
		$id=I('get.id','0','int');

		$where['id']=$id;
		$info=$db->where($where)->find();
		$adminwhere['role']=id;

		if($info!=false){
			$db->where($where)->delete();
			$this->success("删除成功",U('lists'));

		}else{

			$this->error("没有此角色信息");
		}
	}

	public function add(){
		$db=D('Advertisers');
		if(IS_POST){
			if($db->create()){
				if($db->add()){
					$this->success("操作成功",U('lists'));
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

}