<?php

/**
 *      通知公告控制器
 */

namespace Home\Controller;
use Think\Controller;

class InsnoticeController extends CommonController{

   public function _initialize() {
        parent::_initialize();
        $this->dbname = CONTROLLER_NAME;
    }
	
   function _filter(&$map) {
	   $this->time=date("Y-m-d H:i:s");
	    $name1=MODULE_NAME.'/'.CONTROLLER_NAME.'/seeall';
	   if(!in_array(session('uid'),C('ADMINISTRATOR'))&&!authcheck($name,session('uid'))&&!authcheck($name1,session('uid'))){
		   $juid=",".session("uid").",";
		 $map[]=array("juid"=>array('like','%'.$juid.'%')); 
		
	   }
	   if(isset($_REQUEST['time1']) && $_REQUEST['time1'] != ''&&isset($_REQUEST['time2']) && $_REQUEST['time2'] != ''){$map['jztime'] =array(array('egt',I('time1').' 00:00:00'),array('elt',I('time2').' 59:59:59'));}

	   

	}
	
 
  public function _befor_add(){
	  $attid=time();
	  $this->assign('attid',$attid);
	  //获取任务分类
	  $this->clist=D('Datalist')->getselect(44);
    
  }
  public function _befor_insert($data){
		$data['juid']=",".$data['juid'].",";
		return $data;
  }
  public function _after_list($voList){
	  foreach($voList as $k=>&$v){
		 
		  $wtask['rid']=$v['id'];
		  $wtask['uid']=session("uid");
		  $mate=M('feedback');
		  $v['issb']=0;
		
		  if($mate->where($wtask)->count()){
			  $v['issb']=1;
			  }
			
		  }
		
	  return $voList;
	  }	
  public function _after_add($id){
    //获取juid
	if($id){
		$juid=M('Insnotice')->where("id=$id")->find();
		$julist = explode(',',$juid['juid']);
		
		$content="您有督察通报信息 ID ".$id.$juid['title'];
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
	 $this->clist=D('Datalist')->getselect(44);
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
		$whecid['id']=$wcid['rid']=$this->rid=I('id');
		//获取所有参与人员
		$bmlist=M('insnotice')->where($whecid)->field('juid')->find();
		$str1=$bmlist['juid'];
		
		//获取已提交的成员
		$ytjlist=M('feedback')->where($wcid)->field('uid')->group(uid)->select();
		$str="0";
		foreach($ytjlist as $k=>$v){
			$str.=','.$v['uid'];
			}
		$wheauth['id'] =array(array('in',$str1),array('not in',$str));
		//获取未提交的
		$this->dtj=M('user')->where($wheauth)->field('truename as title')->select();
		$wcid['uid']=array('in',$str1);
		$this->shwtg=M('feedback')->field('truename as title,sstatus')->join('left join xy_user on xy_feedback.uid=xy_user.id')->where($wcid)->group('uid')->order('addtime desc')->select();
		
		$this->display();
		}
	
	

}