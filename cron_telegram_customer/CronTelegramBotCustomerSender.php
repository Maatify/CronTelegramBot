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

namespace Maatify\CronTelegramBotCustomer;

use App\Assist\Encryptions\CronTelegramBotCustomerEncryption;
use App\Assist\Encryptions\EnvEncryption;
use App\Assist\OpensslEncryption\OpenSslKeys;
use Maatify\CronTelegramBot\CronTelegramBotSender;
use Maatify\QueueManager\QueueManager;

class CronTelegramBotCustomerSender extends CronTelegramBotSender
{
    const TABLE_NAME = 'cron_telegram_bot_customer';
    const ENTITY_COLUMN_NAME = 'ct_id';
    protected string $tableName = self::TABLE_NAME;
    protected string $entityColumnName = self::ENTITY_COLUMN_NAME;
    protected OpenSslKeys $encryption_class;
    protected string $api_key;
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
        $this->encryption_class = new CronTelegramBotCustomerEncryption();
        if(!empty($_ENV['IS_TELEGRAM_CUSTOMER_ACTIVATE']) && !empty($_ENV['TELEGRAM_API_KEY_CUSTOMER'])) {
            $this->api_key = (new EnvEncryption())->DeHashed($_ENV['TELEGRAM_API_KEY_CUSTOMER']);
        }
    }

    public function CronSend(): void
    {
        if(!empty($_ENV['IS_TELEGRAM_CUSTOMER_ACTIVATE']) && !empty($_ENV['TELEGRAM_API_KEY_CUSTOMER'])) {
            QueueManager::obj()->TelegramBotCustomer();
            parent::Sender();
        }
    }
}