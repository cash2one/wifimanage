<?php
class UserAction extends AdminAction{
protected function _initialize(){
		parent::_initialize();
		$this->doLoadID(800);
	}

	
public function user(){
		import('@.ORG.AdminPage');
		if(IS_GET){
			import('@.ORG.AdminPage');
			//$where['city'] = $_POST['city'];
			$hostaddress = $_GET['user_mac'];
			$ap = $_GET['ap_mac'];
			$sstartdate = strtotime($_GET['sstartdate']);
			$senddate = strtotime($_GET['senddate']);
			if (!empty($hostaddress)){
				$where['hostaddress'] = array("like","%$hostaddress%");
			}
			if (!empty($ap)){
				$where['ap'] = array("like","%$ap%");
			}
			if (!empty($sstartdate) && !empty($senddate)){
				$where['times'] = array("between",array($sstartdate,$senddate));
			}

			if (!empty($sstartdate) && empty($senddate)){
				$where['times'] = array("egt",$sstartdate);
			}

			//$where['ap'] = array('like','%'.$_POST['ap_mac'].'%');
			$res = M('user_log')->where($where)->count();
			$page=new AdminPage($res,20);
			//echo $res;
			//$result = M('user_log')->where("hostaddress like '%$hostaddress%' or ap like '%$ap%'  and outtime IS NOT NULL")->limit($page->firstRow,$page->listRows)->group('hostaddress,ap')->select();
			$result = M('user_log')->where($where)->limit($page->firstRow,$page->listRows)->order('times desc')->select();
			//echo M('user_log')->getlastsql();
			//file_put_contents('a.txt',M('user_log')->getlastsql());
			$this->assign('result',$result);
			$this->assign('page',$page->show());
			$this->display();
		}else{

			$res = M('user_log')->where($where)->count();
			$page=new AdminPage($res,20);
			$result = M('user_log')->where($where)->limit($page->firstRow,$page->listRows)->order('times desc')->select();
			$this->assign('result',$result);
			$this->assign('page',$page->show());
			$this->display();
		}
		
}

public function userhistory(){
		import('@.ORG.AdminPage');
		if(IS_GET){
			import('@.ORG.AdminPage');
			//$where['city'] = $_POST['city'];
			$hostaddress = $_GET['user_mac'];
			$ap = $_GET['ap_mac'];
			$sstartdate = strtotime($_GET['sstartdate']);
			$senddate = strtotime($_GET['senddate']);
			if (!empty($hostaddress)){
				$where['hostaddress'] = array("like","%$hostaddress%");
			}
			if (!empty($ap)){
				$where['ap'] = array("like","%$ap%");
			}
			if (!empty($sstartdate) && !empty($senddate)){
				$where['times'] = array("between",array($sstartdate,$senddate));
			}

			if (!empty($sstartdate) && empty($senddate)){
				$where['times'] = array("egt",$sstartdate);
			}

			//$where['ap'] = array('like','%'.$_POST['ap_mac'].'%');
			$res = M('user_log_history')->where($where)->count();
			$page = new AdminPage($res,20);
			//echo $res;
			//$result = M('user_log')->where("hostaddress like '%$hostaddress%' or ap like '%$ap%'  and outtime IS NOT NULL")->limit($page->firstRow,$page->listRows)->group('hostaddress,ap')->select();
			$result = M('user_log_history')->where($where)->limit($page->firstRow,$page->listRows)->order('times desc')->select();
			//file_put_contents('a.txt',M('user_log')->getlastsql());
			$this->assign('result',$result);
			$this->assign('page',$page->show());
			$this->display();
		}else{

			$res = M('user_log_history')->where($where)->count();
			$page=new AdminPage($res,20);
			$result = M('user_log_history')->where($where)->limit($page->firstRow,$page->listRows)->order('times desc')->select();
			$this->assign('result',$result);
			$this->assign('page',$page->show());
			$this->display();
		}
		
}

public function deploy(){
		$telephone = $_POST['tel'];
		if(empty($telephone)){
			$telephone = 0;
		}else{
			$res = M('ns_user')->where("username=$telephone")->find();
			$a=strstr($res['mac'], ':', TRUE);
			$b=substr($a,0,2).":";
			$c=substr($a,2,2).":";
			$d=substr($a,4,2).":";
			$e=substr($a,6,2).":";
			$f=substr($a,8,2).":";
			$g=substr($a,10,2);
			$h=$b.$c.$d.$e.$f.$g;
			$result = M('user_log_history')->field('lng,lat,shopName,address')->where("ap='$h'")->select();
			$ajax['error'] = 0;
			$ajax['data'] = $this->json_encode_no_zh($result);
			$this->ajaxReturn($ajax);
		}
		
		$this->assign('telephone',$telephone);
		$this->display();
}
public function map(){

		$this->display();
}
public function count(){
	import('@.ORG.AdminPage');
		if(!empty($_GET['sstartdate'])){
			import('@.ORG.AdminPage');
			$sstartdate = strtotime($_GET['sstartdate']);
			$senddate = strtotime($_GET['senddate']);
			if (!empty($sstartdate) && !empty($senddate)){
				$where['dates'] = array("between",array($sstartdate,$senddate));
			}

			if (!empty($sstartdate) && empty($senddate)){
				$where['dates'] = array("egt",$sstartdate);
			}

			//$where['ap'] = array('like','%'.$_POST['ap_mac'].'%');
			$res = M('user_count')->where($where)->count();
			$page=new AdminPage($res,20);
			//echo $res;
			$result = M('user_count')->where($where)->limit($page->firstRow,$page->listRows)->order('dates desc')->group('dates')->select();
			//$result = M('user_log')->where("hostaddress like '%$hostaddress%' or ap like '%$ap%'  and outtime IS NOT NULL")->limit($page->firstRow,$page->listRows)->group('hostaddress,ap')->select();
			//echo M('user_log')->getlastsql();
			$this->assign('result',$result);
			$this->assign('page',$page->show());
			$this->display(); 
		}else{

			$res = M('user_count')->count();
			$page=new AdminPage($res,20);
			$result = M('user_count')->limit($page->firstRow,$page->listRows)->order('dates desc')->group('dates')->select();
			$this->assign('result',$result);
			$this->assign('page',$page->show());
			$this->display();
		} 
}
function json_encode_no_zh($arr) {
	$str = str_replace ( "\\/", "/", json_encode ( $arr ) );
	$search = "#\\\u([0-9a-f]+)#ie";
 
	if (strpos ( strtoupper(PHP_OS), 'WIN' ) === false) {
		$replace = "iconv('UCS-2BE', 'UTF-8', pack('H4', '\\1'))";//LINUX
	} else {
		$replace = "iconv('UCS-2', 'UTF-8', pack('H4', '\\1'))";//WINDOWS
	}
 
	return preg_replace ( $search, $replace, $str );
}
}