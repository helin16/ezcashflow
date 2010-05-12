<?php

class HydraDate
{
	/**
	 * @var DateTime
	 */
	private $dateTime;
	
	public function __construct($string = "now", $timeZone="Australia/Melbourne")
	{
		if($timeZone=="")
			$timeZone = Config::get("time","defaultTimeZone");
		if ($string == "0000-00-00 00:00:00")
			$string = (string)DateUtils::zeroDate();
			
		// Is there a difference between UTC and GMT?
		date_default_timezone_set($timeZone);
		
		if($timeZone == "")
			$this->dateTime = date_create($string);
		else
			$this->dateTime = date_create($string, new DateTimeZone($timeZone));
	}	
	
	/**
	 * @return string
	 */
	public function __toString()
	{
		try
		{
			$dt = date_format($this->dateTime,"Y-m-d H:i:s");
			
			if ($dt == "0000-00-00 00:00:00")
				$dt = (string)DateUtils::zeroDate();
		}
		catch (Exception $ex)
		{
			$dt = (string)DateUtils::zeroDate();
		}	
				
		return $dt;
	}

	/**
	 * setting TimeZone of DateTime Object
	 *
	 * @var String
	 */
	public function setTimeZone($timeZone = 'UTC')
	{
		date_timezone_set($this->dateTime,new DateTimeZone($timeZone));
	}

	public function getTimeZone()
	{
		date_timezone_get($this->dateTime);
	}
	
	/**
	 * Returns a new HydraDate Object with new time zone 
	 *
	 * @param Site $site that has address, that has a timezone

	 * @return HydraDate $dateTimeInTaskSiteTimeZone
	 */
	public function getNewHydraDateConvertedToSiteTimeZone(Site $site)
	{
		if($site == null)
		{
			throw new Exception("Site doesnt exist.");
		}
			
		if($site->getServiceAddress() == null)
		{
			throw new Exception("Site doesnt have an address.");
		}
		
    	$dateTimeInTaskSiteTimeZone = new HydraDate($this->__toString());
		$timeZone = $site->getServiceAddress()->getTimezone();	
    	$dateTimeInTaskSiteTimeZone->setTimeZone($timeZone);	
    	
    	return $dateTimeInTaskSiteTimeZone;
	}
	
	/**
	 * Sets the internal DateTime object
	 *
	 * @param DateTime $dateTime
	 */
	public function setDateTime(DateTime $dateTime)
	{
		$this->dateTime = $dateTime;
	}
	
	/**
	 * Returns the internal DateTime object
	 *
	 * @return DateTime
	 */
	public function getDateTime()
	{
		return $this->dateTime;
	}
	
	/**
	 * Returns only the date part of the internal DateTime object
	 *
	 * @return string
	 */
	public function getDate()
	{
		return date_format($this->dateTime,"Y-m-d");
	}
	
	/**
	 * Returns only the time part of the internal DateTime object
	 *
	 * @return string
	 */
	public function getTime()
	{
		return date_format($this->dateTime,"H:i:s");
	}
	
	/**
	 * Get back a 6 or 10 digit timestamp suitable for SMS messages
	 * 
	 * @param int $digits values 6 or 10
	 * @return int
	 */
	public function getSMSDateTime($digits=6)
	{
		if ($digits == 6)
			return date_format($this->dateTime, "dHi");
			
		return date_format($this->dateTime, "ymdHi");
	}

	/**
	 * Returns a zero date time object
	 *
	 * @return HydraDate::zeroDateTime
	 */
	public static function zeroDateTime()
	{
		return new HydraDate("0001-01-01 00:00:00");
	}
	
	/**
	 * Test if the date is before the date passed in
	 *
	 * @param HydraDate $dateTime
	 * @return bool
	 */
	public function before(HydraDate $dateTime)
	{
		return $this->getUnixTimeStamp() < $dateTime->getUnixTimeStamp();
	}

	/**
	 * Test if the date is after the date passed in
	 *
	 * @param HydraDate $dateTime
	 * @return bool
	 */
	public function after(HydraDate $dateTime)
	{
		return $this->getUnixTimeStamp() > $dateTime->getUnixTimeStamp();
	}
	
