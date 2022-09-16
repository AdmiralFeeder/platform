<?php

declare(strict_types=1);

namespace Shopware\Core\Framework\MessageQueue\ScheduledTask;

use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;

abstract class CronTaskHandler extends ScheduledTaskHandler
{
    /**
     *  @var ScheduledTaskCronTabParser
     */
    protected $scheduledTaskCronTabParser;


    public function __construct(
        EntityRepositoryInterface $scheduledTaskRepository,
        ScheduledTaskCronTabParser $scheduledTaskCronTabParser
    ) {
        parent::__construct($scheduledTaskRepository);
        $this->scheduledTaskCronTabParser = $scheduledTaskCronTabParser;
    }

    protected function rescheduleTask(ScheduledTask $task, ScheduledTaskEntity $taskEntity): void
    {
        $now = new \DateTimeImmutable();

        $newNextExecutionTime = $this->calculateNextExecutionFromCrontab($taskEntity::getCronTab(), $now);

        $this->scheduledTaskRepository->update([
            [
                'id' => $task->getTaskId(),
                'status' => ScheduledTaskDefinition::STATUS_SCHEDULED,
                'lastExecutionTime' => $now,
                'nextExecutionTime' => $newNextExecutionTime,
            ],
        ], Context::createDefaultContext());
    }

    protected function calculateNextExecutionFromCrontab(string $cronTab, \DateTimeImmutable $now): \DateTimeImmutable
    {
        $timeStruct = $this->scheduledTaskCronTabParser->parseCronTab($cronTab);


        $time = new \DateTimeImmutable();
        return $time;
    }
}