---
title: Add crontab configuration to Scheduled Tasks
issue: 
author: Christopher Steinke
author_email: c.steinke@basecom.de
author_github: https://github.com/AdmiralFeeder
---

# Core

Added abstract class `Shopware\Core\Framework\MessageQueue\ScheduledTask\ScheduledCronTask.php` to extend ScheduledTasks for crontab configuration.
Added migration to add `crontab` column to `scheduled_task` table.
Changed `Shopware\Core\Framework\MessageQueue\ScheduledTask\ScheduledTaskHandler` to use either `crontab` column or `run_interval` for scheduling.