	/**
	 * Test if the date is before or equal to the date passed in
	 *
	 * @param HydraDate $dateTime
	 * @return bool
	 */	
	public function beforeOrEqualTo(HydraDate $dateTime)
	{
		return $this->getUnixTimeStamp() <= $dateTime->getUnixTimeStamp();
	}	
	
	/**
	 * Test if the date is after or equal to the date passed in
	 *
	 * @param HydraDate $dateTime
	 * @return bool
	 */	
	public function afterOrEqualTo(HydraDate $dateTime)
	{
		return $this->getUnixTimeStamp() >= $dateTime->getUnixTimeStamp();
	}
	
	/**
	 * Test if the date is equal to the date passed in
	 *
	 * @param HydraDate $dateTime
	 * @return bool
	 */
	public function equal(HydraDate $dateTime)
	{
		return $this->getUnixTimeStamp() == $dateTime->getUnixTimeStamp();
	}
	
	/**
	 * Test if the date is not equal to the date passed in
	 *
	 * @param HydraDate $dateTime
	 * @return bool
	 */
	public function notEqual(HydraDate $dateTime)
	{
		return $this->getUnixTimeStamp() != $dateTime->getUnixTimeStamp();
	}

	/**
	 * Wraps the PHP date_modify function
	 *
	 * @param string $string
	 */
	public function modify($string)
	{
		date_modify($this->dateTime,$string);
	}

	/**
	 * Set the date based on the inputs
	 *
	 * @param int $day
	 * @param int $month
	 * @param int $year
	 */
	public function setDate($day, $month, $year)
	{
		date_date_set($this->dateTime, $year, $month, $day);
	}
	
	/**
	 * Set the time based on the inputs
	 *
	 * @param int $hour
	 * @param int $minute
	 * @param int $second
	 */
	public function setTime($hour, $minute, $second)
	{
		date_time_set($this->dateTime, $hour, $minute, $second);
	}
	
	/**
	 * subtracts or adds sec or min or hr or day or week or month or year
	 * and returns new date 
	 *
	 * @param string $fieldToAdd
	 * @param int $numberToAdd
	 * @return DateTime $dateTime
	 */
	function dateAddSub($fieldToAdd="day", $numberToAdd="0") 
	{
		$formattedDateTime = (strtotime($this->__toString()) != -1) ? strtotime($this->__toString()) : $this->dateTime;   
		$formattedDateTimeArr = getdate($formattedDateTime);

		$yr = $formattedDateTimeArr['year'];
		$mon = $formattedDateTimeArr['mon'];
		$day = $formattedDateTimeArr['mday'];
		$hr = $formattedDateTimeArr['hours'];
		$min = $formattedDateTimeArr['minutes'];
		$sec = $formattedDateTimeArr['seconds'];
		
		switch($fieldToAdd) 
		{
			case "sec":
				$sec += $numberToAdd; 
				break;
			
			case "min":
				$min += $numberToAdd; 
				break;
				
			case "hr":	
				$hr += $numberToAdd; 
				break;
				
			case "day":	
				$day += $numberToAdd; 
				break;
				
			case "wk":
				$day += ($numberToAdd * 7); 
				break;
				
			case "mon": 
				$mon += $numberToAdd; 
				break;
				
			case "yr": 	
				$yr += $numberToAdd; 
				break;
		}   
		
		$newDateTime = mktime($hr, $min, $sec, $mon, $day, $yr);
		
		return new HydraDate(date("Y-m-d H:i:s",$newDateTime));
	}

	/**
	 * returns a new date based on the new day, month, year input
	 *
	 * @param int $day
	 * @param int $month
	 * @param int $year
	 */
	public function modifyDayMonYr($day, $month, $year)
	{
		$formattedDateTime = (strtotime($this->__toString()) != -1) ? strtotime($this->__toString()) : $this->dateTime;   
		$formattedDateTimeArr = getdate($formattedDateTime);

		$hr = $formattedDateTimeArr['hours'];
		$min = $formattedDateTimeArr['minutes'];
		$sec = $formattedDateTimeArr['seconds'];
		$newDateTime = mktime($hr, $min, $sec, $month, $day, $year);
		
		return new HydraDate(date("Y-m-d H:i:s",$newDateTime));
	}
	
