<?php
/**
 * Created by Maatify.dev
 * User: Maatify.dev
 * Date: 2024-07-23
 * Time: 9:08 PM
 * https://www.Maatify.dev
 */

namespace Maatify\CronTelegramBot;

use App\Assist\AppFunctions;
use App\Assist\Encryptions\EnvEncryption;
use App\Assist\OpensslEncryption\OpenSslKeys;
use Maatify\Logger\Logger;
use Maatify\TelegramBot\TelegramBotManager;

abstract class CronTelegramBotSender extends CronTelegramBot
{
    protected OpenSslKeys $encryption_class;

    protected string $api_key;

    public function SentMarker(int $cron_id): void
    {
        $this->Edit([
            'status'     => 1,
            'sent_time'   => AppFunctions::CurrentDateTime(),
        ], "`$this->identify_table_id_col_name` = ? ", [$cron_id]);
    }

    protected function NotSent(): array
    {
        return $this->RowsThisTable('*', '`status` = ? ', [0]);
    }

    protected function Sender(): void
    {
        if ($all = $this->NotSent()) {
            try {
                $telegramBot = TelegramBotManager::obj($this->api_key)->Sender();
                foreach ($all as $item) {
                    $message = match ($item['type_id']) {
                        self::TYPE_OTP => AppFunctions::OTPText() . $this->encryption_class->DeHashed($item['message']),
                        self::TYPE_TEMP_PASSWORD => AppFunctions::TempPasswordText() . $this->encryption_class->DeHashed($item['message']),
                        default => $item['message'],
                    };
                    if ($telegramBot->SendMessage($item['chat_id'], $message)) {
                        $this->SentMarker($item[$this->identify_table_id_col_name]);
                    }
                }
            }catch (\Exception $exception){
                Logger::RecordLog($exception, 'telegram-bot');
            }
        }
    }

}