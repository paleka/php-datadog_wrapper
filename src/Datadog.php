<?php

namespace Datadog;

use GuzzleHttp\Client;

class Datadog
{
    function __construct()
    {
        $config = parse_ini_file("config.ini");
        $this->api_key = $config['api_key'];
        $this->event_endpoint = $config['event_endpoint'];
        $this->series_endpoint = $config['series_endpoint'];

        if ($this->api_key == '') {
            throw new \Exception("API key is missing! Define it inside the config.ini file");
        }

        if ($this->event_endpoint == '') {
            throw new \Exception("API event endpoint is missing! Define it inside the config.ini file");
        }

        if ($this->series_endpoint == '') {
            throw new \Exception("API series endpoint is missing! Define it inside the config.ini file");
        }

    }

    //Example #2 of sending and event using array and getting back the event url
    // sometimes makes more sense to use this

    /*
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

        $eventUrl = $client->eventArr($eventArr);
    */

    /**
     * @param array $event
     * @return bool
     */
    public function eventArr(array $event)
    {
        $url = $this->event_endpoint . $this->api_key;

        $eventUrl = $this->send(
            $url,
            $event
        );

        return $eventUrl;
    }


    //POST TIME SERIES POINTS
    // the hard way
    // also the easier way if you want to pass the prebuilt array of values and use multiple (or one) data points for one metric

    /* EXAMPLE

        $client = new Datadog();

        $test1 = array(
            array(365),
            array(13456789, 123),
        );

        $test2 = array (
            array(20),
            array(13456789, 30),
            array(40)
        );

        $metric1 = array(
                'metric' => 'test1.metric',
                'points' => $test1,
                'host' => 'test1.my.host',
                'tags' => ['test_version:1', 'test_type:5', 'test_stream:7'] // array
        );

        $metric2 = array(
            'metric' => 'test2.metric',
            'points' => $test2,
            'host' => 'test2.my.host',
            'tags' => ['test_version:2', 'test_type:6', 'test_stream:8'] // array
        );

        $metrics = array($metric1, $metric2);
        $client->sendSeries($metrics);

    */

    /**
     * Sending array of metrics
     *
     * @param array $metrics
     * @return bool
     */
    // todo - send arrays of metrics in one request
    /*
    public function sendSeries(array $metrics)
    {
        $add_second = 1; //increment one second so the values won't overwrite
        foreach ($metrics as $key => &$metric) {
            $points_timestamped = [];

            foreach ($metric['points'] as $item) {
                if (count($item) == 1) {
                    array_unshift($item, time() + $add_second);
                    $add_second++;
                }
                $points_timestamped[] = $item;
            }
            $metrics[$key]['points'] = $points_timestamped;
        }

        $series = array('series' => $metrics);
        $url = $this->series_endpoint . $this->api_key;

        $result = $this->send(
            $url,
            $series
        );

        return $result;
    }
    */


    //request to the Datadog API

    /**
     * @param $url
     * @param array $data
     * @return bool | (string) event url
     */
    protected function send($url, array $data)
    {
        $client = new Client();
        //$lib_path = realpath(dirname(__FILE__)); // ( https://curl.haxx.se/ca/cacert.pem )  // use if testing from https
        //$mail_address = 'email@email.com'; //email yourself failed post

        try {
            $res = $client->post(
                $url,
                ['body' => json_encode($data),
                    'timeout' => 180.0, // timeout of the request in seconds
                    'connect_timeout' => 180.0, // seconds to wait while trying to connect to a server
                    'curl' => array(
                        CURLOPT_TIMEOUT => 0,
                        CURLOPT_TIMEOUT_MS => 0,
                        CURLOPT_CONNECTTIMEOUT => 0,
                    )//,'verify' => $lib_path.'/ca_bundle.pem'
                ]);

            $raw_res = json_decode((string)$res->getBody(), true); //get raw response

            //return url if it's an event
            if ($raw_res['event']) {
                $response = $raw_res['event']['url'];
            } else {
                $response = true;
            }
        } catch (\Exception $e) {
            //mail($mail_address, 'datadog wrapper sending failed - RequestException', $e->getMessage());
            $response = false;
        }
        return $response;
    }
}