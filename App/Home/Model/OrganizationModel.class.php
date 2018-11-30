<?php

/**
 *      产品管理模型
 */

namespace Home\Model;
use Think\Model;

class OrganizationModel extends Model{


    protected $_validate = array(
        array('bianhao','','产品编号已经存在！',0,'unique',1),
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

}