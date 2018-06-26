<#php 
namespace {_CONFIG}\{_EP};

class {table_uc_first}Controller extends \{ParentControllerClass}
{

	public function Rules()
	{
		return array(
			'action_args'=>array(
				'index'=>['page'=>'integer'],	
				'edit'=>['id'=>'integer'],	
				'delete'=>['id'=>'integer'],
			),			
			{ADV_RULES}	
		);
	}
		
	public function ActionIndex($page=1)
	{
		$this->_TITLE="{TABLE_UC}";
	
		$conn = get_connection();
		
		{menu_block_use}

		$ds = $this->_MODEL->findAsPager(array('page_size'=>10),$page,1,$this->getRequest()->getArg('ord'));
		
		
		$this->inline_script("
		    $( document ).ready(function() {
        		$('.ref_delete').click(function() 
        		{
        			if(confirm('Удалить объект?'))
        			{
        				return true;
        			}
        			return false;
        		});
    		});
		");
		
		$this->out_view('index',array('ds'=>$ds));
	}
	
	public function ActionCreate()
	{
		{menu_block_use}
		$this->_TITLE="CREATE {TABLE_UC}";
		$this->out_view('itemform',array('{table}'=>$this->_MODEL->CreateNew()));
	}
	
	public function ActionEdit($id)
	{		
		{menu_block_use}
		${table} = $this->_MODEL->findOne('*.'.$this->_MODEL->getPrimaryName()."=$id");
		$this->_TITLE=${table}->getView()." #{EDIT}"; 
		$this->out_view('itemform',array('{table}'=>${table}));
	}
	
	public function ActionSave()
	{
		$newitem = $this->_MODEL->findByPrimary($_POST['{table}']);
		
		if($newitem!=null)
		{
			
		}
		else 
		{
			$newitem = $this->_MODEL->empty_row_form_model();

		}	
		$newitem->FillFromArray($_POST['{table}']);		
		
		$newitem->save();
		
		if(!empty($_POST['back_url']))
			$this->redirect($_POST['back_url']);
		else 
			$this->redirect(as_url('{table}'));		
	}
			
	public function ActionDelete($id)
	{
		$this->_MODEL->Delete($this->_MODEL->_SETTINGS['primary']."=".$id);
		$this->redirect($_SERVER['HTTP_REFERER']);
	}
	
	public function ActionView($id)
	{
		{menu_block_use}
		${table} = $this->_MODEL->findOne('*.'.$this->_MODEL->getPrimaryName()."=$id"); 
		$this->_TITLE=${table}->getView()." #{VIEW}"; 
		$this->out_view('itemview',array('{table}'=>${table}));
	}
	
	{OTHER_METHODS}
}
#>