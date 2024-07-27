<?php
/**
 * @PHP       Version >= 8.0
 * @copyright ©2024 Maatify.dev
 * @author    Mohamed Abdulalim (megyptm) <mohamed@maatify.dev>
 * @since     2024-07-23 9:08 PM
 * @link      https://www.maatify.dev Maatify.com
 * @link      https://github.com/Maatify/CronTelegramBot  view project on GitHub
 * @Maatify   DB :: CronTelegramBot
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
    private array $list;

    public function sentMarker(int $cron_id): void
    {
        $this->Edit([
            'status'     => 1,
            'sent_time'   => AppFunctions::CurrentDateTime(),
        ], "`$this->identify_table_id_col_name` = ? ", [$cron_id]);
    }

    protected function notSentList(): void
    {
        $this->list =  $this->RowsThisTable('*', '`status` = ? AND `recipient_type` = ? ', [0, $this->recipient_type]);
    }

    protected function notSentBySpecifiedRecipientAndChat(int $recipient_id, int $chat_id): void
    {
        $this->list =  $this->RowsThisTable('*',
            '`status` = ? AND `recipient_id` = ? AND `recipient_type` = ? AND `chat_id` = ?',
            [0, $recipient_id, $this->recipient_type, $chat_id]);
    }

    protected function Sender(): void
    {
        $this->notSentList();
        $this->send();
    }

    protected function senderByRecipientAndChat(int $recipient_id, int $chat_id): void
    {
        $this->notSentBySpecifiedRecipientAndChat($recipient_id, $chat_id);
        $this->send();
    }

    private function send(): void
    {
        if(!empty($this->list)) {
            try {
                $telegramBot = TelegramBotManager::obj($this->api_key)->Sender();
                foreach ($this->list as $item) {
                    $message = match ($item['type_id']) {
                        self::TYPE_OTP =>
                        $this->replaceTemplateCode(
                            $this->oTPText(),
                            $this->encryption_class->DeHashed($item['message'])
                        ),
                        self::TYPE_TEMP_PASSWORD =>
                        $this->replaceTemplateCode(
                            $this->tempPasswordText(),
                            $this->encryption_class->DeHashed($item['message'])
                        ),
                        default => $item['message'],
                    };
                    if ($sent = $telegramBot->SendMessage($item['chat_id'], $message)) {
                        if (! empty($sent['ok'])) {
                            $this->sentMarker($item[$this->identify_table_id_col_name]);
                        } else {
                            Logger::RecordLog($sent, 'telegram-bot-error');
                        }
                    }
                }
            } catch (\Exception $exception) {
                Logger::RecordLog($exception, 'telegram-bot');
            }
        }
    }



    protected function replaceTemplateCode(string $template, string $code): string
    {
        return str_replace("{replaced_code}", $code, $template);
    }

    public function oTPText(): string
    {

        return 'your OTP code is {replaced_code}. For your account security, don\'t share this code with anyone.';
    }

    public function tempPasswordText(): string
    {
        return 'your temp password is {replaced_code}. For your account security, don\'t share this password with anyone.';
    }

}