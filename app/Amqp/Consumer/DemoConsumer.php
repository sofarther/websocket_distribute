<?php

declare(strict_types=1);

namespace App\Amqp\Consumer;

use Hyperf\Amqp\Result;
use Hyperf\Amqp\Annotation\Consumer;
use Hyperf\Amqp\Message\ConsumerMessage;
use PhpAmqpLib\Message\AMQPMessage;

/**
 * (exchange="hyperf", routingKey="hyperf", queue="hyperf", name ="DemoConsumer", nums=1)
 */
class DemoConsumer extends ConsumerMessage
{
    protected $exchange = 'hyperf';
    protected $routingKey = 'hyperf';
    protected $queue="hyperf";

    public function consumeMessage($data, AMQPMessage $message): string
    {

        var_dump($data);
        return Result::ACK;
    }
}
