<?php
namespace Common\Controller;
use Think\Controller;
use Vendor\PHPMailer\PHPMailer;
class BaseController extends Controller {
   protected function mtReturn($status,$info,$navTabId="",$closeCurrent=true,$classid='',$winstatus) {
       
	    $udata['id']=session('uid');
        $udata['update_time']=time();
        $Rs=M("user")->save($udata);
        $dat['username'] = session('username');
        $dat['content'] = $info;
		$dat['os']=$_SERVER['HTTP_USER_AGENT'];
        $dat['url'] = U();
        $dat['addtime'] = date("Y-m-d H:i:s",time());
        $dat['ip'] = get_client_ip();
        M("log")->add($dat);
	    $result = array();
        $result['statusCode'] = $status; 
        $result['message'] = $info;
		if($navTabId!==''){
			$result['tabid'] = strtolower($navTabId).'/index';
		}
		else{
			$result['tabid'] = '';
		}
		if($classid){
			$result['tabid'] = strtolower($navTabId).'/index/classid/'.$classid;
		}
		if($winstatus){
			$result['tabid'] = strtolower($navTabId).'/index/winstatus/'.$winstatus;
			}
		
        $result['forward'] = '';
		$result['forwardConfirm']='';
        $result['closeCurrent'] =$closeCurrent;
        if (empty($type))
            $type = C('DEFAULT_AJAX_RETURN');
        if (strtoupper($type) == 'JSON') {
            // 返回JSON数据格式到客户端 包含状态信息
            header("Content-Type:text/html; charset=utf-8");
            exit(json_encode($result));
        } elseif (strtoupper($type) == 'XML') {
            // 返回xml格式数据
            header("Content-Type:text/xml; charset=utf-8");
            exit(xml_encode($result));
        } elseif (strtoupper($type) == 'EVAL') {
            // 返回可执行的js脚本
            header("Content-Type:text/html; charset=utf-8");
            exit($data);
        } else {
            // TODO 增加其它格式
        }
	}
	
	protected function sendMsg($uid,$type,$content,$juid,$navTabId="",$jsuid=0) {
       
	    if($uid!==''&&$uid!==0){
		 $data['uid']=session('uid');
         $data['uname']=session('username');	 
		}else{
		 $data['uid']=0;
         $data['uname']='系统管理员';	
		}
		$data['type'] = $type;
        $data['content'] = $content;
		$data['juid'] = $juid;
		$data['putuid']=$jsuid;
        $data['addtime'] = date("Y-m-d H:i:s",time());
        M('msg')->add($data); 
	}
	protected function sendSMS($phone,$content,$navTabId="") {
	   $neirong=C('SMS_FROM').$content.' 退订回复TD';
       $datas = array(
            'id' => C('SMS_ID'),
            'x' => C('SMS_USER'),
            'y' => C('SMS_YZM'),
			'k'=>$phone,
			'j'=>$neirong,
			'g'=>U(),
			's'=>get_client_ip(),
        );
	   $zt=Post(C('SMS_HOST'), $datas);
	   if($zt=='0'||$zt==0){
		   $msg='成功';
	   }else{
		   $msg='失败';
	   }
	    $data['phone']=$phone;
		$data['content']=$neirong;
		$data['jieguo']=$msg;
		$data['url']=U();
		$data['uid']=session('uid');
		$data['uname']=session('truename');
		$data['addtime'] = date("Y-m-d H:i:s",time());
        $data['ip'] = get_client_ip();
		M('smss')->add($data);
	   
	}
	
