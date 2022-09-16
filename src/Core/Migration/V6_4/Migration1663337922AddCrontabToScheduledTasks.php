<?php declare(strict_types=1);

namespace Shopware\Core\Migration\V6_4;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1663337922AddCrontabToScheduledTasks extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1663337922;
    }

    public function update(Connection $connection): void
    {
        $connection->executeStatement('ALTER TABLE `scheduled_task` ADD `crontab` VARCHAR(255);');
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}
