<?php

/**
 *      分类字典模型
 */

namespace Home\Model;
use Think\Model;

class DatalistModel extends Model{


    protected $_validate = array(
        //array('name','','名称已经存在！',0,'unique',1),
    );
    
		// 自动完成规则
	protected $_auto = array (
	    array('status',1,1), // 对status字段在新增的时候赋值0
		array('uid','getuserid',1,'function'),
        array('uname','gettruename',1,'function'), 		
	    array('addtime','gettime',1,'function'), 
		array('uuid','getuserid',2,'function'),
        array('uuname','gettruename',2,'function'), 		
	    array('updatetime','gettime',2,'function'), 
	);
	//获取夏季
	public function getselect($id){
		$data['pid']=$id;
		$list=$this->where($data)->select();
		return $list;
		}
	//获取名称
	public function getselectname($id){
		$data['id']=$id;
		$list=$this->where($data)->getField('title');
		return $list;
		}
}