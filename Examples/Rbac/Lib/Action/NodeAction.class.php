<?php
class NodeAction extends CommonAction {
	public function _filter(&$map)
	{
        if(!empty($_GET['group_id'])) {
            $map['group_id'] =  $_GET['group_id'];
            $this->assign('nodeName','分组');
        }elseif(empty($_POST['search']) && !isset($map['pid']) ) {
			$map['pid']	=	0;
		}
		if($_GET['pid']!=''){
			$map['pid']=$_GET['pid'];
		}
		$_SESSION['currentNodeId']	=	$map['pid'];
		//获取上级节点
		$node  = D("Node");

        if(isset($map['pid'])) {
            if($node->getById($map['pid'])) {

                $this->assign('level',$node->level+1);
                $this->assign('nodeName',$node->name);
            }else {
                $this->assign('level',1);
            }
        }
	}

	public function _before_index() {
		$model	=	D("Group");
		$list	=	$model->where('status=1')->getField('id,title');
		$this->assign('groupList',$list);
	}

	// 获取配置类型
	public function _before_add() {
		$model	=	D("Group");
		$list	=	$model->where('status=1')->select();
		$this->assign('list',$list);
		$node	=	D("Node");
		$node->getById($_SESSION['currentNodeId']);
        $this->assign('pid',$node->id);
		$this->assign('level',$node->level+1);
	}

    public function _before_patch() {
		$model	=	D("Group");
		$list	=	$model->where('status=1')->select();
		$this->assign('list',$list);
		$node	=	D("Node");
		$node->getById($_SESSION['currentNodeId']);
        $this->assign('pid',$node->id);
		$this->assign('level',$node->level+1);
    }
	public function _before_edit() {
		$model	=	D("Group");
		$list	=	$model->where('status=1')->select();
		$this->assign('list',$list);
	}
    // 批量增加节点
    public function patchAdd() {
        $Node   =  D("Node");
        $count   =  count($_POST['name']);
        for($i=0;$i<$count;$i++) {
            if(!empty($_POST['name'][$i])) {
                $data['name'] =  $_POST['name'][$i];
                $data['title']    =  $_POST['title'][$i];
                $data['remark']   =  $_POST['remark'][$i];
                $data['status'] = $_POST['status'][$i];
                $data['group_id']     = $_POST['group_id'][$i];
                $data['level']   =  $_POST['level'];
                $data['pid']     =  $_POST['pid'];
                if($Node->create($data)) {
                    $result   =  $Node->add();
                    if(!$result) {
                        $this->error('添加失败！');
                    }
                }else{
                    $this->error($Node->getError());
                }
            }
        }
        $this->success('批量添加成功！');
    }
    /**
     +----------------------------------------------------------
     * 默认排序操作
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
    public function sort()
    {
		$node = D('Node');
        if(!empty($_GET['sortId'])) {
            $map = array();
            $map['status'] = 1;
            $map['id']   = array('in',$_GET['sortId']);
            $sortList   =   $node->where($map)->order('sort asc')->select();
        }else{
            if(!empty($_GET['pid'])) {
                $pid  = $_GET['pid'];
            }else {
                $pid  = $_SESSION['currentNodeId'];
            }
            if($node->getById($pid)) {
                $level   =  $node->level+1;
            }else {
                $level   =  1;
            }
            $this->assign('level',$level);
            $sortList   =   $node->where('status=1 and pid='.$pid.' and level='.$level)->order('sort asc')->select();
        }
        $this->assign("sortList",$sortList);
        $this->display();
        return ;
    }
}
?>