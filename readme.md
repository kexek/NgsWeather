# NgsWeather

Class for receiving and parsing actual data about weather from [NGS Weather](http://pogoda.ngs.ru/).

# Version

Current version: 1.0.2a

# Example and usage

## Example

		include_once('NgsWeather.php');
		$novosibirsk = new NgsWeather('nsk','meteo_lenina_12');
		echo $novosibirsk->temperature; // +20

## Available cities and weather stations

* **Novosibirsk** (nsk, meteo_lenina_12)
* **Academgorodok** (academgorodok, meteo_academ_1_mts)
* **Berdsk** (berdsk, meteo_berdsk_1_mts)
* **CP Berezki** (berezki, meteo_berezki_mts)
* **Krasnoyarsk** (krsk, meteo_krsk_1)

## Available variables and description

* **$this->wind_chill** — Wind chill (Celsius, °C)
* **$this->heat_index** — Heat Index (Celsius, °C)
* **$this->pressure** — Pressure (Torr, mm Hg.)
* **$this->temperature** — Temperature (Celsius, °C)
* **$this->wind_speed** — Wind speed (m/s)
* **$this->wind_speed_10_min_avg** — Average wind speed in the past 10 minutes (m/s)
* **$this->wind_direction** — Wind direction (°)
* **$this->humidity** — Humidity (%)
* **$this->time_of_sunrise** — Time of sunrise (hmm or hhmm)
* **$this->time_of_sunrise_normal** — Time of sunrise in readable format (h:mm or hh:mm)
* **$this->time_of_sunset** — Time of sunset (hmm or hhmm)
* **$this->time_of_sunset_normal** — Time of sunset in readable format (h:mm or hh:mm)
* **$this->duration_of_the_day** — Duration of the day (h:mm or h:mm or optionally hh h. mm min.)
* **$this->wind_direction_name** — Wind direction name
* **$this->uv** — Ultraviolet index
* **$this->solar_radiation** — Solar Radiation (W/m²)
