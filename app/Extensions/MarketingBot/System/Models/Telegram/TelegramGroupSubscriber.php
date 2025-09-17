<?php

namespace App\Extensions\MarketingBot\System\Models\Telegram;

use Illuminate\Database\Eloquent\Model;

class TelegramGroupSubscriber extends Model
{
    protected $table = 'ext_telegram_group_subscribers';

    protected $fillable = [
        'user_id',
        'name',
        'username',
        'avatar',
        'phone',
        'client_id',
        'group_chat_id',
        'group_subscriber_id',
        'group_id',
        'unique_id',
        'is_left_group',
        'type',
        'status',
        'is_blacklist',
        'is_bot',
        'is_admin',
        'scopes',
    ];
}
