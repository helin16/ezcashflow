<?php
/**
 * Property Entity
 *
 * @package    Core
 * @subpackage Entity
 * @author     lhe<helin16@gmail.com>
 */
class Property extends BaseEntityAbstract 
{
    /**
     * The address of the property
     * @var Address
     */
    protected $address;
    /**
     * The orignal bought value for the property
     * @var float
     */
    private $boughtValue;
    /**
     * The root account for the income of the property
     * @var AccountEntry
     */
    protected $incomeAcc;
    /**
     * The root account for the outgoing of the property
     * @var AccountEntry
     */
    protected $outgoingAcc;
    /**
     * The root account for the setup of the property
     * @var AccountEntry
     */
    protected $setupAcc;
    /**
     * The comments for property
     * @var string
     */
    private $comments;
    /**
     * The array of Documents
     *
     * @var array[Asset]
     */
    protected $assets;
    /**
     * Getter for Address
     * 
     * @return Address
     */
    public function getAddress()
    {
         $this->loadManyToOne('address');
        return $this->address;
    }
    /**
     * Setter for Address
     * 
     * @param Address $address The address
     * 
     * @return Property
     */
    public function setAddress(Address $address)
    {
        $this->address = $address;
        return $this;
    }
    /**
     * Getter for Address
     * 
     * @return float
     */
    public function getBoughtValue()
    {
        return $this->boughtValue;
    }
    /**
     * 
     * Setter for Address
     * 
     * @param float $boughtValue The bought value
     * 
     * @return Property
     */
    public function setBoughtValue($boughtValue)
    {
        $this->boughtValue = $boughtValue;
        return $this;
    }
    /**
     * Getter for the Income Account
     * 
     * @return AccountEntry
     */
    public function getIncomeAcc()
    {
        $this->loadManyToOne('incomeAcc');
        return $this->incomeAcc;
    }
    /**
     * Getter for the Income Account
     * 
     * @param AccountEntry $incomeAcc The root Income Account
     * 
     * @return Property
     */
    public function setIncomeAcc(AccountEntry $incomeAcc)
    {
        $this->incomeAcc = $incomeAcc;
        return $this;
    }
    /**
     * Getter for the Outgoing Account
     * 
     * @return AccountEntry
     */
    public function getOutgoingAcc()
    {
        $this->loadManyToOne('outgoingAcc');
        return $this->outgoingAcc;
    }
    /**
     * Setter for the Outgoing Account
     * 
     * @param AccountEntry $outgoingAcc The root Outgoing Account
     * 
     * @return Property
     */
    public function setOutgoingAcc($outgoingAcc)
    {
        $this->outgoingAcc = $outgoingAcc;
        return $this;
    }
    /**
     * Getter for the comments
     * 
     * @return string
     */
    public function getComments()
    {
        return $this->comments;
    }
    /**
     * Getter for the comments
     * 
     * @param string $comments The comments
     * 
     * @return Property
     */
    public function setComments($comments)
    {
        $this->comments = $comments;
        return $this;
    }
    /**
     * getter for setupAcc
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
     * @param AccountEntry $setupAcc The setup AccountEntry
     * 
     * @return Property
     */
    public function setSetupAcc(AccountEntry $setupAcc)
    {
        $this->setupAcc = $setupAcc;
        return $this;
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
     * @see BaseEntityAbstract::getJsonArray()
     */
    public function getJsonArray()
    {
        $array = array();
        $array['id'] = $this->getId();
        $array['address'] = $this->getAddress()->__toString();
        $array['boughtValue'] = $this->getBoughtValue();
        $array['comments'] = $this->getComments();
        $array['setupAcc'] = $this->getSetupAcc()->getJsonArray(false);
        $array['incomeAcc'] = $this->getIncomeAcc()->getJsonArray(false);
        $array['outgoingAcc()'] = $this->getOutgoingAcc()->getJsonArray(false);
        $array['created'] = $this->getCreated()->__toString();
        return $array;
    }
	/**
	 * (non-PHPdoc)
	 * @see BaseEntity::__loadDaoMap()
	 */
	public function __loadDaoMap()
	{
		DaoMap::begin($this, 'trans');
		
		DaoMap::setManyToOne("address", "Address", "addr");
		DaoMap::setIntType('boughtValue', 'float', '12, 2', true, false, '0.00');
		DaoMap::setManyToOne("setupAcc", "AccountEntry"," set");
		DaoMap::setManyToOne("incomeAcc", "AccountEntry", "in");
		DaoMap::setManyToOne("outgoingAcc", "AccountEntry"," out");
		DaoMap::setStringType('comments','varchar' , 6400);
		DaoMap::setManyToMany('assets', 'Asset', DaoMap::LEFT_SIDE, 'doc', true);
		parent::loadDaoMap();
		
		DaoMap::commit();
	}
}
?>