<?php

/**
 *      我的文档控制器
 */

namespace Home\Controller;
use Think\Controller;

class WendangsController extends CommonController{

   public function _initialize() {
        parent::_initialize();
        $this->dbname = 'wendang';
    }
	
   function _filter(&$map) {
	   if(!in_array(session('uid'),C('ADMINISTRATOR'))){
		   $map['juid'] = array('like','%'.session("uid").'%');
	   }
	   
	   if(isset($_REQUEST['type']) && $_REQUEST['type'] != ''){$map['type'] =array('EQ', urldecode(I('type'))); }


	}
	
   public function _befor_index(){ 
     $typelist =  M($this->dbname)->where(array('type'=>array('neq',''),'uid'=>array('EQ', session('uid'))))->distinct('type')->field('type')->select();
 $this->assign('typelist', $typelist); 
   }
  
  
  public function _befor_add(){
	  $attid=time();
	  $this->assign('attid',$attid);
    
  }
  
  public function _befor_insert($data){

  }
	
   public function _after_add($id){
    
   }


  
  public function _befor_edit(){
     $model = D($this->dbname);
	 $info = $model->find(I('get.id'));
	 $attid=$info['attid'];
	 $this->assign('attid',$attid);
  }
   
  public function _befor_update($data){

  }
  
    public function _after_edit($id){
     
   }

   public function _befor_view($id){
	   
   }
   
   public function _befor_del($id){
	   
   }

   public function outxls() {
		$model = D($this->dbname);
		$map = $this->_search();
		if (method_exists($this, '_filter')) {
			$this->_filter($map);
		}
		$list = $model->where($map)->field('id,title,type,juid,juname,beizhu')->select();
	    $headArr=array('ID','文档标题','文档分类','共享给','共享给','备注说明');
	    $filename='我的文档';
		$this->xlsout($filename,$headArr,$list);
	}
	
	public function fenxi(){
	 $this->display();
	}
	
	

}