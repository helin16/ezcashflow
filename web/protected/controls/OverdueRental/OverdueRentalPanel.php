<?php
/**
 * The List of properties that are overdue for rental to come in
 * 
 * @package    Web
 * @subpackage Controls
 * @author     lhe<helin16@gmail.com>
 */
class OverdueRentalPanel extends TPanel
{
	/**
	 * The overdue rental information
	 * @var array
	 */
	private $_overRents = null;
	/**
	 * The Item template for the Overdued item
	 * @var string
	 */
	const ITEM_TEMPLATE = '<div class="row"><a class="link" href="/trans/#{transId}"><span class="address">#{address.full}</span><span class="lastdate">#{lastDate}</span><span class="lastamount">#{lastAmount}</span></a></div>';
	/**
	 * The css class name of the wrapper
	 * @var string
	 */
	const CSS_CLASS_WRAPPER = 'overduerentalwrapper';
	/**
	 * The constructor
	 * 
	 * @param array $overDues The overdue information array
	 */
	public function __construct($overDues = null)
	{
		parent::__construct();
		$this->_overRents = $overDues;
	}
	/**
	 * (non-PHPdoc)
	 * @see TControl::onLoad()
	 */
	public function onLoad($param)
	{
	    $cScripts = PageAbstract::getLastestJS(__CLASS__);
        if (isset($cScripts['js']) && ($lastestJs = trim($cScripts['js'])) !== '')
	        $this->getPage()->getClientScript()->registerScriptFile(__CLASS__ . 'Js', $this->publishAsset($lastestJs));
        if (isset($cScripts['css']) && ($lastestCss = trim($cScripts['css'])) !== '')
	        $this->getPage()->getClientScript()->registerStyleSheetFile(__CLASS__ . 'Css', $this->publishAsset($lastestCss));
		if(!$this->Page->IsPostBack || $param == "reload")
		{
		}
	}
	/**
	 * (non-PHPdoc)
	 * @see TPanel::addAttributesToRender()
	 */
	public function addAttributesToRender($writer)
	{
		parent::addAttributesToRender($writer);
		$writer->addAttribute('class', self::CSS_CLASS_WRAPPER);
	}
	/**
	 * (non-PHPdoc)
	 * @see TWebControl::renderContents()
	 */
	public function renderContents($writer)
	{
		parent::renderContents($writer);
		$overdues = $this->_overRents === null ? self::getOverdueRentals() : $this->_overRents;
		foreach($overdues as $overdue)
		{
			$writer->write(
				str_replace('#{transId}', $overdue['lastTrans']['id'], 
					str_replace('#{address.full}', $overdue['property']['address']['full'], 
						str_replace('#{lastDate}', $overdue['lastTrans']['updated'],
							str_replace('#{lastAmount}', '$' . number_format($overdue['lastTrans']['value'], 2),
								self::ITEM_TEMPLATE
							)
						)
					)
				)
			);
		}
	}
	/**
	 * Getting the overdued property information
	 * 
	 * @return array
	 */
	public static function getOverdueRentals()
	{
		$service = new PropertyService();
		$properties = $service->findAll();
		unset($service);
		
		$overDueArray = array();
		$lastMth = new UDate();
		$lastMth->modify('-1 month');
		foreach($properties as $property)
		{
			$lastestTrans = $property->getLastesIncomeTrans(null, 1, 1);
			//if we can't find the lastest transaction in the last month, then it's an overdue
			if(count($lastestTrans) > 0)
			{
				if($lastestTrans[0]->getUpdated()->before($lastMth))
					$overDueArray[] = array('property' => $property->getJsonArray(), 'lastTrans' => $lastestTrans[0]->getJsonArray());
			}
		}
		unset($lastMth, $lastestTrans, $property, $properties);
		return $overDueArray;
	}
}