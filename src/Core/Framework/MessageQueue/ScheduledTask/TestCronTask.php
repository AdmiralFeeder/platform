<?php

declare(strict_types=1);

namespace Shopware\Core\Framework\MessageQueue\ScheduledTask;

class TestCronTask extends ScheduledCronTask
{
    public static function getTaskName(): string
    {
        return 'test_cron_task';
    }

    public static function getCronTab(): string
    {
        return '*/5 18 * * *';
    }
}