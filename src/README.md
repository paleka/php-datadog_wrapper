# PHP DataDog API wrapper

After using it every day at work, this was we ended up with:


## Usage

### Event
Example #1:
``` php
$event = new \Datadog\Send\Event();


$eventUrl = $client
            ->setEventTitle('test title of the event') //string (REQUIRED)
            ->setEventMessage('message of the EVENT') //string | markdown (REQUIRED)
            ->setEventTime(time())
            ->setEventPriority('low') //normal | low
            ->setEventHost('test.host') // string
            ->setEventTags('tag1, tag2') // comma separated list
            ->setEventAlert_type('info') //"error", "warning", "info" or "success"
            ->setEventSource_type_name('my apps') // nagios, hudson, jenkins, user, my apps, feed, chef, puppet, git, bitbucket, fabric, capistrano
            ->sendEvent();

echo $eventUrl; //url of the event
```

Example #2:
``` php
$client = new Datadog();

$eventArr = array(
    'title' => TITLE, //string (REQUIRED)
    'text' => TEXT, //string | markdown (REQUIRED)
    'date_happened' => timestamp,
    'priority' => PRIORITY, //normal | low
    'host' => HOST, // string
    'tags' => TAGS, // comma separated list
    'alert_type' => ALERT_TYPE, //"error", "warning", "info" or "success"
    'source_type_name' => EVENT TYPE // nagios, hudson, jenkins, user, my apps, feed, chef, puppet, git, bitbucket, fabric, capistrano
);

$eventUrl = $client->eventArr($eventArr); //url of the event
```

### Metric
Example #1:
``` php
$metric = new \Datadog\Send\Metric();
$metricCheck = $metric->setMetricName('test_metric_name')
    ->setMetricPoints('100, 98, 1470013851-45') // (points-timestamp, points, points, points-timestamp, points, points..., points-timestamp) ## order and length are irrelevant ## use a "timestamp-points" format or not
    ->setMetricHost('test.my.host')
    ->setMetricTags('test:1, test:2, test:4')
    ->sendMetric();

echo $metricCheck; // boolean
```
Example #2:
``` php
$test1 = array(
        array(20), // datapoint
        array(13456789, 30), // timestamp, datapoint
        array(40) // datapoint
    );

    $client->sendMetric(
    array('test_short.metric',
            'points'=>$test1,
            'host' => 'test_short.my.host',
            'tags' => ['test_short:1', 'test_short:5', 'test_short:7'] // array of strings
    ));
```

####todo
 - send arrays of metrics in one request