<?php

/**
 *      通知公告控制器
 */

namespace Home\Controller;
use Think\Controller;

class MaterialController extends CommonController{

   public function _initialize() {
        parent::_initialize();
        $this->dbname = CONTROLLER_NAME;
    }
	
   function _filter(&$map) {
	    if($sstatus=I('sstatus')){
		   $map['sstatus']=$sstatus;
		   $this->sstatus=$sstatus;
	   }
	   if($keys=I('keys')){
		   $map['title']=array('like','%'.$keys.'%');
	   }
	   if($_REQUEST['classid'])
	   $this->c2list=D('Datalist')->getselect($_REQUEST['classid']);
	   if(($class2id=$_REQUEST['class2id'])&&($classid=$_REQUEST['classid'])){
		   $this->classid=$classid;
		   $this->class2id=$class2id;
		   //获取rid
   			$wh['class2id']=$class2id;
   			
   			$ridlist=M('task')->where($wh)->field('id')->select();
   			$inid="0";
   			foreach($ridlist as $k=>$v){
   				$inid.=",".$v['id'];
   			}
   			$map['rid']=array('in',$inid);
		   }
   			else if($classid=$_REQUEST['classid']){
   			$this->classid=$classid;
   			//获取rid
   			$wh['classid']=$classid;
   			
   			$ridlist=M('task')->where($wh)->field('id')->select();
   			$inid="0";
   			foreach($ridlist as $k=>$v){
   				$inid.=",".$v['id'];
   			}
   			$map['rid']=array('in',$inid);
   		} 
		if($this->id=I('id')){
			$map['rid']=$this->id;
			}
		$name=MODULE_NAME.'/'.CONTROLLER_NAME.'/'.'shenhe';
		 $name1=MODULE_NAME.'/'.CONTROLLER_NAME.'/seeall';
		 $this->isdis=1;
	   if(!in_array(session('uid'),C('ADMINISTRATOR'))&&!authcheck($name,session('uid'))&&!authcheck($name1,session('uid'))){
		 $map[]=array("xy_material.uid"=>array('EQ', session("uid")),"_logic"=>"or");  
		  $this->isdis=2;
	   }
		 if($juname=I('juname')){
   			$map['xy_material.uname']= array('in',$juname);//$sstatus;
   			$this->juname=$juname;
   		}
	   if($_REQUEST['rid'])
	   	$map['rid']=$_REQUEST['rid'];
	   if(isset($_REQUEST['time1']) && $_REQUEST['time1'] != ''&&isset($_REQUEST['time2']) && $_REQUEST['time2'] != ''){
		   $map['xy_material.addtime'] =array(array('egt',I('time1').' 00:00:00'),array('elt',I('time2').' 59:59:59'));
	   }
	}
	
  public function _befor_index(){ 
     //材料任务分类
  	$this->clist=D('Datalist')->getselect(44);
	 $this->shlist=D('Datalist')->getselect(46);
   }
  public function _befor_add(){
	  $attid=time();
	  $this->assign('attid',$attid);
	  $this->id=$whetas['id']=I('id');
	  $this->classid=M('task')->where($whetas)->getField('classid');
	  //获取任务分类
    
  }
  public function _befor_insert($data){

  }
  public function _after_add($id){
    
   }
  public function _befor_edit(){
     $model = D($this->dbname);
	 $info = $model->find(I('get.id'));
	 $attid=$info['attid'];
	 $this->assign('attid',$attid); //获取任务分类
	 $this->clist=D('Datalist')->getselect(44);
  }
   
  public function _befor_update($data){

  }
  public function _after_list($voList){
	  foreach($voList as $k=>&$v){
		  //获取审核状态
		  //获取荣誉称号
	  	  $v['shname']=D('Datalist')->getselectname($v['sstatus']);
		  }
		
	  return $voList;
	  }
  public function _after_edit($id){
     
   }

   public function _befor_view($id){
	$wheid['id']=$id;
	$plist=D('Material')->where($wheid)->field('rid')->find();
	$twhere['id']=$plist['rid'];
	$this->cname=D('task')->where($twhere)->getField('title');
	//获取
   }
   
   public function _befor_del($id){
	   
   }

   public function outxls() {
		$model = D($this->dbname);
		$map = $this->_search();
		if (method_exists($this, '_filter')) {
			$this->_filter($map);
		}
		$list = $model->where($map)->field('id,beizhu,juid,juname,title,jztime,content')->select();
	    $headArr=array('ID','查看记录','接收人ID','接收人','通知标题','截至日期','详细内容');
	    $filename='通知公告';
		$this->xlsout($filename,$headArr,$list);
	}
	
	public function fenxi(){
	 $this->display();
	}
	
	

}