
	public function ActionLogin()
	{
		$this->_TITLE=\Lang::__t('Authorization');
		$this->use_layout('layout_login');
		$this->out_view('loginform',array());
	}
	
	public function ActionAuth()
	{
		$auth_res = $this->_MODEL->auth($_POST['{login_fld}'],$_POST['{passw_fld}']);
		if($auth_res)
		{
			$_SESSION[$this->get_ep_param('sess_user_descriptor')]=array('{login_fld}'=>$_POST['{login_fld}']);
			
			if(!empty($_POST['url_required']))
				$this->redirect($_POST['url_required']);
			else
				$this->redirect(as_url('{this_controller}'));
		}
		else 
			$this->redirect_back();

		//$this->out_view('loginform',array());
	}
	
	
	
	public function ActionLogout()
	{
		$this->logout();
		$this->redirect(as_url('{this_controller}/login'));
	}