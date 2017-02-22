<?php
class UserAuthAction extends BaseApiAction{
	private  $shop=false;
	private  $tplkey="";
	public function index(){
		
	}
	/*
	 * 加载商户信息
	 */
	private  function load_shopinfo(){
		if( cookie('gw_id')!=null){
			$sql="select a.id,a.gw_id,a.shopid,a.routename,b.shopname,b.authmode,b.timelimit ,b.pid,b.tpl_path from ".C('DB_PREFIX')."routemap a left join ".C('DB_PREFIX')."shop b on a.shopid=b.id ";
			$sql.=" where a.gw_id='".cookie('gw_id')."' limit 1";
			$dbmap=D('Routemap');
			$info=$dbmap->query($sql);
		
			if($info!=false){
				cookie('shopid',$info[0]['id']);//代理商
				cookie('pid',$info[0]['pid']);//代理商
				$this->shop=$info;
				$this->tplkey=$info[0]['tpl_path'];
				$this->assign("shopinfo",$info);
				
			}
		
			$dbmap=null;
		}
	}
	/*
	 * 微信密码认证页面
	 */
	public function wxauth(){
		$this->load_shopinfo();
		/*$authmode=$this->shop[0]['authmode'];
		$tmp=explode('#', $authmode);
				foreach($tmp as $v){
					if($v!='#'&&$v!=''){
						$arr[]=$v;
					}
				}
		foreach($arr as $v){
		
			$temp=explode('=', $v);
			
			if(count($temp)>1&&$temp[0]=='3'){
				
				$auth['wx']=$temp[1];
				break;
			}
		}
		$wx=json_decode($auth['wx']);
		$this->assign('wxname',$wx->user);
		if(empty($this->tplkey)||$this->tplkey==""||strtolower($this->tplkey)=="default"){
				$this->display();
		}else{
			$this->display("wxauth$".$this->tplkey);
		}*/
		$ssid = I('get.ssid');
		$bssid = I('get.bssid');
		$this->assign('ssid',$ssid);
		$this->assign('bssid',$bssid);
		$this->display();
	}
	/*
	 * 微信密码认证处理
	 */
	public function dowxauth(){
		$this->load_shopinfo();
		$pwd=I('post.password');
		if($pwd==""){
			$data['msg']="请输入上网认证密码";
						$data['error']=1;
						$this->ajaxReturn($data);
						exit;
		}
		
		$authmode=$this->shop[0]['authmode'];
		$tmp=explode('#', $authmode);
		foreach($tmp as $v){
			if($v!='#'&&$v!=''){
					
				$arr[]=$v;
			}
		}
		
		foreach($arr as $v){
		
			$temp=explode('=', $v);
			if(count($temp)>1&&$temp[0]=='3'){
			
				$auth['wx']=$temp[1];
			}
		}
		
		if(!empty($auth['wx'])){
			$wx=json_decode($auth['wx']);
			if($pwd!=$wx->pwd){
				$data['msg']="上网认证密码不正确";
						$data['error']=1;
						$this->ajaxReturn($data);
						exit;
			}
			$token=md5(uniqid());
			$now=time();
			$tranDb=new Model();
			
			$_POST['user']=uniqid();
			$_POST['password']=md5('123456');
			$_POST['phone']='1399999999';
			$_POST['shopid']=$this->shop[0]['shopid'];
			$_POST['routeid']=$this->shop[0]['id'];
			$_POST['browser']=$this->browser;
			$_POST['add_time']=$now;
			$_POST['update_time']=$now;
			$_POST['login_time']=$now;
			$_POST['add_date']=getNowDate();
			unset($_POST['__hash__']);
			unset($_POST['smscode']);
			
			$_POST['mode']='3';//无需认证
			$_POST['online_time']=$this->getLimitTime();
			C('TOKEN_ON',false);


			$tranDb->startTrans();
			$flag=$tranDb->table(C('DB_PREFIX').'member')->add($_POST);  
			$newid=$tranDb->getLastInsID();
			$arrdata['uid']=$newid;
			$arrdata['add_date']=getNowDate();
			$arrdata['over_time']=$this->getLimitTime();
			$arrdata['update_time']=$now;//更新时间
			$arrdata['login_time']=$now;//首次登录时间
			$arrdata['last_time']=$now;//最后在线时间
			$arrdata['shopid']=$this->shop[0]['shopid'];
			$arrdata['routeid']=$this->shop[0]['id'];
			$arrdata['browser']=$this->browser;
			$arrdata['token']=$token;
			$arrdata['mode']='3';//微信
			$arrdata['agent']=$this->agent;
			$flagauth=$tranDb->table(C('DB_PREFIX').'authlist')->add($arrdata);  
			
			
			if($flag&$flagauth){
							$tranDb->commit();
							cookie('token',$token);
							cookie('mid',$newid);
							$data['error']=0;
							$data['url']=U("userauth/showtoken");
							$this->ajaxReturn($data);
			}else{
						$tranDb->rollback();
						Log::write("微信认证错误:".$tranDb->getLastSql());
						$data['msg']="认证操作失败，请稍候再试";
							$data['error']=1;
							$this->ajaxReturn($data);
			}
					
			
			
		}else{
				
				$data['error']=1;
					$data['msg']='认证操作失败,请稍候再试';
					$this->ajaxReturn($data);
	
		}
		
	}
	public function login(){
		$this->load_shopinfo();
		
	if(empty($this->tplkey)||$this->tplkey==""||strtolower($this->tplkey)=="default"){
				$this->display();
		}else{
			
			$this->display("login$".$this->tplkey);
		}
	}
	
