<?php
// Вытаскивает данные из базы нужные для скаффолдинга
namespace modules\mulgach\scaffapi{
	use Core;
	
	class DbWatcher {
		
		VAR $_CONNECTION;
		
		function __construct(&$dbconnection)
		{
			$this->_CONNECTION = $dbconnection;
		}
		
		function get_basic_table_info($_table,$autofind_authcon=false)
		{
			$result = [];
			
			$result['table']=$_table;
		
			$result['fields'] = $this->_CONNECTION->get_table_fields($_table);
			
			$result['tables'] = $this->_CONNECTION->get_tables();
		//	mul_dbg($result);
			$result['first_table_fields'] = $this->_CONNECTION->get_table_fields($result['tables'][0]);
			$result['view'] = $this->SearchViewFld($result['fields']);
			
			$result['auth_fields']=$this->search_auth_fields($result['fields']);
			
			$primary_fld = $this->_CONNECTION->get_primary($result['fields']);
			
			if($primary_fld!=null)
			{
				$result['primary']['field']=$primary_fld;
				if($result['fields'][$primary_fld]['Extra'] != 'auto_increment')
				{
					$result['primary']['ai']=FALSE;
				}
				else 
					$result['primary']['ai']=TRUE;
			}
			
			$result['model_fields']=array();
			foreach($result['fields'] as $fld_idx => $fld)
			{
				$result['model_fields'][$fld_idx]=array('name'=>$fld['Field'],'type'=>$fld['Type'],'checked_disabled'=>false);
				if( ($fld['Null']=='NO') && (!in_array($fld['Type'],array('text','mediumtext','longtext'))) )
				{
					$result['model_fields'][$fld_idx]['checked_disabled']=true;
					$result['model_fields'][$fld_idx]['required']=true;
				}
				$result['model_fields'][$fld_idx]['maybe_file']= in_array($fld['Type'],array('text','blob'));
			}
			/*
			if($autofind_authcon)
			{
				$auth_fields = $this->search_auth_fields($result['fields']);
				if( isset($auth_fields['login']) && isset($auth_fields['passw']) && isset($auth_fields['hash']) && isset($auth_fields['email']) )
				{
					$result['con_auth'] = $result['table'];					
					$result['authcon']['enable'] = true;
				}
			}
			*/
			return $result;			
		}
		
		function check_auth_con($table_info)
		{
			$result=[];
			$auth_fields = $this->search_auth_fields($table_info['fields']);
			if( isset($auth_fields['login']) && isset($auth_fields['passw']) && isset($auth_fields['hash']) && isset($auth_fields['email']) )
			{
				$result['login'] = $auth_fields['login'];
				$result['passw'] = $auth_fields['passw'];
				$result['hash'] = $auth_fields['hash'];
				$result['email'] = $auth_fields['email'];
				
			}
			return $result;
		}
		
		function fld_in_settings($fld,$settings_str)
		{
			$str_to_find='{'.$fld.'}';
			if(stristr($settings_str, $str_to_find)!= FALSE)
			{
				return true;
			}
			return false;
		}
		
		function search_auth_fields($_fields)
		{
			$result=array();
			foreach ($_fields as $fld => $val)
			{
				if(strstr($fld,'login')!=false)
				{
					$result['login']=$fld;
				}
			
				if(strstr($fld,'passw')!=false)
				{
					$result['passw']=$fld;
				}
			
				if( (strstr($fld,'hash')!=false) || (strstr($fld,'token')!=false) )
				{
					$result['hash']=$fld;
				}
			
				if( (strstr($fld,'e-mail')!=false) || (strstr($fld,'email')!=false) || (strstr($fld,'mail')!=false) )
				{
					$result['email']=$fld;
				}
			}
			return $result;
		}
		
		function SearchViewFld($fieldlist)
		{
			foreach ($fieldlist as $fld => $fldinfo)
			{
				if( ($fldinfo['Type']=='text') || (strstr($fldinfo['Type'],"varchar")!=false) )
				{
					return "{".$fld."}";
				}
			}
			$primary = $this->_CONNECTION->get_primary($fieldlist);
			return "#{".$primary."}";
		}
		
		
		// Проход по триаде
		function watch_triada($cfg,$trname,$_fields)
		{
			$settings = $_fields;
			
			$tr_front = $cfg->get_triada('frontend', $trname);
			
		
			if($tr_front!=null)
			{
				$tr_info = $tr_front->getModelInfo();
							
				$settings['view']=$tr_info['view'];
				
				//
				if(isset($tr_info['constraints']))
				{
					$settings['constraints']=[];
					foreach ($tr_info['constraints'] as $fld_from => $fld_settings)
					{
						$newcon = ['model'=>$fld_settings['model'],'field'=>$fld_from,'field_to'=>$fld_settings['fld']];
						if(isset($fld_settings['required']))
							$newcon['required']=$fld_settings['required'];
						
						$settings['constraints'][] = $newcon;
					}
					
				}
				
			//	mul_dbg($settings['constraints']);
						
					
				$settings['con_auth'] = $tr_front->_PARENT_CONF->get_auth_con();
				
			//	mul_dbg($settings);
				
				$settings['authcon']['enable'] = ($settings['con_auth'] == $settings['table']);		
							
				$settings['connect_from']['frontend'] = $cfg->find_menu_triada('frontend');
				if($settings['connect_from']['frontend']==$tr_front->NAME)	$settings['mainmenu']['frontend']='On';
				$settings['connect_from']['backend'] = $cfg->find_menu_triada('backend');
				if($settings['connect_from']['backend']==$tr_front->NAME)	$settings['mainmenu']['backend']='On';
				//$tr_back = $cfg->get_triada('frontend', $trname);
			}
			
			
			// настройки полей модели	
			foreach($settings['model_fields'] as $fld_idx => $fld)
			{
				if(isset($tr_info['required'][$fld_idx]))
					$settings['model_fields'][$fld_idx]['required']=true;
				
				if($this->fld_in_settings($fld['name'],$settings['view']))
					$settings['model_fields'][$fld_idx]['required']=true;
				
				if(isset($tr_info['file_fields'][$fld_idx]))
				{
					$settings['model_fields'][$fld_idx]['file_fields']=true;
					$settings['model_fields'][$fld_idx]['filter']=$tr_info['file_fields'][$fld['name']]['type'];
				}
					
			}
			return $settings;
		}
		
		
	}
}