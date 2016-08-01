<?php

echo '<pre>';

require_once __DIR__ . '/vendor/autoload.php';


error_reporting(E_ALL);
ini_set('display_errors', 1);


## EXAMPLE 1, with fluent interfaces

$event = new \Datadog\Send\Event();
$eventUrl = $event
    ->setEventTitle('test2 event')
    ->setEventMessage('message of the event')
    ->setEventTime(time())
    ->setEventPriority('low')
    ->setEventHost('test.host')
    ->setEventTags('tag1, tag2')
    ->setEventAlert_type('info')
    ->setEventSource_type_name('my apps')
    ->sendEvent();

echo $eventUrl; //url of the event



die();

$metric = new \Datadog\Send\Metric();
$metricCheck = $metric->setMetricName('test_metric_name')
    ->setMetricPoints('100, 98, 1470013851-45') // (points-timestamp, points, points, points-timestamp, points, points..., points-timestamp) ## order and length are irrelevant ## use a timestamp or not
    ->setMetricHost('test.my.host')
    ->setMetricTags('test:1, test:2, test:4')
    ->sendMetric();

echo $metricCheck; // boolean
die();



## EXAMPLE 2, with arrays

//$client = new \Datadog\Datadog();

/*
$test2 = array(
    array(20),
    array(13456789, 30),
    array(40)
);

$$clientInitTest = $client->sendMetricArr(
    array('test22.metric',
        'points'=>$test2,
        'host' => 'test19.my.host',
        'tags' => ['test19:1', 'test19:5', 'test19:7'] // array
    ));

print_r($$clientInitTest);


die();
*/


die();


/*
$eventArr = array(
    'title' => 'message title 1', //string (REQUIRED)
    'text' => 'message message 1', //string | markdown (REQUIRED)
    'date_happened' => time(),
    'priority' => 'low', //normal | low
    'host' => 'test host', // string
    'tags' => 'tag1, tag2', // comma separated list
    'alert_type' => 'info', //"error", "warning", "info" or "success"
    'source_type_name' => 'my apps' // nagios, hudson, jenkins, user, my apps, feed, chef, puppet, git, bitbucket, fabric, capistrano
);

$client->event($eventArr);
*/




echo '<hr></pre>sent';