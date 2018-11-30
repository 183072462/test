<?php
namespace Home\Controller;
use Think\Controller;
use Common\Controller\BaseController;
Class CommonController extends BaseController{


	public function _initialize(){
		
        $this->_name = CONTROLLER_NAME;
	    
		if(!session('uid')){
			redirect(U('Public/login'));
		}
       
        $config =   S('DB_CONFIG_DATA');
        if(!$config){
            $config =   api('Config/lists');
            S('DB_CONFIG_DATA',$config);
        }
        C($config); 

		$name=MODULE_NAME.'/'.CONTROLLER_NAME.'/'.ACTION_NAME;
		if(!authcheck($name,session('uid'))){
		   //$this->error(''.session('username').'很抱歉,此项操作您没有权限！');
		   $this->mtReturn(300,''.session('username').'很抱歉,此项操作您没有权限！',$_REQUEST['navTabId']);
		}		 
	}
	
	public function noid($num,$type){
	  switch ($type){
		  case 1:
		  return date("Ymdhis",time()) . rand(10,99);
		  break;
		  case 2:
		  return date("Ymd",time()) . rand(1000,9999);
		  break;
		  default:
		  $count = M($this->dbname)->count();
	      return date("ymds",time()) . str_pad( ($count+1), $num, '0', STR_PAD_LEFT);
	  }
	  
		
	}

	
}