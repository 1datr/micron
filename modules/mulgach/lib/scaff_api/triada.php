<?php
namespace modules\mulgach\scaffapi{
	use Core;
	use modules\mulgach\mul_Controller as BaseController;
	
	class scaff_triada
	{
		VAR $_PATH;
		VAR $_VIEWPATH;
		VAR $_PARENT_CONF;
		VAR $_EP;
		VAR $NAME;
		VAR $_CONTROLLER_CLASS;
		VAR $_CONTROLLER_PATH;
		VAR $_MODEL_PATH;
		VAR $_BASEFILE_PATH;
		VAR $menu_site_codes;
		VAR $_SETTINGS=array();
		VAR $_AUTH=FALSE;
	
		function __construct(&$conf_obj,$ep,$triada,$create=true)
		{
			$this->_PARENT_CONF = $conf_obj;
			$this->_EP = $ep;
			$this->NAME = $triada;
			$this->_PATH = url_seg_add( $conf_obj->_PATH, $ep, 'hmvc', $triada);
			$this->_CONTROLLER_PATH = url_seg_add($this->_PATH,ucfirst($triada)."Controller.php");
			$this->_CONTROLLER_CLASS = UcaseFirst($conf_obj->_NAME).'\\'.UcaseFirst($ep)."\\".ucfirst($triada)."Controller";
		//	echo $this->_PATH;
			if(!file_exists($this->_PATH) || $create)
			{
				x_mkdir($this->_PATH);
			}
	
			$this->_VIEWPATH = url_seg_add($this->_PATH,'views');
			if(!file_exists($this->_VIEWPATH) || $create)
			{
				x_mkdir($this->_VIEWPATH);
			}
			
			$baseinfo = url_seg_add($this->_PATH,'baseinfo.php');
			if(file_exists($baseinfo))
			{
				include $baseinfo;
				$this->_SETTINGS=$settings;
			}
		}
		
		function is_auth()
		{
			$_CONTROLLER_FILE = url_seg_add($this->_PATH,'controller.php');
			if(file_exists( $_CONTROLLER_FILE))
			{
				require_once $_CONTROLLER_FILE;
				
				$controller_name = BaseController::ControllerName($this->NAME);
				$controller_name = ucfirst($this->_PARENT_CONF->_NAME).'\\'.ucfirst($this->_EP).'\\'.$controller_name;
				$con_obj = new $controller_name('#test');
				if($this->_EP=='install')
					return method_exists($con_obj, "ActionregAdmin");
				else
					return method_exists($con_obj, "ActionLogin");
			}
			return false;
		}
		// 'actions' => action list
		function make_pure($params,$rewrite_all=true)
		{
	
			if($this->_EP=='install')
			{
				$this->make_install_site();
			}
			else 
			{
				$this->x_make_controller(array(
							'triada'=>$this->NAME,
							'actions'=>$params['actions'],
					), $rewrite_all, 'customcontroller');
			}
			foreach ($params['actions'] as $act)
			{
				if(!empty($act['automakeview']))
				{
					$the_view = url_seg_add($this->_PATH,'views',$act['name'].".php");
					if($rewrite_all || !file_exists($the_view) )
					{
						echo "<p>$the_view rewrited</p>";
						x_file_put_contents($the_view, '');
					}
				}
			}
		}
		
		function make_install_site()
		{
			file_put_contents(url_seg_add($this->_PATH,'controller.php'), 
					parse_code_template( url_seg_add(__DIR__,'/phpt/install/installcontroller.phpt'),
							['triada'=>$this->NAME,'_EP'=> ucfirst($this->_EP),'_CONFIG'=>ucfirst($this->_PARENT_CONF->_NAME)]
						) );
		}
		
		function from_template()
		{
			
		}
		
