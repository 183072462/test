<?php
namespace Home\Controller;
use Think\Controller;
class DwtxlController extends CommonController {
   
    public function _initialize() {
        parent::_initialize();
        $this->dbname = 'user';
    }
	
	
	function _filter(&$map) {
		
		if(!in_array(session('uid'),C('ADMINISTRATOR'))){
		  $map['id'] = array('NEQ', '1'); 
	   }
		
		if(isset($_REQUEST['depname']) && $_REQUEST['depname'] != ''){
		 $map['depname'] =array('EQ',  urldecode(I("depname")));
		}
        
		if(isset($_REQUEST['posname']) && $_REQUEST['posname'] != ''){
		 $map['posname'] =array('EQ',  urldecode(I("posname")));
		}
	}
	
	public function _befor_index(){
	$dlist =  M($this->dbname)->distinct('depname')->field('depname')->select();
    $this->assign('dlist', $dlist); 
	$plist =  M($this->dbname)->distinct('posname')->field('posname')->select();
    $this->assign('plist', $plist);  	 
   }
  
  
 
}