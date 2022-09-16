<?php

declare(strict_types=1);

namespace Shopware\Core\Framework\MessageQueue\ScheduledTask;

use http\Exception\RuntimeException;
use Shopware\Core\Framework\Struct\ArrayStruct;

class ScheduledTaskCronTabParser
{
    public const VALID_VALUES = ['*', ',', '-', '/' ];

    public function parseCronTab(string $cronTab): array
    {
        $explodedCronTab = explode(' ', $cronTab);

        return [
            'minute' => $explodedCronTab[0] ?? null,
            'hour' => $explodedCronTab[1] ?? null,
            'day_month' => $explodedCronTab[2] ?? null,
            'month' => $explodedCronTab[3] ?? null,
            'day_week' => $explodedCronTab[4] ?? null,
        ];

    }

    public function cronTabIsValid(string $cronTab): bool
    {
        $chars = explode('', $cronTab);

        foreach($chars as $char) {
            if (!in_array($char, self::VALID_VALUES) && ((int) preg_match('/[0-9]+/')) === 0) {
                return false;
            }
        }

        $parsedCronTab = $this->parseCronTab($cronTab);

        /**
         * Validate count of entries
         */
        foreach ($parsedCronTab as $parsedStruct) {
            if(!$parsedStruct) {
                return false;
            }
        }

        if ($parsedStruct['minute'] === '*') {
            //TODO
        }

        if(strpos($parsedStruct['minute'], ',')) {
            $and = explode(',', $parsedStruct['minute']);
//            TODO: call everything else
        }

        if(strpos($parsedStruct['minute'], '*/')) {
            $tmp = str_replace('*/', $parsedStruct['minute']);

            // TODO:
        }

        if(strpos($parsedStruct['minute'], '-')) {
            $tmp = $parsedStruct['minute'];

            if(strpos($parsedStruct['minute'], '/')) {
                $tmp = explode('/', $parsedStruct['minute']);
            }

            $between = explode('-', $tmp);
        }



// normal cases
//4,5
//5-6
//4,5-6
//*
//*/4
//*/4,5
//5-6/*
///*
//*/4,5-6


//        $parsedCronTab['minute'] -> 0-59
//        $parsedCronTab['hour'] -> 0-11
//        $parsedCronTab['day'] -> 0-30
//        $parsedCronTab['month'] -> 0-11
//        $parsedCronTab['month'] -> 0-11

        return true;
    }

    private function validateEntry(string $entry, array $allowedTypes): bool
    {

    }
}