		function make_base($rewrite=true)
		{
			$this->make_pure(array('actions'=>array(array('name'=>'index','automakeview'=>'on')),),$rewrite);
			file_put_contents(url_seg_add($this->_VIEWPATH,'error404.php'), file_get_contents( url_seg_add(__DIR__,'/phpt/error404.phpt')) );
			file_put_contents(url_seg_add($this->_VIEWPATH,'error403.php'), file_get_contents( url_seg_add(__DIR__,'/phpt/error403.phpt')) );
			if($this->_EP=='install')
			{
				$arr_templates = array('regsuccess','maketables','dbsettings','dbinfocontainer','index');
				$_vars=[];
				foreach ($arr_templates as $_template)
				{
					file_put_contents(url_seg_add($this->_VIEWPATH,$_template.'.php'), parse_code_template( url_seg_add(__DIR__,'/phpt/install/view/'.$_template.'.phpt'), $_vars ) );
				}
			}
			
		}	
			
		static function exists(&$obj,$ep,$hmvc)
		{
			$triada_path = url_seg_add($obj->_PATH, $ep, 'hmvc', $hmvc);
			
			//mul_dbg("path > ".$triada_path);
			
			if(file_exists($triada_path))
			{
				return true;
			}
			return false;
		}
		
		function make_args($controller_name,$controller_object,$_action_name,&$request)
		{
			$class = new \ReflectionClass($controller_name);
			$method = $class->getMethod($_action_name);
			$method_params = $method->getParameters();
			$method_args=array();
		
			// Проход по параметрам
			$idx_in_req_args=0;
		
			$_RULES = $controller_object->Rules();
			$arg_info = array();
			if(!empty($_RULES['action_args']))
			{
				$arg_info = $_RULES['action_args'][$request->_action];
			}
		
			//print_r($arg_info);
			$request->save_args_as_real();
		
			$_fun_map = array();
			foreach ($method_params as $idx => $arg)
			{
				//	print_r($arg);
				$_fun_map[]=$arg->name;
				if(isset($request->_args[$arg->name]))
				{
					$_val = $request->_args[$arg->name];
					if(!empty($arg_info[$arg->name]))
					{
						//	echo '$_val=('.$arg_info[$arg->name].')$request->_args[$arg->name];';
						eval('$_val=('.$arg_info[$arg->name].')$request->_args[$arg->name];');
					}
					else
					{
						$_val=$request->_args[$arg->name];
					}
					$method_args[] = $_val;
				}
				elseif(isset($request->_args[$idx_in_req_args]))
				{
					//	echo "+ $idx_in_req_args +";
		
					if(isset($arg_info[$arg->name]))
					{
						//	echo '$request->_args[$arg->name]=('.$arg_info[$arg->name].')$request->_args[$idx_in_req_args];';
						eval('$request->_args[$arg->name]=('.$arg_info[$arg->name].')$request->_args[$idx_in_req_args];');
					}
					else
					{
						$request->_args[$arg->name]=$request->_args[$idx_in_req_args];
					}
		
					$request->delete_arg($idx_in_req_args);
					$method_args[$arg->name]=$request->_args[$arg->name];
						
					//	print_r($method_args);
					//getType
					$idx_in_req_args++;
				}
				else
				{
					if($arg->isDefaultValueAvailable())
					{
						$defval = $arg->getDefaultValue();
						$method_args[$arg->name]=$defval;
						$request->_args[$arg->name]=$defval;
					}
				}
			}
		
			//  print_r($method_args);
			$request->setmap($_fun_map);
		
			//print_r($request);
			if(count($method_args)!=count($method_params))
			{
				//	echo ";;xxx;;";
				return NULL;
			}
			return $method_args;
		}
		
		
		function action_info($_action)
		{
			return ['action'=>$_action,'action_func'=>"Action".ucfirst($_action)];
		}
		// файл модели
		function make_model($_params,$rewrite_all=true)
		{
			$this->_MODEL_PATH = url_seg_add( $this->_PATH,'model.php');
			if(!file_exists($this->_MODEL_PATH) || $rewrite_all)
			{
				$vars=array();
				$vars['table_uc_first']=UcaseFirst($_params['table']);
				$vars['TABLE_UC']=strtoupper($_params['table']);
				$vars['table'] = $_params['table'];
				
				$vars['_EP'] = ucfirst($this->_EP);
				$vars['_CONFIG'] = ucfirst($this->_PARENT_CONF->_NAME);
				
				$vars['BaseModelClass'] = 'BaseModel';
				if(isset($_params['authcon']['enable']))				
					$vars['BaseModelClass'] = 'AuthModel';
				x_file_put_contents($this->_MODEL_PATH, parse_code_template(url_seg_add(__DIR__,'/phpt/model.phpt'),$vars));
			}
		}
		
