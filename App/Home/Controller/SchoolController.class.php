<?php

/**
 *      学校少年宫控制器
 */

namespace Home\Controller;
use Think\Controller;

class SchoolController extends CommonController{

   public function _initialize() {
        parent::_initialize();
        $this->dbname = CONTROLLER_NAME;
    }
	
   function _filter(&$map) {
	   if(I('levelid')){
		   $this->levelid=$map['levelid']=I('levelid');
		   
		   }
		   	$name=MODULE_NAME.'/'.CONTROLLER_NAME.'/seeall';
		if(authcheck($name,session('uid'))){
		   
		}		
	   else if(!in_array(session('uid'),C('ADMINISTRATOR'))){
		  $map['uid'] = array('EQ', session('uid')); 
	   }
	     if(isset($_REQUEST['time1']) && $_REQUEST['time1'] != ''&&isset($_REQUEST['time2']) && $_REQUEST['time2'] != ''){$map['createtime'] =array(array('egt',I('time1').' 00:00:00'),array('elt',I('time2').' 59:59:59'));}

	}
	
   public function _befor_index(){ 
   }
  
  
  public function _befor_add(){
	  //获取级别
	  $this->levelist=D('Datalist')->getselect(9);
	  	  //获取区域
	  $this->area=D('Datalist')->getselect(33);
	 
	  $attid=time();
	  $this->assign('attid',$attid);
	  $this->levelid=I('levelid');
    
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
	  //获取级别
	  $this->levelist=D('Datalist')->getselect(9);
	  	  //获取区域
	  $this->area=D('Datalist')->getselect(33);
  }
   
  public function _befor_update($data){

  }
  
    public function _after_edit($id){
     
   }

   public function _befor_view($id){
	    $wheid['id']=$id;
	   $plist=D('school')->where($wheid)->field('levelid,area,attid')->find();
	  //获取荣誉级别levelid
	  $this->levelname=D('datalist')->getselectname($plist['levelid']);
	  //获取区域名
	  $this->area=D('datalist')->getselectname($plist['area']);
	  //获取附件
	  $wfildata['attid']=$plist['attid'];
	  $filelist=M('files')->where($wfildata)->select();
	  $this->assign('filelist',$filelist);
   }
   
   public function _befor_del($id){
	   
   }

   public function outxls() {
		$model = D($this->dbname);
		$map = $this->_search();
		if (method_exists($this, '_filter')) {
			$this->_filter($map);
		}
		$list = $model->where($map)->field('id,title,juid,juname,beizhu')->select();
	    $headArr=array('ID','标题','共享给','共享给','备注说明');
	    $filename='我的文档';
		$this->xlsout($filename,$headArr,$list);
	}
	
	public function fenxi(){
	 $this->display();
	}
	
	

}