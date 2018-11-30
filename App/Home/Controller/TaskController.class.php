<?php

/**
 *      通知公告控制器
 */

namespace Home\Controller;
use Think\Controller;

class TaskController extends CommonController{

   public function _initialize() {
	  
   		parent::_initialize();
        $this->dbname = CONTROLLER_NAME;
        $this->url=CONTROLLER_NAME;
    }
	
   function _filter(&$map) {
	   $_REQUEST ['orderField']="ordersort";
	   $this->time=date("Y-m-d H:i:s");
	   $name1=MODULE_NAME.'/'.CONTROLLER_NAME.'/updorder';
	   
	   if(authcheck($name1,session('uid'))){
		   $this->issee=0;
		}else{
			$this->issee=1;
		}	
		//获取任务分类
			$name=MODULE_NAME.'/'.CONTROLLER_NAME.'/seeall';
		if(authcheck($name,session('uid'))){
		   
		}		
	   else if(!in_array(session('uid'),C('ADMINISTRATOR'))){
		  $juid=",".session("uid").",";
		 $map[]=array("juid"=>array('like','%'.$juid.'%')); 
		
		
	   }

	   if($_REQUEST['class2id']){
		   $map['class2id']=$_REQUEST['class2id'];
		   
		   $this->class2id=$_REQUEST['class2id'];
		   }	 
	   if($_REQUEST['classid']){
		   $map['classid']=$_REQUEST['classid'];
		   $this->classid=$_REQUEST['classid'];
		   $this->class2list=D('Datalist')->getselect($_REQUEST['classid']);
		   $this->url="/classid/45/".CONTROLLER_NAME;
		   }
	   if(isset($_REQUEST['time1']) && $_REQUEST['time1'] != ''&&isset($_REQUEST['time2']) && $_REQUEST['time2'] != ''){
		   $map['jztime'] =array(array('egt',I('time1').' 00:00:00'),array('elt',I('time2').' 59:59:59'));
		   }
	}
	
   public function _after_list($voList){
	  foreach($voList as $k=>&$v){
		 
		  $wtask['rid']=$v['id'];
		  $wtask['uid']=session("uid");
		  $mate=M('material');
		  $v['issb']=0;
		
		  if($mate->where($wtask)->count()&&$v['atimes']==1){
			  $v['issb']=1;
			  }
			
		  }
		
	  return $voList;
	  }
  public function _befor_add(){
	  $attid=time();
	  $this->assign('attid',$attid);
	  if(I('classid')){
		  $classid=$this->classid=I('classid');
		  //获取任务分类
		  $this->clist=D('Datalist')->getselect($classid);
	  }
    
  }
  public function _befor_insert($data){
		return $data;
  }
	
  public function _after_add($id){
    //获取juid
	if($id){
		$juid=M('Task')->where("id=$id")->find();
		$julist = explode(',',$juid['juid']);
		
		$content="您有材料报送任务 ID ".$id.$juid['title'];
		foreach($julist as $k=>$v){
			$this->sendMsg($_SESSION['xykj']['uid'],$_SESSION['xykj']['username'],$content,$v,$navTabId="");
			} 
	}
   }
  public function _befor_edit(){
     $model = D($this->dbname);
	 
	 $info = $model->find(I('get.id'));
	 $attid=$info['attid'];
	 $this->assign('attid',$attid); //获取任务分类
	 $this->class2id=$info['class2id'];
	 
	 $this->clist=D('Datalist')->getselect($info['classid']);
  }
   
  public function _befor_update($data){
		return $data;
  }
  
  public function _after_edit($id){
     if($id){
		$juid=M('Task')->where("id=$id")->getField('juid');
	  
		$julist = explode(',',$juid);
		
		$content="您有材料报送任务";
		foreach($julist as $k=>$v){
			$this->sendMsg($_SESSION['xykj']['uid'],$_SESSION['xykj']['username'],$content,$v,$navTabId="");
			} 
	}
   }

   public function _befor_view($id){
	$wheid['id']=$id;
	$plist=D('task')->where($wheid)->field('class2id')->find();
	$this->cname=D('datalist')->getselectname($plist['class2id']);
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
	//获取上报情况
	public function getsbinfo(){
		//获取部门
		$whecid['id']=$wcid['rid']=$this->id=I('id');
		//获取所有参与人员
		$bmlist=M('task')->where($whecid)->field('juid')->find();
		$str1=$bmlist['juid'];
		//获取已提交的成员
		$ytjlist=M('material')->where($wcid)->field('uid')->group(uid)->select();
		$str="0";
		foreach($ytjlist as $k=>$v){
			$str.=','.$v['uid'];
			}
		$wheauth['id'] =array(array('in',$str1),array('not in',$str));
		//获取未提交的
		$this->dtj=M('user')->where($wheauth)->field('truename as title')->select();
		$wcid['uid']=array('in',$str1);
		$this->shwtg=M('material')->field('truename as title,sstatus')->join('left join xy_user on xy_material.uid=xy_user.id')->where($wcid)->group('uid')->order('addtime desc')->select();
		
		$this->display();
		}
	

}