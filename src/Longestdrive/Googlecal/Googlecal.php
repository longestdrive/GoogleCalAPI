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

		
		try {
			return $this->service->events->insert($calendarId, $event);
		}
		catch (Google_ServiceException $e) {
                            print "Error code :" . $e->getCode() . "\n";
                            // Error message is formatted as "Error calling <REQUEST METHOD> <REQUEST URL>: (<CODE>) <MESSAGE OR REASON>".
                            print "Error message: " . $e->getMessage() . "\n";
                            return false;
                            
                        } catch (Google_Exception $e) {
                            // Other error.
                            print "An error occurred: (" . $e->getCode() . ") " . $e->getMessage() . "\n";
                        return false;
                        }

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

		$event = $this->service->events->get($calendarId, $eventId);



		$event->setSummary($summary);
		$event->setDescription($description);
		//$event->setLocation('Somewhere');
		$start = new \Google_EventDateTime;
		$start->setDateTime($startDate);
		$event->setStart($start);
		$end = new \Google_EventDateTime;
		$end->setDateTime($endDate);
		$event->setEnd($end);

		return $this->service->events->update($calendarId, $eventId, $event);

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

	function calGetEvent($calendarId = 'primary', $eventId) {

		//Get event

		return $event = $this->service->events->get($calendarId, $eventId);

	}




}