<?php

namespace InoOicServer\Util;

use DateTime;
use DateInterval;
use DateTimeZone;


/**
 * Date/time utility functions.
 */
class DateTimeUtil
{


    /**
     * Creates a DateTime object based on the provided date/time string. If not set, "now" is used.
     * For more information about date/time string formats, see http://php.net/manual/en/datetime.formats.php.
     * 
     * @see http://php.net/manual/en/datetime.formats.php
     * 
     * @param string $dateTimeString
     * @return \DateTime
     */
    public function createDateTime($dateTimeString = null, DateTimeZone $dateTimeZone = null)
    {
        if (null === $dateTimeString) {
            $dateTimeString = 'now';
        }
        
        return new DateTime($dateTimeString, $dateTimeZone);
    }


    /**
     * Creates the corresponding expiration DateTime object based on the provided DateTime object and
     * the interval.
     * 
     * @see http://php.net/manual/en/dateinterval.construct.php
     * 
     * @param \DateTime $dateTimeFrom
     * @param string|integer|\DateInterval $interfal
     * @return DateTime
     */
    public function createExpireDateTime(DateTime $dateTimeFrom, $interval)
    {
        $expireDateTime = clone $dateTimeFrom;
        
        if (! $interval instanceof DateInterval) {
            if (($seconds = intval($interval)) !== 0) {
                $interval = sprintf("PT%dS", $seconds);
            }
            
            $interval = new DateInterval($interval);
        }
        
        return $expireDateTime->add($interval);
    }
}