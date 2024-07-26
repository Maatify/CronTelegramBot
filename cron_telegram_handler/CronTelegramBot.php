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
use Maatify\PostValidatorV2\ValidatorConstantsTypes;

abstract class CronTelegramBot extends DbConnector
{
    const TABLE_NAME                 = 'cron_telegram';
    const TABLE_ALIAS                = '';
    const IDENTIFY_TABLE_ID_COL_NAME = 'cron_id';
    const ENTITY_COLUMN_NAME         = 'ct_id';
    protected string $entityColumnName;
    const LOGGER_TYPE                = self::TABLE_NAME;
    const LOGGER_SUB_TYPE            = '';
    const COLS                       =
        [
            self::IDENTIFY_TABLE_ID_COL_NAME => 1,
            self::ENTITY_COLUMN_NAME         => 1,
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

    protected function AddCron(int $entity_column_name, string $chat_id, string $message, int $type_id = 1): void
    {
        $this->Add([
            $this->entityColumnName => $entity_column_name,
            'chat_id'                => $chat_id,
            'type_id'                => $type_id,
            'message'                => $message,
            'record_time'            => AppFunctions::CurrentDateTime(),
            'status'                 => 0,
            'sent_time'              => AppFunctions::DefaultDateTime(),
        ]);
    }

    public function Resend(): void
    {
        $this->ValidatePostedTableId();
        $this->AddCron(
            $this->current_row[$this->entityColumnName ],
            $this->current_row['chat_id'],
            $this->current_row['message'],
            $this->current_row['type_id'],

        );
        $this->logger_keys = [$this->identify_table_id_col_name => $this->row_id];
        $log = $this->logger_keys;
        $log['change'] = 'Duplicate cron id: ' . $this->current_row[$this->identify_table_id_col_name];
        $changes[] = [$this->entityColumnName , '', $this->current_row[$this->entityColumnName ]];
        $changes[] = ['chat_id', '', $this->current_row['chat_id']];
        $changes[] = ['message', '', $this->current_row['message']];
        $changes[] = ['type_id', '', $this->current_row['type_id']];
        $this->Logger($log, $changes, $_GET['action']);
        Json::Success(line: $this->class_name . __LINE__);
    }
}