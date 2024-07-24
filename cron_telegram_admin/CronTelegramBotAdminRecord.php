<?php
/**
 * @PHP       Version >= 8.0
 * @copyright ©2024 Maatify.dev
 * @author    Mohamed Abdulalim (megyptm) <mohamed@maatify.dev>
 * @since     2024-07-24 8:36 AM
 * @link      https://www.maatify.dev Maatify.com
 * @link      https://github.com/Maatify/CronTelegramBot  view project on GitHub
 * @Maatify   DB :: CronTelegramBot
 */

namespace Maatify\CronTelegramBotAdmin;

use App\Assist\Encryptions\CronTelegramBotAdminEncryption;
use App\Assist\OpensslEncryption\OpenSslKeys;
use Maatify\CronTelegramBot\CronTelegramBotRecord;

class CronTelegramBotAdminRecord extends CronTelegramBotRecord
{
    const TABLE_NAME = 'cron_telegram_bot_admin';
    const ENTITY_COLUMN_NAME = 'admin_id';
    protected string $tableName = self::TABLE_NAME;
    protected string $entityColumnName = self::ENTITY_COLUMN_NAME;
    protected OpenSslKeys $encryption_class;
    private static self $instance;

    public static function obj(): self
    {
        if (empty(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function __construct()
    {
        parent::__construct();
        $this->encryption_class = new CronTelegramBotAdminEncryption();
    }
}