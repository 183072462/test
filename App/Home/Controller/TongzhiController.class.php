<?php

/**
 *      通知公告控制器
 */

namespace Home\Controller;
use Think\Controller;

class TongzhiController extends CommonController{

   public function _initialize() {
        parent::_initialize();
        $this->dbname = CONTROLLER_NAME;
    }
	
   function _filter(&$map) {
	   $this->classlist=D('Datalist')->getselect(53);
	   
	   $this->classid=I('classid');
	   if(!in_array(session('uid'),C('ADMINISTRATOR'))){
		 $map[]=array("uid"=>array('EQ', session("uid")),"juid"=>array('like','%'.session("uid").'%'),"_logic"=>"or");  
	   }if($this->classid=I('classid')){
		   $map['classid']=$this->classid;
	   }
	   if(isset($_REQUEST['time1']) && $_REQUEST['time1'] != ''&&isset($_REQUEST['time2']) && $_REQUEST['time2'] != ''){$map['addtime'] =array(array('egt',I('time1').' 00:00:00'),array('elt',I('time2').' 59:59:59'));}

	   

	}
	
   public function _befor_index(){ 
     
   }
  
  
  public function _befor_add(){
	  $attid=time();
	  $this->assign('attid',$attid);
	   //获取稿件分类
	  $this->clist=D('Datalist')->getselect(53);
    
  }
  
	
   public function _after_add($id){
    	
   }


  
  public function _befor_edit(){
     $model = D($this->dbname);
	 $info = $model->find(I('get.id'));
	 $attid=$info['attid'];
	 $this->assign('attid',$attid);
	 $this->clist=D('Datalist')->getselect(53);
	 
	 
  }
   
  
    public function _after_edit($id){
     
   }

   public function _befor_view($id){
	 $id=I('id');
	$list=M($this->dbname)->where(array('id'=>$id))->field('beizhu,classid')->find();
	$beizhu=$list['beizhu'];
	$this->cname=D('datalist')->getselectname($list['classid']);
	$count=M($this->dbname)->where(array('beizhu'=>array('like','%'.session("truename").'%'),'id'=>$id))->count(); 
    if($count==0){
	$data['id']=$id;
	$data['beizhu']=$beizhu."\r\n".session("truename")." : ".date("Y-m-d H:i:s",time());
	M($this->dbname)->save($data);
	}
	
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