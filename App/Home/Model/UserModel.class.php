<?php

namespace Home\Model;
use Think\Model;

class UserModel extends Model{
    protected $_validate = array(

        array('username','','��½�˺��Ѿ����ڣ�',0,'unique',1),
    );


  
}