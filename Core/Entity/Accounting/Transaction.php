<?php
class Transaction extends ProjectEntity 
{
	/**
	 * getter value
	 *
	 * @return value
	 */
	public function getValue()
	{
		return $this->value;
	}
	
	/**
	 * setter value
	 *
	 * @var value
	 */
	public function setValue($value)
	{
		$this->value = $value;
	}
	
	/**
	 * getter from
	 *
	 * @return from
	 */
	public function getFrom()
	{
		return $this->from;
	}
	
	/**
	 * setter from
	 *
	 * @var from
	 */
	public function setFrom($from)
	{
		$this->from = $from;
	}
	
	/**
	 * getter to
	 *
	 * @return to
	 */
	public function getTo()
	{
		return $this->to;
	}
	
	/**
	 * setter to
	 *
	 * @var to
	 */
	public function setTo($to)
	{
		$this->to = $to;
	}
	/**
	 * getter comments
	 *
	 * @return comments
	 */
	public function getComments()
	{
		return $this->comments;
	}
	
	/**
	 * setter comments
	 *
	 * @var comments
	 */
	public function setComments($comments)
	{
		$this->comments = $comments;
	}
	
	
	protected function __meta()
	{
		parent::__meta();

		Map::setField($this,new TString("value"));
		Map::setField($this,new ManyToOne("from","AccountEntry",true));
		Map::setField($this,new ManyToOne("to","AccountEntry"));
		Map::setField($this,new TString("comments",6400));
	}	
}
?>