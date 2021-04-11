<?php

declare(strict_types=1);

namespace App\Constants;

use Hyperf\Constants\AbstractConstants;
use Hyperf\Constants\Annotation\Constants;


class RedisKey
{

    const SERVER_HEARTBEAT_TIME = 'server:heartbeat:timestamp:';
    const SERVER_LOST_HEARTBEAT_COUNT = 'server:heartbeat:lost_count:';
    const SERVER_SET = 'server_set';
    const SERVER_CONSISTENT_HASH = 'server_consistent_hash';
}
