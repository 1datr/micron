<?php
namespace modules\mulgach\hmvc\scaffapi
{
	use Core;
	require_once url_seg_add(__DIR__,"widget.php");

	class BaseController
	{
		VAR $_JS=array();
		VAR $_CSS=array();
		VAR $_BLOCKS=array();
		VAR $_TITLE="";
		VAR $_LAYOUT='basic_layout.php';
		VAR $_META=array();
		VAR $_INLINE_SCRIPT='';
		VAR $_INLINE_STYLE='';
		VAR $_RESULT_TYPE="text/html";
		VAR $_CONTROLLER_DIR=NULL; // директория контроллера (заполняется из модуля page)
		VAR $_ENV;
		VAR $_MODEL;
		VAR $_HEADERS=NULL;
		VAR $_CONNECTION;
	
		function __construct($_env_info=array())
		{
			if($_env_info=='#test')
				return ;
	
				$this->_CONTROLLER_DIR = $_env_info['_CONTROLLER_DIR'];
				$this->_ENV = $_env_info['_ENV'];
	
				$conn = get_connection();
				if(!empty($conn))
				{
					$this->_CONNECTION = $conn;
					$this->_ENV['_CONNECTION']=$this->_CONNECTION;
				}
	
				$model_file = url_seg_add($this->get_current_dir(),"model.php");
				if(file_exists($model_file))
				{
					require_once $model_file;
					$thename = $this->get_this_name();
					//echo $thename;
					$model_ns = new \ReflectionClass($this);
					$model_class_name = $model_ns->getNamespaceName()."\Model".ucfirst(strtolower($thename));
					$model_env = $this->_ENV;
					$model_env['_CONTROLLER']=$this;
					$this->_MODEL = new $model_class_name($this->_CONTROLLER_DIR,$model_env);
				}
		}
	
		public static function ControllerName($trname)
		{
			return ucfirst(strtolower($trname))."Controller";
		}
	
		public function UseModel($model_settings=array())
		{
			$model_env = $this->_ENV;
			$model_env['_CONTROLLER']=$this;
	
			$_model = new BaseModel($this->_CONTROLLER_DIR,$model_env);
			$_model->set_settings($model_settings);
	
			$this->_MODEL = $_model;
			return $this->_MODEL;
		}
	
		public function Rules()
		{
			return array(
			);
		}
	
		public function getCurrCFG()
		{
			$conf_file = "../config.php";
			require_once $conf_file;
			return $_CONFIG;
		}
	
		function CallEvent($eventname, $eparams=[])
		{
			$res = $this->_ENV['page_module']->call_event($eventname, $eparams,['src'=>'controller']);
			$method_name=$eventname;
			$ev_res=array();
			if(method_exists($this, $method_name))
			{
				$this->$method_name($ev_res);
			}
			return $res;
		}
	
		function connect_db($dbparams)
		{
	
			$res = connect_db($dbparams);
			if(!empty($res))
			{
				$this->_ENV['_CONNECTION']=$res;
				$this->_MODEL->_ENV['_CONNECTION']=$this->_ENV['_CONNECTION'];
				return $res;
			}
			return NULL;
		}
	
		function get_this_name()// имя текущего контроллера
		{
			//	$myclassname = get_class($this);
			$myclassname = (new \ReflectionClass($this))->getShortName();
			$matches=array();
			preg_match("/(.*)Controller/", $myclassname, $matches, PREG_OFFSET_CAPTURE);
			//print_r($matches);
			return $matches[1][0];
		}
	
		function _action_exists($act)
		{
			$act_name="Action".ucfirst($act);
			return method_exists($this, $act_name);
		}
	
		function get_image($img)
		{
			$controller_dir = $this->get_current_dir();
			return filepath2url(url_seg_add($controller_dir,$img));
		}
	
		function get_current_dir()
		{
			GLOBAL $_EP, $_CONTROLLER, $_ACTION, $_CONFIG, $_BASEDIR;
			//return url_seg_add($_BASEDIR, url_seg_add("conf", url_seg_add($_CONFIG, $_EP)));
			return $this->_CONTROLLER_DIR;
		}
	
		function get_ep_param($key)
		{
			return $this->_ENV['page_module']->get_ep_conf_param($key);
		}
	
		function get_user_descriptor()
		{
			return $this->get_ep_param('sess_user_descriptor');
		}
	
		function get_user_info($param=NULL)
		{
			$descr = $this->get_user_descriptor();
			if(isset($_SESSION[$descr]))
			{
				if($param==NULL)
					return $_SESSION[$descr];
					else
					{
						if(isset($_SESSION[$descr][$param]))
							return $_SESSION[$descr][$param];
	
							return NULL;
					}
			}
			return NULL;
		}
	
