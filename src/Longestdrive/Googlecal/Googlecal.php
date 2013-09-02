<?php namespace Longestdrive\Googlecal;

class Googlecal {

	protected $client;
	protected $service;
	private $site_ids = array();

	

	public function __construct(\Google_client $client) {

		//$client = new \Google_client;
		$this->setClient($client);
		$this->setService($client);
	}

	public function getClient() {
		return $this->client;
	}

	public function setClient(\Google_Client $client) {
		$this->client = $client;

		return $this;
	}

	public function getService() {
		return $this->service;
	}

	public function setService(\Google_Client $client) {

		$this->service = new \Google_CalendarService($client);

		return $this;
	}

	public function listCalendars() {

		//TODO: MOdify in final to return the events rather than list here

		$calendarList = $this->service->calendarList->listCalendarList();

		return $calendarList;

	}

	public function calListEvents($calendarId = 'primary', $minDate=null, $maxDate=null) {

		$optParams = array(
			'orderBy'=>'startTime',
			'singleEvents'=>true
			);

		if($minDate) {

			$optParams['timeMin'] = $minDate;
		}

		if($maxDate) {

			$optParams['timeMax'] = $maxDate;
		}
		$events = $this->service->events->listEvents($calendarId, $optParams);

		return $events;

	}

	function calAddEvent($calendarId = 'primary', $startDate, $endDate, $summary, $description = null) {

		$event = new \Google_Event;
		$event->setSummary($summary);
		$event->setDescription($description);
		//$event->setLocation('Somewhere');
		$start = new \Google_EventDateTime;
		$start->setDateTime($startDate);
		$event->setStart($start);
		$end = new \Google_EventDateTime;
		$end->setDateTime($endDate);
		$event->setEnd($end);

		return $this->service->events->insert($calendarId, $event);

	}




}