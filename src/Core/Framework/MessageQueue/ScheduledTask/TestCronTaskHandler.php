<?php

declare(strict_types=1);

namespace Shopware\Core\Framework\MessageQueue\ScheduledTask;

use Psr\Log\LoggerInterface;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;

class TestCronTaskHandler extends ScheduledTaskHandler
{
    protected LoggerInterface $logger;

    public function __construct(
        EntityRepositoryInterface $scheduledTaskRepository,
        LoggerInterface $logger
    ) {
        parent::__construct($scheduledTaskRepository);
        $this->logger = $logger;
    }

    public static function getHandledMessages(): iterable
    {
        return [
            TestCronTask::class,
        ];
    }

    public function run(): void
    {
        $this->logger->info('TestCronTaskHandler::run()');
    }
}