<?php namespace Longestdrive\Googlecalapi;


class Googlecal {

	protected $client;
	protected $service;
	private $site_ids = array();

	

	public function __construct(\Google_Client $client) {

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

		$this->service = new \Google_Service_Calendar($client);

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

	/**
	 * [calFetchLimitEvents description]
	 * @param  string  $calendarId [description]
	 * @param  [type]  $start      [description]
	 * @param  [type]  $end        [description]
	 * @param  integer $limit      [description]
	 * @return [type]              [description]
	 */
	public function calFetchLimitEvents($calendarId = 'primary', $start, $end=null, $limit=25) {

		$optParams = array(
			'orderBy'=>'startTime',
			'singleEvents'=>true,
			'maxResults'=>$limit
			);

		if($start) {

			$optParams['timeMin'] = $start;
		}

		if(!is_null($end)) {

			$optParams['timeMax'] = $end;
		}
		$events = $this->service->events->listEvents($calendarId, $optParams);

		return $events;

	}

	function calAddEvent($calendarId = 'primary', $startDate, $endDate, $summary, $description = null) {

		$event = new \Google_Service_Calendar_Event;
//        dd($event);
		$event->setSummary($summary);
		$event->setDescription($description);
		//$event->setLocation('Somewhere');
		$start = new \Google_Service_Calendar_EventDateTime;
		$start->setDateTime($startDate);
		$event->setStart($start);
		$end = new \Google_Service_Calendar_EventDateTime;
		$end->setDateTime($endDate);
		$event->setEnd($end);

		
		
			return $this->service->events->insert($calendarId, $event);
		

	}

	/**
	 * Update a google calendar event 
	 * @param  string $calendarId  [unique id of calendar containing event]
	 * @param  string $eventId     [unique id of event to update]
	 * @param  string $startDate   [format YYYY-MM-DDTHH:MM:SSz]
	 * @param  string $endDate     [format YYYY-MM-DDTHH:MM:SSz]
	 * @param  string $summary     [event summary]
	 * @param  string $description [event description]
	 * @return object              [update event object]
	 */
	function calUpdateEvent($calendarId = 'primary', $eventId, $startDate, $endDate, $summary, $description = null) {

		$event = $this->calGetEvent($calendarId, $eventId);

		if ($event) {
			$event->setSummary($summary);
			$event->setDescription($description);

			$start = new \Google_Service_Calendar_EventDateTime;
			$start->setDateTime($startDate);
			$event->setStart($start);

			$end = new \Google_Service_Calendar_EventDateTime;
			$end->setDateTime($endDate);
			$event->setEnd($end);

			return $this->service->events->update($calendarId, $eventId, $event);
		} else {
			//event not found
			return false;
		}

	}

	/**
	 * Deletes the calender event
	 * @param  string $calendarId [unique id of the calendar containing the event]
	 * @param  string $eventId    [unique id of the event to delete]
	 * @return boolean             [true = deleted, false = error]
	 */
	function calDeleteEvent($calendarId = 'primary', $eventId) {

		//delete event

		return $event = $this->service->events->delete($calendarId, $eventId);

	}

	/**
	 * Fetches a google calendar event
	 * @param string $calendarId
	 * @param string $eventId
	 * @return object Google calender event object or false if not found
	 */
	function calGetEvent($calendarId = 'primary', $eventId) {

		//Get event

		try {

			return  $this->service->events->get($calendarId, $eventId);


		} catch (\Google_Service_Exception $e) {
			if($e->getCode() == 404) {
				return false;
			}

		}

	}




}