		function add_view($viewname,$_template,$vars=array(),$rewrite_all=true)
		{
			$new_view_path=url_seg_add($this->_VIEWPATH,$viewname.".php");
			
			if(!file_exists($new_view_path) || $rewrite_all)
			{
				x_file_put_contents($new_view_path, parse_code_template(url_seg_add(__DIR__,'/phpt/'.$_template.'.phpt'),$vars));
			}
		}
		
		function has_view($_view)
		{
			$path=url_seg_add($this->_VIEWPATH,$_view);
			return file_exists($path);
		}
		
		function delete()
		{
			unlink_folder($this->_PATH); 
		}
		
		function action_exists($act)
		{
			$controller_name = BaseController::ControllerName($this->NAME);
			$_CONTROLLER_FILE = url_seg_add($this->_PATH,$controller_name.'.php');
			if(file_exists( $_CONTROLLER_FILE))
			{
				require_once $_CONTROLLER_FILE;
					
				
				$controller_name = ucfirst($this->_PARENT_CONF->_NAME).'\\'.ucfirst($this->_EP).'\\'.$controller_name;
				$con_obj = new $controller_name('#test');
				
				return $con_obj->_action_exists($act);
			}
			return false;
			
		}
		
		function add_std_data_views($_params,$controller)
		{
			include $this->_BASEFILE_PATH;
			
		//	$tbl_fields = $controller->_ENV['_CONNECTION']->get_table_fields($_params['table']);		
			$tbl_fields = $controller->_CONNECTION->get_table_fields($_params['table']);
			
		//	$_primary = $controller->_ENV['_CONNECTION']->get_primary($tbl_fields);
			$_primary = $controller->_CONNECTION->get_primary($tbl_fields);
			
			if( !($this->has_view('index')) || $_params['rewrite_all'])
			{
				$vars=array();
				$vars['table'] = $_params['table'];
				$vars['primary']=$_primary;
				$vars['TABLE_UC']=strtoupper($_params['table']);
				$this->add_view('index','view_index',$vars,$_params['rewrite_all']);
			}
			
		//	$itemform_view = url_seg_add($dir_views,'itemform.php');
			if( !($this->has_view('itemform'))|| $_params['rewrite_all'])
			{
							
				$vars=array();		
				$vars['table'] = $_params['table'];
				$vars['TABLE_UC']=strtoupper($_params['table']);
				$vars['fld_primary']=$_primary;
				$vars['fields']=$tbl_fields;
				$vars['settings']=$settings;
				$vars['constraints']=$_params['constraints'];
				$vars['fld_passw']='';
				if(isset($_params['authcon']['enable']))
				{
					$vars['fld_passw'] = $_params['authcon']['passw'];
				}
				$this->add_view('itemform','view_itemform',$vars,$_params['rewrite_all']);
			}
			
			if( !($this->has_view('itemview'))|| $_params['rewrite_all'])
			{
			
				$vars=array();
				$vars['table'] = $_params['table'];
				$vars['TABLE_UC']=strtoupper($_params['table']);
				$vars['fld_primary']=$_primary;
				$vars['fields']=$tbl_fields;
				$vars['settings']=$settings;
				$vars['constraints']=$_params['constraints'];
				$vars['fld_passw']='';
				if(isset($_params['authcon']['enable']))
				{
					$vars['fld_passw'] = $_params['authcon']['passw'];
				}
				$this->add_view('itemview','itemview',$vars,$_params['rewrite_all']);
			}
			
		//	контроллер авторизации
			if($_params['authcon']['enable'])
			{
				if( !($this->has_view('loginform'))|| $_params['rewrite_all'])
				{
					
					$vars=array();
					$vars['this_controller'] = $_params['table'];
					$vars['login_fld']=$_params['authcon']['login'];
					$vars['passw_fld']=$_params['authcon']['passw'];
					$vars['fld_passw']=$_params['authcon']['passw'];
				
				//	$vars['settings']=$settings;
				//	$vars['constraints']=$_params['constraints'];
					$this->add_view('loginform','login/form',$vars,$_params['rewrite_all']);
					
					if($this->_EP=='frontend')
					{
						$vars['makeuser_action']=$this->NAME.'/makeuser';
						$fields = $this->_SETTINGS['fields'];
						
						$ordered_fields=array();
						
						$login_field = $this->_SETTINGS['authdata']['login_field'];
						//mul_dbg($login_field);
						$ordered_fields[$login_field]=$fields[$login_field];
						unset($fields[$login_field]);
								
						$passw_field= $this->_SETTINGS['authdata']['passw_field'];
						$ordered_fields[$passw_field]=$fields[$passw_field];
						$passw_field_re = $passw_field."_re";
						$ordered_fields[$passw_field_re]=$fields[$passw_field];
						unset($fields[$passw_field]);
						
						$email_field = $this->_SETTINGS['authdata']['email_field'];
						//mul_dbg($login_field);
						$ordered_fields[$email_field]=$fields[$email_field];
						unset($fields[$email_field]);
						
						$hash_tag = $this->_SETTINGS['authdata']['hash_tag'];
					//	$ordered_fields[$hash_tag]=$fields[$hash_tag];
						unset($fields[$hash_tag]);
						
					//	mul_dbg($ordered_fields);
						
						$ordered_fields=merge_arrays_assoc($ordered_fields, $fields);
						
					//	mul_dbg($ordered_fields);
						
						$vars['fields_ordered']=$ordered_fields;
						$vars['fld_primary']=$_primary;
						$vars['fld_passw']=$passw_field;
						$vars['fld_passw_re']=$passw_field_re;
						$vars['table'] = $_params['table'];
						$vars['TABLE_UC']=strtoupper($_params['table']);
						$vars['fields']=$tbl_fields;
						$vars['settings']=$settings;
						$vars['constraints']=$_params['constraints'];
						
						$this->add_view('register','reg/regtemplate',$vars,$_params['rewrite_all']);
						
						$this->add_view('regsuccess','reg/regsuccess',$vars,$_params['rewrite_all']);
					}
				}
			}
		}
			