	 protected function sendEmail($title,$content,$email){
		$data['email']=$email;
		$data['title']=$title;
		$data['content']=$content;
		$data['url']=U();
		$data['addtime'] = date("Y-m-d H:i:s",time());
        $data['ip'] = get_client_ip();
		M('emails')->add($data);
		 
        $mail             = new PHPMailer();
        $body = $content;
        $mail->IsSMTP();
        $mail->SMTPAuth   = true;                     // enable SMTP authentication
        $mail->SMTPKeepAlive = true;                  // sets the prefix to the servier
        $mail->CharSet = "utf-8";                      // 解决乱码
        $mail->Host       = C('EMAIL_HOST');           // sets SMTP server
        $mail->Port       = 25;
        $mail->Username   = C('EMAIL_USER');     // 用户账号
        $mail->Password   = C('EMAIL_PASSWORD');                 // 用户密码
        $mail->From       = C('EMAIL_FROM');
        $mail->FromName   = C('EMAIL_FRAMNAME');
        $mail->Subject    = $title;
        $mail->AltBody    = $content;
        $mail->WordWrap   = 50;                       // set word wrap
        $mail->MsgHTML($content);
        $mail->AddReplyTo(C('EMAIL_USER'),C('EMAIL_FROM'));
        $mail->AddAddress($email,"accept");     //接收邮件的账号
        $mail->IsHTML(true); // send as HTML
        return $mail->Send();
    }
	 /**
     * 列表页面
     */
	protected function _list($model, $map, $asc = false) {
		//排序字段 默认为主键名
		if (isset($_REQUEST ['orderField'])) {
			$order = $_REQUEST ['orderField'];
		}
		if($order=='') {
			$order = 'id';

		}
		
		//排序方式默认按照倒序排列
		//接受 sost参数 0 表示倒序 非0都 表示正序
		if (isset($_REQUEST ['orderDirection'])) {
			$sort = $_REQUEST ['orderDirection'];
		}
		if($sort=='') {
			$sort = $asc ? 'asc' : 'desc';
		}
		if (isset($_REQUEST ['pageCurrent'])) {
			$pageCurrent = $_REQUEST ['pageCurrent'];
		}
		if($pageCurrent=='') {
			$pageCurrent =1;
		}
		//取得满足条件的记录数
		$count = $model->where($map)->count();
		
		if($this->dbname=='Material'){
			unset($map['_complex']);
			
		
			$count = $model->join('xy_task ON xy_material.rid = xy_task.id')->where($map)->count();
			}
		if($this->dbname=='Feedback'){
			unset($map['_complex']);
			
			$count = $model->join('xy_insnotice ON xy_feedback.rid = xy_insnotice.id')->where($map)->count();
		}
			
		
		if ($count > 0) {
		    $numPerPage=C('PERPAGE');
            $voList = $model->where($map)->order("`" . $order . "` " . $sort)->limit($numPerPage)->page($pageCurrent.','.$numPerPage.'')->select();
			if($this->dbname=='Material'){
			 $voList = $model->join('xy_task ON xy_material.rid = xy_task.id')->field('xy_material.*')->where($map)->order("`" . $order . "` " . $sort)->limit($numPerPage)->page($pageCurrent.','.$numPerPage.'')->select();}if($this->dbname=='Feedback'){
			 $voList = $model->join('xy_insnotice ON xy_feedback.rid = xy_insnotice.id')->field('xy_feedback.*')->where($map)->order("`" . $order . "` " . $sort)->limit($numPerPage)->page($pageCurrent.','.$numPerPage.'')->select();
			
			}
			
			//修改名称
			foreach($voList as $k=>&$v){
				$wh['id']=$v['uid'];
				$v['uname']=M('user')->where($wh)->getField('truename');
				if($v['class2id']){
					$v['class2name']=D('Datalist')->getselectname($v['class2id']);
					}
				if($v['classify']){
					$v['classifyname']=D('Datalist')->getselectname($v['classify']);
					}
				if($v['rid']){
					$wrid['id']=$v['rid'];
					$v['rtitle']=M('task')->where($wrid)->getField('title');
					}
				}
			//列表排序显示
			$sortImg = $sort; //排序图标
			$sortAlt = $sort == 'desc' ? '升序排列' : '倒序排列'; //排序提示
			$sort = $sort == 'desc' ? 1 : 0; //排序方式
			
		   if( method_exists($this, '_after_list')){
				
				$voList=$this->_after_list($voList);
			}
			
			$this->assign('list', $voList);
		}
		$this->assign('totalCount', $count);//数据总数
		$this->assign('currentPage', !empty($_REQUEST[C('VAR_PAGE')]) ? $_REQUEST[C('VAR_PAGE')] : 1);//当前的页数，默认为1
		$this->assign('numPerPage', $numPerPage); //每页显示多少条
		cookie('_currentUrl_', __SELF__);
		return;
	}
	public function index() {
		$model = D($this->dbname);
		$map = $this->_search();
		if (method_exists($this, '_filter')) {
			$this->_filter($map);
		}
		if (!empty($model)) {
			$this->_list($model, $map);
		}
		if (method_exists($this, '_befor_index')) {
			$this->_befor_index();
		}
		$this->display();
	}

