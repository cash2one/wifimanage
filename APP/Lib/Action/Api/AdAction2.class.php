<?php
class AdAction extends BaseApiAction
{
	/**
	 * 广告页首页
	 * 获取ad表的图片地址以及对应的url
	 * $apmac ap的mac地址
	 */
	public function index()
	{
		if(!session('apmac')){
			$apmac = I('apmac');
			session('apmac',$apmac);
		}
		$where['advertise.name'] = 'Banner';
		$this->getList($where);
	}

	/**
	 * 游戏列表
	 * $apmac ap的mac地址
	 */
	public function gameList()
	{
		$where['advertise.name'] = '游戏列表';
		$this->getList($where);
	}

	/**
	 * app列表
	 */
	public function appList()
	{
		$where['advertise.name'] = 'app列表';
		$this->getList($where);
	}

	/**
	 * 视频列表
	 */
	public function videoList()
	{
		$where['advertise.name'] = '视频列表';
		$this->getList($where);
	}

	/**
	 * 列表详情
	 */
	public function listsInfo()
	{
		$lists = M('ad')->find(I('id'));

		$this->assign('lists',$lists);
		$this->display();
	}

	/**
	 * 点击广告时，点击数+1
	 */
	public function adHit()
	{
		$apid = I('apid');
		$where['aid'] = I('aid');
		$where['apid'] = $this->getApid($apid);
		M('adcount')->where($where)->setInc('hit');
		//$this->ajaxReturn();
	}

	/**
	 * 获取数据列表
	 *$apmac 获取的apmac地址
	 * @param array $where 游戏/app/视频
	 * @return array $lists
	 */
	public function getList($where=null)
	{
		$portalInfo = session('portalInfo');
		$apmac = $portalInfo['apmac'];
		$name = $portalInfo['name'];
		$show_limit = M('advertise')->getFieldByName($name,'show_limit');
		//$apmac = '00:16:16:1F:8B:00';
		$limit = I('limit');
		if(empty($limit)){
			$limit = 20;
		}else{
			$limit += 20;
		}
		$this->assign('limit',$limit);

		$where['startdate'] = array('ELT',time());
		$where['enddate'] = array('EGT',time());
		if($apmac){
			$where['ad.default_status'] = 0;
			$lists = D('AdView')->field('aid,uid,ad_thumb,url,description,title,info,apid,`name`')->where($where)->where("left(gw_id,17) = '{$apmac}'")->order('ad_sort asc,aid asc,name asc,ad.add_time asc')->limit(0,$show_limit)->select();
		}
		if(empty($lists)){
			$where['ad.default_status'] = 1;
			$lists = M()->table('wifi_ad ad,wifi_advertise advertise')->field('ad.id as aid,ad_thumb,url,description,title,info,`name`')->where($where)->where("ad.ad_pos = advertise.id")->order('ad.ad_sort asc,aid asc,advertise.name asc,ad.add_time asc')->limit(0,$show_limit)->select();
		}
		$this->adcount($lists);//统计页面浏览次数
		$this->assign('lists',$lists);

		//判断是否数据已全部加载完,如果没有则显示加载更多
		$count = count($lists);
		if($count > $limit){
			$more = 1;
			$this->assign('more',$more);
		}

		//点击加载更多
		if($_POST)
		{
			$data['status'] = 0;
			$data['message'] = "加载中..";
			$data['lists'] = $lists;
			$data['limit'] = $limit;
			return $this->ajaxReturn($data);
		}

		$this->display();
	}

