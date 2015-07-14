<?php
/**
 * This is the reports page
 *
 * @package    Web
 * @subpackage Controller
 * @author     lhe<helin16@gmail.com>
 */
class Controller extends BackEndPageAbstract
{
	/**
	 * The menu item for the top menu
	 *
	 * @var string
	 */
	protected $_menuItem = 'report.profitNlost';
	/**
	 * Getting The end javascript
	 *
	 * @return string
	 */
	protected function _getEndJs()
	{
		$js = parent::_getEndJs();
		$js .= 'pageJs';
		$js .= '.setHTMLID("result-list-div", "result-wrapper")';
		$js .= '.setHTMLID("genBtnId", "gen-btn")';
		$js .= '.setCallbackId("genReports", "' . $this->genReportBtn->getUniqueID() . '")';
		$js .= '.init()';
		$js .= ';';
		return $js;
	}
	/**
	 * Getting the report
	 *
	 * @param unknown $sender
	 * @param unknown $param
	 */
	public function genReport($sender, $param)
	{
		$results = $errors = array ();
		try {
			if(!isset($param->CallbackParameter->fromDate) || ($fromDate = trim($param->CallbackParameter->fromDate)) === '')
				throw new Exception('Error: from date can NOT be empty!');
			if(!isset($param->CallbackParameter->toDate) || ($toDate = trim($param->CallbackParameter->toDate)) === '')
				throw new Exception('Error: to date can NOT be empty!');
			if(!isset($param->CallbackParameter->utcOffset) || ($utcOffsetMins = trim($param->CallbackParameter->utcOffset)) === '')
				throw new Exception('Error: utcOffset can NOT be empty!');
			$fromDate = new UDate($fromDate);
			$toDate = new UDate($toDate);
			$styles = $this->_getShareStyles();
			// Create new PHPExcel object
			$objPHPExcel = new PHPExcel();
			// Set document properties
			$description = "Profit And Lost Report from " . $fromDate->format('Y_m_d_H_i_s') . ' to ' . $toDate->format('Y_m_d_H_i_s');
			$objPHPExcel->getProperties()
				->setCreator(Core::getUser()->getPerson()->getFullName())
				->setLastModifiedBy(Core::getUser()->getPerson()->getFullName())
				->setTitle($description)
				->setSubject($description)
				->setDescription($description);
			//add the title
			$rowNo = 1;
			$activeSheet = $objPHPExcel->setActiveSheetIndex(0)->setTitle('Transactions');
			$this->_getExcelRow($activeSheet, $rowNo++, 'Account', 'Type', 'Value', 'Comments', 'Attachments', 'Created');
			$activeSheet->setSharedStyle($styles['titleRow'], 'A' . ($rowNo - 1)  . ":E" . ($rowNo - 1));
			// all the income
			$rowNo = $this->_getAllTrans($activeSheet, $rowNo, $fromDate, $toDate, AccountType::get(AccountType::ID_INCOME), $utcOffsetMins);
			$activeSheet->setSharedStyle($styles['summaryRow'], 'A' . ($rowNo - 1)  . ":E" . ($rowNo - 1));
			// all the expense
			$rowNo = $this->_getAllTrans($activeSheet, $rowNo, $fromDate, $toDate, AccountType::get(AccountType::ID_EXPENSE), $utcOffsetMins);
			$activeSheet->setSharedStyle($styles['summaryRow'], 'A' . ($rowNo - 1)  . ":E" . ($rowNo - 1));
			//set the column width
			$activeSheet->getColumnDimension('A')->setAutoSize(true);
			$activeSheet->getColumnDimension('B')->setAutoSize(true);
			$activeSheet->getColumnDimension('D')->setWidth(40);

			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
			$file = '/tmp/' . md5($description . trim(new UDate()));
			$objWriter->save($file);
			$results['file'] = array('path' => $file, 'name' => str_replace(' ', '_', $description) . '.xlsx');
		} catch ( Exception $ex ) {
			$errors [] = $ex->getMessage ();
		}
		$param->ResponseData = StringUtilsAbstract::getJson ( $results, $errors );
	}

	private function _getShareStyles()
	{
		$titleRow = new PHPExcel_Style();
		$titleRow->applyFromArray(
				array('font' 	=> array('bold' => true)));
		$summaryRow = new PHPExcel_Style();
		$summaryRow->applyFromArray(
				array('font' 	=> array('bold' => true)));
		return array('summaryRow' => $summaryRow, 'titleRow' => $titleRow);
	}

	private function _getAllTrans(PHPExcel_Worksheet &$activeSheet, $rowNo, $fromDate, $toDate, AccountType $accountType, $utcOffsetMins)
	{
		$this->_getExcelRow($activeSheet, $rowNo++, '', $accountType->getName() , '', '', '', '');
		Transaction::getQuery()->eagerLoad('Transaction.accountEntry', 'inner join', 'trans_acc', 'trans_acc.id = trans.accountEntryId and trans_acc.typeId=:typeId');
		$transactions = Transaction::getAllByCriteria('logDate between :fromDate and :toDate', array('fromDate'=> trim($fromDate), 'toDate' => trim($toDate), 'typeId' => $accountType->getId()));
		$startRow = $rowNo;
		foreach($transactions as $index => $transaction) {
			$this->_getExcelRow($activeSheet,
					$rowNo++,
					implode(' / ', $transaction->getAccountEntry()->getBreadCrumbs()),
					$transaction->getAccountEntry()->getType()->getName(),
					$transaction->getValue(),
					$transaction->getDescription(),
					$transaction->getAttachments(),
					trim($transaction->getLogDate()->modify(($utcOffsetMins > 0 ? '+' : '') . $utcOffsetMins . ' minutes'))
			);
		}
		$this->_getExcelRow($activeSheet, $rowNo++,	'Sub-Total:', '', (count($transactions) === 0 ? '0': '=SUM(C' . $startRow . ':C' . (($rowNo - 2) > $startRow ? ($rowNo - 2) : $startRow) . ')'), '', '', '');
		return $rowNo;
	}

	private function _getExcelRow(PHPExcel_Worksheet &$workSheet, $rowNo, $account, $type, $value, $comments, $attachments, $createdTime)
	{
		$colNo = 0;
		$workSheet->getCellByColumnAndRow($colNo++, $rowNo)->setValue($createdTime);
		$workSheet->getCellByColumnAndRow($colNo++, $rowNo)->setValue($type);
		$workSheet->getCellByColumnAndRow($colNo++, $rowNo)->setValue($account);
		$workSheet->getCellByColumnAndRow($colNo++, $rowNo)->setValue($value);
		$workSheet->getCellByColumnAndRow($colNo++, $rowNo)->setValue($comments);
		if(is_string($attachments)) {
			$workSheet->getCellByColumnAndRow($colNo++, $rowNo)->setValue(trim($attachments));
		} else {
			foreach($attachments as $attachment) {
				$workSheet->getCellByColumnAndRow($colNo++, $rowNo)
					->setValue( $attachment->getAsset()->getFileName())
					->getHyperlink()->setUrl((isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/asset/get?id=' . $attachment->getAsset()->getSkey());
			}
		}
		$workSheet->getStyle('C' . $rowNo)->getNumberFormat()->applyFromArray(array('code'=>PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE));
	}
}
?>