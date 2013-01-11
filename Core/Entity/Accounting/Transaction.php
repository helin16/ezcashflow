<?php
/**
 * Transaction Entity
 *
 * @package    Core
 * @subpackage Entity
 * @author     lhe<helin16@gmail.com>
 */
class Transaction extends BaseEntityAbstract 
{
    /**
     * The value of the transaction
     * 
     * @var string
     */
	private $value;
    /**
     * The comments of the transaction
     * 
     * @var string
     */
	private $comments;
    /**
     * The from account
     * 
     * @var AccountEntry
     */
	protected $from;
    /**
     * The to account
     * 
     * @var AccountEntry
     */
	protected $to;
	/**
	 * The array of Documents
	 * 
	 * @var array[Asset]
	 */
	protected $assets;
	/**
	 * getter value
	 *
	 * @return string
	 */
	public function getValue()
	{
		return $this->value;
	}
	/**
	 * setter value
	 * 
	 * @param string $value The value of the transaction
	 * 
	 * @return Transaction
	 */
	public function setValue($value)
	{
		$this->value = $value;
		return $this;
	}
	/**
	 * getter from
	 *
	 * @return AccountEntry
	 */
	public function getFrom()
	{
		$this->loadManyToOne("from");
		return $this->from;
	}
	/**
	 * setter from
	 * 
	 * @param AccountEntry $from The from account
	 * 
	 * @return Transaction
	 */
	public function setFrom(AccountEntry $from = null)
	{
		$this->from = $from;
		return $this;
	}
	/**
	 * getter to
	 *
	 * @return AccountEntry
	 */
	public function getTo()
	{
		$this->loadManyToOne("to");
		return $this->to;
	}
	/**
	 * setter to
	 *
	 * @param AccountEntry $from The to account
	 * 
	 * @return Transaction
	 */
	public function setTo($to)
	{
		$this->to = $to;
		return $this;
	}
	/**
	 * getter comments
	 *
	 * @return string
	 */
	public function getComments()
	{
		return $this->comments;
	}
	/**
	 * setter comments
	 *
	 * @param string $comments The new comments
	 * 
	 * @return Transaction
	 */
	public function setComments($comments)
	{
		$this->comments = $comments;
	}
	/**
	 * getter for the assets
	 * 
	 * @return multitype:Asset
	 */
	public function getAssets()
	{
	    $this->loadManyToMany('assets');
	    return $this->assets;
	}
	/**
	 * setter for the assets
	 * 
	 * @param array $assets The array of asset
	 * 
	 * @return Transaction
	 */
	public function setAssets($assets)
	{
	    $this->assets = $assets;
	    return $this;
	}
	/**
	 * (non-PHPdoc)
	 * @see BaseEntity::__loadDaoMap()
	 */
	public function __loadDaoMap()
	{
		DaoMap::begin($this, 'trans');
		DaoMap::setStringType('value');
		DaoMap::setStringType('comments','varchar',6400);
		DaoMap::setManyToOne("from","AccountEntry","from",true);
		DaoMap::setManyToOne("to","AccountEntry","to");
		DaoMap::setManyToMany('assets', 'Asset', DaoMap::LEFT_SIDE, 'doc', true);
		parent::loadDaoMap();
		DaoMap::commit();
	}
}
?>