<?php
declare(strict_types=1);

namespace App\Console\Base\Common;

trait ReturnCodes {
    protected int $RETURN_FAILED = 0;
    protected int $RETURN_SUCCESS = 1;
    protected int $RETURN_NEW_IDENTITY = 2;
    protected int $RETURN_NEW_ADDRESS = 3;
    protected int $RETURN_ALREADY_EXISTS = 4;
}