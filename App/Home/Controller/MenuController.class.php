<?php
namespace Home\Controller;
use Think\Controller;

Class MenuController extends CommonController{
  
  public function _initialize() {
        parent::_initialize();
        $this->dbname = CONTROLLER_NAME;
    }
	
	
  public function _befor_index(){ 
    $list=cateTree($pid=0,$level=0,$this->dbname);
    $this->assign('list',$list);	
  }
  
  
  public function _befor_add(){
    $list=cateTree($pid=0,$level=0,$this->dbname);
     $this->assign('list',$list);
  }
  
  public function _befor_insert($data){
	 $pid = I('pid');
	 if ($pid==0){
	 $data['level']=0;
	 }else{
	 $level=D($this->dbname)->where('id='.$pid.'')->field('level')->limit(1)->select();
     $level=$level[0]['level']+1;
     $data['level']=$level;
	 }
	 return $data;
  }
  
  
  public function _befor_edit(){
     $list=cateTree($pid=0,$level=0,$this->dbname);
     $this->assign('list',$list);
  }
  
    public function _befor_update($data){
	 $pid = I('pid');
	 if ($pid==0){
	 $data['level']=0;
	 }else{
	 $level=D($this->dbname)->where('id='.$pid.'')->field('level')->limit(1)->select();
     $level=$level[0]['level']+1;
     $data['level']=$level;
	 }
	 return $data;
  }
  
  public function _befor_lock($id){
	  $data=D($this->dbname)->find($id);
	  D($this->dbname)->where(array("pid"=>$id))->setField('status',$data['status']);
   }
  

  
  
}