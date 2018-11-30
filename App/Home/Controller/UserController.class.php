<?php
namespace Home\Controller;
use Think\Controller;
class UserController extends CommonController {
   
    public function _initialize() {
        parent::_initialize();
        $this->dbname = CONTROLLER_NAME;
    }
	
	
	function _filter(&$map) {
		
		if(!in_array(session('uid'),C('ADMINISTRATOR'))){
		  $map['id'] = array('NEQ', '1'); 
	   }
		
		if(isset($_REQUEST['depname']) && $_REQUEST['depname'] != ''){
		 $map['depname'] =array('EQ',  urldecode(I("depname")));
		}
        
		if(isset($_REQUEST['posname']) && $_REQUEST['posname'] != ''){
		 $map['posname'] =array('EQ',  urldecode(I("posname")));
		}
	}
	
	public function _befor_index(){
	$dlist =  M($this->dbname)->distinct('depname')->field('depname')->select();
    $this->assign('dlist', $dlist); 
	$plist =  M($this->dbname)->distinct('posname')->field('posname')->select();
    $this->assign('plist', $plist);  	 
   }
  
   public function _befor_add(){
     $list=orgcateTree($pid=0,$level=0,$type=0);
     $this->assign('list',$list);
  }
  
  public function _befor_insert($data){
	 $password=md5(md5(I('pwd')));
	 $data['password']=$password;
	 unset($data['pwd']);
	 return $data;
  }
  
  
  
  public function _befor_edit(){
     $list=orgcateTree($pid=0,$level=0,$type=0);
     $this->assign('list',$list);
  }
  
    public function _befor_update($data){
	 if (strlen(I('pwd'))!==32){
	 $password=md5(md5(I('pwd')));
	 $data['password']=$password;
	 }
	 unset($data['pwd']);
	 return $data;
  }

   
   public function editrule(){
	$uid=I('id');
			M('auth_group_access')->where('uid='.$uid.'')->delete();
			
			$gcdata['uid']=$uid;
			$gcdata['group_id']=M('auth_group')->where(array("title"=>I('depname')))->getField('id');
			M('auth_group_access')->data($gcdata)->add();
			$gcdata['group_id']=M('auth_group')->where(array("title"=>I('posname')))->getField('id');
			M('auth_group_access')->data($gcdata)->add();
	$this->mtReturn(200,"设置成功".$id,$_REQUEST['navTabId'],false); 
	//}
  }

   public function _befor_del($id){
	  M('auth_group_access')->where('uid='.$id.'')->delete(); 
   }
	
}