	public function smslogin(){
		if(IS_POST){
			$this->load_shopinfo();
			$userdb=D('Member');
			$user=I('post.user');
			$pwd=I('post.smscode');
			$mac['mac']=I('post.mac');
			$where['mac'] = I('post.mac');
			$info=$userdb->where($where)->find();
			if($info == false && $_POST['act']!='noauth'){
				if(empty($user)||empty($pwd)||$user==""||$pwd==""){
					$data['msg']="提交参数不完整，登录失败";
					$data['error']=1;
					$this->ajaxReturn($data,'jsonp');
					exit;
				}
				if(!isPhone($user)){
					$data['msg']="请填写有效的11位手机号码";
					$data['error']=1;
					$this->ajaxReturn($data,'jsonp');
					exit;
				}
				if($pwd==""||!isSmsCode($pwd)){
					$data['msg']="验证码不正确";
					$data['error']=1;
					$this->ajaxReturn($data,'jsonp');
					exit;
				}
				$se=cookie('smscode');
				if(empty($se)){
					$data['msg']="验证码不正确,请重新输入";
					$data['error']=1;
					$this->ajaxReturn($data,'jsonp');
					exit;
				}
				if($se!=$pwd){
					$data['msg']="验证码不正确,请重新输入";
					$data['error']=1;
					$this->ajaxReturn($data,'jsonp');
					exit;
				}
			}
			unset($_POST['act']);
				if($info!=false){
					//更新用户
					C('TOKEN_ON',false);
					$token=md5(uniqid());
					$now=time();
					$tranDb=new Model();

					$_POST['browser']=$this->browser;
					$_POST['add_time']=$now;
					$_POST['update_time']=$now;
					$_POST['login_time']=$now;
					$_POST['add_date']=getNowDate();
					$_POST['login_count']=$info['login_count']+1;
					unset($_POST['__hash__']);
					unset($_POST['smscode']);
					unset($_POST['mac']);
					unset($_POST['user']);
					if($this->shop!=false){
						$_POST['shopid']=$this->shop[0]['shopid'];
					//	$_POST['routeid']=$this->shop[0]['id'];
						$_POST['online_time']=$this->getLimitTime();
					}
					$tranDb->startTrans();
					$flag=$tranDb->table(C('DB_PREFIX').'member')->where($mac)->save($_POST);
					$newid=$tranDb->getLastInsID();
					$arrdata['uid']=$newid;
					$arrdata['mac']=$mac['mac'];
					$arrdata['add_date']=getNowDate();
					$arrdata['over_time']=$this->getLimitTime();
					$arrdata['update_time']=$now;//更新时间
					$arrdata['login_time']=$now;//首次登录时间
					$arrdata['last_time']=$now;//最后在线时间
					$arrdata['shopid']=$this->shop[0]['shopid'];
					$arrdata['routeid']=$_POST['routeid'];
					$arrdata['browser']=$this->browser;
					$arrdata['token']=$token;
					$arrdata['mode']='1';//无需认证
					$arrdata['agent']=$this->agent;
					$flagauth=$tranDb->table(C('DB_PREFIX').'authlist')->add($arrdata);


					if($flag!=false && $flagauth){
						$tranDb->commit();
						cookie('token',$_POST['token']);
						cookie('mid',$newid);
						$data['error']=0;
						$data['url']=U("userauth/showtoken");
						$this->ajaxReturn($data,'jsonp');
					}else{
						$tranDb->rollback();
						Log::write("手机认证错误:".$tranDb->getLastSql());
						$data['msg']="验证失败，请稍候再试";
						$data['error']=1;
						$this->ajaxReturn($data,'jsonp');
					}
				}
				elseif($info==false)
				{
					//添加用户
					C('TOKEN_ON', false);
					$token = md5(uniqid());
					$now = time();
					$tranDb = new Model();

					$_POST['token'] = $token;
					$_POST['password'] = md5($user);
					$_POST['phone'] = $user;
					$_POST['browser'] = $this->browser;
					$_POST['mode'] = '1';//注册认证
					$_POST['add_time'] = $now;
					$_POST['update_time'] = $now;
					$_POST['login_time'] = $now;
					$_POST['add_date'] = getNowDate();
					$_POST['login_count']=1;
					unset($_POST['__hash__']);
					unset($_POST['smscode']);
					if ($this->shop != false) {
						$_POST['shopid'] = $this->shop[0]['shopid'];
					//	$_POST['routeid'] = $this->shop[0]['id'];
						$_POST['online_time'] = $this->getLimitTime();
					}
					$tranDb->startTrans();
					$flag = $tranDb->table(C('DB_PREFIX') . 'member')->add($_POST);
					$newid = $tranDb->getLastInsID();
					$arrdata['uid'] = $newid;
					$arrdata['mac']=$mac['mac'];
					$arrdata['add_date'] = getNowDate();
					$arrdata['over_time'] = $this->getLimitTime();
					$arrdata['update_time'] = $now;//更新时间
					$arrdata['login_time'] = $now;//首次登录时间
					$arrdata['last_time'] = $now;//最后在线时间
					$arrdata['shopid'] = $this->shop[0]['shopid'];
					$arrdata['routeid'] = $_POST['routeid'];
					$arrdata['browser'] = $this->browser;
					$arrdata['token'] = $token;
					$arrdata['mode'] = '1';//无需认证
					$arrdata['agent'] = $this->agent;
					$flagauth = $tranDb->table(C('DB_PREFIX') . 'authlist')->add($arrdata);

					if($flag && $flagauth){
						$tranDb->commit();
						cookie('token', $_POST['token']);
						cookie('mid', $newid);
						$data['error'] = 0;
						$data['url'] = U("userauth/showtoken");
						$this->ajaxReturn($data,'jsonp');
					} else {
						$tranDb->rollback();
						Log::write("手机认证错误:" . $tranDb->getLastSql());
						$data['msg'] = "验证失败，请稍候再试";
						$data['error'] = 1;
						$this->ajaxReturn($data,'jsonp');
					}
				}
			}
	} 
	