	/**
	 * returns a new date based on the new hour, minute, second input
	 *
	 * @param int $hour
	 * @param int $minute
	 * @param int $second
	 */
	public function modifyHrMinSec($hour, $minute, $second)
	{
		$formattedDateTime = (strtotime($this->__toString()) != -1) ? strtotime($this->__toString()) : $this->dateTime;   
		$formattedDateTimeArr = getdate($formattedDateTime);

		$yr = $formattedDateTimeArr['year'];
		$mon = $formattedDateTimeArr['mon'];
		$day = $formattedDateTimeArr['mday'];
		$newDateTime = mktime($hour, $minute, $second, $mon, $day, $yr);
		
		return new HydraDate(date("Y-m-d H:i:s",$newDateTime));
	}	
	
	public function getDateTimeString($timeZone)
	{
		$formattedDateTime = (strtotime($this->__toString()) != -1) ? strtotime($this->__toString()) : $this->dateTime;   
		$formattedDateTimeArr = getdate($formattedDateTime);

		$hr = $formattedDateTimeArr['hours'];
		$min = $formattedDateTimeArr['minutes'];
		$sec = $formattedDateTimeArr['seconds'];		
		$yr = $formattedDateTimeArr['year'];
		$mon = $formattedDateTimeArr['mon'];
		$day = $formattedDateTimeArr['mday'];
		$newDateTime = mktime($hr, $min, $sec, $mon, $day, $yr);
		
		return new HydraDate(date("Y-m-d H:i:s",$newDateTime),$timeZone);
	}	
	
	/**
	 * returns seconds from the date time object
	 *
	 * @return int seconds
	 */
	function getSeconds() 
	{
		$formattedDateTime = (strtotime($this->__toString()) != -1) ? strtotime($this->__toString()) : $this->dateTime;   
		$formattedDateTimeArr = getdate($formattedDateTime);

		return $formattedDateTimeArr['seconds'];
	}
	
	/**
	 * returns minutes from the date time object
	 *
	 * @return int $minutes
	 */
	function getMinutes() 
	{
		$formattedDateTime = (strtotime($this->__toString()) != -1) ? strtotime($this->__toString()) : $this->dateTime;   
		$formattedDateTimeArr = getdate($formattedDateTime);

		return $formattedDateTimeArr['minutes'];
	}
	
	/**
	 * returns hours from the date time object
	 *
	 * @return int $hours
	 */
	function getHours() 
	{
		$formattedDateTime = (strtotime($this->__toString()) != -1) ? strtotime($this->__toString()) : $this->dateTime;   
		$formattedDateTimeArr = getdate($formattedDateTime);

		return $formattedDateTimeArr['hours'];
	}
	
	/**
	 * returns day of the month from the date time object
	 *
	 * @return int $dayOfTheMonth
	 */
	function getDayOfTheMonth() 
	{
		$formattedDateTime = (strtotime($this->__toString()) != -1) ? strtotime($this->__toString()) : $this->dateTime;   
		$formattedDateTimeArr = getdate($formattedDateTime);

		return $formattedDateTimeArr['mday'];
	}
	
	/**
	 * returns day of the week from the date time object
	 *
	 * @return int $dayOfTheWeek
	 */
	function getDayOfTheWeek() 
	{
		$formattedDateTime = (strtotime($this->__toString()) != -1) ? strtotime($this->__toString()) : $this->dateTime;   
		$formattedDateTimeArr = getdate($formattedDateTime);

		return $formattedDateTimeArr['wday'];
	}
	
	/**
	 * returns day of the year from the date time object
	 *
	 * @return int $dayOfTheYear
	 */
	function getDayOfTheYear() 
	{
		$formattedDateTime = (strtotime($this->__toString()) != -1) ? strtotime($this->__toString()) : $this->dateTime;   
		$formattedDateTimeArr = getdate($formattedDateTime);

		return $formattedDateTimeArr['yday'];
	}
	
