<?php
namespace modules\base\tree{
	use Core;
	
	// ����� ����� ���� ������	����� ������� �����. ��� ���� ��������
	class tree_node
	{
		VAR $_ITEMS=[];
		VAR $_STANDSTILL=FALSE; // ��������� �����
		VAR $number=NULL;
		VAR $numerator_obj;
	
		function add_item($item)
		{
			if(count($this->_ITEMS)>0)
			{
				$obj_num = clone $this->_ITEMS[count($this->_ITEMS)-1]->numerator_obj;
				$obj_num->inc();
			}
			else
			{
				$obj_num = clone $this->numerator_obj;
				$obj_num->down();
	
			}
	
			$item->numerator_obj = $obj_num;
			$item->number = $obj_num->getText();
	
			$this->_ITEMS[]=$item;
		}
	
	
	}

}