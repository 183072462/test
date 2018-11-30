<?php
namespace Home\Controller;
use Think\Controller;
class EmptyController extends  Controller {
    public function _empty() {
        send_http_status(404);
        //$this->error();
    }
}