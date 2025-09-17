<?php

namespace App\Extensions\MarketingBot\System\Models\Whatsapp;

use Illuminate\Database\Eloquent\Model;

class WhatsappChannel extends Model
{
    protected $table = 'ext_whatsapp_channels';

    protected $fillable = [
        'user_id',
        'whatsapp_sid',
        'whatsapp_token',
        'whatsapp_phone',
        'whatsapp_sandbox_phone',
        'whatsapp_environment',
    ];

    public function isSandbox(): bool
    {
        return $this->whatsapp_environment === 'sandbox';
    }
}
