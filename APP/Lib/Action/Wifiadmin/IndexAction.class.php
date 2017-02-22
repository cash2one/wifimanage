<?php
class IndexAction extends AdminAction{
	
	public function  index(){
		$nav['m']=$this->getActionName();
		$nav['a']='index';

		$time = strtotime(date('Y-m-d',time()));
		$now = time();

		//展示中的广告
		$adonline = M('ad')->where("enddate>=$time")->count('id');


		//累计投放广告
		//$adcount = M('adcount')->field(COUNT('id'))->limit(1)->order('id ASC')->select();
		$adcountData = file_get_contents('./filecache/adcount.txt');
		$adcountDataArr = json_decode($adcountData,true);
		if(!empty($adcountDataArr)){
			foreach($adcountDataArr as $key=>$value){
				$adcount += $value['adcount'];
			}
		}
	
		

		//累计浏览用户
		//$adshowup = M('adcount')->field('SUM(showup) as showup')->select();
		$adshowupData = file_get_contents('./filecache/adshowup.txt');
		$adshowupDataArr = json_decode($adshowupData,true);
		if(!empty($adshowupDataArr)){
			foreach($adshowupDataArr as $key=>$value){
				$adshowup += $value['adshowup'];
			}
		}


	


		//累计点击用户
		//$adhit = M('adcount')->field('SUM(hit) as hit')->where('hit <> 0')->select(); 
		$adhitData = file_get_contents('./filecache/adhit.txt');
		$adhitDataArr = json_decode($adhitData,true);
		if(!empty($adhitDataArr)){
			foreach($adhitDataArr as $key=>$value){
				$adhit += $value['adhit'];
			}
		}
		
	
		
				


		$this->assign('adonline',$adonline);
		$this->assign('adcount',$adcount);
		$this->assign('adshowup',$adshowup);
		$this->assign('adhit',$adhit);	
		$this->assign('nownav',$nav);
		$this->display();
	}


	public function pwd(){
		$nav['m']=$this->getActionName();
		$nav['a']='index';
		$this->assign('nownav',$nav);
		if(IS_POST){
				$pwd=I('post.password');
				if($pwd==""){
					$data['error']=1;
			    	$data['msg']="新密码不能为空";
			    	return $this->ajaxReturn($data);
				}
				if(!validate_pwd($pwd)){
					$data['error']=1;
			    		$data['msg']="密码由4-20个字符 ，数字，字母或下划线组成";
			    		return $this->ajaxReturn($data);
				}
							
				$db=D('Admin');
				$info=$db->where(array('id'=>$this->userid))->field('id,user,password')->find();
				log::write($info['password']);
				if(md5($_POST['oldpwd'])!=$info['password']){

			
						$data['error']=1;
			    		$data['msg']="旧密码不正确";
			    		return $this->ajaxReturn($data);
				}
			
			
			$_POST['update_time']=time();
			$_POST['password']=md5($_POST['password']);
			$where['id']=$this->userid;
				if($db->where($where)->save($_POST)){
					$data['error']=0;
		    		$data['msg']="更新成功";
		    		return $this->ajaxReturn($data);
				}else{
					$data['error']=1;
		    		$data['msg']=$db->getError();
		    		return $this->ajaxReturn($data);
				}
		}else{
			$this->display();
		}
	}
}