	protected function _search($dbname='') {
		$dbname = $dbname ? $dbname : $this->dbname;
		$model = D($dbname);
		$map = array();
		foreach ($model->getDbFields() as $key => $val) {
			if (isset($_REQUEST['keys']) && $_REQUEST['keys'] != '') {
				if(in_array($val, C('SEARCHKEY'))){
					$map[$val] = array('like','%'.urldecode(trim($_REQUEST['keys'])).'%');
				}else{
					//$map [$val] = $_REQUEST['keys'];
				}
			}
		}
		$map['_logic'] = 'or'; 
        if (isset($_REQUEST['keys']) && $_REQUEST['keys'] != '') {
			$where['_complex'] = $map;
			return $where;
		}else{
			$map['id']=array("neq","0");
			$where['_complex'] = $map;
			return $where;
			}
	}
	public function addsmsfb($juid,$phone,$neixian,$content){
		if($phone==1){
				 $uwher['id']=array('in',$juid);
				 $uwher['phone']=array(array('neq','无'),array('neq',''));
				 $phonelist=M('user')->where($uwher)->field('phone,id')->select();
				
				
				 foreach($phonelist as $k=>$v){
					$dat['addtime']=time();
					$dat['phone']=$v['phone'];
					$dat['content']=$content;
					M('msmlog')->add($dat);
					$this->pubinfo($v['phone'],$content);
					 }
				
				 }
			if($neixian==1){
				$phostr=$phostr? $phostr:"0";
				 $uwherr['id']=array('in',$juid);
				 $uwherr['neixian']=array(array('neq','无'),array('neq',''));
				 $phonelist=M('user')->where($uwherr)->field('neixian,id')->select();
				
				 foreach($phonelist as $k=>$v){
					$dat['addtime']=time();
					$dat['phone']=$v['neixian'];
					$dat['content']=$content;
					$this->pubinfo($v['neixian'],$content);
					M('msmlog')->add($dat);
					 
					 }
					
				 }
		
		
		}
	public function add() {
		if(IS_POST){
		  $model = D($this->dbname);
		  $data=I('post.');
		
			
		if($this->dbname=='Person'){
			//查找有审核权限的用户组667
			$condtion['rules']=array("like","%667%");
			$usegroup=M('auth_group')->field('id')->where($condtion)->select();
			 if($usegroup){
				 $str='0';
				 foreach($usegroup as $k=>$v){
					 $str.=",".$v['id'];
					 }
					$wheu['group_id']=array('in',$str);
					$ulist=M('auth_group_access')->where($wheu)->field('uid')->select();
					$title="有个人荣誉申报提交";
					
				 }
			  }
		if($this->dbname=='Organization'){
			//查找有审核权限的用户组667
			$condtion['rules']=array("like","%668%");
			$usegroup=M('auth_group')->field('id')->where($condtion)->select();
			 if($usegroup){
				 $str='0';
				 foreach($usegroup as $k=>$v){
					 $str.=",".$v['id'];
					 }
					$wheu['group_id']=array('in',$str);
					$ulist=M('auth_group_access')->where($wheu)->field('uid')->select();
					$title="有单位荣誉申报提交";
					
				 }
			  }
		if($this->dbname=='Material'){
			//查找有审核权限的用户组667
			$condtion['rules']=array("like","%603%");
			$usegroup=M('auth_group')->field('id')->where($condtion)->select();
			 if($usegroup){
				 $str='0';
				 foreach($usegroup as $k=>$v){
					 $str.=",".$v['id'];
					 }
					$wheu['group_id']=array('in',$str);
					$ulist=M('auth_group_access')->where($wheu)->field('uid')->select();
					$rid=I('rid');
					$title=M('task')->where("id=$rid")->getField('title');
					$title="有材料上报提交 ID:$rid  $title";
					
				 }
			  }
		if($this->dbname=='Feedback'){
			//查找有审核权限的用户组667
			$condtion['rules']=array("like","%656%");
			$usegroup=M('auth_group')->field('id')->where($condtion)->select();
			 if($usegroup){
				 $str='0';
				 foreach($usegroup as $k=>$v){
					 $str.=",".$v['id'];
					 }
					$wheu['group_id']=array('in',$str);
					$ulist=M('auth_group_access')->where($wheu)->field('uid')->select();
					$rid=I("rid");
					$title=M('insnotice')->where("id=$rid")->getField('title');
				$title="有督查通报反馈提交 ID:$rid  $title";
					
				 }
			  }
		  if($this->dbname=='Task'){
			
			 $this->addsmsfb($data['juid'],$data['phone'],$data['neixian'],"您单位有新的材料报送任务，请及时登录浏阳市文明创建管理系统 liuyang.rongyu.org 处理！");
			 
			  }
			if($this->dbname=="Tongzhi"){
			 $this->addsmsfb($data['juid'],$data['phone'],$data['neixian'],"您单位有新的通知公告信息，请及时登录浏阳市文明创建管理系统 liuyang.rongyu.org 处理！");
			 
				} 
			if($this->dbname=="Insnotice"){
				$this->addsmsfb($data['juid'],$data['phone'],$data['neixian'],"您单位有新的督查通报信息，请及时登录浏阳市文明创建管理系统 liuyang.rongyu.org 处理！");
			 
				} $data = $model->create();
				
		  if (false === $data = $model->create()) {
			   $this->mtReturn(300,$model->getError(),$_REQUEST['navTabId'],true);  
            }
		$data['addtime']=I('addtime')?I('addtime'):$data['addtime'];
		
          if (method_exists($this, '_befor_insert')) {
                $data = $this->_befor_insert($data);
            }
			if($data['juid'])
		$data['juid']=",".$data['juid'].",";
	
          if($id=$model->add($data)){
			if (method_exists($this, '_after_add')) {
			  $id = $model->getLastInsID();
			  $this->_after_add($id);
		    }
			$uid=$id = $model->getLastInsID();
			if(CONTROLLER_NAME=='User'){
			M('auth_group_access')->where('uid='.$id.'')->delete();
			
			$gcdata['uid']=$uid;
			$gcdata['group_id']=M('auth_group')->where(array("title"=>I('depname')))->getField('id');
			M('auth_group_access')->data($gcdata)->add();
			$gcdata['group_id']=M('auth_group')->where(array("title"=>I('posname')))->getField('id');
			M('auth_group_access')->data($gcdata)->add();
			 }
			
			 foreach($ulist as $K=>$v){
						$this->sendMsg(session('uid'),'',$title,$v['uid'],$navTabId="",$jsuid);
						}
			$this->mtReturn(200,"新增成功".$id,$_REQUEST['navTabId'],true,$_REQUEST['classid'],$_REQUEST['winstatus']);  
		  }
	      
		}
		if (method_exists($this, '_befor_add')) {
			$this->_befor_add();
		}
		$this->display();
	}
	public function edit() {
	  $model = D($this->dbname);
	  if(IS_POST){
		   $data=I('post.');
		  if($this->dbname=='Task'){
			 $this->addsmsfb($data['juid'],$data['phone'],$data['neixian'],"您单位有新的材料报送任务，请及时登录浏阳市文明创建管理系统 liuyang.rongyu.org 处理！");
			 
			  }
			if($this->dbname=="Tongzhi"){
			 $this->addsmsfb($data['juid'],$data['phone'],$data['neixian'],"您单位有新的通知公告信息，请及时登录浏阳市文明创建管理系统 liuyang.rongyu.org 处理！");
			 
				} 
			if($this->dbname=="Insnotice"){
				$this->addsmsfb($data['juid'],$data['phone'],$data['neixian'],"您单位有新的督查通报信息，请及时登录浏阳市文明创建管理系统 liuyang.rongyu.org 处理！");
			 
				} 
		   if (false === $data = $model->create()) {
			   $this->mtReturn(300,$model->getError(),$_REQUEST['navTabId'],true,$_REQUEST['classid'],$_REQUEST['winstatus']);  
            }
           if (method_exists($this, '_befor_update')) {
                $data = $this->_befor_update($data);
            }
			if($data['juid'])
			$data['juid']=",".$data['juid'].",";
			
			  if($model->save($data)){
				   if($data['truename']){
				   session('truename',$data['truename']);
				   }
			if (method_exists($this, '_after_edit')) {
			  $id = $data['id'];
			  $this->_after_edit($id);
			  } 
		  }	
          $id = $data['id'];
		 if(CONTROLLER_NAME=='User'){
			$uid=I('id');
			M('auth_group_access')->where('uid='.$uid.'')->delete();
			
			$gcdata['uid']=$uid;
			$gcdata['group_id']=M('auth_group')->where(array("title"=>I('depname')))->getField('id');
			M('auth_group_access')->data($gcdata)->add();
			$gcdata['group_id']=M('auth_group')->where(array("title"=>I('posname')))->getField('id');
			M('auth_group_access')->data($gcdata)->add();
			 }
		  $this->mtReturn(200,"编辑成功".$id,$_REQUEST['navTabId'],true,$_REQUEST['classid'],$_REQUEST['winstatus']); 		  
	   }
	     if (method_exists($this, '_befor_edit')) {
			$this->_befor_edit();
		 }
		$id = $_REQUEST [$model->getPk()];
		$vo = $model->getById($id);
		$this->assign('id',$id);
		$this->assign('Rs', $vo);
		$this->display();
	}
	public function updorder(){
	  $whe['id']=I('id');
	  $data['ordersort']=I('ordersort');
	  M('task')->where($whe)->save($data);
	  
  }
	public function editjztime(){
		
		$id="0";
		
		foreach($_REQUEST['ids'] as $i)
		{
		 
		 $id=$id.",".$i;
		}
		$whe['id']=array('in',$id);
		$dat['jztime']=I('jztime');
		$model = D($this->dbname);
		   if (false === $_REQUEST['ids']) {
			   $this->mtReturn(300,$model->getError(),$_REQUEST['navTabId'],true);  
            }
          $model->where($whe)->save($dat);
		 $this->mtReturn(200,"批量设置成功",$_REQUEST['navTabId'],false);
		 
	  	
		}
	public function view() {
	    $model = D($this->dbname);
		$id = $this->id=$_REQUEST [$model->getPk()];
		$vo = $model->getById($id);
		if (method_exists($this, '_befor_view')) {
			$this->_befor_view($id);
		}
		$this->assign('Rs', $vo);
		//获取审核信息
		$wh['source']=$this->dbname;
		$wh['conid']=$id;
		$shlist=M('shlog')->where($wh)->order("id desc")->select();
		foreach($shlist as $K => &$v){
			 $v['status']=D('Datalist')->getselectname($v['status']);
			}
		$this->assign('shlist',$shlist);
		$this->display();
	}
	
