<?php
/**
 * @PHP       Version >= 8.0
 * @copyright ©2024 Maatify.dev
 * @author    Mohamed Abdulalim (megyptm) <mohamed@maatify.dev>
 * @since     2024-07-23 9:04 PM
 * @link      https://www.maatify.dev Maatify.com
 * @link      https://github.com/Maatify/CronTelegramBot  view project on GitHub
 * @Maatify   DB :: CronTelegramBot
 */

namespace Maatify\CronTelegramBot;

use App\Assist\OpensslEncryption\OpenSslKeys;

abstract class CronTelegramBotRecord extends CronTelegramBot
{
    protected OpenSslKeys $encryption_class;
    public function RecordMessage(int $recipient_id,string $chat_id, string $message): void
    {
        $this->AddCron($recipient_id, $chat_id, $message, self::TYPE_MESSAGE);
    }

    public function RecordConfirmCode(int $recipient_id,string $chat_id, string $code, ): void
    {

        $this->AddCron($recipient_id, $chat_id, $this->encryption_class->Hash($code), self::TYPE_OTP);
    }

    public function RecordTempPassword(int $recipient_id,string $chat_id, string $code): void
    {
        $this->AddCron($recipient_id, $chat_id, $this->encryption_class->Hash($code), self::TYPE_TEMP_PASSWORD);
    }
}