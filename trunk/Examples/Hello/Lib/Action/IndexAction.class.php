<?php
// Hello
class IndexAction extends Action{
    public function index() {
        $this->assign('hello','Hello,ThinkPHP');
        $this->display();
    }
}
?>