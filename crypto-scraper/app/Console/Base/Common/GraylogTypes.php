<?php

namespace App\Console\Base\Common;

abstract class GraylogTypes {
    const SUCCESS = "success";
    const WAITING = "waiting";
    const CONSUMED = "consumed";
    const PRODUCED = "produced";
    const STORED = "stored";
    const INFO = "info";
    const NO_DATA = "no_data";
    const ERROR = "error";
    const WARN = "warning";
}