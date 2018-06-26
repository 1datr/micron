<?php
namespace __master\Frontend;

class SiteController extends \BaseController
{
	public function Rules()
	{
		return array(
			'action_access'=>array(
				new \ActionAccessRule('deny',_array_diff($this->getActions(),array('login','auth')),'anonym','site/login')	
			),
		);
	}
		
	public function ActionIndex()
	{
		$this->_TITLE="Master";
		$this->add_css($this->get_current_dir()."/css/style.css");
		
		$this->add_block('BASE_MENU', 'site', 'menu');
	//	$this->add_keyword('xxx');
		$this->out_view('index',array());
	}
	
	public function ActionMenu()
	{
		$menu = $this->getinfo('basemenu');
		//print_r($menu);
		$this->out_view('menu',array('menu'=>$menu));
	}
	
	public function ActionLogin()
	{
	//	print_r($_SESSION);
		
		$this->_TITLE=\Lang::__t('Master. Authorization');
		$this->use_layout('layout_login');
		$this->out_view('loginform',array());
	}
	
	public function ActionAuth()
	{
	//	print_r($_POST);
		$auth_res = $this->_MODEL->auth($_POST['login'],$_POST['password']);//		
		if($auth_res)
		{
			$_SESSION[$this->get_user_descriptor()]=array('login'=>$_POST['login']);
//			echo as_url('site');

			if(!empty($_POST['url_required']))
				$this->redirect($_POST['url_required']);
			else 
				$this->redirect(as_url('site'));
		}
		else 
		{
			$this->redirect_back();
		}
		
	}
	
	public function BeforeAction(&$params)
	{
		if(!in_array($params['action'],['login','auth']))
		{
			//mul_dbg($params);
			$this->add_block('SIDEBAR_LEFT', 'configs', 'conflist');
		}
	}
	
	public function ActionLogout()
	{
		$this->logout();
		$this->redirect(as_url('site/login'));
	}
}
?>