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
            self::ENTITY_COLUMN_NAME => $entity_column_name,
            'chat_id'                => $chat_id,
            'type_id'                => $type_id,
            'message'                => $message,
            'record_time'            => AppFunctions::CurrentDateTime(),
            'status'                 => 0,
            'sent_time'              => AppFunctions::DefaultDateTime(),
        ]);
    }

    public function Remove(): void
    {
        $this->ValidatePostedTableId();
        $note = $this->postValidator->Optional('note', ValidatorConstantsTypes::Description, $this->class_name . __LINE__);
        $this->Delete("`$this->identify_table_id_col_name` = ? ", [$this->row_id]);
        $this->logger_keys = [$this->identify_table_id_col_name => $this->row_id];
        $logger[$this->identify_table_id_col_name] = $this->row_id;
        $changes = array();
        foreach ($this->current_row as $key => $value) {
            $logger_change = $logger[$key] = $value;

            $changes[] = [
                $key,
                $logger_change,
                '',
            ];
        }
        if (! empty($note)) {
            $logger['reason'] = $note;

            $changes[] = [
                'reason',
                '',
                $note,
            ];
        }
        $this->Logger($logger, $changes, 'Remove');

        Json::Success(line: $this->class_name . __LINE__);
    }
}