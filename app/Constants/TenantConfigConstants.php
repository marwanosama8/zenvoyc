<?php

namespace App\Constants;

class TenantConfigConstants
{
    public const OVERRIDABLE_CONFIGS = [  // correspond to laravel config keys
        'mail.mailers.smtp.host',
        'mail.mailers.smtp.port',
        'mail.mailers.smtp.username',
        'mail.mailers.smtp.password',
    ];

    
    public const MAIL_CONFIGS = [
        'mail.mailers.smtp.host',
        'mail.mailers.smtp.port',
        'mail.mailers.smtp.username',
        'mail.mailers.smtp.password',
    ];


}
