<?php 
namespace __master\Frontend;

class ConfigsController extends \BaseController
{
	public function Rules()
	{
		return array(
				'action_access'=>array(
						new \ActionAccessRule('deny',$this->getActions(),'anonym','site/login')
				),
		);
	}
	//
	public function ActionIndex()
	{
		$this->_TITLE="CONFIGS";
		$this->add_css($this->get_current_dir()."/css/style.css");
		
		$this->add_block('BASE_MENU', 'site', 'menu');
		
		use_jq_plugin('confirmdelete',$this);
		
		global $_BASEDIR;
		$files_in_conf_dir = get_files_in_folder( url_seg_add($_BASEDIR,"/conf"));
		
		GLOBAL $_CONFIG;
		
		$this->out_view('index',array('files'=>$files_in_conf_dir,'curr_config'=>$this->getCurrCFG() ));
	}	
	
	public function BeforeAction(&$params)
	{
		$this->add_block('SIDEBAR_LEFT', 'configs', 'conflist');
	}
	
	public function ActionConflist()
	{
		global $_BASEDIR;
		$files_in_conf_dir = get_files_in_folder( url_seg_add($_BASEDIR,"/conf"));
		
		GLOBAL $_CONFIG;
		
		$this->out_view('conflist',array('files'=>$files_in_conf_dir,'curr_config'=>$this->getCurrCFG() ));
	}
	
	public function ActionChangecfgfile()
	{
		
	}
	
	public function ActionSetdbcfg()
	{
		GLOBAL $_BASEDIR;
		require_once url_seg_add($_BASEDIR,'api/mullib/scaff_api/index.php');
		$cnf = new scaff_conf($_POST['cfg']);
		$cnf->set_db_conf_code($_POST['code']);
		$this->redirect(as_url('configs'));
	}
	
	public function ActionEditconf($cfg='main')
	{
		$this->add_block('BASE_MENU', 'site', 'menu');
		GLOBAL $_BASEDIR;		
		$this->_TITLE=$cfg.' #{Edit database connection config}';
		require_once url_seg_add($_BASEDIR,'api/mullib/scaff_api/index.php');
		$cnf = new \scaff_conf($cfg); 
		
		$this->out_view('configform',array('cfg'=>$cfg,'conf_code'=>$cnf->get_db_conf_code()));
	}
	
	public function ActionPack($cfg=null)
	{
		if($cfg==NULL)
		{				
			$cfg= $this->getCurrCFG();
		}
		
		GLOBAL $_BASEDIR;
		$this->_TITLE=$cfg.' #{Edit database connection config}';
		require_once url_seg_add($_BASEDIR,'api/mullib/scaff_api/index.php');
		$cnf = new \scaff_conf($cfg);
		
		$arcplg = \mul_archive::use_archive_plg('zip');
		
		$zip_file_name = time().'.mca.zip';
		if ($arcplg->ARCMAN->open($zip_file_name, \ZipArchive::CREATE) === true){
			
			$fldr = $arcplg->AddFolder($cnf->_PATH,['/dbconf.php']);
			//$zip->addFile($cfg->_PATH);
		
			$arcplg->ARCMAN->close();
			$this->OutFile($zip_file_name);
			
			unlink($zip_file_name);
		}else{
			echo 'Не могу создать архив!';
		}
	}
	
	public function ActionNew()
	{
		if(!empty($_POST['newcfg']))
		{
			GLOBAL $_BASEDIR;		
			require_once url_seg_add($_BASEDIR,'api/mullib/scaff_api/index.php');
			$cnf = new \scaff_conf($_POST['newcfg'],array('rewrite'=>true));
		}
		$this->redirect_back();
	}
	
	public function ActionDrop($cfg)
	{		
		GLOBAL $_BASEDIR;
		$cfgdir = url_seg_add($_BASEDIR,"/conf",$cfg);
		
		unlink_folder($cfgdir);
		
		$this->redirect_back();
	}
	
