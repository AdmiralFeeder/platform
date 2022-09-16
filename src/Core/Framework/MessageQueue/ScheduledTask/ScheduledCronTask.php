<?php declare(strict_types=1);

namespace Shopware\Core\Framework\MessageQueue\ScheduledTask;

abstract class ScheduledCronTask extends ScheduledTask
{
    /**
     * @return string the crontab string. Default '* * * * *'
     */
    abstract public static function getCronTab(): string;

    public static function getDefaultInterval(): int
    {
        return 0;
    }
}
