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
        return $this->RowsThisTable('*', '`status` = ? AND `recipient_type` = ? ', [0, $this->recipient_type]);
    }

    protected function Sender(): void
    {
        if ($all = $this->NotSent()) {
            try {
                $telegramBot = TelegramBotManager::obj($this->api_key)->Sender();
                foreach ($all as $item) {
                    $message = match ($item['type_id']) {
                        self::TYPE_OTP =>
                            $this->ReplaceTemplateCode(
                                $this->OTPText(),
                                $this->encryption_class->DeHashed($item['message'])
                            ),
                        self::TYPE_TEMP_PASSWORD =>
                        $this->ReplaceTemplateCode(
                            $this->TempPasswordText(),
                            $this->encryption_class->DeHashed($item['message'])
                        ),
                        default => $item['message'],
                    };
                    if ($sent = $telegramBot->SendMessage($item['chat_id'], $message)) {
                        if(!empty($sent['ok'])) {
                            $this->SentMarker($item[$this->identify_table_id_col_name]);
                        }else{
                            Logger::RecordLog($sent, 'telegram-bot-error');
                        }

                    }
                }
            }catch (\Exception $exception){
                Logger::RecordLog($exception, 'telegram-bot');
            }
        }
    }



    protected function ReplaceTemplateCode(string $template, string $code): string
    {
        return str_replace("{replaced_code}", $code, $template);
    }

    public function OTPText(): string
    {

        return 'your OTP code is {replaced_code}. For your account security, don\'t share this code with anyone.';
    }

    public function TempPasswordText(): string
    {
        return 'your temp password is {replaced_code}. For your account security, don\'t share this password with anyone.';
    }

}