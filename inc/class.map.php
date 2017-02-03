<?php

if(!defined('WPINC')) // MUST have WordPress.
	exit('Do NOT access this file directly: '.basename(__FILE__));

use Ivory\GoogleMap;
use Ivory\GoogleMap\Map;
use Ivory\GoogleMap\Service\Geocoder\GeocoderService;
use Http\Adapter\Guzzle6\Client;
use Http\Message\MessageFactory\GuzzleMessageFactory;
use Ivory\GoogleMap\Service\Geocoder\Request\GeocoderAddressRequest;

/**
 * Directory MAP
 */
class NHPA_Map {

  public function NHPA_Map_func($atts) {


    $atts = shortcode_atts( array(
      'height' => '400px',
			'address_meta' => 'homeaddress',
			'limit' => 0,
			'initial_load' => 10,
			'initial_address' => '',
			'sidebar_meta' => 'Name, name|Address, address |Office Phone , office_phone',
			'locate_user_pos' => 0
    ), $atts, 'nhpa_map' );

		$address_meta = $atts['address_meta'];

    ob_start();

		$explode_addr = explode("|", $address_meta);

		$explode_addr = ( (count($explode_addr) > 1) ? $explode_addr : $explode_addr[0] );



		$the_users = get_users([ 'number' => 10, 'fields' => ['ID'], 'orderby' => 'registered' ]);

		if (empty($atts['limit']))
			$the_users = get_users([ 'exclude'=> [1], 'fields' => ['ID'], 'orderby' => 'registered' ]);
		else
			$the_users = get_users([ 'exclude'=> [1], 'number' => $atts['limit'], 'fields' => ['ID'], 'orderby' => 'registered' ]);

		$user_address = "";
		$user_address_array = [];

		foreach ($the_users as $key => $the_user_id) {

			if (!is_array($explode_addr))
				$user_address = self::user_address_from_single_meta($the_user_id->ID, $explode_addr);
			else
				$user_address = self::user_address_from_multiple_meta($the_user_id->ID, $explode_addr);

			if (empty($user_address))
				countinue;

			if (!empty($user_address)) {

				if ($key < $atts['initial_load'])
					$geoCoder = self::geoCoder($user_address);
				else
					$geoCoder = 1;


				if (!empty($geoCoder))
					$user_address_array[] = [ 'id' => (int) $the_user_id->ID , 'address' => $user_address, 'geocode' => $geoCoder ];


			}

		}

		$user_address_array = array_filter($user_address_array);

		//d($user_address_array);

		include pmpro_nhpa_PLUGIN_DIR."template".DS."map_template.php";

		self::do_map();

    $output = ob_get_clean();

    return $output;

  }

	private static function do_map() {

	}

	private static function user_address_from_single_meta($user_id = null, $address_meta = "") {

		if (empty($user_id) || empty($address_meta))
			return;

			$user_address = get_user_meta($user_id, $address_meta, true);

			if (empty($user_address))
				return;

			return $user_address;

	}

	private static function user_address_from_multiple_meta($user_id = null, $address_meta = "") {

		if (empty($user_id) || empty($address_meta))
			return;

			$user_address = [];

		foreach ($address_meta as $key => $single_address_meta) {

			$user_address[] = get_user_meta($user_id, $single_address_meta, true);

		}

		$total_count = count($user_address);
		$initial_empty_count = 0;

		foreach ($user_address as $key => $user_address_smeta) {

			if (empty($user_address_smeta)) {

				unset($user_address[$key]);

				$initial_empty_count++;
			}

		}

		if ($initial_empty_count === $total_count)
			return;

			if (empty($user_address))
				return;

			$user_address = implode(", ", $user_address);

			return $user_address;

	}

	public function geoCoderJSON() {

		$address = $_POST['address'];

		if (empty($address))
			wp_die();

		echo json_encode(self::geoCoder($address, 0));

		wp_die();

	}

	public static function geoCoder($address = "" , $json = false) {

		if (empty($address))
			return;


$geocoder = new GeocoderService(new Client(), new GuzzleMessageFactory());
$request = new GeocoderAddressRequest($address);
$response = $geocoder->geocode($request);

	if (empty($response->hasResults()))
		return;

if (empty($response->getResults()[0]))
	return;

$result = $response->getResults()[0];


if (empty($result->getGeometry()->getLocation()->getLatitude()) || empty($result->getGeometry()->getLocation()->getLongitude()))
	return;

	if (!$json)
		return ['lat' => $result->getGeometry()->getLocation()->getLatitude(), 'long' => $result->getGeometry()->getLocation()->getLongitude()] ;
	else
		return json_encode(['lat' => $result->getGeometry()->getLocation()->getLatitude(), 'long' => $result->getGeometry()->getLocation()->getLongitude()]);

	}

}

 ?>
