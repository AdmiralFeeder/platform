<?php

declare(strict_types=1);

namespace Shopware\Core\Framework\MessageQueue\ScheduledTask;

use Psr\Log\LoggerInterface;

class TestCronTask extends ScheduledCronTask
{
    protected LoggerInterface $logger;

    public static function getTaskName(): string
    {
        return 'test_cron_task';
    }

    public static function getCronTab(): string
    {
        return '* * * * *';
    }
}