	public function dayin() {
	    $model = D($this->dbname);
		$id = $_REQUEST [$model->getPk()];
		$vo = $model->getById($id);
		$this->assign('Rs', $vo);
		$this->display();
	}
	//获取二级分类
	public function getclist(){
		//获取分类列表
		$clist=D('Datalist')->getselect(I('classid'));
		if($clist){
			$result='<label>二级分类:</label><select name="class2id" id="class2id" data-toggle="selectpicker"><option value="0">全选</option>';
			foreach($clist as $k=>$v){
				$result.='<option value ="'.$v['id'].'">'.$v['title'].'</option>';
				}
          $result.='</select>';
			}
		echo $result;
		exit;
		}
	public function main() {
		if (method_exists($this, '_befor_main')) {
			$this->_befor_main();
		}
		$this->display();
	}
	public function lock(){
		$model = D($this->dbname);
		$id = I('get.id');
		if($id){
			$data=$model->find($id);
			$data['id']=$id;
			if($data['status']==1){
				$data['status']=0;
				$msg='锁定';
			}else{
				$data['status']=1;
				$msg='启用';
			}
			$model->save($data);
			if (method_exists($this, '_befor_lock')) {
                $this->_befor_lock($id);
            }
			$this->mtReturn(200,$msg.$id,$_REQUEST['navTabId'],false);
		}

	}
	public function shenhe(){
		$model = D($this->dbname);
		$id = $_REQUEST['id'];
		$jsuid=$model->where("id=$id")->getField('uid');
		
		$sstatus=$_REQUEST['sstatus'];
		if($id){
			$data=$model->find($id);
			$data['id']=$id;
			$data['sstatus']=$sstatus;
			$model->save($data);
			if (method_exists($this, '_befor_shenhe')) {
                $this->_befor_shenhe($id);
            }
			//添加审核记录
			$shlog['uid']=$_SESSION['xykj']['uid'];
			$shlog['uname']=$_SESSION['xykj']['username'];
			$shlog['conid']=$id;
			$shlog['resource']=$_REQUEST['resource'];
			$shlog['source']=$this->dbname;
			$shlog['addtime']=date("Y-m-d H:i:s");
			$shlog['status']=$sstatus;
			M('shlog')->add($shlog);
			//发送系统消息
			$statusname=D('Datalist')->getselectname($sstatus);$models=$this->dbname;
			if($models=='Feedback'){
				//获取接收者编号
				$rid=$model->where("id=$id")->getField('rid');
				
				$title=M('insnotice')->where("id=$rid")->getField('title');
				$title="您有督查通告整改反馈审核".$statusname.",ID：".$id.";".$title;
				}else if($models=='Person'){
						$title=M('Person')->where("id=$id")->getField('title');
						$title="您有个人荣誉申报审核".$statusname.",ID：".$id.";".$title;
					}else if($models=='Organization'){
						$title=M('Organization')->where("id=$id")->getField('title');
						$title="您有单位荣誉申报审核".$statusname.",ID：".$id.";".$title;
					}else if($models=='Manuscript'){
						$title=M('Manuscript')->where("id=$id")->getField('title');
						$title="您有稿件上报审核".$statusname.",ID：".$id.";".$title;
					}else{
						$rid=$model->where("id=$id")->getField('rid');
						$title=M('Task')->where("id=$rid")->getField('title');
						$title="您有材料审核".$statusname.",ID：".$id.";".$title;
					}
			
				//获取接受信息者所属部门
				//获取职位
			//$zwid=M('auth_group_access')->getField('depname')
			$this->sendMsg(session('uid'),'',$title,$jsuid,$navTabId="",$jsuid);
			
			$this->mtReturn(200,$msg.$id,$_REQUEST['navTabId'],false);	  
	   
		}

	}
	public function nosh(){
		$this->id=$_REQUEST['id'];
		$this->sstatus=$_REQUEST['sstatus'];
		$this->display('public/nosh');
	}
	public function del(){
		$model = D($this->dbname);

			 $info=$model->where('status=0')->select();
		      foreach($info as $key=>$v){
		       $attid=$v['attid'];
		       $ad['attid']=0;
			   M('files')->where(array("attid"=>$attid))->save($ad);
			   if (method_exists($this, '_befor_del')) {
                $this->_befor_del($v['id']);
               }
			}
			 $info=M('files')->where('attid=0 or status=0')->select();
             foreach($info as $key=>$v){
               $filepath=$v['folder'].$v['filename'];
               unlink($filepath);
	          }
			 M('files')->where('attid=0 or status=0')->delete();
             if (method_exists($this, '_after_del')) {
                $this->_after_del();
               }
			$Rs=$model->where('status=0')->delete();   
			$this->mtReturn(200,'清理'.$Rs.'条无用的记录',$_REQUEST['navTabId'],false);

	}
	//批量删除
	public function pldel(){
		$id=$_REQUEST['ids'];
		$whe['id']=array('in',$id);
		$model = D($this->dbname);
		$model->where($whe)->delete();
		$this->mtReturn(200,'删除成功',$_REQUEST['navTabId'],false);
		}
	