		function logout()
		{
			$userinfo = $_SESSION[$this->get_user_descriptor()];
			$this->BeforeLogout($userinfo);
			unset($_SESSION[$this->get_user_descriptor()]);
			$this->AfterLogout($userinfo);
		}
	
		function BeforeAction(&$params)
		{
	
		}
	
		function AfterAction(&$params)
		{
	
		}
	
		function BeforeLogout($userinfo)
		{
	
		}
	
		function AfterLogout($userinfo)
		{
	
		}
	
		function add_block($area,$controller,$action=NULL,$args=array())
		{
			if(empty($this->_BLOCKS[$area]))
			{
				$this->_BLOCKS[$area]=array();
			}
	
			if($action==NULL)
				$this->_BLOCKS[$area][]=array($controller);
				else
					$this->_BLOCKS[$area][]=array('controller'=>$controller,'action'=>$action,'args'=>$args);
		}
	
		function IsActionEnable($_action,$user=NULL)
		{
			if($_action=='error')
				return true;
				$res = true;
				$rules=$this->Rules();
				if(!empty($rules['action_access']))
				{
					foreach ($rules['action_access'] as $idx => $rule)
					{
						$rule_res = $rule->calc_for($this,$_action);
						$res = $res && $rule_res;
					}
				}
				return $res;
		}
	
		public function getActions()
		{
			$methods = get_class_methods(get_class($this));
			$methodlist=array();
			$method_not_to_include=array('validate','error');
			foreach($methods as $method)
			{
				$matches = array();
				preg_match_all('/^Action(.+)$/Uis', $method, $matches);
				$newmethod = mb_strtolower($matches[1][0]);
				if(($newmethod=='') || ( in_array($newmethod,$method_not_to_include)))
					continue;
					if(!in_array($newmethod,$methodlist))
					{
						$methodlist[]=$newmethod;
					}
	
			}
			return $methodlist;
		}
	
		public function ActionError($ErrorNo)
		{
			$this->use_layout('error_layout');
			$this->_TITLE = "#{ERROR} ".$ErrorNo;
			$this->out_view('error'.$ErrorNo,array());
			//echo "<h3>".Lang::__t('Error').' '.$ErrorNo."</h3>";
		}
	
		public function ActionUploadfile()
		{
			$filemap = $this->_MODEL->UploadfilesTemp();
			$this->out_json($filemap);
			//mul_dbg($_FILES);
			//mul_dbg($_POST);
	
		}
	
		public function ActionGetprogress()
		{
	
		}
	
		public function ActionStopupload()
		{
	
		}
	
		function getinfo($infodescr)
		{
			$_DATA=NULL;
			$path = url_seg_add($this->_CONTROLLER_DIR,url_seg_add('/../..',"/info/$infodescr.php"));
			if(file_exists($path))
			{
				include $path;
			}
			return $_DATA;
		}
	
		function _POST($key)
		{
			if($_POST[$key])
				return $_POST[$key];
				else
					return NULL;
		}
	
		function ActionValidate()
		{
			$res = array();
	
	
			$this->CallEvent('BeforeValidate', ['controller'=>$this,'row'=>$therow,'res'=>&$res]);
	
			//	mul_dbg($therow);
	
			if(!empty($this->_MODEL))
			{
				// валидуем по модели
				//mul_dbg($_POST[$this->_MODEL->get_domen()]);
					
				$therow = $_POST[$this->_MODEL->get_domen()];//_TABLE];
				$res = $this->_MODEL->validate($_POST);
			}
	
			$this->CallEvent('AfterValidate', ['controller'=>$this,'row'=>$therow,'res'=>&$res]);
			$this->out_json($res);
		}
	
		function inline_script($script)
		{
			$this->_INLINE_SCRIPT=$this->_INLINE_SCRIPT.$script;
		}
	
		function inline_css($css)
		{
			$this->_INLINE_STYLE=$this->_INLINE_STYLE.$css;
		}
	
		protected function file_if_relative(&$thefile_name)
		{
			if(substr($thefile_name,0,1)=='#')
			{
				$thefile_name = url_seg_add($this->_CONTROLLER_DIR,strtr($thefile_name,array('#'=>'/')));
			}
		}
	
		function add_js($js)
		{
			$this->file_if_relative($js);
			$this->_JS[]=$js;
		}
	
		// add css
		function add_css($css)
		{
			$this->file_if_relative($css);
			$this->_CSS[]=$css;
		}
	
		// add meta tag
		function add_meta($meta)
		{
			$this->_META[]=$meta;
		}
	
