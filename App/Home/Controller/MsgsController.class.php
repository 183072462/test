<?php

/**
 *      消息管理控制器
 */

namespace Home\Controller;
use Think\Controller;
use Common\Controller\BaseController;
class MsgsController extends BaseController{

   public function _initialize() {
        if(!session('uid')){redirect(U('Public/login'));}
		$config =   S('DB_CONFIG_DATA');
        if(!$config){
            $config =   api('Config/lists');
            S('DB_CONFIG_DATA',$config);
        }
        C($config); 
        $this->dbname = 'msg';
    }
	
   function _filter(&$map) {
	   if(!in_array(session('uid'),C('ADMINISTRATOR'))){
		   
	   }
	   
	   if(isset($_REQUEST['type']) && $_REQUEST['type'] != ''){$map['type'] =array('EQ', urldecode(I('type'))); }


	}
	
   public function _befor_index(){ 
     $typelist =  M($this->dbname)->where(array('type'=>array('neq','')))->distinct('type')->field('type')->select();
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
		$list = $model->where($map)->field('id,type,content,juid,viewtime')->select();
	    $headArr=array('ID','类型','内容','接收人ID','查看时间');
	    $filename='消息管理';
		$this->xlsout($filename,$headArr,$list);
	}
	
	public function fenxi(){
	 $this->display();
	}
	
	

}