	/**
	 * returns month from the date time object
	 *
	 * @return int $month
	 */
	function getMonth() 
	{
		$formattedDateTime = (strtotime($this->__toString()) != -1) ? strtotime($this->__toString()) : $this->dateTime;   
		$formattedDateTimeArr = getdate($formattedDateTime);

		return $formattedDateTimeArr['mon'];
	}
	
	/**
	 * returns year from the date time object
	 *
	 * @return int $year
	 */
	function getYear() 
	{
		$formattedDateTime = (strtotime($this->__toString()) != -1) ? strtotime($this->__toString()) : $this->dateTime;   
		$formattedDateTimeArr = getdate($formattedDateTime);

		return $formattedDateTimeArr['year'];
	}
	
	/**
	 * returns day of the week in string (week day) from the date time object
	 *
	 * @return string $weekDay
	 */
	function getWeekDay() 
	{
		$formattedDateTime = (strtotime($this->__toString()) != -1) ? strtotime($this->__toString()) : $this->dateTime;   
		$formattedDateTimeArr = getdate($formattedDateTime);

		return $formattedDateTimeArr['weekday'];
	}
	
	/**
	 * returns month in string from the date time object
	 *
	 * @return string $monthInString
	 */
	function getMonthInString() 
	{
		$formattedDateTime = (strtotime($this->__toString()) != -1) ? strtotime($this->__toString()) : $this->dateTime;   
		$formattedDateTimeArr = getdate($formattedDateTime);

		return $formattedDateTimeArr['month'];
	}
	
	/**
	 * Returns Unix TimeStamp
	 *
	 * @return unixtimestamp
	 */
	public function getUnixTimeStamp()
	{
		$formattedDateTime = (strtotime($this->__toString()) != -1) ? strtotime($this->__toString()) : $this->dateTime;   
		$formattedDateTimeArr = getdate($formattedDateTime);

		$hr = $formattedDateTimeArr['hours'];
		$min = $formattedDateTimeArr['minutes'];
		$sec = $formattedDateTimeArr['seconds'];		
		$yr = $formattedDateTimeArr['year'];
		$mon = $formattedDateTimeArr['mon'];
		$day = $formattedDateTimeArr['mday'];
		$newDateTime = mktime($hr, $min, $sec, $mon, $day, $yr);
		
		return $newDateTime;
	}
	
	/**
	 * Returns all months of the year in array format for the reporting stuf ....
	 *
	 * @return HydraDate::getMonthsOfYear
	 */
	public static function getMonthsOfYear()
	{
		return array("1"=>"JAN", "2"=>"FEB", "3"=>"MAR", "4"=>"APR", "5"=>"MAY", "6"=>"JUN", "7"=>"JUL", "8"=>"AUG", "9"=>"SEP", "10"=>"OCT", "11"=>"NOV", "12"=>"DEC");
	}	
	
	/**
	 * Returns Years in array format for the reporting stuf ....
	 *
	 * @return HydraDate::getLastFewYearFromNow
	 */
	public static function getLastFewYearFromNow($lastHowManyYears=5)
	{
		$nowYear = HydraDate::getNowYear();
		$yearArray = array();
		
		for($i = $lastHowManyYears; $i >= 0; $i--)
			array_push($yearArray, $nowYear-$i);
	
		return $yearArray;
	}	
	
	/**
	 * Returns current Year for the reporting stuf ....
	 *
	 * @return HydraDate::getNowYear
	 */
	public static function getNowYear($timeZone='')
	{
		$now = new HydraDate("now",$timeZone);
		return $now->getYear();
	}	
	
	/**
	 * Returns current month for the reporting stuf ....
	 *
	 * @return HydraDate::getNowMonth
	 */
	public static function getNowMonth($timeZone='')
	{
		$now = new HydraDate("now",$timeZone);
		return $now->getMonth();
	}	
}

?>