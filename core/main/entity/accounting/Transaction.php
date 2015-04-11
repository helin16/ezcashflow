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
	 *
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
	 * The transaction log date
	 *
	 * @var UDate
	 */
	private $logDate;
	/**
	 * The transaction log created by
	 *
	 * @var UserAccount
	 */
	protected $logBy = null;
	/**
	 * The groupId
	 *
	 * @var string
	 */
	private $groupId = '';
	/**
	 * The minus or plus value to a transaction for a accountentry
	 *
	 * @var value
	 */
	private $value = 0;
	/**
	 * The run time group id
	 *
	 * @var string
	 */
	private static $_groupId = '';
	/**
	 * The organization
	 * 
	 * @var Organization
	 */
	protected $organization;
	/**
	 * Getter for accountEntry
	 *
	 * @return AccountEntry
	 */
	public function getAccountEntry()
	{
		$this->loadManyToOne ( 'accountEntry' );
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
	 *
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
	 * The getter for logDate
	 *
	 * @return UDate
	 */
	public function getLogDate()
	{
		return $this->logDate;
	}
	/**
	 * Setter for logDate
	 *
	 * @param mixed $value The new value of logDate
	 *
	 * @return Transaction
	 */
	public function setLogDate($value)
	{
		$this->logDate = $value;
		return $this;
	}
	/**
	 * Getter for logBy
	 *
	 * @return UserAccount
	 */
	public function getLogBy()
	{
		$this->loadManyToOne ( 'logBy' );
		return $this->logBy;
	}
	/**
	 * Setter for logBy
	 *
	 * @param UserAccount $value The logBy
	 *
	 * @return Transaction
	 */
	public function setLogBy(UserAccount $value)
	{
		$this->logBy = $value;
		return $this;
	}
	/**
	 * Getting the type of the transaction
	 *
	 * @return string
	 */
	public function getType()
	{
		return $this->getCredit () === null ? self::TYPE_DEBIT : self::TYPE_CREDIT;
	}
	/**
	 * Getting signed value of the transaction base on the account
	 *
	 * @return number
	 */
	public function getValue()
	{
		return trim ( $this->value );
	}
	/**
	 * Setter for the value
	 *
	 * @param number $value
	 *
	 * @return Transaction
	 */
	public function setValue($value)
	{
		$this->value = $value;
		return $this;
	}
	/**
	 * Getting organization
	 *
	 * @return Organization
	 */
	public function getOrganization()
	{
		$this->loadManyToOne('organization');
		return $this->organization;
	}
	/**
	 * Setter for the value
	 *
	 * @param Organization $organization
	 *
	 * @return Organization
	 */
	public function setOrganization(Organization $value)
	{
		$this->organization = $value;
		return $this;
	}
	/**
	 * Getting the signed value from debit and credit for an account entry
	 *
	 * @return NULL number
	 */
	public function getSignedValue()
	{
		if (! $this->getAccountEntry () instanceof AccountEntry || ! $this->getAccountEntry ()->getType () instanceof AccountType)
			return null;
		$value = ($this->getCredit () === null ? $this->getDebit () : $this->getCredit ());
		$type = $this->getType ();
		if ($this->getType () === self::TYPE_CREDIT) { // credit
			if (in_array ( $this->getAccountEntry ()->getType ()->getId (), array (
					AccountType::ID_ASSET
			) ))
				return 0 - $value;
			return $value;
		} else { // debit
			if (in_array ( $this->getAccountEntry ()->getType ()->getId (), array (
					AccountType::ID_ASSET,
					AccountType::ID_EXPENSE
			) ))
				return $value;
			return 0 - $value;
		}
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
		if(trim($this->getLogDate()) === '')
			$this->setLogDate(new UDate());
		if(!$this->getLogBy() instanceof UserAccount)
			$this->setLogBy(Core::getUser());
		$this->setValue($this->getSignedValue());
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
	 * (non-PHPdoc)
	 *
	 * @see BaseEntityAbstract::getJson()
	 */
	public function getJson($extra = '', $reset = false)
	{
		$array = array ();
		if (! $this->isJsonLoaded ( $reset )) {
			$array ['accountEntry'] = $this->getAccountEntry ()->getJson ();
			$array ['type'] = $this->getType ();
			$attachments = $this->getAttachments ();
			$array ['attachments'] = count ( $attachments ) === 0 ? array () : array_map ( create_function ( '$a', 'return $a->getJson();' ), $attachments );
			$array ['logBy'] = $this->getLogBy () instanceof UserAccount ? $this->getLogBy ()->getJson () : array ();
			$array ['createdBy'] = $this->getCreatedBy () instanceof UserAccount ? $this->getCreatedBy ()->getJson () : array ();
		}
		return parent::getJson ( $array, $reset );
	}
	/**
	 * (non-PHPdoc)
	 *
	 * @see BaseEntity::__loadDaoMap()
	 */
	public function __loadDaoMap() {
		DaoMap::begin ( $this, 'trans' );
		DaoMap::setManyToOne ( 'organization', 'Organization', 'trans_org' );
		DaoMap::setStringType ( 'groupId', 'varchar', 32 );
		DaoMap::setDateType ( 'logDate' );
		DaoMap::setManyToOne ( 'logBy', 'UserAccount', 'trans_logby', true );
		DaoMap::setManyToOne ( 'accountEntry', 'AccountEntry', 'trans_acc' );
		DaoMap::setIntType ( 'credit', 'double', '10,4', false, true );
		DaoMap::setIntType ( 'debit', 'double', '10,4', false, true );
		DaoMap::setStringType ( 'description', 'varchar', 255 );
		DaoMap::setIntType ( 'value', 'double', '10,4', false, true );
		parent::__loadDaoMap ();

		DaoMap::createIndex ( 'logDate' );
		DaoMap::createIndex ( 'groupId' );
		DaoMap::commit ();
	}
	/**
	 * Creating a Transaction
	 *
	 * @param Organization $organization
	 * @param AccountEntry $fromAcc
	 * @param AccountEntry $toAcc
	 * @param number       $value
	 * @param string       $description
	 * @param UserAccount  $logBy
	 *
	 * @param array $assets
	 *
	 * @return Ambigous <BaseEntityAbstract, GenericDAO>
	 */
	public static function transactions(Organization $organization, AccountEntry $fromAcc, AccountEntry $toAcc, $value, $description = '', UDate $logDate = null, UserAccount $logBy = null, array $assets = array())
	{
		$groupId = StringUtilsAbstract::getRandKey(__CLASS__);
		$creditItem = new Transaction();
		$debitItem = new Transaction();
		if (!$logBy instanceof UserAccount){
			$creditItem->setLogBy ( Core::getUser() );
			$debitItem->setLogBy ( Core::getUser() );
		}
		$creditItem->setOrganization($organization)->setAccountEntry ( $fromAcc )->setCredit ( $value )->setLogDate ( $logDate )->setDescription ( $description )->setGroupId($groupId)->save ();
		$debitItem->setOrganization($organization)->setAccountEntry ( $toAcc )->setDebit( $value )->setLogDate ( $logDate )->setDescription ( $description )->setGroupId($groupId)->save ();
		foreach($assets as $asset) {
			if(!$asset instanceof Asset)
				continue;
			$creditItem->addAttachment($asset);
			$debitItem->addAttachment($asset);
		}
		return array($creditItem, $debitItem);
	}
	/**
	 * Getting the pair of transactions
	 *
	 * @param string $groupId The group id
	 *
	 * @return Ambigous <Ambigous, multitype:, multitype:BaseEntityAbstract >
	 */
	public static function getTransGroup($groupId)
	{
		return self::getAllByCriteria('groupId = ?', array(trim($groupId)), true, 1, 2, array('trans.debit' => 'asc'));
	}
	/**
	 * Updating the transactions
	 *
	 * @param string       $groupId
	 * @param AccountEntry $fromAcc
	 * @param AccountEntry $toAcc
	 * @param unknown      $value
	 * @param string       $description
	 * @param UDate        $logDate
	 * @param UserAccount  $logBy
	 * @param array        $assets
	 *
	 * @return array
	 */
	public static function updateTrans($groupId, AccountEntry $fromAcc, AccountEntry $toAcc, $value, $description = '', UDate $logDate = null, UserAccount $logBy = null, array $assets = array())
	{
		list($creditItem, $debitItem) = self::getTransGroup($groupId);
		if (!$logBy instanceof UserAccount){
			$creditItem->setLogBy ( Core::getUser() );
			$debitItem->setLogBy ( Core::getUser() );
		}
		$creditItem->setAccountEntry ( $fromAcc )->setCredit ( $value )->setLogDate ( $logDate )->setDescription ( $description )->save ();
		$debitItem->setAccountEntry ( $toAcc )->setDebit( $value )->setLogDate ( $logDate )->setDescription ( $description )->save ();
		if(count($assets) > 0) {
			$creditItem->clearAttachments();
			$debitItem->clearAttachments();
			foreach($assets as $asset) {
				if(!$asset instanceof Asset || intval($asset->getActive()) !== 1)
					continue;
				$creditItem->addAttachment($asset);
				$debitItem->addAttachment($asset);
			}
		}
		return array($creditItem, $debitItem);
	}
}

?>
