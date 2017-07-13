<?php
/**
* @package		jVitals Library
* @version		1.0
* @date			2013-09-11
* @copyright	(C) 2007 - 2013 jVitals Digital Technologies Inc. All rights reserved.
* @license    	http://www.gnu.org/copyleft/gpl.html GNU/GPLv3
* @link     	http://jvitals.com
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

class JvitalsTime {
	
	/*
	  display from database  - JvitalsTime::getUser($timestamp, 'utc')->format('Y-m-d', true);
	  display current tstamp - JvitalsTime::getUser()->format('Y-m-d', true);
	*/
	public static function getUser($timestamp = 'now', $from_tz = 'server') {
		if ($timestamp == 'now') $timestamp = time();
		$date = JFactory::getDate($timestamp, self::getTz($from_tz));
		$date->setTimezone(self::getUserTz());
		return $date;
	}

	/*
	  save from user input - JvitalsTime::getUtc($timestamp, 'user')->toSql(true);
	  save current tstamp  - JvitalsTime::getUtc()->toSql();
	*/
	public static function getUtc($timestamp = 'now', $from_tz = 'server') {
		if ($timestamp == 'now') $timestamp = time();
		$date = JFactory::getDate($timestamp, self::getTz($from_tz));
		$date->setTimezone(self::getUtcTz());
		return $date;
	}

	public static function getServer($timestamp, $from_tz = 'server') {
		if ($timestamp == 'now') $timestamp = time();
		$date = JFactory::getDate($timestamp, self::getTz($from_tz));
		$date->setTimezone(self::getServerTz());
		return $date;
	}
	
	public static function getJoomla($timestamp, $from_tz = 'server') {
		if ($timestamp == 'now') $timestamp = time();
		$date = JFactory::getDate($timestamp, self::getTz($from_tz));
		$date->setTimezone(self::getJoomlaTz());
		return $date;
	}
	
	public static function getTz($type = 'server') {
		switch ($type) {
			case 'user' :
				$tz = self::getUserTz();
				break;
			case 'joomla' :
				$tz = self::getJoomlaTz();
				break;
			case 'server' :
				$tz = self::getServerTz();
				break;
			case 'utc' :
			default:
				$tz = self::getUtcTz();
		}
		return $tz;
	}
	
	public static function getUtcTz() {
		$srv_tz = new DateTimeZone("UTC");
		return $srv_tz;
	}
	
	public static function getServerTz() {
		$srv_tz = new DateTimeZone(date('e'));
		return $srv_tz;
	}
	
	public static function getJoomlaTz() {
		$config = JFactory::getConfig();
		$joomla_tz = new DateTimeZone($config->get('offset'));
		return $joomla_tz;
	}
	
	public static function getUserTz() {		
		$loggeduser = JFactory::getUser();
		if (!(int)$loggeduser->id) {
			$user_tz = self::getJoomlaTz();
		} else {
			$config = JFactory::getConfig();
			$user_tz = new DateTimeZone($loggeduser->getParam('timezone', $config->get('offset')));
		}
		return $user_tz;
	}
	
	public static function showJoomlaTimezone() {
		$config = JFactory::getConfig();
		return $config->get('offset');
	}
	
	public static function showUserTimezone() {
		$loggeduser = JFactory::getUser();
		if (!(int)$loggeduser->id) {
			return self::showJoomlaTimezone();
		} else {
			$config = JFactory::getConfig();			
			return $loggeduser->getParam('timezone', $config->get('offset'));
		}		
	}
	
	/**
	 * Function to convert a static time into a relative measurement
	 *
	 * @param   string  $date  The date to convert
	 * @param   string  $unit  The optional unit of measurement to return
	 *                         if the value of the diff is greater than one
	 * @param   string  $time  An optional time to compare to, defaults to now
	 *
	 * @return  string  The converted time string
	 */
	public static function relative($date, $unit = null, $time = null) {
		$date = self::getUser($date, 'utc')->format('Y-m-d H:i:s', true);
		
		if (is_null($time)) {
			// Get now
			$time = self::getUser()->format('Y-m-d H:i:s', true);
		}

		// Get the difference in seconds between now and the time
		$diff = strtotime($time) - strtotime($date);

		// Less than a minute
		if ($diff < 60) {
			return JText::_('JLIB_HTML_DATE_RELATIVE_LESSTHANAMINUTE');
		}

		// Round to minutes
		$diff = round($diff / 60);

		// 1 to 59 minutes 
		if ($diff < 60 || $unit == 'minute') {
			return JText::plural('JLIB_HTML_DATE_RELATIVE_MINUTES', $diff);
		}

		// Round to hours
		$diff = round($diff / 60);

		// 1 to 23 hours
		if ($diff < 24 || $unit == 'hour') {
			return JText::plural('JLIB_HTML_DATE_RELATIVE_HOURS', $diff);
		}

		// Round to days
		$diff = round($diff / 24);

		// 1 to 6 days
		if ($diff < 7 || $unit == 'day') {
			return JText::plural('JLIB_HTML_DATE_RELATIVE_DAYS', $diff);
		}

		// Round to weeks
		$diff = round($diff / 7);

		// 1 to 4 weeks
		if ($diff <= 4 || $unit == 'week') {
			return JText::plural('JLIB_HTML_DATE_RELATIVE_WEEKS', $diff);
		}

		// Over a month, return the absolute time
		return $date;
	}
}
