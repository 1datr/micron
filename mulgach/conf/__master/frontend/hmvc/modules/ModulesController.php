<?php 
namespace __master\Frontend;

class ModulesController extends \BaseController
{
	public function Rules()
	{
		return array(
				'action_access'=>array(
						new \ActionAccessRule('deny',$this->getActions(),'anonym','site/login')
				),
		);
	}
		
	public function ActionIndex()
	{
		$this->_TITLE="Master";
		$this->add_css($this->get_current_dir()."/css/style.css");
		
		$this->add_block('BASE_MENU', 'site', 'menu');
		$this->add_keyword('modules');
		
		global $_BASEDIR;
		$modules_dir = url_seg_add($_BASEDIR,'modules');
		//echo $modules_dir;
		$dirs = get_nested_dirs($modules_dir);
		$dirlist=array();
		foreach ($dirs as $dir)
		{
			$dirlist[]=basename($dir);
		}
	//	print_r($dirlist);
		
		$this->out_view('index',array('modules'=>$dirlist));
	}
	
	public function ActionMakeplg()
	{
		if(!empty($_POST['plgname']))
		{
			GLOBAL $_BASEDIR;
			require_once url_seg_add($_BASEDIR,'api/mullib/scaff_api/index.php');
			
			$module = new \scaff_module($_POST['module']);
			$module->create_plugin($_POST['plgname'],array('rewrite'=>true));
		
			$this->redirect(as_url('modules'));
		}
	}
	
	public function BeforeAction(&$params)
	{
		$this->add_block('SIDEBAR_LEFT', 'configs', 'conflist');
	}
	
	
}
?>