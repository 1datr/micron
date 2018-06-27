<?php
namespace modules\mulgach\hmvc {
	use Core;
	use modules\mulgach\HMVCRequest;
	use modules\mulgach\scaffapi\scaff_triada as scaff_triada;
	use modules\mulgach\scaffapi\scaff_conf as scaff_conf;
	
	class Module extends Core\Module 
		{		
			VAR $_INFO;
			VAR $CONF_OBJ;
			
			public function AfterLoad()
			{
				$this->load_lib('hmvc_request');
			
			}
			
			public function required()
			{
				return ['mulgach'];
			}
			
			public function request($req_str)
			{
				$req_str = $_REQUEST['r'];
				$hmvc_req = new HMVCRequest($req_str);
				$simply_res = $this->get_simply_request($hmvc_req);
				
				//print_r($hmvc_req);
				if(!$simply_res['success'])
				{
					$_req2 = $hmvc_req->get_alternative();
					$simply_res = $this->get_simply_request($_req2);
				}
				
				print_r($simply_res);
			}
			
			private function check_action_access($req,&$triada)
			{
				$triada = null;
				if(!scaff_triada::exists($this->CONF_OBJ, $this->_INFO['_EP'], $req->_controller))
				{
					return false;
				}
				$scaff_triada = new scaff_triada($this->CONF_OBJ, $this->_INFO['_EP'], $req->_controller);
				$exists = $scaff_triada->action_exists($req->_action);
				if($exists)
				{
					$triada = $scaff_triada;
				}
				
				return $exists;
				//print_r($scaff_triada);
			}
			
			private function get_simply_request($hmv_req)
			{
				$res=['success'=>true];
				$triada=null;
				if($checkres = $this->check_action_access($hmv_req,$triada))
				{
					$res['result']=$this->call_action($triada, $hmv_req);
				}
				else 
				{
					$res['success']=false;	
				}
				return $res;
			}
			

