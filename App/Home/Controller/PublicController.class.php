<?php
namespace Home\Controller;
use Think\Controller;
use Common\Controller\BaseController;
class PublicController extends BaseController {

    public function login(){
	  if(IS_POST){ 
        $admin = I('post.');
        $rs = D('Admin', 'Service')->login($admin);
		if (!$rs['status']) {
            $this->error($rs['data']);
        }
		Redirect("http://".$_SERVER['SERVER_NAME'].__ROOT__);
		//$this->success('登录成功，正在跳转...',__ROOT__,0);
	  }
	  else{
		  $userbrowser = $_SERVER['HTTP_USER_AGENT'];
		 
			if (preg_match('/like Gecko/', $userbrowser )&&!preg_match('/Safari/i', $userbrowser ) ) {
				echo '系统不支持IE浏览器，请使用360浏览器或谷歌浏览器。或者火狐浏览器。';
				die;
			}
		  $this->display();
	  }
    }

	public function verify(){
	    ob_clean();
		$config =    array(
			'fontSize'    =>    20,    // 验证码字体大小
		    'length'      =>    4,     // 验证码位数
		    'imageH'	  => 	35,
		    'useNoise'    =>    false, // 关闭验证码杂点
		);
		$verify = new \Think\Verify($config);
		$verify->codeSet = '0123456789';
		$verify->entry();
	}
	
	public function logout() {
		D('Admin', 'Service')->logout();
		$this->redirect('Public/login');
	}
	
   public function clear(){
	  rmdirs(RUNTIME_PATH);
	  $this->display();
   }
	

	public function changepwd() {
		if(IS_POST){
	    $password=I('post.password');
		$map = array();
		if(I('post.password')!=I('post.repassword'))
		{
			$this->mtReturn(300,'两次输入密码不一致！',$_REQUEST['navTabId']);
		}
		$map['password'] = md5(md5((I('post.oldpassword'))));
		$map['id'] = session('uid');
		$User = M("User");
		if (!$User->where($map)->field('id')->find()) {
			$this->mtReturn(300,'旧密码不符或者用户名错误！',$_REQUEST['navTabId']);
			
		} else {
			if (empty($password) || strlen($password) < 5) {
			$this->mtReturn(300,'密码长度必须大于6个字符！',$_REQUEST['navTabId']);
			}else{
			$User->password =md5(md5(($password)));
			$User->save();
			$this->mtReturn(200,'密码修改成功！',$_REQUEST['navTabId'],true);
			}
			
		}
		}else{
		  $this->display();	
		}
	}
	
	public function changeinfo() {
		if(!session('uid')){redirect(U('Public/login'));}
		$rs=M('user')->find(session('uid'));$whe['id']=session('uid');
		if(IS_POST){
			M('user')->where($whe)->save(I('post.'));
			$this->mtReturn(200,'修改成功！',$_REQUEST['navTabId'],true);
		}else{
		  $this->assign('id', $id);
		  $this->assign('Rs', $rs);
		  $this->display();	
		}
	}
	

   public function online(){
   $info=M('user');
   $where['update_time']=array('gt',time()-600);
   $numPerPage=10;
   cookie('_currentUrl_', __SELF__);
   $list=$info->where($where)->limit($numPerPage)->page($_GET['pageNum'].','.$numPerPage.'')->select();
   $this->assign('list',$list);
    $count = $info->where($where)->count();
    $this->assign('totalCount', $count);
    $this->assign('currentPage', !empty($_GET['pageNum']) ? $_GET['pageNum'] : 1);
    $this->assign('numPerPage', $numPerPage); 
    $this->display();
   }
   
   public function attfile(){
       $attid=I('attid');
	   $this->assign('attid',$attid);
       $this->display();
   }
   public function attimg(){
       $attid=I('attid');
	   $this->assign('hdimgid',$attid);
	   $whea['attid']=$attid;
	   if($attlist=M('files')->where($whea)->order("id desc")->find())
	   $this->img=$attlist['folder'].$attlist['filename'];
	   $this->display();
   }
   
