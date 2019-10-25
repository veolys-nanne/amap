<?php

namespace App\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

class DateTimeToStringTransformer implements DataTransformerInterface
{
    public function transform($datetime): string
    {
        if (null === $datetime) {
            return '';
        }

        return $datetime->format('d/m/Y');
    }

    public function reverseTransform($datetimeString): \DateTime
    {
        $datetime = \DateTime::createFromFormat('d/m/Y', $datetimeString);

        return $datetime;
    }
}
