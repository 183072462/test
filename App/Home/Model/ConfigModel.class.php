<?php

namespace Home\Model;
use Think\Model;

class ConfigModel extends Model{
    protected $_validate = array(
        array('name','','名称已经存在！',0,'unique',1),
    );


  
}