	public function smsCode(){
		import("ORG.Util.String");
		if(IS_POST){
			
			$this->load_shopinfo();
			$phone=I('post.phone');
						
			if(isPhone($phone)){
				
				$userdb=D('Member');
				$where['user']=$phone;
				$where['routeid']=$this->shop[0]['id'];
				$info=$userdb->where($where)->find();
				$code=String::randString(6,'1');
				
				
				if($info!=false){
					//注册
				}
				$sdata['phone']=$phone;
				$sdata['code']=$code;
				session('smscode',json_encode($sdata));
				$data['msg']=$code;
				$data['error']=0;
				$this->ajaxReturn($data);
				exit;
			}else{
					$data['msg']="请填写有效的11位手机号码";
						$data['error']=1;
						$this->ajaxReturn($data);
						exit;
			}
		}
	}
	
	public function mobile(){
		$this->load_shopinfo();
		import("ORG.Util.String");
		
		cookie('smscode',String::randString(6,'1'));
		$this->assign('smscode',cookie('smscode'));
	if(empty($this->tplkey)||$this->tplkey==""||strtolower($this->tplkey)=="default"){
				$this->display();
		}else{
			$this->display('mobile$'.$this->tplkey);
		}
	}
	
	public function dologin(){
		import("ORG.Util.String");
		$this->load_shopinfo();
		if(IS_POST){
				$db=D('Member');
				$user=I('post.user');
				$pwd=I('post.password');
				if(empty($user)||empty($pwd)||$user==""||$pwd==""){
						$data['msg']="提交参数不完整，登录失败";
						$data['error']=1;
						$this->ajaxReturn($data);
						exit;
				}
				if(!validate_user($user)){
						$data['msg']="用户名不合法";
						$data['error']=1;
						$this->ajaxReturn($data);
						exit;
				}
				$where['user']=$user;
				$info=$db->where($where)->find();
				if($info==false){
						$data['msg']="用户名不存在";
						$data['error']=1;
						$this->ajaxReturn($data);
						exit;
				}else{
					if($info['password']!=md5($pwd)){
						$data['msg']="用户名密码不正确";
						$data['error']=1;
						$this->ajaxReturn($data);
						exit;
					}
							
					$token=md5(uniqid());
					$tranDb=new Model();
					$save['token']=$token;
					$save['browser']=$this->browser;
					$save['online_time']=$this->getLimitTime();
					$save['update_time']=$now;
					$save['login_time']=$now;
					$arrdata['uid']=$info['id'];
					$arrdata['add_date']=getNowDate();
					$arrdata['over_time']=$this->getLimitTime();
					$arrdata['update_time']=$now;//更新时间
					$arrdata['login_time']=$now;//首次登录时间
					$arrdata['last_time']=$now;//最后在线时间
					$arrdata['shopid']=$this->shop[0]['shopid'];
					$arrdata['routeid']=$this->shop[0]['id'];
					$arrdata['browser']=$this->browser;
					$arrdata['token']=$token;
					$arrdata['mode']='0';//无需认证
					$arrdata['agent']=$this->agent;
					$flag=$tranDb->table(C('DB_PREFIX').'member')->where($where)->save($save); 
					$flagauth=$tranDb->table(C('DB_PREFIX').'authlist')->add($arrdata);  
					if($flag&&$flagauth){
						$tranDb->commit();
						cookie('token',$token);
						cookie('mid',$info['id']);
						$data['error']=0;
						$data['url']=U("userauth/showtoken");
						$this->ajaxReturn($data);
					}else{
						$tranDb->rollback();
						$data['msg']="登录失败，请稍候再试";
							$data['error']=1;
							$this->ajaxReturn($data);
					}
					//Log::write($token);
					
					
				}
				
			}
	}
	