		function make_baseinfo($_params,$controller,$template='baseinfo')
		{
			$_template = url_seg_add(__DIR__,'/phpt/',$template.'.phpt');		
		//	$file_baseinfo= url_seg_add( $this->_PATH,'baseinfo.php');
			
			$vars=array();
			$vars['table']=$_params['table'];
			$vars['filesparams']='';
			
		//	mul_dbg($controller);		
			//$tbl_fields = $controller->_ENV['_CONNECTION']->get_table_fields($_params['table']);
			
			$tbl_fields = $controller->_CONNECTION->get_table_fields($_params['table']);
			
			$files_params='';
			foreach ($_params['model_fields'] as $idx => $fld)
			{
				if(isset($fld['file_fields']))
				{
					$files_params=$files_params."'".$_params['model_fields'][$idx]['name']."'=>array('type'=>'".$_params['model_fields'][$idx]['filter']."'),";
					// make folder to save files
					x_mkdir( url_seg_add($this->_PATH,'../../../files/',$this->NAME,$_params['model_fields'][$idx]['name'] ) );
				}
			}
			$vars['filesparams']="'file_fields'=>array({$files_params}),";
				
		//	$this->gather_fields_captions($tbl_fields);
			
			$fields_code = xx_implode($tbl_fields, ',', "'{idx}'=>array('Type'=>'{Type}','TypeInfo'=>\"{TypeInfo}\")",
					function(&$theval,&$idx,&$thetemplate,&$ctr,&$thedelimeter){
						//	$theval['TypeInfo']=strtr($theval['TypeInfo'],array("'"=>"'"));
					});
				
			$vars['array_fields']="array({$fields_code})";
			
			
			$con_str="";
			
		//	mul_dbg($_params['constraints']);
			
			if(!empty($_params['constraints']))
			{
				foreach ($_params['constraints'] as $idx => $binding)
				{
					$required = ((!empty($binding['required'])) ? true : false);
					$con_str = $con_str."'".$binding['field']."'=>array('model'=>'".$binding['model']."','fld'=>'".$binding['field_to']."','required'=>".json_encode($required)."),";
				}
			}
			$constraints="";
			
			$vars['array_constraints']="array($con_str)";
			$vars['array_rules']='array()';
			$_primary = $controller->_CONNECTION->get_primary($tbl_fields);
			$vars['primary']=$_primary;
			$vars['view']=$_params['view'];
			
			$vars['authparams']='';
		//	mul_dbg($_params['authcon']);
		//	mul_dbg($_params['authcon'][$this->_EP]['enable']);
			$_basic_req_fields_auth=[];
			
			if( eql_ife($_params['authcon'], 'enable', true))
			{			
				
				$vars['authparams']=parse_code_template(url_seg_add(__DIR__,'phpt/authparams.phpt') , 
						array(
							'src_type'=>'db',
							'src_authdata'=>$_params['table'],
							'login_field'=>$_params['authcon']['login'],
							'passw_field'=>$_params['authcon']['passw'],
							'hash_field'=>$_params['authcon']['hash'],
							'email_field'=>$_params['authcon']['email'],
						));
				
				$_basic_req_fields_auth=[$_params['authcon']['login'], $_params['authcon']['passw'], $_params['authcon']['email'] ];
			}
			
			//print_r($_params['model_fields']);
			
			$vars['required']='array('.xx_implode($_params['model_fields'], ',', "'{name}'",function($theval,&$idx,&$thetemplate,&$ctr,&$thedelimeter) use($_basic_req_fields_auth)
			{
				if(empty($theval['required']) && (!in_array($theval['name'],$_basic_req_fields_auth)))
				{
					$thetemplate='';
					$thedelimeter='';
				}
			}).')';
			
			$this->_BASEFILE_PATH=url_seg_add($this->_PATH,'baseinfo.php');
			/*if(!file_exists($this->_CONTROLLER_PATH) || $rewrite)
			{*/
			x_file_put_contents($this->_BASEFILE_PATH, parse_code_template($_template, $vars));
			//}
		}
		
