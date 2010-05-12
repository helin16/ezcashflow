<?php
class Transaction extends HydraEntity 
{
	private $value;
	private $comments;
	protected $from;
	protected $to;
	
	
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
	
	
	public function __loadDaoMap()
	{
		DaoMap::begin($this, 'trans');
		
		DaoMap::setStringType('value');
		DaoMap::setStringType('comments','varchar',6400);
		
		DaoMap::setManyToOne("from","AccountEntry","from");
		DaoMap::setManyToOne("to","AccountEntry","to");
		
		DaoMap::commit();
	}
}
?>