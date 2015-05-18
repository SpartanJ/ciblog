<?php

class Geocode
{
	const GMAPS_GEOCODE_API = 'http://maps.google.com/maps/api/geocode/json?';
	
	//REF: http://code.google.com/intl/es-ES/apis/maps/documentation/geocoding/
	
	
	private static function make_address($name)
	{
		$s=preg_replace("/[^a-zA-Z0-9,\s]/", '', $name); //removes symbols
		return urlencode(strtolower($s));
	
	}

	public static function GetLatLong($address, &$status)
	{
		$url = 'address='.self::make_address($address);
		$url .= '&region=ar&sensor=false';
		
		$cinit = curl_init();
		curl_setopt($cinit, CURLOPT_URL, self::GMAPS_GEOCODE_API.$url);
		curl_setopt($cinit, CURLOPT_HEADER,0);
		curl_setopt($cinit, CURLOPT_USERAGENT, $_SERVER["HTTP_USER_AGENT"]);
		curl_setopt($cinit, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($cinit, CURLOPT_RETURNTRANSFER, 1);
		$response = curl_exec($cinit);
		curl_close($cinit);
		
		$res = json_decode($response);

		if($res->status == 'OK')
		{
			if($res->results[0]->formatted_address)
			{
				return $res->results[0]->geometry->location;
			}
			else
			{
				return false;
			}
			
		}
		else
		{
			$status = $res->status;
			return false;
		}
		
	}
	
}

?>