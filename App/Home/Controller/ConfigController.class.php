<?php
namespace Home\Controller;
use Think\Controller;
class ConfigController extends CommonController {
   
    public function _initialize() {
        parent::_initialize();
        $this->dbname = CONTROLLER_NAME;
    }
	function _filter(&$map) {
		if(IS_POST&&isset($_REQUEST['filter']) && $_REQUEST['filter'] != ''){
		 $map['fenlei'] = array('EQ', I('filter'));
		}
	}
  
   public function _after_add($data){
     //S('DB_CONFIG_DATA',null);
   }

  public function _befor_insert($data){
	 $data['addtime']=date("Y-m-d H:i:s",time());
	 return $data;
  }
  
   
  public function _befor_update($data){
	 $data['updatetime']=date("Y-m-d H:i:s",time());
	 return $data;
  }
  
    public function _after_edit($data){
     //S('DB_CONFIG_DATA',null);
   }

   public function _befor_del($id){
	  //S('DB_CONFIG_DATA',null);
   }
	
}