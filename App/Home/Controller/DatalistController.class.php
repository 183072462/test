<?php

/**
 *      分类字典控制器
 *      [X-Mis] (C)2007-2099  
 *      This is NOT a freeware, use is subject to license terms
 *      http://www.xinyou88.com
 *      tel:400-000-9981
 *      qq:16129825
 */

namespace Home\Controller;
use Think\Controller;

class DatalistController extends CommonController{

   public function _initialize() {
        parent::_initialize();
        $this->dbname = CONTROLLER_NAME;
    }
	
   function _filter(&$map) {
	   if(!in_array(session('uid'),C('ADMINISTRATOR'))){
		   
	   }
	   if(isset($_REQUEST['pid']) && $_REQUEST['pid'] != ''){
	      $map['pid'] =array('EQ', $_REQUEST['pid']);
       }else{
		  $map['pid'] =array('EQ', 0);
	   }
	   

	}
	
   public function _befor_index(){ 
   }
  
  
  public function _befor_add(){
	  $list=cateTreed($pid=0,$level=0);
	  $this->assign('list',$list);
    
  }
	
   public function _after_add($id){
    
   }

  public function _befor_insert($data){
     if (I('pid')==0){
     $data['level']=0;
	 }else{
	 $plevel=M($this->dbname)->where('id='.I('pid').'')->field('level')->limit(1)->select();
     $level=$plevel[0]['level']+1;
     $data['level']=$level;
	 }
    return $data;

  }
  
  public function _befor_edit(){
    $list=cateTreed($pid=0,$level=0);
    $this->assign('list',$list);
  }
   
  public function _befor_update($data){
     if (I('pid')==0){
     $data['level']=0;
	 }else{
	 $plevel=M($this->dbname)->where('id='.I('pid').'')->field('level')->limit(1)->select();
     $level=$plevel[0]['level']+1;
     $data['level']=$level;
	 }
    return $data;
  }
  
    public function _after_edit($id){
     
   }

   public function _befor_lock($id){
	  $data=D($this->dbname)->find($id);
	  D($this->dbname)->where(array("pid"=>$id))->setField('status',$data['status']);
   }
   
   public function _befor_del($id){
	   
   }


	
	

}