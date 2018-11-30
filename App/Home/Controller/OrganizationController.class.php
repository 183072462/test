<?php

/**
 *      单位管理控制器
 */

namespace Home\Controller;
use Think\Controller;

class OrganizationController extends CommonController{

   public function _initialize() {
        parent::_initialize();
        $this->dbname = CONTROLLER_NAME;
    }
	
   function _filter(&$map) {
   		if($levelid=I('levelid')){
   			$map['levelid']=$levelid;
   			$this->levelid=$levelid;
   		}  if($juname=I('juname')){
   			$map['uname']= array('in',$juname);//$sstatus;
   			$this->juname=$juname;
   		}	  if($sstatus=I('sstatus')){
   			$map['sstatus']=$sstatus;
   			$this->sstatus=$sstatus;
   		}
		
		if($winstatus=I('winstatus')){
   			$map['winstatus']=$winstatus;
   			$this->winstatus=$winstatus;
   		}
   		if($callname=I('callname')){
   			$map['callname']=$callname;
   			$this->callname=$callname;
   		}
   		if($unitproid=I('unitproid')){
   			$map['unitproid']=$unitproid;
   			$this->unitproid=$unitproid;
   		}
   		if($orgname=I('orgname')){
   			$map['orgname']=array('like',"%$orgname%");
   			
   		}
	    $name1=MODULE_NAME.'/'.CONTROLLER_NAME.'/seeall';
	   if(!in_array(session('uid'),C('ADMINISTRATOR'))&&!authcheck($name,session('uid'))&&!authcheck($name1,session('uid'))){
		  $map['uid'] = array('EQ', session('uid')); 
	   }
	   if($_REQUEST['state']=='gq'){
		   $dtime=date("Y-m-d");
		   $map['jztime']=array('lt',$dtime);
		   $this->state='gq';
		   }
		  
	      if(isset($_REQUEST['timehj1']) && $_REQUEST['timehj1'] != ''&&isset($_REQUEST['timehj2']) && $_REQUEST['timehj2'] != ''){$map['wintime'] =array(array('egt',I('timehj1').' 00:00:00'),array('elt',I('timehj2').' 59:59:59'));}
	      if(isset($_REQUEST['timejz1']) && $_REQUEST['timejz1'] != ''&&isset($_REQUEST['timejz2']) && $_REQUEST['timejz2'] != ''){$map['jztime'] =array(array('egt',I('timejz1').' 00:00:00'),array('elt',I('timejz2').' 59:59:59'));}
	     
	}
		
  public function index() {

		$model = D($this->dbname);
		$map = $this->_search();
		//获取审核章台
	$this->sstalist=D('datalist')->getselect(46);
		//获奖状态
		$this->stalist=D('Datalist')->getselect(29);
		//获取级别
		$this->levelist=D('Datalist')->getselect(9);
		//荣誉称号
		$this->calllist=D('Datalist')->getselect(17);
		//单位性质
		$this->unitlist=D('Datalist')->getselect(21);
		if (method_exists($this, '_filter')) {
			$this->_filter($map);
		}
		if (!empty($model)) {
			$this->_list($model, $map);
		}
		if (method_exists($this, '_befor_index')) {
			$this->_befor_index();
		}
		$this->display();
	}
  public function _after_list($list){
  	foreach($list as $K=>&$v){
  		//获取荣誉级别levelid
  		$v['levelname']=D('datalist')->getselectname($v['levelid']);
  	$v['shname']=D('Datalist')->getselectname($v['sstatus']);
  		//获取荣誉称号callname
  		$v['callname']=D('datalist')->getselectname($v['callname']);
  		
  		//获取单位性质
  		$v['unitproidname']=D('datalist')->getselectname($v['unitproid']);
  		
  	}
  	return $list;
  }
  public function _befor_add(){
	 
	  $attid=time();
	  //获取级别
	  $this->levelist=D('Datalist')->getselect(9);
	 
	  //获取荣誉称号
	  $this->calllist=D('Datalist')->getselect(17);
	  //获奖状态
	  $this->winstatus=D('Datalist')->getselect(29);
	  	  //获取单位性质
	  $this->unitproidlist=D('Datalist')->getselect(21);
	  $this->uid=session('uid');
	  $this->assign('attid',$attid);
    
  }
  
  public function _befor_insert($data){
  }
	
   public function _after_add($id){
    
   }


  
  public function _befor_edit(){
     $model = D($this->dbname);
	  //获取级别
	  $this->levelist=D('Datalist')->getselect(9);
	 
	  //获取荣誉称号
	  $this->calllist=D('Datalist')->getselect(17);
	  //获奖状态
	  $this->winstatus=D('Datalist')->getselect(29);
	  //获取单位性质
	  $this->unitproidlist=D('Datalist')->getselect(21);
	 $info = $model->find(I('get.id'));
	 $attid=$info['attid'];
	 $this->assign('attid',$attid);
  }
   
  public function _befor_update($data){

  }
  
    public function _after_edit($id){
     
   }

   public function _befor_view($id){
	   $wh['id']=$id;
	   $plist=D('organization')->where($wh)->field('levelid,callname,unitproid,attid,winstatus')->find();
	  
	  //获取荣誉级别levelid
	  $levelname=D('datalist')->getselectname($plist['levelid']);
	  $this->assign('levelname',$levelname);
	  //获取荣誉称号callname
	  $callname=D('datalist')->getselectname($plist['callname']);
	  $this->assign('callname',$callname);
	  //获取单位性质
	  $unitproidname=D('datalist')->getselectname($plist['unitproid']);
	  $this->assign('unitproidname',$unitproidname);
	  //获奖状况callname
	  $winstatus=D('datalist')->getselectname($plist['winstatus']);
	  $this->assign('winstatus',$winstatus);
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
		if($id=I('id')){
			$map['id']=array('in',$id);
			}
		$list = $model->where($map)->field('id,levelid,callname,orgname,unitproid,leader,wintime,jztime')->select();
		
		foreach($list as $K=>&$v){
  		//获取荣誉级别levelid
  		$v['levelid']=D('datalist')->getselectname($v['levelid']);
  		$v['sstatus']=D('Datalist')->getselectname($v['sstatus']);
  		//获取荣誉称号callname
  		$v['callname']=D('datalist')->getselectname($v['callname']);
  		
  		//获取单位性质
  		$v['unitproid']=D('datalist')->getselectname($v['unitproid']);
  		
  	}
  	
	    $headArr=array('ID','荣誉级别','荣誉称号','单位名称','单位性质','负责人','获奖日期','截至日期');
	    $filename='单位管理';
		$this->xlsout($filename,$headArr,$list);
	}
	
	public function fenxi(){
	 $this->display();
	}
	
	

}