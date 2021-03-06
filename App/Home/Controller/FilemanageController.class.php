<?php

/**
 *      我的文档控制器
 *      [X-Mis] (C)2007-2099  
 *      This is NOT a freeware, use is subject to license terms
 *      http://www.xinyou88.com
 *      tel:400-000-9981
 *      qq:16129825
 */

namespace Home\Controller;
use Think\Controller;

class FilemanageController extends CommonController{

   public function _initialize() {
        parent::_initialize();
        $this->dbname = CONTROLLER_NAME;
    }
	
   function _filter(&$map) {
	   if($_REQUEST['classify']){
		   $map['classify']=$_REQUEST['classify'];
		   $this->classify=$_REQUEST['classify'];
		   }
	  $this->classifylist=D('Datalist')->getselect(68);
	  $name1=MODULE_NAME.'/'.CONTROLLER_NAME.'/seeall';
	   if(!in_array(session('uid'),C('ADMINISTRATOR'))&&!authcheck($name,session('uid'))&&!authcheck($name1,session('uid'))){
		  $map['uid'] = array('EQ', session('uid')); 
	   }
	   if(isset($_REQUEST['time1']) && $_REQUEST['time1'] != ''&&isset($_REQUEST['time2']) && $_REQUEST['time2'] != ''){$map['addtime'] =array(array('egt',I('time1').' 00:00:00'),array('elt',I('time2').' 59:59:59'));}

	}
	
   public function _befor_index(){ 
   }
  
  
  public function _befor_add(){
	 
	  //特色加分分类
	  $this->classifylist=D('Datalist')->getselect(68);
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
	 //特色加分分类
	  $this->classifylist=D('Datalist')->getselect(68);
  }
   
  public function _befor_update($data){

  }
  
    public function _after_edit($id){
     
   }

   public function _befor_view($id){
	   $wheid['id']=$id;
	   $plist=D('filemanage')->where($wheid)->field('classify,attid')->find();
	   //获取荣誉级别levelid
	  $this->classify=D('datalist')->getselectname($plist['classify']);
	  //获取附件
	  $wfildata['attid']=$plist['attid'];
	  $this->filelist=M('files')->where($wfildata)->select();
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