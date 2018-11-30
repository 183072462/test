<?php
namespace Home\Controller;
use Think\Controller;
class MainController extends Controller {


   public function _initialize(){
	   $config =   S('DB_CONFIG_DATA');
        if(!$config){
            $config =   api('Config/lists');
            S('DB_CONFIG_DATA',$config);
        }
        C($config); 
      $clist = M("vote")->distinct('fenlei')->field('fenlei')->order('fenlei')->select();
      $this->assign('clist', $clist); 
	}
	
    public function index(){
		
		$this->display(); 
		
	}
	
	public function fenlei(){
		 $time=date("Y-m-d H:i:s",time());
		$list=M('vote')->where(array('status'=>1,'fenlei'=>urldecode(I('fenlei')),'stime'=>array('elt',$time)))->order('piao desc')->limit(20)->page($_GET['p'].',20')->select();
		 $this->assign('list',$list);
         $count = M('vote')->where(array('status'=>1,'fenlei'=>urldecode(I('fenlei')),'stime'=>array('elt',$time)))->count();
		 $Page = new \Think\Page($count,20);
         $show  = $Page->show();
         $this->assign('page',$show);
         $this->assign('count',$count);
		 $this->assign('fenlei',urldecode(I('fenlei')));
         $this->display();  
	}
  
    public function show(){
		$id=I('id');
		$Rs=M('vote')->where(array('status'=>1))->find($id);
		$this->assign('Rs', $Rs);
		$this->display(); 
		
	}
	
	public function vote(){
		$id=I('id'); 
		$time=date("Y-m-d H:i:s",time());
		$counts = M('vote')->where(array('id'=>$id,'status'=>1,'fenlei'=>urldecode(I('fenlei')),'stime'=>array('elt',$time),'etime'=>array('egt',$time)))->count();
		if($counts!==1){
			$this->error('投票未开始，或投票已经结束！');		
		}
		$count = M('votes')->where(array('title'=>cookie('voteid_'.$id.''),'xid'=>$id))->limit(5000)->count();
        if($count>0){
			$this->error('您已经投过票了，不要重复投票哦！');		
		}else{
		  session('id',session_id());
		  cookie('voteid_'.$id.'',session('id'),1440000); 
          $data['xid']=$id;
		  $data['title']=session_id();
		  $data['addtime'] = date("Y-m-d H:i:s",time());
          $data['ip'] = get_client_ip();
		  M('votes')->add($data);
		  M("vote")->where(array('id'=>$id))->setInc('piao',1);
		  $this->success('投票成功！',U('main/index'),1);
		}
		
		
	}

	
	
}