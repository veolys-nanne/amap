<?php

namespace App\Doctrine;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\DateType;

class DateKeyType extends DateType
{
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        $value = parent::convertToPHPValue($value, $platform);
        if (null !== $value) {
            $value = DateKey::fromDateTime($value);
        }

        return $value;
    }

    public function getName()
    {
        return 'DateKey';
    }
}