   public function uploads(){
	   if(!session('uid')){redirect(U('Public/login'));}
	   $list=M('files')->where('attid='.I("attid"))->select();
	   $this->assign('list',$list);
       $this->display();
   }
   
   public function filelock(){
	   if(!session('uid')){redirect(U('Public/login'));}
		$model = D('files');
		$id = I('get.id');
		if($id){
			$data=$model->find($id);
			$data['id']=$id;
			if($data['status']==1){
				$data['status']=0;
				$msg='锁定';
				if (method_exists($this, '_befor_lock')) {
                $this->_befor_lock($id);
                 }	
			}else{
				$data['status']=1;
				$msg='启用';
			}
			$model->save($data);
			$this->mtReturn(200,$msg.$id,$_REQUEST['navTabId'],true);
		}

	}
   
   public function upload(){
	 if(!session('uid')){redirect(U('Public/login'));}
     $upload = new \Think\Upload();
	 $upload->maxSize   =     C('UPLOAD_MAXSIZE') ;
	 $upload->exts      =     C('UPLOAD_EXTS');
	 $upload->savePath  =     C('UPLOAD_SAVEPATH');
	 $filenam= explode('.',$_FILES['file']['name']); 
	 //$upload->saveName = $filenam[0].uniqid();
	 $info   =  $upload->upload(); 
	 //print_r($info);
	 $attid=$_REQUEST['attid'];
	 $gourl = 'index.php/home/public/attfile/attid/'.$attid.'/'; 
	 if(!$info) {
        echo "<script language='javascript' type='text/javascript'>"; 
		echo "alert('上传失败！$upload->getError()');";
		echo "window.location.href='$gourl'"; 
		echo "</script>";  			 
	}else{     
	   $data['attid']=$attid;
	   $data['folder']='Uploads/'.str_replace('./','',$info["file"]["savepath"]);
	   $data['filename']=$info["file"]["savename"];
	   $data['filetype']=$info["file"]["ext"];
	   $data['filedesc']=$info["file"]["name"];
	   $data['uid']=session('uid');
	   $data['addtime']=date("Y-m-d H:i:s",time());
	   M('files')->data($data)->add();
		$filename=$info["file"]["name"];
		echo json_encode(array("error"=>"0","pic"=>$filename,"name"=>'a',"id"=>$filename));
	  }
	}
   public function uploadimg(){
	 if(!session('uid')){redirect(U('Public/login'));}
     $upload = new \Think\Upload();
	 $upload->maxSize   =     C('UPLOAD_MAXSIZE') ;
	 $upload->exts      =     C('UPLOAD_EXTS');
	 $upload->savePath  =     C('UPLOAD_SAVEPATH');
	 $info   =  $upload->upload(); 
	 $gourl = 'index.php/home/public/attimg/attid/'.I('hdimgid').'/'; 
	 if(!$info) {
        echo "<script language='javascript' type='text/javascript'>"; 
		echo "alert('上传失败！$upload->getError()');";
		echo "window.location.href='$gourl'"; 
		echo "</script>";  			 
	   //$this->error($upload->getError());    
	}else{     
	 	 $data['attid']=I('hdimgid');
	   $data['folder']='Uploads/'.str_replace('./','',$info["filename"]["savepath"]);
	   $data['filename']=$info["filename"]["savename"];
	   $data['filetype']=$info["filename"]["ext"];
	   $data['filedesc']=$info["filename"]["name"];
	   $data['uid']=session('uid');
	   $data['addtime']=date("Y-m-d H:i:s",time());
	   //dump($data);
	   $id=M('files')->data($data)->add();
		$filename=$info["filename"]["name"];
		echo "<script language='javascript' type='text/javascript'>"; 
		echo "alert('文件$filename 上传成功');";
		echo "window.location.href='$gourl'"; 
		echo "</script>";  
	  }
	}
   
	
    public function delok(){
	  $this->display(); 
   }
	
	
	
}