		// add keyword meta
		function add_keyword($kwd)
		{
			foreach ($this->_META as $meta)
			{
				if($meta['name']=='keywords')
				{
					$meta['content']=$meta['content'].",$kwd";
					return ;
				}
			}
			$this->_META[]=array('name'=>'keywords','content'=>"$kwd");
		}
	
		function use_plugin($plug)
		{
	
		}
	
		// переключить лейаут
		function use_layout($newlayout)
		{
			//echo url_seg_add(__DIR__,'../../views',"{$newlayout}.php");
			if( file_exists(url_seg_add($this->get_current_dir(),'../../views',"{$newlayout}.php")) )
				$this->_LAYOUT=$newlayout.'.php';
		}
	
		function out_view($view,$vars=array())
		{
			foreach ($vars as $var => $val)
			{
				$$var = $val;
			}
	
			if(file_exists($view))
				$_view_path = $view;
				else
					$_view_path = $this->get_view_path($view);
					if($this->get_ep_param('print_template_path'))		echo "<!-- {$_view_path} -->";
					include $_view_path;
	
					//url_seg_add($this->get_current_dir(),url_seg_add("/views/",$view)).".php";
	
		}
	
		function get_view_code($view,$vars=array())
		{
			foreach ($vars as $var => $val)
			{
				$$var = $val;
			}
	
			if(file_exists($view))
				$_view_path = $view;
				else
					$_view_path = $this->get_view_path($view);
	
					ob_start();
					if($this->get_ep_param('print_template_path'))		echo "<!-- {$_view_path} -->";
					include $_view_path;
					$_content = ob_get_clean();
					return $_content;
					//url_seg_add($this->get_current_dir(),url_seg_add("/views/",$view)).".php";
	
		}
	
		function x_out_view($view,$vars=array())
		{
			foreach ($vars as $var => $val)
			{
				$$var = $val;
			}
	
			$_view_path = $this->get_view_path($view);
			if(!file_exists($_view_path))
				x_file_put_contents($_view_path, '
	');
				if($this->get_ep_param('print_template_path'))		echo "<!-- {$_view_path} -->";
				include $_view_path;
	
		}
	
		function out_json($object,$json_opts=JSON_UNESCAPED_UNICODE)
		{
			$this->_RESULT_TYPE="application/json";
			$json_res_str = json_encode($object);
			$json_res_str = Lang::translate_str($json_res_str);
			echo $json_res_str;
		}
	
		function out_ajax_block($view,$vars=array(),$out_js=true)
		{
			foreach ($vars as $var => $val)
			{
				$$var = $val;
			}
	
			$_view_path = $this->get_view_path($view);
	
			ob_start();
			if($this->get_ep_param('print_template_path'))
				echo "<!-- {$_view_path} -->";
	
	
				include $_view_path;
				$the_content = ob_get_clean();
	
				$the_content =	\Lang::translate_str($the_content);
	
				$obj_json = array('html'=>$the_content,'js'=>$this->_JS,'css'=>$this->_CSS,'inline_js'=>$this->_INLINE_SCRIPT);
				if($out_js)
				{
					$this->_RESULT_TYPE="application/json";
					echo json_encode($obj_json);
					return null;
				}
				else
					return $obj_json;
		}
	
		function draw_controller_request($req)
		{
			$this->_JS = array_merge($this->_JS,$req['js']);
			$this->_CSS = array_merge($this->_JS,$req['css']);
			$this->_META = array_merge($this->_JS,$req['meta']);
			echo $req['content'];
		}
	
		function getRequest()
		{
			return $this->_ENV['page_module']->_REQUEST;
		}
	
		function usewidget($wid_object,$params=array())
		{
			//	ob_start();
			$wid_object->out($params);
			//	$html = ob_end_clean();
	
			$this->_JS = array_merge($this->_JS,$wid_object->_JS);
			$this->_CSS = array_merge($this->_CSS,$wid_object->_CSS);
			$this->_META = array_merge($this->_META,$wid_object->_META);
			//	echo $html;
		}
	
		function redirect($url)
		{
			//	set_back_page($_SERVER['HTTP_REFERER']);
			?>
			<script type="text/javascript">
	<!--
		document.location="<?=$url?>";
	//-->
	</script>
			<?php 
		}
		
		function redirect_back()
		{
		//	set_back_page($_SERVER['HTTP_REFERER']);
			$this->redirect($_SERVER['HTTP_REFERER']);
		}
		
		function get_view_path($view)
		{
			$path_from_theme = $this->_ENV['page_module']->get_template_from_theme($view);
		//	echo ">>{$path_from_theme} {$view} >>";
			//echo $path_from_theme;
			if($path_from_theme==NULL)
			{
				$path_native = url_seg_add($this->get_current_dir(),url_seg_add("/views/",$view)).".php";
				return $path_native;
			}
			return $path_from_theme;
		}
		
		function query($controller,$method,$args=array())
		{
			return $this->_ENV['page_module']->controller_request(
					array(
							'controller'=>$controller,
							'method'=>$method,
							'args'=>$args,
						)
					);
		}
		
		function OutFile($the_file,$mime_type=NULL)
		{
			if($mime_type==NULL)
			{
				$mime_type=mime_content_type($the_file);
			}
		
			$this->_RESULT_TYPE=$mime_type;
			echo file_get_contents($the_file);
		}
		
		function get_controller($controller)
		{
			return $this->_ENV['page_module']->get_controller($controller);
		}
		
		// для блоб-полей
		public function ActionGetblob($primary_id,$blobfld)
		{
			
		}
		
		
	}
	