		function make_controller($vars=array(),$rewrite,$template='controller')
		{
			$_template = url_seg_add(__DIR__,'/phpt/',$template.'.phpt');
			
			$this->_CONTROLLER_PATH=url_seg_add($this->_PATH,$vars['controller_file_base_name']);
			//	$this->_CONTROLLER_PATH=url_seg_add($this->_PATH,'controller.php');
			$vars['_EP'] = ucfirst($this->_EP);
			$vars['_CONFIG'] = ucfirst($this->_PARENT_CONF->_NAME);
			if(!file_exists($this->_CONTROLLER_PATH) || $rewrite)
			{
				x_file_put_contents($this->_CONTROLLER_PATH, parse_code_template($_template, $vars));
			}
		}
		
		public function frontend_from_table($_params,$controller,$opts)
		{
			GLOBAL $_BASEDIR;
		
			$conf_obj=$opts['conf_obj'];
		
		//	mul_dbg(__LINE__." went");
		//	$dbparams = $this->_PARENT_CONF->connect_db_if_exists($controller);
			
			
			$rewrite_all=false;
			if(isset($_params['rewrite_all']))
				$rewrite_all=true;
				
			$ep='frontend';
		
			//	mul_dbg($vars);
		
				// коды для меню
			$this->menu_site_codes=array('menu_method'=>'','menu_block_use'=>'');
			if(!empty($_params['mainmenu'][$this->_EP]))
			{
				$vars_menu=array('auth_con'=>$_params['con_auth']);
				$this->menu_site_codes['menu_method']=parse_code_template(url_seg_add(__DIR__,'/phpt/backend/sitemenu.phpt'),$vars_menu);
		
				$this->menu_site_codes['menu_block_use'] = '$this->add_block("BASE_MENU", "'.$_params["table"].'", "menu");';
		
				if(isset($_params['authcon']['enable']))
				{
					$_params['con_auth']=$_params['table'];
				}
				
				$this->add_view('menu', 'backend/menu', array('auth_con'=>$_params['con_auth']));
					
			//	mul_dbg($this->menu_site_codes);
				/*	$menu_info_file = parse_code_template(url_seg_add(__DIR__,'../../phpt/backend/menu.phpt'),array());
					x_file_put_contents(url_seg_add($hmvc_dir,'views/menu.php'), $menu_info_file);*/
					
			//	$this->add_view('views/menu','backend/menu',array('auth_con'=>$_params['con_auth'][$this->_EP]));
		
				$menu_info_file = url_seg_add($this->_PATH,'../../info/basemenu.php');
				x_file_put_contents($menu_info_file,
						parse_code_template(url_seg_add(__DIR__,'phpt/backend/basemenu.phpt'),array('tables'=>$controller->_ENV['_CONNECTION']->get_tables()))
						);
			}
			else
			{
				$this->menu_site_codes['menu_block_use'] = '$this->add_block("BASE_MENU", "'.$_params['connect_from'][$this->_EP].'", "menu");';
			}
			
			// add controller file
			$this->x_make_controller($_params,$rewrite_all);
			
			// Модель
			$this->make_model($_params,$rewrite_all);
					
			// Файлик		
			$this->make_baseinfo($_params,$controller);
			
			// Стандартные вьюхи
			$this->add_std_data_views($_params,$controller);
		
			// прокачиваем надписи
			if(!empty($_params['captions'][$ep]))
			{
				$thelang=new \Lang(NULL, $this->_PARENT_CONF->_NAME,$this->_EP);
				foreach ($_params['captions'][$ep] as $fld_key => $val)
				{
					$thelang->add_key($fld_key,$val);
				}
			}
		
		}
		
