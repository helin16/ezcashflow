<?php
class UniversalDate
{
	/**
	 * @var DateTime
	 */
	private $dateTime;
	
	public function __construct($string = "now", $timeZone="")
	{
		if ($string == "0000-00-00 00:00:00")
			$string = (string)$this->zeroDate();
			
		// Is there a difference between UTC and GMT?
		date_default_timezone_set('UTC');
		
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
				$dt = (string)$date;
		}
		catch (Exception $ex)
		{
			$dt = (string)$this->zeroDate();
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
	 * Test if the date is before the date passed in
	 *
	 * @param UniversalDate $dateTime
	 * @return bool
	 */
	public function before(UniversalDate $dateTime)
	{
		return $this->getUnixTimeStamp() < $dateTime->getUnixTimeStamp();
	}

	/**
	 * Test if the date is after the date passed in
	 *
	 * @param UniversalDate $dateTime
	 * @return bool
	 */
	public function after(UniversalDate $dateTime)
	{
		return $this->getUnixTimeStamp() > $dateTime->getUnixTimeStamp();
	}
	
	/**
	 * Test if the date is before or equal to the date passed in
	 *
	 * @param UniversalDate $dateTime
	 * @return bool
	 */	
	public function beforeOrEqualTo(UniversalDate $dateTime)
	{
		return $this->getUnixTimeStamp() <= $dateTime->getUnixTimeStamp();
	}	
	
	/**
	 * Test if the date is after or equal to the date passed in
	 *
	 * @param UniversalDate $dateTime
	 * @return bool
	 */	
	public function afterOrEqualTo(UniversalDate $dateTime)
	{
		return $this->getUnixTimeStamp() >= $dateTime->getUnixTimeStamp();
	}
	
	/**
	 * Test if the date is equal to the date passed in
	 *
	 * @param UniversalDate $dateTime
	 * @return bool
	 */
	public function equal(UniversalDate $dateTime)
	{
		return $this->getUnixTimeStamp() == $dateTime->getUnixTimeStamp();
	}
	
	/**
	 * Test if the date is not equal to the date passed in
	 *
	 * @param UniversalDate $dateTime
	 * @return bool
	 */
	public function notEqual(UniversalDate $dateTime)
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
		
		return new UniversalDate(date("Y-m-d H:i:s",$newDateTime));
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
		
		return new UniversalDate(date("Y-m-d H:i:s",$newDateTime));
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
		
		return new UniversalDate(date("Y-m-d H:i:s",$newDateTime));
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
		
		return new UniversalDate(date("Y-m-d H:i:s",$newDateTime),$timeZone);
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
	
	public function zeroDate()
	{
		$date = new UniversalDate();
    	$date->setDate(1, 1, 1);
    	$date->setTime(0, 0, 0);
    	return $date;
	}
	
	public static function bigDate()
    {
    	$date = new UniversalDate();
    	$date->setDate(31, 11, 9999);
    	$date->setTime(23, 59, 59);
        return $date;
    }
    
    /**
     * formatting : Sat 20th Jun 8:30pm
     */
    public static function formatDateFromHtml($string)
    {
    	$dateArray = explode(" ",$string);
    	$day = preg_replace("/(\d+)(\w+)/","$1",$dateArray[1]);
    	$month=1;
    	switch(strtoupper(trim($dateArray[2])))
    	{
    		case "JAN":{$month=1;break;}
    		case "FEB":{$month=2;break;}
    		case "MAR":{$month=3;break;}
    		case "APR":{$month=4;break;}
    		case "MAY":{$month=5;break;}
    		case "JUN":{$month=6;break;}
    		case "JUL":{$month=7;break;}
    		case "AUG":{$month=8;break;}
    		case "SEP":{$month=9;break;}
    		case "OCT":{$month=10;break;}
    		case "NOV":{$month=11;break;}
    		case "DEC":{$month=12;break;}
    	}
    	
    	$timeArray = explode(" ",preg_replace("/(\d+):(\d)(.+)/","$1 $2 $3",trim($dateArray[3])));
    	$hour = $timeArray[0];
    	$min = $timeArray[1];
    	if(strtoupper($timeArray[2])=="PM")
    		$hour = $hour+12;
    	
    	
    	$now = new UniversalDate();
    	$now_month = $now->getMonth();
    	$year = $now->getYear();
    	
    	if($now_month<$month)
    		$year++;
    	
    	return sprintf("%04d-%02d-%02d %02d:%02d:00",$year,$month,$day,$hour,$min);
    }
}

?>