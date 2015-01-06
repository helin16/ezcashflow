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
	 * (non-PHPdoc)
	 * @see BaseEntityAbstract::preSave()
	 */
	public function preSave()
	{
		if(intval($this->getCredit()) !== 0 && intval($this->getDebit()) !== 0)
			throw new EntityException('You can NOT save this transaction with both credit or debit at the same time, diffrent transaction needed!');
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
    public function create($name)
    {
    	$item = new AccountEntry();
    	return $item->setName(trim($name))
    		->save();
    }
}

?>