		public function install_from_table($_params,$controller,$opts)
		{
			GLOBAL $_BASEDIR;
			
			$conf_obj=$opts['conf_obj'];
			
			//	mul_dbg(__LINE__." went");
			//	$dbparams = $this->_PARENT_CONF->connect_db_if_exists($controller);
			
			
			$rewrite_all=false;
			if(isset($_params['rewrite_all']))
				$rewrite_all=true;
			
			// add controller file
			$this->x_make_controller($_params,$rewrite_all);
			
			// Модель
			$this->make_model($_params,$rewrite_all);
			
			// Файлик
			$this->make_baseinfo($_params,$controller);
				
			$vars_menu=array('triada'=>$this->NAME,'table'=>$_params['table']);
					
			
			$template_file_name="installtablecontroller";
			
			// 
			if($_params['name'])
			{
				$template_file_name="installcontroller";
			}
			
			if(isset($_params['authcon']['enable']))
			{
	
				$template_file_name="installauthcontroller";
				$this->make_install_user_form();
	
			}		
			
			$vars_menu['_EP'] = ucfirst($this->_EP);
			$vars_menu['_CONFIG'] = ucfirst($this->_PARENT_CONF->_NAME);
			
			file_put_contents(url_seg_add($this->_PATH,'controller.php'), 
					parse_code_template(url_seg_add(__DIR__,'/phpt/install',$template_file_name.'.phpt'),$vars_menu));
			
		}
		
	
		public function make_install_user_form()
		{
			$template_file_name="installauthcontroller";
				
			$uvars['makeuser_action']=$this->NAME.'/regadmin';
			$fields = $this->_SETTINGS['fields'];
			
			$ordered_fields=array();
			
			$login_field = $this->_SETTINGS['authdata']['login_field'];
			//mul_dbg($login_field);
			$ordered_fields[$login_field]=$fields[$login_field];
			unset($fields[$login_field]);
			
			$passw_field= $this->_SETTINGS['authdata']['passw_field'];
			$ordered_fields[$passw_field]=$fields[$passw_field];
			$passw_field_re = $passw_field."_re";
			$ordered_fields[$passw_field_re]=$fields[$passw_field];
			unset($fields[$passw_field]);
			
			$email_field = $this->_SETTINGS['authdata']['email_field'];
			//mul_dbg($login_field);
			$ordered_fields[$email_field]=$fields[$email_field];
			unset($fields[$email_field]);
			
			$hash_tag = $this->_SETTINGS['authdata']['hash_tag'];
			//	$ordered_fields[$hash_tag]=$fields[$hash_tag];
			unset($fields[$hash_tag]);
			
			//	mul_dbg($ordered_fields);
			
			$ordered_fields=merge_arrays_assoc($ordered_fields, $fields);
			
			//	mul_dbg($ordered_fields);
			
			$uvars['fields_ordered']=$ordered_fields;
			$uvars['fld_primary']=$_primary;
			$uvars['fld_passw']=$passw_field;
			$uvars['fld_passw_re']=$passw_field_re;
			$uvars['table'] = $_params['table'];
			$uvars['TABLE_UC']=strtoupper($_params['table']);
			$uvars['fields']=$tbl_fields;
			$uvars['settings']=$settings;
			$uvars['constraints']=$_params['constraints'];
				
			file_put_contents(url_seg_add($this->_PATH,'views/adduserform.php'),
					parse_code_template(url_seg_add(__DIR__,'/phpt/install/view/adduserform.phpt'),
							$uvars
							));
		}
		