	class AuthController extends BaseController 
	{
		
		public function ActionLogin()
		{
			$this->_TITLE=\Lang::__t('Authorization');
			$this->use_layout('layout_login');
			$this->out_view('login',array());
		}
		
		public function ActionAuth()
		{
			$auth_res = $this->_MODEL->auth($_POST['login'],$_POST['passw']);
			if($auth_res!=false)
			{
				$user_descr = $this->get_ep_param('sess_user_descriptor');
				$_SESSION[$user_descr] = $auth_res;
				$this->redirect('?r=workers/');
			}
			else 
			{
				echo "#{BAD AUTORIZATION}";
			}
		}
		
		public function ActionLogout()
		{
			$user_descr = $this->get_ep_param('sess_user_descriptor');
			unset($_SESSION[$user_descr]);
			$this->redirect_back();
		}
	}
	
	class StepProcess
	{
		VAR $PID;
		VAR $PASSW;
		VAR $ERROR=0;
		VAR $ERROR_MSG="";
		VAR $TERMINATED=FALSE;
		VAR $_DIALOG;
		VAR $_REDIR_URL;
		
		function __construct($pid=NULL,$_passw=NULL)
		{
			use_jq_plugin('__ui', find_module('page')->getController() );
			
			if($pid==NULL)
			{
				if(!isset($_SESSION['processes'])) // Инициализируем хранилище процессов в сессии
				{
					$_SESSION['processes']=[];
				}
				
				$this->PASSW = GenRandStr();
				$_SESSION['processes'][]=['passw'=>$this->PASSW,'Data'=>[],'terminated'=>false];
				$this->PID=count($_SESSION['processes'])-1;
				
			//	$this->PID = GenRandStr()
			}
			else 
			{
				$this->PID=$pid;
				if(isset($_SESSION['processes'][$pid]))
				{
					if($_SESSION['processes'][$pid]['passw']!=$_passw)
					{
						$this->ERROR=2;
						$this->ERROR_MSG="Wrong password";
					}
					else	// без ошибокподключились  
					{
						$this->TERMINATED=$_SESSION['processes'][$pid]['terminated'];
						$this->PASSW = $_passw;
					}
				}
				else 
				{
					$this->ERROR=1;
					$this->ERROR_MSG="Process not exists";
				}
			}
		}
		
		function terminate($_ERROR=0,$ERR_TEXT="")
		{
			$this->TERMINATED=true;
			$this->ERROR=$_ERROR;
			$this->ERROR_MSG=$ERR_TEXT;
			unset($_SESSION['processes'][$this->PID]);
		}
		
		function redirect($redir_url)
		{
			$this->_REDIR_URL = $redir_url;
		}
		
		function Data($data_key,$data_val=NULL)
		{
			if($data_val===NULL)
			{
				return $_SESSION['processes'][$this->PID]['Data'][$data_key];
			}
			else 
			{
				$_SESSION['processes'][$this->PID]['Data'][$data_key]=$data_val;
			}
		}
		
		function Dialog($_controller,$tpl,$tplvars=[],$dlgopts=[])
		{
			$dlg_js = $_controller->out_ajax_block($tpl,$tplvars,false);
			use_jq_plugin('__ui',$_controller);
			$this->_DIALOG = $dlg_js;
			$this->_DIALOG['settings']=$dlgopts;
		}
		
		function getBasicModel()
		{
			return [
					'fields'=>array('pid'=>array('Type'=>'bigint','TypeInfo'=>"20"),'passw'=>array('Type'=>'text','TypeInfo'=>""),),
					'required'=>array('id','passw')
					];
		}
		
		function getDialog()
		{
			return $this->_DIALOG;
		}
		
		
	}
}