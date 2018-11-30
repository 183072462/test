<?php
namespace Home\Controller;
use Think\Controller;

Class DepController extends CommonController{
  
  	 public function _initialize() {
        parent::_initialize();
        $this->dbname = 'auth_group';
    }
	
   public function index(){ 
    $list=M($this->dbname)->where(array('type'=>1))->order('sort')->select();
    $this->assign('list',$list);
    $this->display(); 
   }
  
   public function _befor_add(){
     $list=orgcateTree($pid=0,$level=0,$type=0);
	 $this->assign('type',I('get.type'));
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
  
  
  public function _befor_edit(){
     $list=orgcateTree($pid=0,$level=0,$type=0);
	 $this->assign('type',I('get.type'));
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
  
  public function editrule(){ 
  if (IS_POST){
   $data['id']=I('id');
   $data['rules']=implode(',',I('rules'));
   M('auth_group')->data($data)->save();
   $this->mtReturn(200,'权限设置成功',$_REQUEST['navTabId']);  
  }
  $id=I('get.id');
  $rs=M('auth_group')->where('id='.I('get.id'))->getField('rules');
  $rs=explode(',',$rs);
  $info = M('auth_rule'); 
  $list = $info->where('level=0')->order('sort')->select();
  $this->assign('id',$id);
  $this->assign('rs',$rs);
  $this->assign('list',$list);
  $this->display(); 
  
  }
  
}