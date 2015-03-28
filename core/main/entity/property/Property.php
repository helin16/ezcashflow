<?php
class Property extends BaseEntityAbstract
{
	protected $name;
	/**
	 * Organization
	 *
	 * @var Organization
	 */
	protected $organization;
	/**
	 * The price of the property when bought
	 *
	 * @var double
	 */
	private $boughtPrice = 0.0000;
	/**
	 * The setup cost accountentry
	 *
	 * @var AccountEntry
	 */
	protected $setupAcc = null;
	/**
	 * The income accountentry
	 *
	 * @var AccountEntry
	 */
	protected $incomeAcc = null;
	/**
	 * The expense accountentry
	 *
	 * @var AccountEntry
	 */
	protected $expenseAcc = null;
	/**
	 * The description of the property
	 *
	 * @var AccountEntry
	 */
	private $description = '';
	/**
	 * Getter for name
	 *
	 * @return string
	 */
	public function getName()
	{
	    return $this->name;
	}
	/**
	 * Setter for name
	 *
	 * @param string $value The name
	 *
	 * @return Property
	 */
	public function setName($value)
	{
	    $this->name = $value;
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
	 * @param string $value The description
	 *
	 * @return Property
	 */
	public function setDescription($value)
	{
	    $this->description = $value;
	    return $this;
	}
	/**
	 * Getter for organization
	 *
	 * @return Organization
	 */
	public function getOrganization()
	{
		$this->loadManyToOne('organization');
	    return $this->organization;
	}
	/**
	 * Setter for organization
	 *
	 * @param Organization $value The organization
	 *
	 * @return Property
	 */
	public function setOrganization(Organization $value)
	{
	    $this->organization = $value;
	    return $this;
	}
	/**
	 * Getter for setupAcc
	 *
	 * @return AccountEntry
	 */
	public function getSetupAcc()
	{
		$this->loadManyToOne('setupAcc');
	    return $this->setupAcc;
	}
	/**
	 * Setter for setupAcc
	 *
	 * @param AccountEntry $value The setupAcc
	 *
	 * @return Property
	 */
	public function setSetupAcc(AccountEntry $value = null)
	{
	    $this->setupAcc = $value;
	    return $this;
	}
	/**
	 * Getter for incomeAcc
	 *
	 * @return AccountEntry
	 */
	public function getIncomeAcc()
	{
		$this->loadManyToOne('incomeAcc');
	    return $this->incomeAcc;
	}
	/**
	 * Setter for incomeAcc
	 *
	 * @param AccountEntry $value The incomeAcc
	 *
	 * @return Property
	 */
	public function setIncomeAcc(AccountEntry $value = null)
	{
	    $this->incomeAcc = $value;
	    return $this;
	}
	/**
	 * Getter for expenseAcc
	 *
	 * @return AccountEntry
	 */
	public function getExpenseAcc()
	{
		$this->loadManyToOne('expenseAcc');
	    return $this->expenseAcc;
	}
	/**
	 * Setter for expenseAcc
	 *
	 * @param AccountEntry $value The expenseAcc
	 *
	 * @return Property
	 */
	public function setExpenseAcc(AccountEntry $value = null)
	{
	    $this->expenseAcc = $value;
	    return $this;
	}
	/**
	 * Getter for boughtPrice
	 *
	 * @return double
	 */
	public function getBoughtPrice()
	{
	    return $this->boughtPrice;
	}
	/**
	 * Setter for boughtPrice
	 *
	 * @param double $value The boughtPrice
	 *
	 * @return Property
	 */
	public function setBoughtPrice($value)
	{
	    $this->boughtPrice = $value;
	    return $this;
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
			$array['setupAcc'] = $this->getSetupAcc() instanceof AccountEntry ? $this->getSetupAcc()->getJson() : null;
			$array['incomeAcc'] = $this->getIncomeAcc() instanceof AccountEntry ? $this->getIncomeAcc()->getJson() : null;
			$array['expenseAcc'] = $this->getExpenseAcc() instanceof AccountEntry ? $this->getExpenseAcc()->getJson() : null;
			$array['attachments'] = count($attachments = $this->getAttachments()) === 0 ? array() : array_map(create_function('$a', 'return $a->getJson();'), $attachments);
		}
		return parent::getJson($array, $reset);
	}
	/**
	 * (non-PHPdoc)
	 * @see BaseEntity::__loadDaoMap()
	 */
	public function __loadDaoMap()
	{
		DaoMap::begin($this, 'prop');
		DaoMap::setStringType('name', 'varchar', 200);
		DaoMap::setStringType('description', 'varchar', 255);
		DaoMap::setManyToOne('organization', 'Organization', 'prop_org');
		DaoMap::setManyToOne('setupAcc', 'AccountEntry', 'prop_setup_acc', true);
		DaoMap::setManyToOne('incomeAcc', 'AccountEntry', 'prop_income_acc', true);
		DaoMap::setManyToOne('expenseAcc', 'AccountEntry', 'prop_expense_acc', true);
		DaoMap::setIntType('boughtPrice', 'double', '10,4');
		parent::__loadDaoMap();

		DaoMap::createIndex('name');
		DaoMap::commit();
	}
	/**
	 * Creating a property
	 *
	 * @param Organization $org
	 * @param string       $name
	 * @param string       $description
	 * @param AccountEntry $setupAcc
	 * @param AccountEntry $incomeAcc
	 * @param AccountEntry $expenseAcc
	 *
	 * @return Property
	 */
	public static function create(Organization $org, $name, $description = null, $boughtPrice = 0.0000, AccountEntry $setupAcc = null, AccountEntry $incomeAcc = null, AccountEntry $expenseAcc = null)
	{
		$item = new Property();
		return $item->setOrganization($org)
			->setName(trim($name))
			->setBoughtPrice($boughtPrice)
			->setDescription(trim($description))
			->setSetupAcc($setupAcc)
			->setIncomeAcc($incomeAcc)
			->setExpenseAcc($expenseAcc)
			->save();
	}
}