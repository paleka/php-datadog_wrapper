<?php

namespace Datadog\Send;

use Datadog\Datadog;

class Event extends Datadog
{
    protected $eventTitle;
    protected $eventMessage;
    protected $eventTime;
    protected $eventPriority;
    protected $eventHost;
    protected $eventTags;
    protected $eventAlert_type;
    protected $eventSource_type_name;


    function __construct()
    {
        parent::__construct();
        // if not set, use current time
        $this->eventTime = time();
    }


    /** required
     *
     * @param $eventTitle , string
     * @return $this
     */
    public function setEventTitle($eventTitle)
    {
        $this->eventTitle = $eventTitle;
        return $this;
    }

    /** required
     *
     * @param $eventMessage , string | markdown , hard limit 4000 characters
     * @return $this
     */
    public function setEventMessage($eventMessage)
    {
        $this->eventMessage = $eventMessage;
        return $this;
    }

    /**
     * @param $eventTime , timestamp
     * @return $this
     */
    public function setEventTime($eventTime)
    {
        $this->eventTime = $eventTime;
        return $this;
    }

    /**
     * @param $eventPriority , normal | low
     * @return $this
     */
    public function setEventPriority($eventPriority)
    {
        $this->eventPriority = $eventPriority;
        return $this;
    }

    public function setEventHost($eventHost)
    {
        $this->eventHost = $eventHost;
        return $this;
    }

    /**
     * @param $eventTags , comma separated list
     * @return $this
     */
    public function setEventTags($eventTags)
    {
        $this->eventTags = $eventTags;
        return $this;
    }

    /**
     * @param $eventAlert_type , "error" | "warning" | "info" | "success"
     * @return $this
     */
    public function setEventAlert_type($eventAlert_type)
    {
        $this->eventAlert_type = $eventAlert_type;
        return $this;
    }

    /**
     * @param $eventSource_type_name , nagios | hudson | jenkins | user | my apps | feed | chef | puppet | git | bitbucket | fabric | capistrano
     * @return $this
     */
    public function setEventSource_type_name($eventSource_type_name)
    {
        $this->eventSource_type_name = $eventSource_type_name;
        return $this;
    }

    public function sendEvent()
    {
        return $this->event();
    }

    /* Example #1 of sending and event and getting back the event url
     *
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
    */

    /**
     * @return string, url of the event created
     */
    public function event()
    {
        if (!$this->eventTitle || strlen($this->eventTitle) < 3) {
            throw new \InvalidArgumentException("Event title shouldn't be empty or less than 3 characters");
        }
        if (!$this->eventMessage || strlen($this->eventMessage) < 3) {
            throw new \InvalidArgumentException("Event message shouldn't be empty or less than 3 characters");
        }

        $event = [
            'title' => $this->eventTitle,
            'text' => $this->eventMessage,
            'date_happened' => $this->eventTime,
            'priority' => $this->eventPriority,
            'host' => $this->eventHost,
            'tags' => $this->eventTags,
            'alert_type' => $this->eventAlert_type,
            'source_type_name' => $this->eventAlert_type
        ];

        $url = $this->event_endpoint . $this->api_key;

        $eventUrl = $this->send(
            $url,
            $event
        );

        return $eventUrl;
    }

}