<?php

namespace Datadog\Send;

use Datadog\Datadog;

class Metric extends Datadog
{

    protected $metricName;
    protected $metricPoints; //two dimensional arrays
    protected $metricHost;
    protected $metricTags;


    public function setMetricName($metricName)
    {
        $this->metricName = $metricName;
        return $this;
    }

    public function setMetricPoints(...$metricPoints)
    {
        $this->metricPoints = $metricPoints;
        return $this;
    }

    public function setMetricHost($metricHost)
    {
        $this->metricHost = $metricHost;
        return $this;
    }

    public function setMetricTags($metricTags)
    {
        $this->metricTags = $metricTags;
        return $this;
    }


    public function sendMetric()
    {

        if (!$this->metricName || strlen($this->metricName) < 3) {
            throw new \InvalidArgumentException("Metric name shouldn't be empty or less than 3 characters");
        }
        // todo @robert check as an array
        if (!$this->metricPoints) {
            throw new \InvalidArgumentException("Metric points shouldn't be empty");
        }

        //## DATAPOINTS - remove space if any and assign string to var
        $metricPointsArr = str_replace(' ', '', $this->metricPoints);
        $metricPoints = $metricPointsArr[0];
        $pointsArr = explode(',', $metricPoints);

        //check if the datapoints have a timestamp, otherwise sum them and assign current timestamp
        $datapoints = [];
        $pointsSumNoTimestamp = 0;

        foreach ($pointsArr as $point) {
            $hasDash = strpos($point, '-');
            if ($hasDash != false) {
                $pointsWithTimestamp = array_map('intval', explode('-', $point));
                $datapoints[] = $pointsWithTimestamp;
            } else {
                //datapoints with no timestamp will be summed, otherwise datadog API will overwrite it with the last value
                $pointsSumNoTimestamp += $point;

            }
        }
        $datapoints[] = [time(), $pointsSumNoTimestamp];

        //## TAGS - remove space if any and assign string to var
        $metricTagsArr = str_replace(' ', '', $this->metricTags);
        $tagsArr = explode(',', $metricTagsArr);

        $metricArr = [
            'metric' => $this->metricName,
            'points' => $datapoints,
            'host' => $this->metricHost,
            'tags' => $tagsArr // array
        ];


        $series['series'][0] = $metricArr;
        $url = $this->series_endpoint . $this->api_key;

        $result = $this->send(
            $url,
            $series
        );

        return $result;

    }

    /*

    send single metric with one or multiple values

    EXAMPLE

    $test2 = array(
        array(20),
        array(13456789, 30),
        array(40)
    );

    $client->sendMetric(
    array('test_short.metric',
            'points'=>$test2,
            'host' => 'test_short.my.host',
            'tags' => ['test_short:1', 'test_short:5', 'test_short:7'] // array
    ));

    */

    public function sendMetricArr(array $metric)
    {
        //so the order of elements is not important
        foreach ($metric as $key => $item) {
            if ($key == 0 && !is_string($key)) {
                //adding on top, order matters
                $metric = array('metric' => $item) + $metric;
                unset($metric[$key]);

            } elseif (is_array($item)) {
                foreach ($item as $point_key => $point) {
                    if (count($point) == 1) {
                        array_unshift($point, time());
                    }
                    $metric[$key][$point_key] = $point;
                }
            }
        }

        $series['series'][0] = $metric;
        $url = $this->series_endpoint . $this->api_key;


        $result = $this->send(
            $url,
            $series
        );

        return $result;
    }

}