	//批量删除文件
	public function pldelfile(){
		$id=$_REQUEST['ids'];
		$whe['id']=array('in',$id);
		$model = M('files');
		$info=M('files')->where($whe)->select();
             foreach($info as $key=>$v){
               $filepath=$v['folder'].$v['filename'];
               unlink($filepath);
	          }
		$model->where($whe)->delete();
		$this->mtReturn(200,'删除成功',$_REQUEST['navTabId'],false);
		}
	public function _fenxi($fd,$ft,$type,$map) {
		import("Org.Util.Chart");
        $chart = new \Chart;
		$model = D($this->dbname);
		$this->fd=$fd;
		$map = $this->_search();
	    if (method_exists($this, '_filter')) {
	      $this->_filter($map);
	    }
		$list = $model->where($map)->distinct($this->fd)->field($this->fd)->select();
	    foreach ($list as $key =>$vo){	
			$info=$info.",".$vo[$this->fd];
			$co = $model->where(array($this->fd=>$vo[$this->fd]))->where($map)->count('id');
			$count=$count.",".$co;
		}
    $title = $ft; 
    $data = explode(",", substr ($count, 1)); 
    $size = 140; 
    $width = 550; 
    $height = 300; 
    $legend = explode(",", substr ($info, 1));
    ob_end_clean();
	if ($type == 1) {
		$chart->create3dpie($title,$data,$size,$height,$width,$legend);
     }
	 if ($type == 2) {
		$chart->createcolumnar($title,$data,$size,$height,$width,$legend);
     }
	 if ($type == 3) {
		$chart->createmonthline($title,$data,$size,$height,$width,$legend);
     }
	 if ($type == 4) {
		$chart->createring($title,$data,$size,$height,$width,$legend);
     }
	 if ($type == 5) {
		$chart->createhorizoncolumnar($title,$subtitle,$data,$size,$height,$width,$legend);
     }
   
	}
	
