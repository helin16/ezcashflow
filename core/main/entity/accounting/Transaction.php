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
	const TYPE_CREDIT = 'CREDIT';
	const TYPE_DEBIT = 'DEBIT';
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
	private $credit = null;
	/**
	 * The debit amount
	 *
	 * @var double
	 */
	private $debit = null;
	/**
	 * The description for transcation
	 *
	 * @var string
	 */
	private $description = '';
	/**
	 * The running balance of the account
	 *
	 * @var double
	 */
	private $balance;
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
	 * Getter for balance
	 *
	 * @return number
	 */
	public function getBalance()
	{
	    return $this->balance;
	}
	/**
	 * Setter for balance
	 *
	 * @param number $value The balance
	 *
	 * @return Transaction
	 */
	public function setBalance($value)
	{
	    $this->balance = $value;
	    return $this;
	}
	/**
	 * (non-PHPdoc)
	 * @see BaseEntityAbstract::preSave()
	 */
	public function preSave()
	{
		if($this->getCredit() === null && $this->getDebit() === null)
			throw new EntityException('One value needed: credit or debit!');
		if($this->getCredit() !== null && $this->getDebit() !== null)
			throw new EntityException('You can NOT save this transaction with both credit or debit at the same time, diffrent transaction needed!');
		if(trim(self::$_groupId) === '')
			self::$_groupId = StringUtilsAbstract::getRandKey(__CLASS__);
		if(trim($this->getGroupId()) === '')
			$this->setGroupId(self::$_groupId);
		if(trim($this->getBalance()) === '')
			$this->setBalance($this->getAccountEntry()->getRuningBalance() + $this->getValue());
	}
	/**
	 * (non-PHPdoc)
	 * @see BaseEntityAbstract::postSave()
	 */
	public function postSave()
	{
		if(intval($this->getActive()) === 0) {
			self::updateByCriteria('active = 0', 'groupId = ?', array(trim($this->getGroupId())));
		}
	}
	/**
	 * Getting the type of the transaction
	 *
	 * @return string
	 */
	public function getType()
	{
		return $this->getCredit() === null ? self::TYPE_DEBIT : self::TYPE_CREDIT;
	}
	/**
	 * Getting signed value of the transaction base on the account
	 *
	 * @return number
	 */
	public function getValue()
	{
		if(!$this->getAccountEntry() instanceof AccountEntry || !$this->getAccountEntry()->getType() instanceof AccountType)
			return null;
		$value = ($this->getCredit() === null ? $this->getDebit() : $this->getCredit());
		$type = $this->getType();
		if($this->getType() === self::TYPE_CREDIT) { //credit
			if(in_array($this->getAccountEntry()->getType()->getId(), array(AccountType::ID_ASSET)))
				return 0 - $value;
			return $value;
		} else { //debit
			if(in_array($this->getAccountEntry()->getType()->getId(), array(AccountType::ID_ASSET, AccountType::ID_EXPENSE)))
				return $value;
			return 0 - $value;
		}
	}
	/**
	 * (non-PHPdoc)
	 * @see BaseEntityAbstract::getJson()
	 */
	public function getJson($extra = '', $reset = false)
	{
		$array = array();
		if(!$this->isJsonLoaded($reset))
		{
			$array['accountEntry'] = $this->getAccountEntry()->getJson();
			$array['value'] = $this->getValue();
			$array['type'] = $this->getType();
		}
		return parent::getJson($array, $reset);
	}
    /**
     * (non-PHPdoc)
     * @see BaseEntity::__loadDaoMap()
     */
    public function __loadDaoMap()
    {
    	DaoMap::begin($this, 'trans');
    	DaoMap::setManyToOne('accountEntry', 'AccountEntry', 'trans_acc');
    	DaoMap::setIntType('credit', 'double', '10,4', false, true);
    	DaoMap::setIntType('debit', 'double', '10,4', false, true);
    	DaoMap::setStringType('description', 'varchar', 255);
    	DaoMap::setIntType('balance', 'double', '10,4', false, true);
    	parent::__loadDaoMap();

    	DaoMap::commit();
    }
    /**
     * Creating a Organization
     *
     * @param string $name
     *
     * @return Ambigous <BaseEntityAbstract, GenericDAO>
     */
    public static function create(AccountEntry $acc, $credit = null, $debit = null, $description = '')
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
