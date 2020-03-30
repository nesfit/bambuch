<?php
declare(strict_types=1);

namespace App\Models\Constants;

abstract class TaskConstants {
    const NONE = 'disabled';
    const MINUTE = 'every minute';
    const HOURLY = 'hourly';
    const DAILY = 'daily';
    const WEEKLY = 'weekly';
    
    const MIDNIGHT = '00:00';
}