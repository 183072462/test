<?php
namespace Home\Controller;
use Think\Controller;

Class LogController extends CommonController{

    public function _initialize() {
        parent::_initialize();
        $this->dbname = CONTROLLER_NAME;
    }
	
	function _filter(&$map) {
		//$map['id'] = array('egt', 2);
     if(isset($_REQUEST['time1']) && $_REQUEST['time1'] != ''&&isset($_REQUEST['time2']) && $_REQUEST['time2'] != ''){$map['addtime'] =array(array('egt',I('time1').' 00:00:00'),array('elt',I('time2').' 59:59:59'));}
	}



  
  public function Del() {
	$jztime=time()-180*24*60*60;
    $jztime=date("Y-m-d H:i:s",$jztime);
    $list = M('log')->where("addtime<'".$jztime."'")->delete();
	$this->mtReturn(200,'清理180天以前的日志记录成功!!!','',false); 
  }
}