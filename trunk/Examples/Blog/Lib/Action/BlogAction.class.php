<?php
import("@.Action.PublicAction");
class BlogAction extends PublicAction {

	protected function _initialize() {
		$Cate	=	D("Category");
		$cate	 =	 $Cate->findAll();
		$this->assign("category",$cate);
		if(ACTION_NAME != 'add') {
			$Blog	=	D("Blog");
			$new = $Blog->where("status=1")->field("id,readCount,commentCount,categoryId,cTime,title")->order('cTime desc')->top10();
			$Comment = D("Comment");
			$comment	=	$Comment->where("module='Blog' and status=1")->order("id desc")->top8();
			$this->assign("lastArticles",$new);
			$this->assign("lastComments",$comment);

			// 获取归档日志
			if(!isset($_SESSION['BlogArchiveList'])) {
				$Blog = D("Blog");
				$old	=	strtotime('-2 year');
				$new = $Blog->max('cTime');
				$list = array();
				$time    = $new;
				while($time>=$old) {
					$list[] = array('year'=>date('Y',$time),'month'=>date('m',$time),'show'=>$time);
					$time    = strtotime('-1 month',$time);
				}
				$_SESSION['BlogArchiveList']	=	$list;
			}else {
				$list  = $_SESSION['BlogArchiveList'];
			}
			$this->assign('monthList',$list);
		}

		parent::_initialize();
	}

	public function index() {
		$Blog	=	D("BlogView");
		// 日志列表
		$count	=	$Blog->count(array('status'=>1));
		import("ORG.Util.Page");
		if(!isset($_GET['mode'])) {
			$_GET['mode']='normal';
		}
		if($_GET['mode']=='list') {
			$listRows = 45;
			$fields	=	'id,title,categoryId,category,cTime,readCount,commentCount';
		}else{
			$listRows = 8;
			$fields	=	'id,title,categoryId,category,cTime,readCount,commentCount,content,tags';
		}
		$p          = new Page($count,$listRows);
		$p->setConfig('header' ,'篇日志 ');
		$this->assign("mode",$_GET['mode']);
		$list	=	$Blog->where('status=1')->field($fields)->order('id desc')->limit($p->firstRow.','.$p->listRows)->findAll();
		$page  = $p->show();
		$this->assign("list",$list);
		$this->assign("page",       $page);

		// 标签列表
        $dao = D("Tag");
        $list  = $dao->where("module='Blog'")->field('id,name,count')->order('count desc')->limit('0,25')->findAll();
        $this->assign('tags',$list);

		$stat = array();
		// 统计数据
		$Blog	=	D("Blog");
		$stat['blogCount']	=	$Blog->count("status=1");
		$stat['readCount']	=	$Blog->sum('readCount',"status=1");
		$stat['commentCount']	=	$Blog->sum('commentCount',"status=1");
		$stat['beginTime']	=	$Blog->min('cTime');
		$this->assign($stat);
		$this->display();
	}


	public function read() {
		$this->getAttach();
		$this->getComment();
		$Blog = D("BlogView");
		$id	=	$_GET['id'];
		$blog	=	$Blog->find(array('id'=>$id));
		if($blog) {
			$this->assign('vo',$blog);
			$this->assign('keywords',str_replace(' ',',',$blog->tags));
			$this->assign('title',$blog->title);
			$this->display();
		}else{
			$this->redirect('index','Blog');
		}
	}

	public function _after_read() {
		$Blog = D("Blog");
		$blog = $this->get('vo');
		// 阅读计数
		$id	=	$blog->id;
		if(!isset($_SESSION['blog_read_count_'.$id])) {
			$Blog->setInc('readCount',"id=".$id);
			$_SESSION['blog_read_count_'.$id]		=	true;
		}
	}

	public function _before_add() {
		if(isset($_SESSION['userId'])) {
			$verify	=	build_verify(8);
			$_SESSION['attach_verify']	=	$verify;
			$this->assign('verify',$verify);

			$Category	=	D("Category");
			$list	=	$Category->findAll();
			$this->assign("category",$list);
		}
	}


