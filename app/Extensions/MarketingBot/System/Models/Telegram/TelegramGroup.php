<?php

namespace App\Extensions\MarketingBot\System\Models\Telegram;

use Illuminate\Database\Eloquent\Model;

class TelegramGroup extends Model
{
    protected $table = 'ext_telegram_groups';

    protected $fillable = [
        'user_id',
        'name',
        'group_id',
        'bot_id',
        'type',
        'group_type',
        'supergroup_subscriber_id',
        'status',
    ];
}
