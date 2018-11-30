<?php

namespace Home\Model;
use Think\Model;

class UserModel extends Model{
    protected $_validate = array(

        array('username','','╣гб╫ук╨еря╬╜╢Фтзё║',0,'unique',1),
    );


  
}