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

use App\DB\Handler\ParentClassHandler;
use Maatify\Json\Json;
use Maatify\PostValidatorV2\ValidatorConstantsTypes;
use Maatify\PostValidatorV2\ValidatorConstantsValidators;

abstract class CronTelegramBotPortal extends ParentClassHandler
{
    public const IDENTIFY_TABLE_ID_COL_NAME = CronTelegramBot::IDENTIFY_TABLE_ID_COL_NAME;
    public const TABLE_NAME                 = CronTelegramBot::TABLE_NAME;
    public const TABLE_ALIAS                = CronTelegramBot::TABLE_ALIAS;
    public const LOGGER_TYPE                = CronTelegramBot::LOGGER_TYPE;
    public const LOGGER_SUB_TYPE            = CronTelegramBot::LOGGER_SUB_TYPE;
    public const COLS                       = CronTelegramBot::COLS;
    public const IMAGE_FOLDER               = self::TABLE_NAME;
    const RECIPIENT_TYPE = 'customer';
    protected string $identify_table_id_col_name = self::IDENTIFY_TABLE_ID_COL_NAME;
    protected string $tableName = self::TABLE_NAME;
    protected string $tableAlias = self::TABLE_ALIAS;
    protected string $logger_type = self::LOGGER_TYPE;
    protected string $logger_sub_type = self::LOGGER_SUB_TYPE;
    protected array $cols = self::COLS;
    protected string $image_folder = self::IMAGE_FOLDER;
    protected string $recipient_type = self::RECIPIENT_TYPE;


    // to use in list of AllPaginationThisTableFilter()
    protected array $inner_language_tables = [];

    // to use in list of source and destination rows with names
    protected string $inner_language_name_class = '';

    protected array $cols_to_filter = [
        [self::IDENTIFY_TABLE_ID_COL_NAME, ValidatorConstantsTypes::Int, ValidatorConstantsValidators::Optional],
        ['recipient_id', ValidatorConstantsTypes::Int, ValidatorConstantsValidators::Optional],
        ['recipient_type', ValidatorConstantsTypes::Col_Name, ValidatorConstantsValidators::Optional],
        ['chat_id', ValidatorConstantsTypes::Int, ValidatorConstantsValidators::Optional],
        ['type_id', ValidatorConstantsTypes::Int, ValidatorConstantsValidators::Optional],
        [ValidatorConstantsTypes::Status, ValidatorConstantsTypes::Status, ValidatorConstantsValidators::Optional],
    ];

    // to use in add if child classes no have language_id
    protected array $child_classes = [];

    // to use in add if child classes have language_id
    protected array $child_classe_languages = [];
    public function allPaginationThisTableFilter(string $order_with_asc_desc = ''): void
    {
        [$tables, $cols] = $this->HandleThisTableJoins();
        $where_to_add = '';
        $where_val_to_add = [];
        if (! empty($_POST['record_date_from'])) {
            $record_date_from = $this->postValidator->Optional('record_date_from', ValidatorConstantsTypes::Date, $this->class_name . __LINE__);
            $record_date_from .= ' 00:00:00';
            $where_to_add .= ' AND `record_time` >= ?';
            $where_val_to_add[] = $record_date_from;
        }
        if (! empty($_POST['record_date_to'])) {
            $record_date_to = $this->postValidator->Optional('record_date_to', ValidatorConstantsTypes::Date, $this->class_name . __LINE__);
            $record_date_to .= ' 23:59:59';
            $where_to_add .= ' AND `record_time` <= ?';
            $where_val_to_add[] = $record_date_to;
        }
        if (! empty($_POST['sent_date_from'])) {
            $sent_date_from = $this->postValidator->Optional('sent_date_from', ValidatorConstantsTypes::Date, $this->class_name . __LINE__);
            $sent_date_from .= ' 00:00:00';
            $where_to_add .= ' AND `sent_time` >= ?';
            $where_val_to_add[] = $sent_date_from;
        }
        if (! empty($_POST['sent_date_to'])) {
            $sent_date_to = $this->postValidator->Optional('sent_date_to', ValidatorConstantsTypes::Date, $this->class_name . __LINE__);
            $sent_date_to .= ' 23:59:59';
            $where_to_add .= ' AND `sent_time` <= ?';
            $where_val_to_add[] = $sent_date_to;
        }
        $this->pagination($tables, $cols, $where_to_add, $where_val_to_add);
    }

    public function cronTelegramBotInitialize(): void
    {
        Json::Success(CronTelegramBot::ALL_TYPES_NAME, line: $this->class_name . __LINE__);
    }

    protected function pagination(string $tables, string $cols, string $where_to_add, array $where_val_to_add): void
    {
        $result = $this->ArrayPaginationThisTableFilter($tables, $cols, $where_to_add, $where_val_to_add, " ORDER BY `$this->identify_table_id_col_name` ASC");
        if (! empty($result['data'])) {
            $result['data'] = array_map(function ($item) {
                $types = CronTelegramBot::ALL_TYPES_NAME;
                $item['type_name'] = $types[$item['type_id']];
                if(in_array($item['type_id'], [CronTelegramBot::TYPE_OTP, CronTelegramBot::TYPE_TEMP_PASSWORD])){
                    $item['message'] = "{Encrypted}";
                }

                return $item;
            }, $result['data']);
        }
        Json::Success(
            $result
        );
    }

    public function Initialize(): void
    {
        $all = array();
        foreach (CronTelegramBot::ALL_TYPES_NAME as $key => $type) {
            $all[] = [
                'type_id' => $key,
                'type_name' => $type,
            ];
        }
        Json::Success($all, line: $this->class_name . __LINE__);
    }
}