		public function backend_from_table($_params,$controller,$opts)
		{
			GLOBAL $_BASEDIR;
		
			$conf_obj=$opts['conf_obj'];
	 
		//	$dbparams = $this->_PARENT_CONF->connect_db_if_exists($controller);
		
			$rewrite_all=false;
			if(isset($_params['rewrite_all']))
				$rewrite_all=true;
					
			$ep='backend';		
		
				// коды для меню
			$this->menu_site_codes=array('menu_method'=>'','menu_block_use'=>'');
			if(isset($_params['mainmenu'][$this->_EP]))
			{
				$vars_menu=array();
				$this->menu_site_codes['menu_method']=parse_code_template(url_seg_add(__DIR__,'/phpt/backend/sitemenu.phpt'),$vars_menu);
		
				$this->menu_site_codes['menu_block_use'] = '$this->add_block("BASE_MENU", "'.$_params["table"].'", "menu");';
					
				if(isset($_params['authcon']['enable']))
				{
					$_params['con_auth']=$_params['table'];
				}
				//	mul_dbg($this->menu_site_codes);
				$this->add_view('menu', 'backend/menu', array('auth_con'=>$_params['con_auth']));
		
					/*	$menu_info_file = parse_code_template(url_seg_add(__DIR__,'../../phpt/backend/menu.phpt'),array());
						x_file_put_contents(url_seg_add($hmvc_dir,'views/menu.php'), $menu_info_file);*/
		
				//$this->add_view('views/menu','backend/menu',array());
		
				$menu_info_file = url_seg_add($this->_PATH,'../../info/basemenu.php');
				x_file_put_contents($menu_info_file,
							parse_code_template(url_seg_add(__DIR__,'phpt/backend/basemenu.phpt'),array('tables'=>$controller->_ENV['_CONNECTION']->get_tables()))
							);
			}
			else
			{
				$this->menu_site_codes['menu_block_use'] = '$this->add_block("BASE_MENU", "'.$_params['connect_from'][$this->_EP].'", "menu");';
			}
				
			//mul_dbg($this->menu_site_codes);
		
			$this->x_make_controller($_params,$rewrite_all);
			//	mul_dbg($vars);
		
			// Модель
			$this->make_model($_params,$rewrite_all);
		
			// Файлик
			$this->make_baseinfo($_params,$controller);
			// Стандартные вьюхи
			$this->add_std_data_views($_params,$controller);
		
			// прокачиваем надписи
			if(!empty($_params['captions'][$ep]))
			{
				$thelang=new \Lang(NULL, $this->_PARENT_CONF->_NAME,$this->_EP);
				foreach ($_params['captions'][$ep] as $fld_key => $val)
				{
					$thelang->add_key($fld_key,$val);
				}
			}
		
		}
		
		
		
