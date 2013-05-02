<?php

/**
 * NgsWeather class
 *
 * Receive and parse weather data from pogoda.ngs.ru API (http://pogoda.ngs.ru/json/)
 *
 * @version   1.0.2a
 * @author    Dmitry Lakhno <d.lakhno@office.ngs.ru>
 * @copyright 2013 Dmitry Lakhno
 */

class NgsWeather
{
	/**
	 * Wind chill (Celsius, °C)
	 *
	 * @var integer|string
	 */
	public $wind_chill;

	/**
	 * Heat Index (Celsius, °C)
	 *
	 * @var integer|string
	 */
	public $heat_index;

	/**
	 * Pressure (Torr, mm Hg.)
	 *
	 * @var integer
	 */
	public $pressure;

	/**
	 * Temperature (Celsius, °C)
	 *
	 * @var integer|string
	 */
	public $temperature;

	/**
	 * Wind speed (m/s)
	 *
	 * @var integer
	 */
	public $wind_speed;

	/**
	 * Average wind speed in the past 10 minutes (m/s)
	 *
	 * @var integer
	 */
	public $wind_speed_10_min_avg;

	/**
	 * Wind direction (degrees)
	 *
	 * @var integer
	 */
	public $wind_direction;

	/**
	 * Humidity (%)
	 *
	 * @var integer
	 */
	public $humidity;

	/**
	 * Time of sunrise (hmm or hhmm)
	 *
	 * @var integer
	 */
	public $time_of_sunrise;

	/**
	 * Time of sunrise in readable format (h:mm or hh:mm)
	 *
	 * @var bool|string
	 */
	public $time_of_sunrise_normal;

	/**
	 * Time of sunset (hmm or hhmm)
	 *
	 * @var integer
	 */
	public $time_of_sunset;

	/**
	 * Time of sunset in readable format (h:mm or hh:mm)
	 *
	 * @var bool|string
	 */
	public $time_of_sunset_normal;

	/**
	 * Ultraviolet index
	 *
	 * @var integer
	 */
	public $uv;

	/**
	 * Solar radiation
	 *
	 * @var integer
	 */
	public $solar_radiation;

	/**
	 * Duration of the day (h:mm or h:mm or optionally hh h. mm min.)
	 *
	 * @var string
	 */
	public $duration_of_the_day;

	/**
	 * Wind direction name
	 *
	 * @var string
	 */
	public $wind_direction_name;
	
	/**
	 * stdClass with weather data response
	 *
	 * @var object
	 */
	private $weather;

	public function __construct($cityCode, $weatherStation)
	{
		$this->getData($cityCode, $weatherStation);
		$this->setVariables();
		$this->time_of_sunrise_normal = $this->getConvertedTime($this->time_of_sunrise);
		$this->time_of_sunset_normal = $this->getConvertedTime($this->time_of_sunset);
		$this->duration_of_the_day = $this->getDayDuration($this->time_of_sunrise_normal, $this->time_of_sunset_normal);
		$this->wind_direction_name = $this->getWindDirection($this->wind_direction);

	}

	/**
	 * Sending request, receiving data, decoding it from JSON
	 *
	 * @param string $cityCode       City alias
	 * @param string $weatherStation Weather station code
	 *
	 * @throws Exception Throws exception if can't receive weather data
	 */
	public function getData($cityCode, $weatherStation)
	{
		$requestData = array(
			'method'  => 'POST',
			'header'  => 'Connection: close\r\n' .
				'Content-Type: application/json',
			'content' => '{
					"method": "getForecast",
					"params": [ "' . $weatherStation . '", "' . $cityCode . '"]
				}'
		);
		$requestContext = stream_context_create(array('http' => $requestData));
		$requestResult = @file_get_contents('http://pogoda.ngs.ru/json/', false, $requestContext);
		$requestArray = json_decode($requestResult);

		if (!$requestResult || $requestArray->error) {
			throw new Exception("Can't receive weather data for $cityCode - $weatherStation");
		} else {
			$this->weather = $requestArray->result;
		}
	}

	/**
	 * Creating variables from request response
	 */
	private function setVariables()
	{
		$reflect = new ReflectionObject($this);
		$props = $reflect->getProperties();
		# As it contains not only fields but also methods we can't simply merge them
		foreach ($this->weather as $key => $value) {
			foreach ($props as $prop) {
				if ($prop->getName() == $key && $prop->isPublic()) {
					$this->{$key} = $value;
				}
			}
		}
	}

	/**
	 * Function to convert date from hmm or hhmm into h:mm or hh:mm relatively
	 *
	 * @param string $time Time in hmm or hhmm format
	 *
	 * @return string in h:mm or hh:mm format
	 * @throws Exception Throws exception when can't reformat
	 */
	public function getConvertedTime($time)
	{
		if (strlen($time) == '3') {
			return substr($time, 0, -2) . ':' . substr($time, 1);
		} elseif (strlen($time) == '4') {
			return substr($time, 0, -2) . ':' . substr($time, 2);
		} else {
			throw new Exception("Can't convert '$time' with getConvertedTime function");
		}
	}

	/**
	 * Function to calculate the length of the day
	 *
	 * @param string  $startTime Time of sunrise in h:mm or hh:mm format
	 * @param string  $endTime   Time of sunset in h:mm or hh:mm format
	 * @param boolean $formatted Should we format time or not
	 *
	 * @return string Duration of the day
	 */
	public function getDayDuration($startTime, $endTime, $formatted = false)
	{
		$startTimeArray = explode(':', $startTime);
		$endTimeArray = explode(':', $endTime);
		# Calculate everything into minutes and subtract time of sunrise from time of sunset
		$timeDifference = ($endTimeArray[0] * 60 + $endTimeArray[1] - $startTimeArray[0] * 60 - $startTimeArray[1]);
		# Calculate result back to hours. Integer - hours, the rest - minutes
		if (!$formatted) {
			return floor($timeDifference / 60) . ' h ' . ($timeDifference % 60) . ' min';
		} else {
			return floor($timeDifference / 60) . ':' . ($timeDifference % 60);
		}
	}

	/**
	 * Function to determine the direction of the wind by degrees
	 *
	 * @param integer|string wind direction in degrees
	 *
	 * @return string Wind direction naming
	 */
	public function getWindDirection($degree)
	{

		switch ($degree) {
			# North
			case (($degree > '0' && $degree <= '22.5') || ($degree <= '337.5' && $degree >= '360')):
				$degree_name = 'north';
				break;
			# North-east
			case ($degree > '22.5' && $degree <= '67.5'):
				$degree_name = 'north-east';
				break;
			# East
			case ($degree > '67.5' && $degree <= '112.5'):
				$degree_name = 'east';
				break;
			# South-east
			case ($degree > '112.5' && $degree <= '157.5'):
				$degree_name = 'south-east';
				break;
			# South
			case ($degree > '157.5' && $degree <= '202.5'):
				$degree_name = 'south';
				break;
			# South-west
			case ($degree > '202.5' && $degree <= '247.5'):
				$degree_name = 'south-west';
				break;
			# West
			case ($degree > '247.5' && $degree <= '292.5'):
				$degree_name = 'west';
				break;
			# North-west
			case ($degree > '295.5' && $degree <= '337.5'):
				$degree_name = 'north-west';
				break;
			# There is no wind
			default:
				$degree_name = 'no';
				break;

		} # end switch

		return $degree_name;

	} # end getWindDirection function

} # end NgsWeather class