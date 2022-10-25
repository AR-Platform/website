<?php

require_once(__DIR__ . "/../../../config/config.php");
require_once(ROOT_DIR . "/src/vendor/autoload.php");

use PhpMqtt\Client\MqttClient;

/**
 * Sends the enforce-message for the given course and content.
 *
 * If the contentID is null a message will be sent that stops the active enforcing.
 *
 * @param int $courseID The ID of the course.
 * @param int|null $contentID The ID of the content or null.
 * @return void No return value.
 */
function sendEnforceMessage(int $courseID, int|null $contentID)
{
    sendMessage("server/" . $courseID, $contentID == null ? "" : "enforce:$contentID", true);
}

/**
 * Sends a MQTT message to a given topic and allows for the retain flag to be set.
 *
 * @param string $topic The desired topic.
 * @param string $message The message to send.
 * @param bool $retain If set to True the message will be saved and send to every new subscriber until removed.
 * @return void No return value.
 */
function sendMessage(string $topic, string $message, bool $retain)
{
    try {
        $client = new MqttClient(MQTT_SERVER);
        $client->connect();
        $client->publish(MQTT_TOPIC . "/" . $topic, $message, 1, $retain);
        $client->loop(true, true);
        $client->disconnect();
    } catch (Exception $e) {
        echo $e;
    }
}

/**
 * Pings all course members of the specified course and counts the amount of responses.
 *
 * @param int $courseID The ID of the course to ping.
 * @param int $time The specified wait time before returning the amount.
 * @return int The amount of responses within the specified time frame.
 */
function pingCourseMember(int $courseID, int $time): int
{
    set_time_limit($time + 5);
    $uuids = array();
    try {
        $client = new MqttClient(MQTT_SERVER);

        $client->registerLoopEventHandler(function ($mqtt, $elapsedTime) use (&$time) {
            if ($elapsedTime >= $time) {
                $mqtt->interrupt();
            }
        });

        $client->connect();
        $client->publish(MQTT_TOPIC . "/server/" . $courseID, "ping", 1, false);
        $client->subscribe(MQTT_TOPIC . "/client/" . $courseID, function ($topic, $message) use (&$uuids, &$match) {
            $uuids[] = $message;
        });
        $client->loop(true, true, 1);
        $client->disconnect();
    } catch (Exception) {
    }
    return count(array_unique($uuids));
}
