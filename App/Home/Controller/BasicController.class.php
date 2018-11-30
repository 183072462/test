<?php
namespace Home\Controller;
use Think\Controller;
use Common\Controller\BaseController;
class BasicController extends BaseController {
	
	public function _initialize(){

		if(!session('uid')){
			redirect(U('Public/login'));
		}
       
        $config =   S('DB_CONFIG_DATA');
        if(!$config){
            $config =   api('Config/lists');
            S('DB_CONFIG_DATA',$config);
        }
        C($config); 
	 
	}

  
   public function user(){
	    $info=M('user');
		if (isset($_REQUEST ['orderField'])) {$order = $_REQUEST ['orderField'];}
		if($order=='') {$order = $info->getPk();}
			
		if (isset($_REQUEST ['orderDirection'])) {$sort = $_REQUEST ['orderDirection'];}
		if($sort=='') {$sort = $asc ? 'asc' : 'desc';}

		if (isset($_REQUEST ['pageCurrent'])) {$pageCurrent = $_REQUEST ['pageCurrent'];}
		if($pageCurrent=='') {$pageCurrent =1;}   

       $key=I('keys');
	   $where['posname']='普通用户';
	   if($key){
	   $where['username'] = array('like','%'.$key.'%');   
       $where['truename'] = array('like','%'.$key.'%');
       $where['depname'] = array('like','%'.$key.'%');
       $where['posname'] = array('like','%'.$key.'%');
       $where['_logic'] = 'or'; }
	 if(IS_POST&&isset($_REQUEST['filter']) && $_REQUEST['filter'] != ''){
		 $map['depname'] = array('EQ', I('filter'));
		 $where['_complex'] = $map;
		}
   $numPerPage=500;
   cookie('_currentUrl_', __SELF__);
   $list=$info->where($where)->order("`" . $order . "` " . $sort)->limit($numPerPage)->page($pageCurrent.','.$numPerPage.'')->select();
  
   $juid=explode(",",I('juname'));
   foreach($list as $k=>&$v){
	if (in_array($v['id'], $juid))
	  {
	  $v['pp']=1;
	  }
	else
	  {
	 $v['pp']=0;
	  }}
	$this->assign('list',$list);
    $count = $info->where($where)->count();
    $this->assign('totalCount', $count);
    $this->assign('currentPage', !empty($_GET['pageNum']) ? $_GET['pageNum'] : 1);
    $this->assign('numPerPage', $numPerPage); 
	$filters=orgcateTree($pid=0,$level=0,$type=0);
    $this->assign('filters',$filters);
    $this->display();
   }
    public function org(){
	    $info=M('auth_group');
		if (isset($_REQUEST ['orderField'])) {$order = $_REQUEST ['orderField'];}
		if($order=='') {$order = $info->getPk();}
			
		if (isset($_REQUEST ['orderDirection'])) {$sort = $_REQUEST ['orderDirection'];}
		if($sort=='') {$sort = $asc ? 'asc' : 'desc';}

		if (isset($_REQUEST ['pageCurrent'])) {$pageCurrent = $_REQUEST ['pageCurrent'];}
		if($pageCurrent=='') {$pageCurrent =1;}   

       $key=I('keys');
	   $where['pid']=0;
	   if($key){
	   $where['title'] = array('like','%'.$key.'%');   
       }
   $numPerPage=50;
   cookie('_currentUrl_', __SELF__);
   $list=$info->where($where)->order("`" . $order . "` " . $sort)->limit($numPerPage)->page($pageCurrent.','.$numPerPage.'')->select();
	$this->assign('list',$list);
    $count = $info->where($where)->count();
    $this->assign('totalCount', $count);
    $this->assign('currentPage', !empty($_GET['pageNum']) ? $_GET['pageNum'] : 1);
    $this->assign('numPerPage', $numPerPage); 
	$filters=orgcateTree($pid=0,$level=0,$type=0);
    $this->assign('filters',$filters);
    $this->display();
   }
   
   
	
}