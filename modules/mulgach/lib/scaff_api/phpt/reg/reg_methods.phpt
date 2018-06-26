public function ActionRegister()
{
	$this->_TITLE=Lang::__t('User registration');
	$reg_form_struct = $this->_MODEL->empty_row_form_model();
	$this->out_view('register',array('captcha'=>$captcha,'reg_struct'=>$reg_form_struct));
}
	
public function ActionMakeuser()
{
	$this->_MODEL->reguser($_POST['{table}']);
	$this->redirect(as_url('{table}/regsuccess'));
}

public function ActionRegsuccess()
{
	$this->out_view('regsuccess',[]);
}

public function BeforeAction(&$params)
{
	if(in_array($params['action'],array('makeuser')))
	{
		$this->_MODEL->scenario("register");
	}	
	elseif($params['action']=='validate')
	{
		$req = $this->getRequest();
		if($req->_args[0]=="makeuser")
		{
			$this->_MODEL->scenario('register');			
		}
	}
}