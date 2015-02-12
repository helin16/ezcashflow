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
	 * AccountEntry
	 * @var AccountEntry
	 */
	protected $accountEntry;
	/**
	 * The credit amount
	 *
	 * @var double
	 */
	private $credit = 0;
	/**
	 * The debit amount
	 *
	 * @var double
	 */
	private $debit = 0;
	/**
	 * The description for transcation
	 *
	 * @var string
	 */
	private $description = '';
	/**
	 * The groupId
	 *
	 * @var string
	 */
	private $groupId = '';
	/**
	 * The run time group id
	 *
	 * @var string
	 */
	private static $_groupId = '';
	/**
	 * Getter for accountEntry
	 *
	 * @return AccountEntry
	 */
	public function getAccountEntry()
	{
		$this->loadManyToOne('accountEntry');
	    return $this->accountEntry;
	}
	/**
	 * Setter for accountEntry
	 *
	 * @param AccountEntry $value The accountEntry
	 *
	 * @return Transaction
	 */
	public function setAccountEntry(AccountEntry $value)
	{
	    $this->accountEntry = $value;
	    return $this;
	}
	/**
	 * Getter for credit
	 *
	 * @return
	 */
	public function getCredit()
	{
	    return $this->credit;
	}
	/**
	 * Setter for credit
	 *
	 * @param double $value The credit
	 *
	 * @return Transaction
	 */
	public function setCredit($value)
	{
	    $this->credit = $value;
	    return $this;
	}
	/**
	 * Getter for debit
	 *
	 * @return number
	 */
	public function getDebit()
	{
	    return $this->debit;
	}
	/**
	 * Setter for debit
	 *
	 * @param double $value The debit
	 *
	 * @return Transaction
	 */
	public function setDebit($value)
	{
	    $this->debit = $value;
	    return $this;
	}
	/**
	 * Getter for description
	 *
	 * @return string
	 */
	public function getDescription()
	{
	    return $this->description;
	}
	/**
	 * Setter for description
	 *
	 * @param unkown $value The description
	 *
	 * @return Transaction
	 */
	public function setDescription($value)
	{
	    $this->description = $value;
	    return $this;
	}
	/**
	 * Getter for groupId
	 *
	 * @return string
	 */
	public function getGroupId()
	{
	    return $this->groupId;
	}
	/**
	 * Setter for groupId
	 *
	 * @param string $value The groupId
	 *
	 * @return Transaction
	 */
	public function setGroupId($value)
	{
	    $this->groupId = $value;
	    return $this;
	}
	/**
	 * (non-PHPdoc)
	 * @see BaseEntityAbstract::preSave()
	 */
	public function preSave()
	{
		if(intval($this->getCredit()) !== 0 && intval($this->getDebit()) !== 0)
			throw new EntityException('You can NOT save this transaction with both credit or debit at the same time, diffrent transaction needed!');
		if(trim(self::$_groupId) === '')
			self::$_groupId = StringUtilsAbstract::getRandKey(__CLASS__);
		if(trim($this->getGroupId()) === '')
			$this->setGroupId(self::$_groupId);
	}
    /**
     * (non-PHPdoc)
     * @see BaseEntity::__loadDaoMap()
     */
    public function __loadDaoMap()
    {
    	DaoMap::begin($this, 'trans');
    	DaoMap::setManyToOne('accountEntry', 'AccountEntry', 'trans_acc');
    	DaoMap::setIntType('credit', 'double', '10,4', false);
    	DaoMap::setIntType('debit', 'double', '10,4', false);
    	DaoMap::setStringType('description', 'varchar', 255);
    	parent::__loadDaoMap();

    	DaoMap::createIndex('credit');
    	DaoMap::createIndex('debit');
    	DaoMap::commit();
    }
    /**
     * Creating a Organization
     *
     * @param string $name
     *
     * @return Ambigous <BaseEntityAbstract, GenericDAO>
     */
    public static function create(AccountEntry $acc, $credit = 0, $debit = 0, $description = '')
    {
    	$item = new Transaction();
    	return $item->setAccountEntry($acc)
    		->setCredit($credit)
    		->setDebit($debit)
    		->setDescription($description)
    		->save();
    }
}

?>
