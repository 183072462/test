<?php
namespace Home\Controller;
use Think\Controller;

Class RuleController extends CommonController{
	
	 public function _initialize() {
        parent::_initialize();
        $this->dbname = 'auth_rule';
    }
	
   public function index(){ 
    $list = D($this->dbname)->where('level=0')->order('sort')->select();
    $this->assign('list',$list);
    $this->display(); 
   }
  
   public function _befor_add(){
     $list=cateTree($pid=0,$level=0,$this->dbname);
     $this->assign('list',$list);
  }
  
  public function _befor_insert($data){
	 $pid = I('pid');
	 if ($pid==0){
	 $data['level']=0;
	 }else{
	 $level=D($this->dbname)->where('id='.$pid.'')->field('level')->limit(1)->select();
     $level=$level[0]['level']+1;
     $data['level']=$level;
	 }
	 return $data;
  }
  public function _after_add($id){
	$level=2;
	$rule=str_replace("/index","",I("name"));
	if(I("view")){
		$ruled['level']=$level;
		$ruled['name']=$rule."/view";
		$ruled['title']="查看";
		$ruled['pid']=$id;
		$ruled['sort']=0;
		M("auth_rule")->add($ruled);
	}
	if(I("add")){
		$ruled['level']=$level;
		$ruled['name']=$rule."/add";
		$ruled['title']="新增";
		$ruled['pid']=$id;
		$ruled['sort']=1;
		M("auth_rule")->add($ruled);
	}
	if(I("edit")){
		$ruled['level']=$level;
		$ruled['name']=$rule."/edit";
		$ruled['title']="编辑";
		$ruled['pid']=$id;
		$ruled['sort']=2;
		M("auth_rule")->add($ruled);
	}
	if(I("lock")){
		$ruled['level']=$level;
		$ruled['name']=$rule."/lock";
		$ruled['title']="锁定";
		$ruled['pid']=$id;
		$ruled['sort']=3;
		M("auth_rule")->add($ruled);
	}
	if(I("del")){
		$ruled['level']=$level;
		$ruled['name']=$rule."/del";
		$ruled['title']="删除";
		$ruled['pid']=$id;
		$ruled['sort']=4;
		M("auth_rule")->add($ruled);
	}
	if(I("shenhe")){
		$ruled['level']=$level;
		$ruled['name']=$rule."/shenhe";
		$ruled['title']="审核";
		$ruled['pid']=$id;
		$ruled['sort']=5;
		M("auth_rule")->add($ruled);
	}
	if(I("dayin")){
		$ruled['level']=$level;
		$ruled['name']=$rule."/dayin";
		$ruled['title']="打印";
		$ruled['pid']=$id;
		$ruled['sort']=6;
		M("auth_rule")->add($ruled);
	}
	if(I("outxls")){
		$ruled['level']=$level;
		$ruled['name']=$rule."/outxls";
		$ruled['title']="导出";
		$ruled['pid']=$id;
		$ruled['sort']=6;
		M("auth_rule")->add($ruled);
	}
   }
  
  public function _befor_edit(){
     $list=cateTree($pid=0,$level=0,$this->dbname);
     $this->assign('list',$list);
  }
  
    public function _befor_update($data){
	 $pid = I('pid');
	 if ($pid==0){
	 $data['level']=0;
	 }else{
	 $level=D($this->dbname)->where('id='.$pid.'')->field('level')->limit(1)->select();
     $level=$level[0]['level']+1;
     $data['level']=$level;
	 }
	 return $data;
  }
  
   public function _after_edit($id){
	  M('auth_rule')->where(array("pid"=>$id))->setField('status',I('status'));
   }
   
}