	public function edit() {
		$Blog = D("Blog");
		$id	=	$_GET['id'];
		$blog	=	$Blog->getById($id);
		if($blog ) {
			$this->getAttach();
			$this->assign('vo',$blog);
			$this->display();
		}else{
			$this->redirect('index','Blog');
		}
	}
    // 获取归档日志
    public function archive()
    {
    	if(checkdate($_REQUEST['month'],'01',$_REQUEST['year'] ) )
        {
                $begin_time    = strtotime($_REQUEST['year'].$_REQUEST['month'].'01');
                $end_time = strtotime('+1 month',$begin_time);
                $dao = D("BlogView");
                $this->assign('date',$begin_time);
                $this->assign('title',toDate($begin_time,'Y年m月').' 归档日志');
				$where	=	array('cTime'=>array(array('gt',$begin_time),array('lt', $end_time)),'status'=>1);
                $count  =  $dao->count($where);
                //创建分页对象
				$fields	=	'id,title,category,categoryId,cTime,readCount,commentCount';
                //分页查询数据
                $voList     = $dao->where($where)->field($fields)->order('cTime desc')->findAll();
                //分页显示
                //模板赋值显示
                $this->assign('list',       $voList);
				$this->assign("count",$count);
    	}else {
                $this->redirect('index');
                return ;
        }
        $this->display();
    }

  public function tag()
    {
    	$dao = D("Tag");
        if(!empty($_GET['name'])) {
        	$name=trim($_GET['name']);
            $list  = $dao->getFields("id,id","module='Blog' and name='$name'");
            $tagId  =  implode(',',$list);
            $dao = D("Tagged");
            import("ORG.Util.Page");
			$listRows = 45;
			$fields	=	'a.id,a.categoryId,a.cTime,a.readCount,a.commentCount,a.title,c.title as category';
            $count  =  $dao->count("tagId  in ('$tagId')");
            $p          = new Page($count,$listRows);
            $p->setConfig('header' ,'篇日志 ');
            $dao = D("Blog");
            $list     = $dao->query("select ".$fields." from ".C('DB_PREFIX').'blog as a,'.C('DB_PREFIX').'tagged as b, '.C('DB_PREFIX').'category as c where b.tagId  in ('.$tagId.') and a.categoryId= c.id and a.status=1  and a.id=b.recordId order by a.id desc limit '.$p->firstRow.','.$p->listRows);
			if($list) {
				//分页显示
				$page       = $p->show();
				//模板赋值显示
				$this->assign("page",       $page);
				$this->assign('list',$list);
			}
			$this->assign('tag',$name);
			$this->assign("count",$count);
        }else {
            $list  = $dao->findAll("module='Blog'",'id,name,count');
            $this->assign('tags',$list);
        }
        $this->display();
    }

    // 查看分类日志
    public function category()
    {
    	$id   =  $_REQUEST['id'];
        if(!empty($id)) {
        	$dao = D("Category");
            $category  = $dao->getById($id);
            if($category) {
                $this->assign('categoryName',$category->title);
                $dao = D("BlogView");
                //取得满足条件的记录数
                $count      = $dao->count(array("status"=>1,'categoryId'=>$id));
                import("ORG.Util.Page");
				$listRows = 45;
				$fields	=	'id,title,categoryId,cTime,readCount,commentCount';
                $p          = new Page($count,$listRows);
                //分页查询数据
                $voList     = $dao->where(array("status"=>1,'categoryId'=>$id))->field($fields)->order('cTime desc')->limit($p->firstRow.','.$p->listRows)->findAll();
                //分页显示
                $page       = $p->show();
                //模板赋值显示
                $this->assign('list',       $voList);
				$this->assign("count",$count);
                $this->assign("page",       $page);
            }else {
            	$this->redirect('index');
                return ;
            }

        }else {
        	$this->redirect('index');
        }
        $this->display();
        return ;
    }

}
?>