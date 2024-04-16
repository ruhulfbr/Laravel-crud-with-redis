<?php

namespace App\Console\Commands\Consumers;
use Illuminate\Console\Command;
use Junges\Kafka\Contracts\MessageConsumer;use Junges\Kafka\Facades\Kafka;
use Junges\Kafka\Contracts\ConsumerMessage;

class MyTopicConsumer extends Command
{
    protected $signature = "consume:my-topic";

    protected $description = "Consume Kafka messages from 'my-topic'.";

    public function handle()
    {
        $consumer = Kafka::consumer(['topic'])
            ->withBrokers('localhost:8092')
            ->withAutoCommit()
            ->withHandler(function(ConsumerMessage $message, MessageConsumer $consumer) {
                print_r($message->getBody());
            })
            ->build();

        $consumer->consume();
    }
}
