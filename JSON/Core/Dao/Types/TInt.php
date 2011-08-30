<?php

class TInt extends BaseDescriptor
{
	public function __construct($field,$width = 8,$default = 0,$nullable=false,$isUnique=false)
	{
		parent::__construct($field,'int',$width,$default,$nullable,$isUnique);
	}
}

?>