	/**
	 * portal页面接口
	 * $apmac apmac地址
	 * $ad_type 广告位名称
	 * $telephone 手机号码
	 * @return 返回json $lists
	 */
	public function portal()
	{
		session('portalInfo',I());
		$apmac = I('apmac');
		$name = I('name');

		//$where['name'] = array(array('eq','PORTAL全屏'),array('eq','登录页'),'or');
		$where['name'] = $name;
		$show_limit = M('advertise')->getFieldByName($name,'show_limit');
		$where['startdate'] = array('ELT',time());
		$where['enddate'] = array('EGT',time());
		if($apmac){
			$where['ad.default_status'] = 0;
			$subQuery = D('AdView')->field('aid,uid,mode,ad_thumb,url,description,title,info,apid,`name`,ad_sort')->where($where)->where("left(routemap.gw_id,17) = '{$apmac}'")->order('ad_sort asc,aid desc,ad.add_time desc')->limit(0,$show_limit)->buildSql(); 
			$lists = M()->table($subQuery.' a')->group('ad_sort')->order('ad_sort asc')->select();
		}
		if(empty($lists)){
			$where['ad.default_status'] = 1;			
			$subQuery = M()->table('wifi_ad ad,wifi_advertise advertise')->field('ad.id as aid,ad.uid as uid,ad.mode as mode,ad.ad_sort as ad_sort,ad_thumb,url,description,title,info,`name`')->where($where)->where("ad.ad_pos = advertise.id")->order('ad.ad_sort asc,aid desc,ad.add_time desc')->limit(0,$show_limit)->buildSql(); 
			$lists = M()->cache(true)->table($subQuery.' a')->group('ad_sort')->select();
		}
		$this->adcount($lists);
		$jsonpcallback = $_REQUEST['jsonpcallback'];
		echo "$jsonpcallback(".json_encode($lists).")";
	}

	public function getApid($apid=null)
	{
		if(!isset($apid)){
			$portalInfo = session('portalInfo');
			$apid = M('routemap')->getFieldByGw_id($portalInfo['apmac'],'id');
			if(empty($apid)){
				$apid = 0;
			}
		}
		return $apid;
	}

	/**
	 * 记录广告浏览详情和次数
	 * @param array $lists
	 */
	public function adcount($lists)
	{
		foreach($lists as $value){
			//记录广告浏览详情
			$portalInfo = session('portalInfo');
			$apid = $this->getApid($value['apid']);
			$recode['adid'] = $value['aid'];
			$recode['apid'] = $apid;
			$recode['userip'] = $portalInfo['userip'];
			$recode['apmac'] = $portalInfo['apmac'];
			$recode['acname'] = $portalInfo['acname'];
			$recode['nasid'] = $portalInfo['nasid'];
			$recode['tel_version'] = $portalInfo['tel_version'];
			$recode['username'] = $portalInfo['telephone'];
			$recode['usermac'] = $portalInfo['usermac'];
			$recode['add_time'] = time();
			$recode['add_date'] = date('Y-m-d');
			M('adrecode')->add($recode);

			//查询是否已经存在记录，没有则新增，有则增加次数
			$check = M('adcount')->where(array('apid'=>$apid,'aid'=>$value['aid'],'add_hour'=>date('G')))->find();

			$adcount = M('Adcount');
			if(!$check){
				//新增数据
				$data['aid'] = $value['aid'];
				$data['apid'] = $apid;
				$data['add_time'] = time();
				$data['add_date'] = date('Y-m-d');
				$data['add_hour'] = date('G');
				//$data['mode'] = 2;
				$data['ip'] = get_client_ip();
				$data['browser'] = get_browser();
				$data['uid'] = $value['uid'];
				$data['mode'] = $value['mode'];
				$adcount->add($data);
			}else{
				//增加浏览次数1
				$adcount->where(array('id'=>$check['id']))->setInc('showup');
			}
		}
	}
	public function adcountnum(){
		$time=1453838400;
		$index=0;
		while ($index<100)
		{
			$model=M();
			$date=date('Y-m-d',$time);
			$model->execute("insert into wifi_adcount(apid,aid,showup,hit,add_time,add_date,uid)VALUES (17,67,11,11,'$time','$date',3)");
			$index++;
			$time+=3600;
		}
	}
}
