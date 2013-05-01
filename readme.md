# NgsWeather

Class for receiving and parsing actual data about weather from [NGS Weather](http://pogoda.ngs.ru/).

# Version

Current version: 1.0a

# Example and usage

## Example

		include_once('NgsWeather.php');
		$novosibirsk = new NgsWeather('nsk','meteo_lenina_12');
		echo $novosibirsk->temperature; // +20

## Available cities and meteostations

* Novosibirsk (nsk, meteo_lenina_12)
* Academgorodok (academgorodok, meteo_academ_1_mts)
* Berdsk (berdsk, meteo_berdsk_1_mts)
* CP Berezki (berezki, meteo_berezki_mts)
* Krasnoyarsk (krsk, meteo_krsk_1)

## Available variables and description

Pressure (Torr, mm Hg.)

		$this->pressure

Temperature (Celsius, °C)

		$this->temperature

Wind speed (m/s)

		$this->wind_speed

Average wind speed in the past 10 minutes (m/s)

		$this->wind_speed_10_min_avg

Wind direction (°)

		$this->wind_direction

Humidity (%)

		$this->humidity

Time of sunrise (hmm or hhmm)

		$this->time_of_sunrise

Time of sunrise in readable format (h:mm or hh:mm)

		$this->time_of_sunrise_normal

Time of sunset (hmm or hhmm)

		$this->time_of_sunset

Time of sunset in readable format (h:mm or hh:mm)

		$this->time_of_sunset_normal

Duration of the day (h:mm or h:mm or optionally hh h. mm min.)

		$this->duration_of_the_day

Wind direction name

		$this->wind_direction_name

Ultraviolet index

		$this->uv

Solar Radiation (W/m²)

		$this->solar_radiation