	// параметры POST - file
	public function ActionInstall()
	{
		function get_conf_name($file_key)
		{
			$file_info = pathinfo($_FILES[$file_key]['name']);
			
			$finfo2 = pathinfo($file_info['filename']);
			if($finfo2['extension']=='mca')
				return $finfo2['filename'];
			
			return $file_info['filename'];
		}
		
		GLOBAL $_BASEDIR;
		require_once url_seg_add($_BASEDIR,'api/mullib/scaff_api/index.php');
		//form_req_cfg_name
		
		$the_post=$_POST;
		
		if(!isset($_POST['pid']))	// первый степ
		{
			$sp = new \StepProcess();
			$sp->Data('step','req_cfg_name');
		//	mul_dbg($sp->PASSW);
			// Копируем файл во времянку
			if(isset($_FILES['cfgfile']['tmp_name'])) // есть файл конфы
			{
				$cfgname = get_conf_name('cfgfile');
				
				$new_file_name = "./temp/".time()."_".$_FILES['cfgfile']['name'];
				x_file_put_contents($new_file_name, file_get_contents($_FILES['cfgfile']['tmp_name']));
				//basename($_FILES['cfgfile']['name']);
				// зачинаем процесс
					
				$sp->Data('procent',0);
				$sp->Data('zip_file',$new_file_name);
			
				// Проверяем наличие конфы с таким именем
				$conf_str = $cfgname;
				$i=1;
				while( \scaff_conf::exists($conf_str) )
				{
					$conf_str=$cfgname.$i;
					$i++;
				}
			
				$sp->Data('name_must_be',$conf_str);
				
				$sp_model = $sp->getBasicModel();
				
				
				$this->out_json(['pid'=>$sp->PID,'passw'=>$sp->PASSW]);								
				
			}
						
		}
		else 
		{
			$sp = new \StepProcess($_POST['pid'],$_POST['passw']);
			if($sp->Data('step')=='req_cfg_name')	// второй степ
			{						
			//	mul_dbg($sp->PASSW);
				
				$sp_model = $sp->getBasicModel();
				$sp_model['fields']['newcfgname']=['newcfgname'=>array(
						'Type'=>'text',
						'TypeInfo'=>"20",
				)];
				$sp->Dialog($this,'form_req_cfg_name',['sp'=>$sp,'newcfg_model'=>$sp_model,],['title'=>\Lang::__t('Install new configuration')]);
				$sp->Data('step','unpack_config');
				$this->out_json(['pid'=>$sp->PID,
						'procent'=> number_format($sp->Data('procent'), 2, '.', ','),
						'terminated'=>$sp->TERMINATED,
						'dialog'=>$sp->getDialog(),
						'redirect'=>$sp->_REDIR_URL,
				]);
			}
			elseif (isset($_POST['abort']))	// грохнуть процесс
			{
				unlink($sp->Data('zip_file'));
				$sp->terminate();
				$this->out_json(['pid'=>$sp->PID,
						'procent'=> number_format($sp->Data('procent'), 2, '.', ','),
						'terminated'=>$sp->TERMINATED,
						'dialog'=>$sp->getDialog(),
						'redirect'=>$sp->_REDIR_URL,
				]);
			}
			elseif (isset($_POST['newcfgname']))
			{
				$sp->Data('name_must_be',$_POST['newcfgname']);
				if($sp->Data('step')=='unpack_config') // третий степ
				{
					$arcplg = \mul_archive::use_archive_plg('zip');
					
					// распаковываем файлы
					if ($arcplg->ARCMAN->open($sp->Data('zip_file')) === TRUE)
					{
						// Извлекаем файлы из архива в папку конфы
						$new_cfg_dir = \scaff_conf::get_cfg_dir($sp->Data('name_must_be'));
												
						$arcplg->ARCMAN->extractTo($new_cfg_dir);
						$arcplg->ARCMAN->close();
						// меняем неймспейсы
						unlink($sp->Data('zip_file'));
						
						//mul_dbg($triads);
					}
					else 
					{
						//echo 'Ошибка при извлечении файлов из архива';
					}
							
					$sp->Data('step','rename_namespaces');
					//
					$this->out_json(['pid'=>$sp->PID,
							'procent'=> number_format($sp->Data('procent'), 2, '.', ','),
							'terminated'=>$sp->TERMINATED,
							'dialog'=>$sp->getDialog(),
							'redirect'=>$sp->_REDIR_URL,
					]);
					}
			}
			elseif($sp->Data('step')=='rename_namespaces')
			{
				$scaff_cfg = new \scaff_conf($sp->Data('name_must_be'));
				$scaff_cfg->rename_namespaces();
				
				$sp->terminate();
				$sp->redirect('configs');
				$this->out_json(['pid'=>$sp->PID,
						'procent'=> number_format($sp->Data('procent'), 2, '.', ','),
						'terminated'=>$sp->TERMINATED,
						'dialog'=>$sp->getDialog(),
						'redirect'=>$sp->_REDIR_URL,
				]);
			}
		}			
		
	}
	
	public function ActionSetcurrent()
	{
		GLOBAL $_BASEDIR;
		require_once url_seg_add($_BASEDIR,'api/mullib/scaff_api/index.php');
		
		\scaff_conf::set_current_cfg($_POST['cfg']);
		
		$this->redirect_back();
	}
	
}


?>