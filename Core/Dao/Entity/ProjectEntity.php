<?php
class ProjectEntity extends Entity 
{
	/**
	 * getter CreatedBy
	 *
	 * @return CreatedBy
	 */
	public function getCreatedBy()
	{
		return $this->createdBy;
	}
	
	/**
	 * setter CreatedBy
	 *
	 * @var CreatedBy
	 */
	public function setCreatedBy($CreatedBy)
	{
		$this->createdBy = $CreatedBy;
	}
	
	/**
	 * getter UpdatedBy
	 *
	 * @return UpdatedBy
	 */
	public function getUpdatedBy()
	{
		return $this->updatedBy;
	}
	
	/**
	 * setter UpdatedBy
	 *
	 * @var UpdatedBy
	 */
	public function setUpdatedBy($UpdatedBy)
	{
		$this->updatedBy = $UpdatedBy;
	}
	
	protected function __meta()
	{
		parent::__meta();

		Map::setField($this,new ManyToOne("createdBy","UserAccount"));
		Map::setField($this,new ManyToOne("updatedBy","UserAccount"));
	}
}
?>