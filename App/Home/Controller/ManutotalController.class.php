<?php

/**
 *      稿件统计控制器
 */

namespace Home\Controller;
use Think\Controller;

class ManutotalController extends CommonController{
	
   public function index() {
	   $name='home/manutotal/seeall';
	   //获取所有用户
	   $this->userlist=M('user')->field('id,username,truename')->select();
	   if(!in_array(session('uid'),C('ADMINISTRATOR'))&&!authcheck($name,session('uid'))){
		  $where['uid']=session('uid');
	   }
	   $uname='总计';
	   if($uid=$_REQUEST['juid']){
		   $this->juname=$_REQUEST['juname'];
		   $this->juid=$uid;
		   $where['uid']=array('in',$uid);
		   if(!in_array(session('uid'),C('ADMINISTRATOR'))){
		  $this->uid=$_REQUEST['uid'];
			$uname='';
	   }
	   		
			}
	   if(isset($_REQUEST['time1']) && $_REQUEST['time1'] != ''&&isset($_REQUEST['time2']) && $_REQUEST['time2'] != ''){
		   $where['updtime'] =array(array('egt',I('time1').' 00:00:00'),array('elt',I('time2').' 59:59:59'));
	   }
	   $where['pubid']=array('gt',0);
	   //符合条件的会员列表
	   $userlist=M('manuscript')->field('uid,uname')->where($where)->group('uid')->select();
	$u=0;
	  foreach($userlist as $k1=>&$v1){
		  $u.=",".$v1['uid'];
	  }
	  if(I('ispub')==2){
		  
	  $sql="select id as uid,username as uname from xy_user where id not in (".$u.") and posname='普通用户'";
	
	  $userlist=M()->query($sql);
	  }
		
	   foreach($userlist as $k=>&$v){
		   $where['uid']=$v['uid']? $v['uid']:$v['id'];
		    //获取合计40
		  $where['pubid']=array('gt',0);
		   $v['hj']=M('manuscript')->where($where)->count();
		 
		   //获取省网41
		   $where['pubid']=41;
		   $v['sw']=M('manuscript')->where($where)->count();
		   //获取市网40
		   $where['pubid']=40;
		   $v['shiw']=M('manuscript')->where($where)->count();
		  //获取市网40
		   $where['pubid']=39;
		   $v['xw']=M('manuscript')->where($where)->count();
		   //获取县网40
		   $where['pubid']=42;
		   $v['yw']=M('manuscript')->where($where)->count();
		   }
		   $this->userlist=$userlist;
		$total=array();
		 foreach($userlist as $k1=>$v1){
		 	$total['yw']+=$v1['yw'];
			$total['sw']+=$v1['sw'];
			$total['shiw']+=$v1['shiw'];
			$total['xw']+=$v1['xw'];
			$total['hj']+=$v1['hj'];
		 }
		 $this->total=$total;
		$this->display();

	}
   
   
}