<?php

namespace Home\Model;
use Think\Model;

class ConfigModel extends Model{
    protected $_validate = array(
        array('name','','�����Ѿ����ڣ�',0,'unique',1),
    );


  
}