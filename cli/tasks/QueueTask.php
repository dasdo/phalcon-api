<?php

namespace Gewaer\Cli\Tasks;

use Phalcon\Cli\Task as PhTask;
use Gewaer\Models\UserLinkedSources;
use Gewaer\Models\Users;
use Throwable;

/**
 * CLI To send push ontification and pusher msg
 *
 * @package Gewaer\Cli\Tasks
 *
 * @property Config $config
 * @property \Pusher\Pusher $pusher
 * @property \Monolog\Logger $log
 */
class QueueTask extends PhTask
{
    public function notificationAction()
    {

         /**
     * Every handler needs the below code
     */

        /**
         * The job itself
         */
        $jobArray = [
        'id' => $job_id++,
        'notification' => 'hello you need to pay your account',
        'sleep_period' => rand(0, 3)
    ];

        /**
         * Need to convert it to rabbitmq msg
         */
        $msg = new \PhpAmqpLib\Message\AMQPMessage(
            json_encode($jobArray, JSON_UNESCAPED_SLASHES),
            ['delivery_mode' => 2] // make message persistent
    );

        /**
         * Actual way to send jobs to queue
         */
        $channel->basic_publish($msg, '', RABBITMQ_QUEUE_NAME);


        ///////////////////////////////////////////////////////////////////////

    
        $channel = $this->queue->channel();

        // Create the queue if it doesnt already exist.
        $channel->queue_declare(
            $queue = "notifications",
            $passive = false,
            $durable = true,
            $exclusive = false,
            $auto_delete = false,
            $nowait = false,
            $arguments = null,
            $ticket = null
        );

        echo ' [*] Waiting for notifications. To exit press CTRL+C', "\n";

        $callback = function ($msg) {

            /**
             * Assign  message body as an assoc array to job
             */
            $job = json_decode($msg->body, $assocForm = true);

            /**
             * Custom actions here on jobs
             */
            echo($job['notification']);
            sleep($job['sleep_period']);

            /**
             * Log the delivery info
             */
            $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
        };

        $channel->basic_qos(null, 1, null);

        $channel->basic_consume(
            $queue = "notifications",
            $consumer_tag = '',
            $no_local = false,
            $no_ack = false,
            $exclusive = false,
            $nowait = false,
            $callback
        );

        while (count($channel->callbacks)) {
            $channel->wait();
        }

        $channel->close();
        $connection->close();
    }
}