	public function regu(){
		$this->load_shopinfo();
		if(IS_POST){
			$user=I('post.user');
				$pwd=I('post.password');
				$phone=I('post.phone');
				$qq=I('post.qq');
				if(!validate_user($user)){
						$data['msg']="用户名必须是3到20位数字或字母组成";
						$data['error']=1;
						$this->ajaxReturn($data);
						exit;
				}
				
					if(!isPhone($phone)){
						$data['msg']="请填写有效的11位手机号码";
						$data['error']=1;
						$this->ajaxReturn($data);
						exit;
					}
					if(!isQQ($qq)){
						$data['msg']="请填写有效的QQ号码";
						$data['error']=1;
						$this->ajaxReturn($data);
						exit;
					}
			$db=D('Member');
			
			$where['user']=$user;
			$where['routeid']=$this->shop[0]['id'];
	
			$info=$db->where($where)->find();
			if($info!=false){
				$data['msg']="当前帐号已存在";
					$data['error']=1;
					$this->ajaxReturn($data);
			}
			$token=md5(uniqid());
			$now=time();
			$tranDb=new Model();
			
			$_POST['token']=$token;
					
					unset($_POST['__hash__']);
					unset($_POST['smscode']);
			
			$_POST['password']=md5($_POST['password']);		
			$_POST['shopid']=$this->shop[0]['shopid'];
			$_POST['routeid']=$this->shop[0]['id'];
			$_POST['browser']=$this->browser;
			$_POST['mode']='0';//注册认证
			$_POST['add_time']=$now;
			$_POST['update_time']=$now;
			$_POST['login_time']=$now;
			$_POST['add_date']=getNowDate();
			$_POST['online_time']=$this->getLimitTime();
			$tranDb->startTrans();
			$flag=$tranDb->table(C('DB_PREFIX').'member')->add($_POST);  
			$newid=$tranDb->getLastInsID();
			$arrdata['uid']=$newid;
					$arrdata['add_date']=getNowDate();
					$arrdata['over_time']=$this->getLimitTime();
					$arrdata['update_time']=$now;//更新时间
					$arrdata['login_time']=$now;//首次登录时间
					$arrdata['last_time']=$now;//最后在线时间
					$arrdata['shopid']=$this->shop[0]['shopid'];
					$arrdata['routeid']=$this->shop[0]['id'];
					$arrdata['browser']=$this->browser;
					$arrdata['token']=$token;
					$arrdata['mode']='0';//
					$arrdata['agent']=$this->agent;
					$flagauth=$tranDb->table(C('DB_PREFIX').'authlist')->add($arrdata);  

					if($flag&$flagauth){
							$tranDb->commit();
							cookie('token',$_POST['token']);
							cookie('mid',$newid);
							$data['error']=0;
							$data['url']=U("userauth/showtoken");
							$this->ajaxReturn($data);
					}else{
						$tranDb->rollback();
						Log::write("注册认证错误:".$tranDb->getLastSql());
						$data['msg']="注册失败，请稍候再试";
							$data['error']=1;
							$this->ajaxReturn($data);
					}
				
			
		}
	}
	