			private function call_action($triada,$request)
			{
					
				GLOBAL $_BASEDIR;
				$bad_result = array('ok'=>false);
			
		//		print_r($triada);
				
				require_once $triada->_CONTROLLER_PATH;
			/*
				if( file_exists($con_info['_CONTROLLER_FILE']))
				{
					require_once $con_info['_CONTROLLER_FILE'];
				}
				else
				{
					return array_merge($bad_result,array('error'=>'404'));
				}
			
				$controller_name = $con_info['_CONTROLLER_CLASS'];
			*/
				// получить страницу из контроллера
				ob_start();
			
				$controller_name = $triada->_CONTROLLER_CLASS;
			
				$this->_CONTROLLER = new $controller_name(
						array(
								'_CONTROLLER_DIR' => $con_info['_DIR_CONTROLLER'],
								'_ENV'=>$this->_ENV_INFO,
						));
				$_action_name = $con_info['_ACTION'];
			
				// “акого метода нет в контроллере попытка 1
			
				if(!method_exists($this->_CONTROLLER, $_action_name))
				{
					return array_merge($bad_result,array('error'=>'404',));
				}
				//print_r($con_info);
				if(!$this->_CONTROLLER->IsActionEnable($con_info['_ACTION_NAME']))
				{
					return array_merge($bad_result,array('error'=>'403','notry'=>true));
				}
			
				// ѕараметры метода
			
				$method_args = $this->make_args($controller_name, $this->_CONTROLLER, $_action_name, $request);
				//print_r($method_args);
				if($method_args==NULL && !is_array($method_args) )
				{
					return array_merge($bad_result,array('error'=>'404','notry'=>true));
				}
				//print_r($method_args);
				try{
					$before_args = [
							'action'=>$con_info['_ACTION_NAME'],
							'args'=>$method_args,
					];
					$this->_CONTROLLER->BeforeAction($before_args);
						
					call_user_func_array(array($this->_CONTROLLER,$_action_name), $method_args);
					//$controller_object->$_action_name();
					$this->_CONTROLLER->AfterAction($before_args);
				}
				catch (Exception $exc)
				{
					echo $exc->getMessage().' '.$exc->getCode();
				}
				$content = ob_get_contents();
				ob_end_clean();
			
				$controller_object = $this->_CONTROLLER;
			
				$this->_CONTROLLER = $old_controller;
			
				return array(
						'content'=>$content,
						'css'=>$controller_object->_CSS,
						'js'=>$controller_object->_JS,
						'title'=>$controller_object->_TITLE,
						'basic_layout'=>$controller_object->_LAYOUT,
						'_BLOCKS'=>$controller_object->_BLOCKS,
						'meta'=>$controller_object->_META,
						'_INLINE_SCRIPT'=>$controller_object->_INLINE_SCRIPT,
						'_INLINE_CSS'=>$controller_object->_INLINE_STYLE,
						'_RESULT_TYPE'=>$controller_object->_RESULT_TYPE,
						'_HEADERS'=>$controller_object->_HEADERS,
						'ok'=>true,
				);
			}
			
			
			// инфо о том где лежит текущий контроллер
			function controller_info($_CONTROLLER=NULL, $_ACTION=NULL)
			{
				if($_CONTROLLER==NULL)
				{
					GLOBAL $_CONTROLLER;
				}
				if($_ACTION==NULL)
				{
					GLOBAL $_ACTION;
				}
				GLOBAL $_CONFIGS_AREA, $_CONFIG, $_BASEDIR;
			
				$res = array(
						'_DIR_CONFIG' => dir_dotted(url_seg_add($_CONFIGS_AREA,$_CONFIG)),
						'_ACTION_NAME' => $_ACTION,
				);
			
				$_CONTROLLER_SLICES=explode('.', $_CONTROLLER);
			
				$res['_DIR_EP'] = dir_dotted(url_seg_add($this->_INFO['_CURR_CONF_DIR'], $this->_INFO['_EP']));
			/*	if(count($_CONTROLLER_SLICES)>1)	// секци€
				{
					$_CONTROLLER=strtr($_CONTROLLER,array('.'=>'/'));					
					$res['_DIR_SECTION'] = dir_dotted(url_seg_add($res['_DIR_CONFIG'], url_seg_add( url_seg_add('sections',$_CONTROLLER_SLICES[0]),$_EP)));
					$res['_CONTROLLER_NAME']=$_CONTROLLER_SLICES[1];
					$res['_DIR_CONTROLLER'] = dir_dotted(url_seg_add($res['_DIR_SECTION'],"/hmvc/".$res['_CONTROLLER_NAME']));
						
				}
				else 				//
				{
					$res['_CONTROLLER_NAME']=$_CONTROLLER;
					$res['_DIR_CONTROLLER'] = dir_dotted(url_seg_add($res['_DIR_EP'],"/hmvc/{$_CONTROLLER}"));
				}
				$res['_CONTROLLER_CLASS'] = strtoupper(substr($res['_CONTROLLER_NAME'],0,1)).substr($res['_CONTROLLER_NAME'],1,strlen($res['_CONTROLLER_NAME'])-1)."Controller";
			
				$_CONTROLLER_FILE_NEW = url_seg_add( $res['_DIR_CONTROLLER'], ucfirst($_CONTROLLER)."Controller.php");
				$_CONTROLLER_FILE_OLD = url_seg_add( $res['_DIR_CONTROLLER'],"controller.php");
				$res['_CONTROLLER_FILE']=$_CONTROLLER_FILE_NEW;
			
				$this->rename_controller_file($_CONTROLLER_FILE_OLD, $_CONTROLLER_FILE_NEW);
				$res['_ACTION'] = "Action".strtoupper(substr($_ACTION,0,1)).substr($_ACTION,1,strlen($_ACTION)-1);
			*/
				//	print_r($res);
			
				return $res;
			}
			
			private function rename_controller_file($_old_file_name,$new_file_name)
			{
				if(file_exists($_old_file_name))
				{
					rename($_old_file_name, $new_file_name);
				}
			}
			
			public function mulgach_onpage()
			{
				$params = [];
				// параметры текущего запроса
				$this->_INFO = $this->MLAM->call_module('mulgach','info',$params);
				
				$this->CONF_OBJ = new scaff_conf($this->_INFO['_CONFIG'],['conf_dir'=>$this->_INFO['_CURR_CONF_DIR']]);
				
				$this->request($_REQUEST['r']);
				//print_r($mul_params);
				
				
				//print_r($_req2);
			//	echo "<h2>MMMN</h2>";
			}					
	
		}	
}