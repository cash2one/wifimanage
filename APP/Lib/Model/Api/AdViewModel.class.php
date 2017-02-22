<?php
class AdViewModel extends ViewModel{
    public $viewFields  = array(
        'ad'=>array('id'=>'aid','ad_pos'=>'ad_pos','ad_thumb','title','description','info','url','uid'=>'ad_uid','ad_sort'),
        'advertise'=>array('id'=>'advid','name','_on'=>'ad.ad_pos = advertise.id'),
        'routemap_ad'=>array('adid','apid','_on'=>'ad.id = routemap_ad.adid'),
        'routemap'=>array('gw_id','_on'=>'routemap_ad.apid = routemap.id'),
    );
}