	public function showtoken(){
		$this->load_shopinfo();
		$url="http://".cookie('gw_address').":".cookie('gw_port')."/wifidog/auth?token=".cookie('token');
		$jump=U('User/index');
		
		/*
	    if(cookie('gw_url')!=null){
	    	$jump=cookie('gw_url');
	    }else{
	    	$jump=U('User/index');
	    }*/
		$wait=C('WAITSECONDS');
		$open=C('OPENPUSH');
		$way=C('SHOWWAY');//展示时间标准
		$pid=$this->shop[0]['pid'];
		$agentpush=false;
		
		if(empty($pid)||$pid<=0){
			//无代理
			$wait=C('WAITSECONDS');
		}else{
			//获取代理商广告信息
			$adset=D('Agentpushset');
			$awhere['aid']=$pid;
			$adinfo=$adset->where($awhere)->find();
			if($adinfo==false){
				//无设定
			}else{
				if($adinfo['pushflag']==1){
					$agentpush=true;
				}
				if($way==1){
					$wait=$adinfo['showtime'];//展示时间
				}
			}
		}
		
		if($open==1){
			$where['state']=1;
			$where['startdate']= array('elt',time());
			$where['enddate']= array('egt',time());
			
			if($agentpush){
				$where['aid']=array('in','0,'.$pid);
			}else{
				$where['aid']=0;
			}
			$ads=D('Pushadv')->where($where)->field("id,pic,aid")->select();
			
			
			/*统计展示*/
				///////////////////////////
				$tr=new Model();
				$time=time();
				$tr->startTrans();
				$arrdata['showup']=1;
				$arrdata['hit']=0;
				$arrdata['shopid']=$this->shop[0]['shopid'];
				$arrdata['add_time']=$time;
				$arrdata['add_date']=getNowDate();
				
				
				
				foreach($ads as $k=>$v){
					if($v['aid']>0){
						$arrdata['mode']=50;
						$arrdata['agent']=$v['aid'];
					}else{
						$arrdata['mode']=99;
						$arrdata['agent']=0;
					}
					$arrdata['aid']=$v['id'];
					$tr->table(C('DB_PREFIX')."adcount")->add($arrdata);
					
				}
				$tr->commit();
				///////////////////////////
			
			$this->assign('ad',$ads);
			
		}
		
		$this->assign('waitsecond',$wait);
		$this->assign('wifiurl',$url);
		$this->assign('jumpurl',$jump);
		if(empty($this->tplkey)||$this->tplkey==""||strtolower($this->tplkey)=="default"){
				$this->display();
		}else{
			
			$this->display("showtoken$".$this->tplkey);
		}
	}
	
