<?php
namespace __master\Frontend;

class ModelEmaker extends \BaseModel
{
	
	function OnScenarioChange()
	{
		switch($this->scenario())
		{
			case 'makenew':{
				$this->_SETTINGS=array(
					//	'table'=>'frm_makenew',
						'domen'=>'makenew',
					//	'name'=>'makenew',
						'fields'=>[
								'cfg'=>['Type'=>'text'],
								'ename'=>['Type'=>'text'],							
								'auth_entity'=>['Type'=>'boolean'],							
						],
						'required'=>['ename',]
						
				);	
				}; break;
			case 'efield':{
				$this->_SETTINGS=array(
					//	'table'=>'frm_makenew',
						'domen'=>'entity',
						//	'name'=>'makenew',						
						'fields'=>[
								'cfg'=>['Type'=>'text'],
								'ename'=>['Type'=>'text'],
								'oldname'=>['Type'=>'text'],
								'view'=>['Type'=>'text'],
								'auth_con'=>['Type'=>'text'],	
								//auth fields
								'auth_fld_login'=>['Type'=>'text'],
								'auth_fld_email'=>['Type'=>'text'],
								'auth_fld_passw'=>['Type'=>'text'],
								'auth_fld_hash'=>['Type'=>'text'],
								
								'main_menu'=>['Type'=>'boolean'],
								
								'fieldlist'=>new \ModelInfo([
									'domen'=>'field',
									'validate_proc'=>function($row,&$res,$prefix="")
										{
											//mul_dbg($row);
											if(isset($row['file']))
											{
												$type_class = $this->_ENV['page_module']->_CONTROLLER->_CONNECTION->GetTypeClass($row['type']);
												if(!in_array($type_class, ['text','binary']))
												{
													add_keypair($res,$prefix."[fldname]","#{Field with this type could not be a file}");
												}

											}
										},
									'fields'=>[
										'fldname'=>['Type'=>'text'],
										//'nameroot'=>['Type'=>'text'],
										'fldname_old'=>['Type'=>'text'],
										'type'=>['Type'=>'text'],
										'typeinfo'=>new \ModelInfo([
											'mode'=>'variant',
											'onload'=>function($parentrow)
												{
												
												}
										]),
										'defval'=>['Type'=>'text'],
										'defval_enable'=>['Type'=>'boolean','defval'=>true],
										'primary'=>['Type'=>'boolean'],
										'required'=>['Type'=>'boolean'],
										'deletable'=>['Type'=>'boolean','defval'=>true],
										'file_enabled'=>['Type'=>'boolean'],
										'file'=>['Type'=>'boolean'],
										'filetype'=>['Type'=>'text','dependency'=>['fld'=>'file','type'=>'iftrue']],
									],
									'required'=>['fldname','type']
										
								]),
							'redirect_here'=>['Type'=>'boolean'],
							'build'=>['Type'=>'boolean'],
						],
						'required'=>['ename',]
				
				);
				};break;
		}
	}
	
}
