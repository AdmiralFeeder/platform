<?php declare(strict_types=1);

namespace Shopware\Core\Framework\MessageQueue\ScheduledTask;

use Cron\CronExpression;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\MessageQueue\Handler\AbstractMessageHandler;

abstract class ScheduledTaskHandler extends AbstractMessageHandler
{
    /**
     * @var EntityRepositoryInterface
     */
    protected $scheduledTaskRepository;

    public function __construct(EntityRepositoryInterface $scheduledTaskRepository)
    {
        $this->scheduledTaskRepository = $scheduledTaskRepository;
    }

    abstract public function run(): void;

    /**
     * @param ScheduledTask $task
     */
    public function handle($task): void
    {
        $taskId = $task->getTaskId();

        if ($taskId === null) {
            // run task independent of the schedule
            $this->run();

            return;
        }

        /** @var ScheduledTaskEntity|null $taskEntity */
        $taskEntity = $this->scheduledTaskRepository
            ->search(new Criteria([$taskId]), Context::createDefaultContext())
            ->get($taskId);

        if ($taskEntity === null || !$taskEntity->isExecutionAllowed()) {
            return;
        }

        $this->markTaskRunning($task);

        try {
            $this->run();
        } catch (\Throwable $e) {
            $this->markTaskFailed($task);

            throw $e;
        }

        $this->rescheduleTask($task, $taskEntity);
    }

    protected function markTaskRunning(ScheduledTask $task): void
    {
        $this->scheduledTaskRepository->update([
            [
                'id' => $task->getTaskId(),
                'status' => ScheduledTaskDefinition::STATUS_RUNNING,
            ],
        ], Context::createDefaultContext());
    }

    protected function markTaskFailed(ScheduledTask $task): void
    {
        $this->scheduledTaskRepository->update([
            [
                'id' => $task->getTaskId(),
                'status' => ScheduledTaskDefinition::STATUS_FAILED,
            ],
        ], Context::createDefaultContext());
    }

    protected function rescheduleTask(ScheduledTask $task, ScheduledTaskEntity $taskEntity): void
    {
        $now = new \DateTimeImmutable();

        if ($task instanceof ScheduledCronTask) {
            $nextExecutionTime = $this->calculateNextExecutionTime($taskEntity, $now);

            $this->scheduledTaskRepository->update([
                [
                    'id' => $task->getTaskId(),
                    'status' => ScheduledTaskDefinition::STATUS_SCHEDULED,
                    'lastExecutionTime' => $now,
                    'nextExecutionTime' => $nextExecutionTime,
                ],
            ], Context::createDefaultContext());

            return;
        }

        $nextExecutionTimeString = $taskEntity->getNextExecutionTime()->format(Defaults::STORAGE_DATE_TIME_FORMAT);
        $nextExecutionTime = new \DateTimeImmutable($nextExecutionTimeString);
        $newNextExecutionTime = $nextExecutionTime->modify(sprintf('+%d seconds', $taskEntity->getRunInterval()));

        if ($newNextExecutionTime < $now) {
            $newNextExecutionTime = $now;
        }

        $this->scheduledTaskRepository->update([
            [
                'id' => $task->getTaskId(),
                'status' => ScheduledTaskDefinition::STATUS_SCHEDULED,
                'lastExecutionTime' => $now,
                'nextExecutionTime' => $newNextExecutionTime,
            ],
        ], Context::createDefaultContext());
    }

    protected function calculateNextExecutionTime(ScheduledTaskEntity $taskEntity, \DateTimeImmutable $now): \DateTimeInterface
    {
        $cron = new CronExpression($taskEntity->getCrontab());
        $nextCronTime = $cron->getNextRunDate('now', 0, true, 'UTC');
        $now = $now->setTime((int) $now->format('h'), (int) $now->format('i'), 0, 0);
        if ($now->getTimestamp() === $nextCronTime->getTimestamp()) {
            $nextCronTime = $cron->getNextRunDate('now', 1, true, 'UTC');
        }

        return $nextCronTime;
    }
}
