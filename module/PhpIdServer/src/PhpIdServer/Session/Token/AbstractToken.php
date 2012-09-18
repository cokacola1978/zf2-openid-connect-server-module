<?php

namespace PhpIdServer\Session\Token;

use PhpIdServer\Entity\TimeDependentEntity;


/**
 * Abstract entity, subclass for entities dealing with tokens/codes and issue/expiration times.
 *
 */
class AbstractToken extends TimeDependentEntity
{


    /**
     * Returns true, if the expiration time is already in the past.
     * 
     * @return boolean
     */
    public function isExpired ()
    {
        return ((new \DateTime('now')) > $this->getExpirationTime());
    }


    public function setIssueTime ($timeString)
    {
        $this->setValue(static::FIELD_ISSUE_TIME, $this->_timeStringToDateObject($timeString));
    }


    public function setExpirationTime ($timeString)
    {
        $this->setValue(static::FIELD_EXPIRATION_TIME, $this->_timeStringToDateObject($timeString));
    }
}