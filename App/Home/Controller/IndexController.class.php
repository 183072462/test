<?php
namespace Home\Controller;
use Think\Controller;
use Common\Controller\BaseController;
class IndexController extends BaseController {
 

    public function _initialize(){
		
		if(!session('uid')){
			redirect(U('public/login'));
		}
		$config =   S('DB_CONFIG_DATA');
        if(!$config){
            $config =   api('Config/lists');
            S('DB_CONFIG_DATA',$config);
        }
        C($config); 
		//获取系统消息数目
		$where['viewtime']=array('lt','2000-01-01');
		$where['juid']=array('eq',session('uid'));
		$this->msgnum=M('msg')->where($where)->count();
	}
    
	
	public function index(){
	   if(IS_POST){
		M('user')->data(I("post."))->save();
		$this->mtReturn(200,'保存成功',$_REQUEST['navTabId'],false);
	   }
	   $Rs=M('user')->find(session('uid'));
	   $this->assign('Rs', $Rs);
	   $this->display();
	}
	
	public function main(){
	   $Rs=M('user')->find(session('uid'));
	   $this->assign('Rs', $Rs);
	   $this->display();
	}

	
}