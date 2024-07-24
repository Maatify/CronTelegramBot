<?php
/**
 * Created by Maatify.dev
 * User: Maatify.dev
 * Date: 2024-07-23
 * Time: 9:04 PM
 * https://www.Maatify.dev
 */

namespace Maatify\CronTelegramBot;

use App\Assist\OpensslEncryption\OpenSslKeys;

abstract class CronTelegramBotRecord extends CronTelegramBot
{
    protected OpenSslKeys $encryption_class;
    public function RecordMessage(int $entity_id,string $chat_id, string $message): void
    {
        $this->AddCron($entity_id, $chat_id, $message, self::TYPE_MESSAGE);
    }

    public function RecordConfirmCode(int $entity_id,string $chat_id, string $code, ): void
    {

        $this->AddCron($entity_id, $chat_id, $this->encryption_class->Hash($code), self::TYPE_OTP);
    }

    public function RecordTempPassword(int $entity_id,string $chat_id, string $code): void
    {
        $this->AddCron($entity_id, $chat_id, $this->encryption_class->Hash($code), self::TYPE_TEMP_PASSWORD);
    }
}