<?php
/**
 * @PHP       Version >= 8.0
 * @copyright ©2024 Maatify.dev
 * @author    Mohamed Abdulalim (megyptm) <mohamed@maatify.dev>
 * @since     2024-07-23 7:29 PM
 * @link      https://www.maatify.dev Maatify.com
 * @link      https://github.com/Maatify/CronTelegramBot  view project on GitHub
 * @Maatify   DB :: CronTelegramBot
 */

namespace Maatify\CronTelegramBot;

use App\Assist\AppFunctions;
use App\DB\DBS\DbConnector;
use Maatify\Json\Json;

abstract class CronTelegramBot extends DbConnector
{
    const TABLE_NAME                 = 'cron_telegram_bot';
    const TABLE_ALIAS                = '';
    const IDENTIFY_TABLE_ID_COL_NAME = 'cron_id';
    const RECIPIENT_TYPE             = 'customer';

    const LOGGER_TYPE     = self::TABLE_NAME;
    const LOGGER_SUB_TYPE = '';
    const COLS            =
        [
            self::IDENTIFY_TABLE_ID_COL_NAME => 1,
            'recipient_id'                   => 1,
            'recipient_type'                 => 0,
            'chat_id'                        => 1,
            'type_id'                        => 1,
            'message'                        => 0,
            'record_time'                    => 0,
            'status'                         => 1,
            'sent_time'                      => 0,
        ];

    protected string $tableName = self::TABLE_NAME;
    protected string $tableAlias = self::TABLE_ALIAS;
    protected string $identify_table_id_col_name = self::IDENTIFY_TABLE_ID_COL_NAME;
    protected array $cols = self::COLS;
    protected string $recipient_type = self::RECIPIENT_TYPE;
    const TYPE_OTP           = 1;
    const TYPE_TEMP_PASSWORD = 2;
    const TYPE_MESSAGE       = 3;
    const TYPE_ADMIN_MESSAGE = 4;

    const ALL_TYPES_NAME = [
        self::TYPE_OTP           => 'confirm code',
        self::TYPE_TEMP_PASSWORD => 'temp password',
        self::TYPE_MESSAGE       => 'message',
        self::TYPE_ADMIN_MESSAGE => 'administrator message',
    ];

    protected function AddCron(int $recipient_id, string $chat_id, string $message, int $type_id = 1): void
    {
        $this->Add([
            'recipient_id'      => $recipient_id,
            'recipient_type' => $this->recipient_type,
            'chat_id'        => (int)$chat_id,
            'type_id'        => $type_id,
            'message'        => $message,
            'record_time'    => AppFunctions::CurrentDateTime(),
            'status'         => 0,
            'sent_time'      => AppFunctions::DefaultDateTime(),
        ]);
    }

    public function Resend(): void
    {
        $this->ValidatePostedTableId();
        $this->Add([
            'recipient_id'      => (int)$this->current_row['recipient_id'],
            'recipient_type' => $this->current_row['recipient_type'],
            'chat_id'        => (int)$this->current_row['chat_id'],
            'type_id'        => (int)$this->current_row['type_id'],
            'message'        => $this->current_row['message'],
            'record_time'    => AppFunctions::CurrentDateTime(),
            'status'         => 0,
            'sent_time'      => AppFunctions::DefaultDateTime(),
        ]);
        $this->logger_keys = [$this->identify_table_id_col_name => $this->row_id];
        $log = $this->logger_keys;
        $log['change'] = 'Duplicate cron id: ' . $this->current_row[$this->identify_table_id_col_name];
        $changes[] = ['recipient', '', $this->current_row['recipient']];
        $changes[] = ['recipient_type', '', $this->current_row['recipient_type']];
        $changes[] = ['chat_id', '', $this->current_row['chat_id']];
        $changes[] = ['type_id', '', $this->current_row['type_id']];
        $this->Logger($log, $changes, $_GET['action']);
        Json::Success(line: $this->class_name . __LINE__);
    }
}