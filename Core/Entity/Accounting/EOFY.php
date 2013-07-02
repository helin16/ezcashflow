<?php
/**
 * The End of Financial Year/ tax return documents
 * 
 * @author lhe
 */
class EOFY extends BaseEntityAbstract
{
    /**
     * The start date of the financial year
     * 
     * @var UDate
     */
	private $start;
	/**
	 * The end date of the financial year
	 * 
	 * @var UDate
	 */
	private $end;
	/**
	 * The comments for the financial year
	 * 
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
	 * The getter for start
	 *
	 * @return UDate
	 */
	public function getStart()
	{
	    return $this->start;
	}
	/**
	 * The setter of the start
	 * 
	 * @param string|UDate $start The start date of the FY
	 * 
	 * @return EOFY
	 */
	public function setStart($start)
	{
	    if(!$start instanceof UDate)
	        $start = new UDate($start);
        $this->start = $start;
	    return $this;
	}
	/**
	 * The getter for end
	 *
	 * @return string
	 */
	public function getEnd()
	{
	    return $this->end;
	}
	/**
	 * The setter for the end
	 * 
	 * @param string|UDate $end The end date of the FY
	 * 
	 * @return EOFY
	 */
	public function setEnd($end)
	{
	    if(!$end instanceof UDate)
	        $end = new UDate($end);
	    $this->end = $end;
	    return $this;
	}
	/**
	 * The getter for comments
	 *
	 * @return string
	 */
	public function getComments()
	{
	    return $this->comments;
	}
	/**
	 * The setter for the comments
	 * 
	 * @param string $comments The comments
	 * 
	 * @return EOFY
	 */
	public function setComments($comments)
	{
	    $this->comments = $comments;
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
	 * getting the account entry for json
	 *
	 * @return multitype:boolean NULL multitype: unknown
	 */
	public function getJsonArray()
	{
	    $tran = $this->_getJsonFromPM();
	    $tran['assets'] = array();
	    foreach($this->getAssets() as $asset)
	    {
	        $tran['assets'][] = $asset->getJsonArray();
	    }
	    return $tran;
	}
	/**
	 * (non-PHPdoc)
	 * @see BaseEntity::__loadDaoMap()
	 */
	public function __loadDaoMap()
	{
	    DaoMap::begin($this, 'eofy');
	    DaoMap::setDateType('start');
	    DaoMap::setDateType('end');
	    DaoMap::setStringType('comments','varchar',6400);
	    DaoMap::setManyToMany('assets', 'Asset', DaoMap::LEFT_SIDE, 'doc', true);
	    parent::loadDaoMap();
	    DaoMap::commit();
	}
}