		function x_make_controller($_params,$rewrite_all,$template='controller')
		{
			$vars=array();
			$vars['table_uc_first']=UcaseFirst($_params['table']);
			$vars['TABLE_UC']=strtoupper($_params['table']);
			$vars['table'] = $_params['table'];
			$vars['controller_file_base_name'] = UcaseFirst($_params['table'])."Controller.php";
			$vars['OTHER_METHODS']='';
			$vars['ADV_RULES']='';		
			$vars = array_merge($vars,$_params);
			
		//	mul_dbg('menu site codes');
		//	mul_dbg($this->menu_site_codes);
			
			$vars['menu_block_use']=$this->menu_site_codes['menu_block_use'];
			$vars['OTHER_METHODS']=$vars['OTHER_METHODS'].$this->menu_site_codes['menu_method'];
			
			$vars['ParentControllerClass']='BaseController';
	
			// add controller file
			//mul_dbg($_params);		
			
			if(!empty($_params['authcon']['enable']))	// Контроллер авторизации
			{
				$vars['ParentControllerClass']='AuthController';
				
				if( !($this->has_view('loginform'))|| $_params['rewrite_all'])
				{
					$vars['this_controller'] = $_params['table'];
					$vars['login_fld']=$_params['authcon']['login'];
					$vars['passw_fld']=$_params['authcon']['passw'];
					$allowed_methods="'login','auth'";
						
					//	$vars['settings']=$settings;
					//	$vars['constraints']=$_params['constraints'];
					$vars['OTHER_METHODS']= $vars['OTHER_METHODS'] . parse_code_template( url_seg_add(__DIR__,'phpt/login/action.phpt' ), array(
							'login_fld' => $_params['authcon']['login'],
							'passw_fld' => $_params['authcon']['passw'],
							'this_controller' => $_params['table'],
					));
					
					if($this->_EP=='frontend')	// рега для фронтенда
					{
						$vars['OTHER_METHODS']= $vars['OTHER_METHODS'] .parse_code_template( url_seg_add(__DIR__,'phpt/reg/reg_methods.phpt' ), array(
								//'auth_con' => $_params['table'],
								'table' => $_params['table'],
						));
						$allowed_methods="'login','auth','register','makeuser','regsuccess'";
					}
					
					if( in_array($this->_EP,array('backend','frontend')))
					{
						$vars['ADV_RULES']=$vars['ADV_RULES']. parse_code_template( url_seg_add(__DIR__,'phpt/backend/backend_rules_auth.phpt' ), array(		
							'auth_con' => $_params['table'],
							'allowed_methods' => $allowed_methods,
						));
					}
					
				//	$this->add_view('loginform','login/action',$vars,$_params['rewrite_all']);
				}
			}
			else // простой контроллер
			{
				if( in_array($this->_EP,array('backend','frontend')))
				{
				//	mul_dbg($_params);
					
					$vars['ADV_RULES']=$vars['ADV_RULES']. parse_code_template( url_seg_add(__DIR__,'phpt/backend/backend_rules.phpt' ), array(
							'auth_con' => $_params['con_auth'],
					));
				}
			}
		//	mul_dbg($vars);
		//	mul_dbg($_params);
			$this->make_controller($vars,$rewrite_all,$template);
		}
		
		public static function GetTriads($confs,$ep)
		{
		
		}
		
		function getModelInfo()
		{
			$this->_BASEFILE_PATH=url_seg_add($this->_PATH,'baseinfo.php');
			if(file_exists($this->_BASEFILE_PATH))
			{
				include $this->_BASEFILE_PATH;
				return $settings;
			}
			
			return NULL;
		}
		
		function getView($_viewname)
		{
			return url_seg_add($this->_PATH,'views',$_viewname).".php";
		}
		
		function getExistingModelInfo($triada,$ep="frontend")
		{
			GLOBAL $_BASEDIR;
			$baseinfo_file=url_seg_add($this->_PATH,$ep,"hmvc",$triada,"baseinfo.php");
			//echo $baseinfo_file;
			if(file_exists($baseinfo_file))
			{
				include $baseinfo_file;
				return $settings;
			}
		
			return NULL;
		}
	}

}