<?php
class AccountType extends BaseEntityAbstract
{
	const ID_ASSET = 1;
	const ID_LIABILITY = 2;
	const ID_INCOME = 3;
	const ID_EXPENSE = 4;
	/**
	 * The name of the account type
	 *
	 * @var string
	 */
	private $name;
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
	 * @return AccountType
	 */
	public function setName($value)
	{
	    $this->name = $value;
	    return $this;
	}
	/**
	 * (non-PHPdoc)
	 * @see BaseEntity::__loadDaoMap()
	 */
	public function __loadDaoMap()
	{
		DaoMap::begin($this, 'acc_type');
		DaoMap::setStringType('name', 'varchar', 10);
		parent::__loadDaoMap();

		DaoMap::createIndex('name');
		DaoMap::commit();
	}
	/**
	 * Getting an account type
	 *
	 * @param int $id
	 *
	 * @return AccountType
	 */
	public static function get($id)
	{
		if(!self::cacheExsits($id))
			self::addCache($id, parent::get($id));
		return self::getCache($id);
	}
}