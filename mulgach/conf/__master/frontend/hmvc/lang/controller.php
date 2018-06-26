<?php 
namespace __master\Frontend;

class LangController extends \BaseController
{
	public function Rules()
	{
		return array(
			'action_access'=>array(
					new \ActionAccessRule('deny',$this->getActions(),'anonym','site/login')
			),
		);
	}
		
	public function ActionIndex($config='main')
	{
		$this->_TITLE= \Lang::__t("Search");
		
		$this->add_block('BASE_MENU', 'site', 'menu');
	//	$this->add_keyword('xxx');
		$dr = $this->_MODEL->empty_row_form_model();
		$dr->setfield('config',$config);
		
		$this->out_view('index',array('results'=>array(),'config'=>$config,'dr'=>$dr,));
	}
	
	public function ActionSet()
	{
		//mul_dbg($_POST);
		$the_lng = new \Lang($_POST['lang']['lang'],$_POST['lang']['config'],$_POST['lang']['ep']);
		$the_lng->add_key($_POST['lang']['langkey'],$_POST['lang']['translation']);
		$this->redirect_back();
	}
	
	public function BeforeAction(&$params)
	{
		$this->add_block('SIDEBAR_LEFT', 'configs', 'conflist');
	}
	
	public function ActionSearch($config='main',$ep='frontend',$lang,$langkey,$translation)
	{
		$this->_TITLE= \Lang::__t("Search result by ").$langkey." ".$translation;
		
		$this->add_block('BASE_MENU', 'site', 'menu');
	//	$this->add_keyword('xxx');
		$results=array();
		$req = $this->getRequest();
		
		$dr = $this->_MODEL->empty_row_form_model();
		$dr->setfield('config',$config);
		$dr->setfield('ep',$ep);
		$dr->setfield('lang',$lang);
		
		$the_lng = new \Lang($lang,$config,$ep);
		$results = $the_lng->search_fuzzy($langkey,$translation);
	
		$this->out_view('index',array('results'=>$results,'request'=>$req,'current_ep'=>$ep,'dr'=>$dr,'config'=>$config,'lang'=>$lang));
	}
	
}
?>