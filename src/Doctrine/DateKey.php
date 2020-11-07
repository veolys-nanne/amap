<?php

namespace App\Doctrine;

class DateKey extends \DateTime
{
    public function __toString()
    {
        return $this->format('Y-m-d');
    }

    public static function fromDateTime(\DateTime $dateTime)
    {
        return new static($dateTime->format('Y-m-d'));
    }
}
