<?php

/**
 *      稿件上报控制器
 */

namespace Home\Controller;
use Think\Controller;

class ManuscriptController extends CommonController{

   public function _initialize() {
        parent::_initialize();
        $this->dbname = CONTROLLER_NAME;
    }
	
   function _filter(&$map) {
	    $name1=MODULE_NAME.'/'.CONTROLLER_NAME.'/seeall';
	   if(!in_array(session('uid'),C('ADMINISTRATOR'))&&!authcheck($name,session('uid'))&&!authcheck($name1,session('uid'))){
		  $map['uid'] = array('EQ', session('uid')); 
	   }
	   $names1=MODULE_NAME.'/'.CONTROLLER_NAME.'/pub';
	   if(authcheck($names1,session('uid'))){
		 $this->ispub=1;
	   }
	   if($uid=I('uid')){
		   $map['uid']=$uid;
		   $this->uid=$uid;
		   }
	   if($pubid=I('pubid')){
		   $map['pubid']=$pubid;
		   $this->pubid=$pubid;
	   }
	   if($sstatus=I('sstatus')){
		   $map['sstatus']=$sstatus;
		   $this->sstatus=$sstatus;
	   }
	   if(isset($_REQUEST['time1']) && $_REQUEST['time1'] != ''&&isset($_REQUEST['time2']) && $_REQUEST['time2'] != ''){
		   $map['addtime'] =array(array('egt',I('time1').' 00:00:00'),array('elt',I('time2').' 59:59:59'));
	   }

	}
	
   public function _befor_index(){ 
	   $this->publist=D('Datalist')->getselect(38);
	   $this->shlist=D('Datalist')->getselect(46);
   }
  
  public function _after_list($voList){
	  foreach($voList as $k=>&$v){
		  $v['shname']=D('Datalist')->getselectname($v['sstatus']);
		  //获取审核状态
		  //获取荣誉称号
		  if($v['pubid'])
	  	  $v['pubname']=D('Datalist')->getselectname($v['pubid']);
		 else 
		 $v['pubname']="暂未上报";
		  }
	  return $voList;
	  }  
  public function _befor_add(){
	 
	  $attid=time();
	  $this->assign('attid',$attid);
	  //获取稿件分类
	  $this->clist=D('Datalist')->getselect(50);
    
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
	  //获取稿件分类
	  $this->clist=D('Datalist')->getselect(50);
  }
   
  public function _befor_update($data){

  }
  
    public function _after_edit($id){
     
   }

   public function _befor_view($id){
	   $wheid['id']=$id;
	  //获取附件
	  $wfildata['attid']=$plist['attid'];
	  $this->filelist=M('files')->where($wfildata)->select();
	  $wheid['id']=$id;
	$plist=D('manuscript')->where($wheid)->field('classid,pubid')->find();
	$this->cname=D('datalist')->getselectname($plist['classid']);
	if($plist['pubid'])
	  	  $this->pubname=D('Datalist')->getselectname($plist['pubid']);
		 else 
		 $this->pubname="暂未上报";
   }
   
   public function _befor_del($id){
	   
   }
   public function pub(){
	   
	   //获取网站
	  $this->pubweb=D('Datalist')->getselect(38);
	  $this->id=$whid['id']=I('id');
	  $this->Rs=M('Manuscript')->where($whid)->find();
	  $this->display();
			   
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