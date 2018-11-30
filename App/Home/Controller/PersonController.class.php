<?php

/**
 *      个人管理控制器
 */

namespace Home\Controller;
use Think\Controller;

class PersonController extends CommonController{

   public function _initialize() {
        parent::_initialize();
        $this->dbname = CONTROLLER_NAME;
    }
	
   function _filter(&$map) {
	    $name1=MODULE_NAME.'/'.CONTROLLER_NAME.'/seeall';
	   if(!in_array(session('uid'),C('ADMINISTRATOR'))&&!authcheck($name,session('uid'))&&!authcheck($name1,session('uid'))){
		 $map[]=array("uid"=>array('EQ', session("uid")),"_logic"=>"or");  
	   }
	    if($levelid=I('levelid')){
   			$map['levelid']=$levelid;
   			$this->levelid=$levelid;
   		}
		 if($sex=I('sex')){
   			$map['sex']=$sex;
   			$this->sex=$sex;
   		}
   		if($callname=I('callname')){
   			$map['callname']=$callname;
   			$this->callname=$callname;
   		}  if($winstatus=I('winstatus')){
   			$map['winstatus']=$winstatus;
   			$this->winstatus=$winstatus;
   		}
		  if($sstatus=I('sstatus')){
   			$map['sstatus']=$sstatus;
   			$this->sstatus=$sstatus;
   		}
		  if($juname=I('juname')){
   			$map['uname']= array('in',$juname);//$sstatus;
   			$this->juname=$juname;
   		}

		if(isset($_REQUEST['timehj1']) && $_REQUEST['timehj1'] != ''&&isset($_REQUEST['timehj2']) && $_REQUEST['timehj2'] != ''){$map['wintime'] =array(array('egt',I('timehj1').' 00:00:00'),array('elt',I('timehj2').' 59:59:59'));}
	   if($truename=I('truename')){
   			$map['truename']=array('like',"%$truename%");
   			
   		}
	     if(isset($_REQUEST['time1']) && $_REQUEST['time1'] != ''&&isset($_REQUEST['time2']) && $_REQUEST['time2'] != ''){$map['addtime'] =array(array('egt',I('time1').' 00:00:00'),array('elt',I('time2').' 59:59:59'));}

	}
		
  public function index() {
//获取将状态

	  $this->stalist=D('datalist')->getselect(29);
	//获取审核章台
	$this->sstalist=D('datalist')->getselect(46);
		$model = D($this->dbname);
		$map = $this->_search();
				//获取级别
		$this->levelist=D('Datalist')->getselect(9);
		//荣誉称号
		$this->calllist=D('Datalist')->getselect(14);
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
  		 //获奖状况callname
	  $v['winstatus']=D('datalist')->getselectname($v['winstatus']);
	 
  		
  		
  	}
  	return $list;
  }
  public function _befor_add(){
	 
	  $attid=time();
	  //获取级别
	  $this->levelist=D('Datalist')->getselect(9);
	 
	  //获取荣誉称号
	  $this->calllist=D('Datalist')->getselect(14);
	  //获奖状态
	  $this->winstatus=D('Datalist')->getselect(29);
	  $this->hdimgid=uniqid();
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
	  $this->calllist=D('Datalist')->getselect(14);
	  //获奖状态
	  $this->winstatus=D('Datalist')->getselect(29);
	 $info = $model->find(I('get.id'));
	 $attid=$info['attid'];
	 $this->assign('attid',$attid);
	 //获取头像
	 $whea['attid']=$info['hdimgid'];
	 if($attlist=M('files')->where($whea)->order("id desc")->find())
	   $this->headimg=$attlist['folder'].$attlist['filename'];
  }
   
  public function _befor_update($data){

  }
  
    public function _after_edit($id){
     
   }

   public function _befor_view($id){
	   $wheid['id']=$id;
	   $plist=D('person')->where($wheid)->field('levelid,callname,hdimgid,attid,winstatus')->find();
	  //获取荣誉级别levelid
	  $levelname=D('datalist')->getselectname($plist['levelid']);
	  $this->assign('levelname',$levelname);
	  //获取荣誉称号callname
	  $callname=D('datalist')->getselectname($plist['callname']);
	  $this->assign('callname',$callname);
	  
	  //获奖状况callname
	  $winstatus=D('datalist')->getselectname($plist['winstatus']);
	  $this->assign('winstatus',$winstatus);
	  //获取将状态
	  $this->stalist=D('datalist')->getselectname(29);
	  //获取附件
	  $wfildata['attid']=$plist['attid'];
	  $filelist=M('files')->where($wfildata)->select();
	  $this->assign('filelist',$filelist);
	  //获取照片
	  $fildatas['attid']=$plist['hdimgid'];
	  $fileimglist=M('files')->where($fildatas)->field('folder,filename')->order("id desc")->find();
	  $fileimg="/".$fileimglist['folder'].$fileimglist['filename'];
	  $this->assign('fileimg',$fileimg);
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