    public function xlsout($filename='数据表',$headArr,$list){
			
		//导入PHPExcel类库，因为PHPExcel没有用命名空间，只能inport导入
		import("Org.Util.PHPExcel");
		import("Org.Util.PHPExcel.Writer.Excel5");
		import("Org.Util.PHPExcel.IOFactory.php");
		$this->getExcel($filename,$headArr,$list);
	}
	public function xlsin(){
			
		//导入PHPExcel类库，因为PHPExcel没有用命名空间，只能inport导入
		import("Org.Util.PHPExcel");
		//要导入的xls文件，位于根目录下的Public文件夹
		$filename="./Public/1.xls";
		//创建PHPExcel对象，注意，不能少了\
		$PHPExcel=new \PHPExcel();
		//如果excel文件后缀名为.xls，导入这个类
		import("Org.Util.PHPExcel.Reader.Excel5");
		//如果excel文件后缀名为.xlsx，导入这下类
		//import("Org.Util.PHPExcel.Reader.Excel2007");
		//$PHPReader=new \PHPExcel_Reader_Excel2007();

		$PHPReader=new \PHPExcel_Reader_Excel5();
		//载入文件
		$PHPExcel=$PHPReader->load($filename);
		//获取表中的第一个工作表，如果要获取第二个，把0改为1，依次类推
		$currentSheet=$PHPExcel->getSheet(0);
		//获取总列数
		$allColumn=$currentSheet->getHighestColumn();
		//获取总行数
		$allRow=$currentSheet->getHighestRow();
		//循环获取表中的数据，$currentRow表示当前行，从哪行开始读取数据，索引值从0开始
		for($currentRow=1;$currentRow<=$allRow;$currentRow++){
			//从哪列开始，A表示第一列
			for($currentColumn='A';$currentColumn<=$allColumn;$currentColumn++){
				//数据坐标
				$address=$currentColumn.$currentRow;
				//读取到的数据，保存到数组$arr中
				$arr[$currentRow][$currentColumn]=$currentSheet->getCell($address)->getValue();
			}

		}
			
	}
	public function yl(){
		$whe['id']=I('id');
		$list=M('files')->where($whe)->find();
		$this->url="http://dcsapi.com/?k=141365573&url=http://lnmp1.eswjdg.com/".$list['folder'].$list['filename'];
		$this->display('Person/yl');
		}
	public	function getExcel($fileName,$headArr,$data){
		//对数据进行检验
		if(empty($data) || !is_array($data)){
			die("data must be a array");
		}
		//检查文件名
		if(empty($fileName)){
			exit;
		}

		$date = date("Y_m_d",time());
		$fileName .= "_{$date}.xls";


		//创建PHPExcel对象，注意，不能少了\
		$objPHPExcel = new \PHPExcel();
		$objProps = $objPHPExcel->getProperties();
			
		//设置表头
         $key = 0;
         //print_r($headArr);exit;
         foreach($headArr as $v){
             //注意，不能少了。将列数字转换为字母\
             $colum = \PHPExcel_Cell::stringFromColumnIndex($key);
             $objPHPExcel->setActiveSheetIndex(0) ->setCellValue($colum.'1', $v);
             $key += 1;
         }
         $column = 2;
         $objActSheet = $objPHPExcel->getActiveSheet();
     
        foreach($data as $key => $rows){ //行写入
             $span = 0;
             foreach($rows as $keyName=>$value){// 列写入
                 $j = \PHPExcel_Cell::stringFromColumnIndex($span);
                 $objActSheet->setCellValue($j.$column, $value);
                 $span++;
             }
            $column++;
        }


		$fileName = iconv("utf-8", "gb2312", $fileName);
		//重命名表
		// $objPHPExcel->getActiveSheet()->setTitle('test');
		//设置活动单指数到第一个表,所以Excel打开这是第一个表
		$objPHPExcel->setActiveSheetIndex(0);
		ob_end_clean();//清除缓冲区,避免乱码
		header('Content-Type: application/vnd.ms-excel');
		header("Content-Disposition: attachment;filename=\"$fileName\"");
		header('Cache-Control: max-age=0');

		$objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output'); //文件通过浏览器下载
		exit;
	}
	
public function pubinfo($mobile,$content){
	
	$target = "http://106.ihuyi.com/webservice/sms.php?method=Submit";
	
	//$post_data = "account=C81666050&password=9c2fbfd4374cea06408d9b3b14bd9def&mobile=".$mobile."&content=".rawurlencode("您的验证码是：1234。请不要把验证码泄露给其他人。");
	$post_data = "account=C81666050&password=9c2fbfd4374cea06408d9b3b14bd9def&mobile=".$mobile."&content=".rawurlencode($content);
	//用户名是登录用户中心->短信营销->帐户参数设置->APIID"您的验证码是：1234。请不要把验证码泄露给其他人。"
	//查看密码请登录用户中心->短信营销->帐户参数设置->APIKEY
	$gets =  xml_to_array(Posts($post_data, $target));
	
		}
}