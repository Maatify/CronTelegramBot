<?php
/**
 * @PHP       Version >= 8.0
 * @copyright ©2024 Maatify.dev
 * @author    Mohamed Abdulalim (megyptm) <mohamed@maatify.dev>
 * @since     2024-07-24 9:16 AM
 * @link      https://www.maatify.dev Maatify.com
 * @link      https://github.com/Maatify/CronTelegramBot  view project on GitHub
 * @Maatify   DB :: CronTelegramBot
 */

namespace Maatify\CronTelegramBotCustomer;

use Maatify\CronTelegramBot\CronTelegramBotPortal;

class CronTelegramBotCustomerPortal extends CronTelegramBotPortal
{
    const TABLE_NAME = 'cron_telegram_bot_customer';
    const ENTITY_COLUMN_NAME = 'ct_id';
    private static self $instance;

    public static function obj(): self
    {
        if (empty(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }
}