	public function reg(){
		$this->load_shopinfo();
			
		if(empty($this->tplkey)||$this->tplkey==""||strtolower($this->tplkey)=="default"){
				$this->display();
		}else{
			
			$this->display("reg$".$this->tplkey);
		}
	
	}
	
	public function comments(){
		$this->load_shopinfo();
	if(empty($this->tplkey)||$this->tplkey==""||strtolower($this->tplkey)=="default"){
				$this->display();
		}else{
			
			$this->display("comments$".$this->tplkey);
		}
	}
	
	private  function getLimitTime(){
		if($this->shop[0]['timelimit']!=""&&$this->shop[0]['timelimit']!="0"){
			import("ORG.Util.Date");
			$dt=new Date(time());
			$date=$dt->dateAdd($this->shop[0]['timelimit'],'n');//默认7天试用期
			return strtotime($date);
		}
		return "";
		
	}
	
	/*
	 * 一键上网
	 */
	public function noAuth(){
		$db = D('Ad');
		$gw_id = preg_replace('/:/i', "",I('get.apmac'));
		$sql="select DISTINCT a.ad_pos,ad_thumb,a.info,ad_sort from ".C('DB_PREFIX')."ad a left join ".C('DB_PREFIX')."advertise c on a.ad_pos=c.id left join ".C('DB_PREFIX')."shop b on c.id=b.shop_pos left join ". C('DB_PREFIX')."routemap d on b.id=d.shopid";
		$where.="a.startdate <= '".strtotime(date("Y-m-d"))."' and a.enddate >= '".strtotime(date("Y-m-d"))."' and d.gw_id = '".$gw_id."' order by ad_sort asc";
		if(!empty($where)){
			$sql.=" where ".$where;
		}
		$lists = $db->query($sql);
		if(empty($lists)){
			$lists = $db->where("title like '%通用%'")->select();
		}
		$this->assign('lists',$lists);
		$this->display();
	}
	
	/*
	 * 客户留言
	 */
	public function addmsg(){
		$this->load_shopinfo();
		if(IS_POST){
				$user=I('post.user');
				
				$phone=I('post.phone');
				$info=I('post.content');
				
				
					if(!isPhone($phone)){
						$data['msg']="请填写有效的11位手机号码";
						$data['error']=1;
						$this->ajaxReturn($data);
						exit;
					}
					
			$db=D('Comment');
			$_POST['shopid']=$this->shop[0]['shopid'];
			$_POST['routeid']=$this->shop[0]['id'];
			if($db->create()){
				if($db->add()){
					$newid=$db->getLastInsID();
					$data['error']=0;
					$this->ajaxReturn($data);
				}else{
					$data['msg']="提交留言失败，请稍候再试";
					$data['error']=1;
					$this->ajaxReturn($data);
				}
			}else{
				$data['msg']=$db->getError();
				$data['error']=1;
				$this->ajaxReturn($data);
			}
		}
	}
	
	/*
	* 广告显示
	*/
	public function showad(){
		$this->load_shopinfo();
		$id=I('get.id','0','int');
		$db=D('Ad');
		$where['id']=$id;
		$where['uid']=$this->shop[0]['shopid'];
		
		$info=$db->where($where)->find();

		/*统计展示*/
				///////////////////////////
				$tr=new Model();
				$time=time();
				$tr->startTrans();
				$arrdata['showup']=0;
				$arrdata['hit']=1;
				$arrdata['shopid']=$this->shop[0]['shopid'];
				$arrdata['add_time']=$time;
				$arrdata['add_date']=getNowDate();
				$arrdata['mode']=1;
	
				$arrdata['aid']=$id;
				$tr->table(C('DB_PREFIX')."adcount")->add($arrdata);
				
				$tr->commit();
				///////////////////////////
		$this->assign('adinfo',$info);
		$this->display();
		
	}
}