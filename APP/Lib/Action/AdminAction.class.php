<?php
/*
 * 后台基本信息
 */
class AdminAction extends BaseAction{
	public  $userid;
	public function doLoadID($id){
		$nav['m']=$this->getActionName();
		$nav['a']=$id;
		$this->assign('nownav',$nav);
	}
	protected function _initialize(){
		parent::_initialize();
		//判断权限
		if (C('USER_AUTH_ON') && !in_array(MODULE_NAME, explode(',', C('NOT_AUTH_MODULE')))) {
			import("@.ORG.WIFIRBAC");
			if (!WIFIRBAC::AccessDecision(GROUP_NAME)) {	
				
				//检查认证识别号
				if (!$_SESSION [C('USER_AUTH_KEY')]) {
					//跳转到认证网关
					redirect(PHP_FILE . C('USER_AUTH_GATEWAY'));
				}
				// 没有权限 抛出错误
				
				if (C('RBAC_ERROR_PAGE')) {
		
					// 定义权限错误页面
					redirect(C('RBAC_ERROR_PAGE'));
				} else {
					if (C('GUEST_AUTH_ON')) {
						$this->assign('jumpUrl', PHP_FILE . C('USER_AUTH_GATEWAY'));
					}
					// 提示错误信息
					
					$this->error(L('_VALID_ACCESS_'));
		
				}
		
			}else{
					
				$this->userid=session(C('USER_AUTH_KEY'));
				$this->loadMenu();
			}
		
		}
		
	}

	private  function  loadMenu(){
		$where['status']=1;
		$where['menuflag']=1;
		$where['m']=array('not in','Shop,shop,agent,notice');//不显示网站管理 商户管理 代理商管理 信息管理 后期可以删除
		$where['title'] = array('not in','网站管理,上网统计报表,用户统计报表,上网记录,注册用户,添加推送广告,广告推送统计');
		$order['sort']='asc';
		$order['id']='asc';

		$nav=M('treenode')->where($where)->order($order)->field('id,title,g,m,a,ico,single,pid,level')->select();
		$this->assign('nav',$nav);
		
		if(session('adminmame')==C('SPECIAL_USER')){
			$ids=M('treenode')->field('id')->select();
			foreach ($ids as $node){
	            $access[]	=	$node['id'];
	        }
			$this->assign('navids',$access);
		}else{
			$ids=WIFIRBAC::getAccessIDList($_SESSION[C('USER_AUTH_KEY')]);
			$this->assign('navids',$ids);
		}
	}
}