<?php

if(!defined('WPINC')) // MUST have WordPress.
	exit('Do NOT access this file directly: '.basename(__FILE__));

/**
 * Directory Search
 */
class NHPA_Directory_Search
{

  function __construct() {


  }

	public static function search_grab_matched_users_func() {

		if (empty($_POST['search_params']))
			wp_die();

		global $wpdb;

			ob_start();

			$prefix = $wpdb->get_blog_prefix();


		$search_params = $_POST['search_params'];
		$search_results = array();
		$search_type = "";


		foreach ($search_params as $key => $search_param) {

			if (empty($search_param['value'])) {
				unset($search_params[$key]);
				continue;
			}

			if ( strcmp($search_param['meta'], "search_type") === 0)
				$search_type = $search_param['value'];

			$meta_key = $search_param['meta'];
			$meta_value = $search_param['value'];

			if (!is_array($meta_value))
				$search_results[] = $wpdb->get_results( "SELECT * FROM `{$prefix}usermeta` WHERE meta_key = '{$meta_key}' AND `meta_value` LIKE '%{$meta_value}%'", OBJECT );
			else {

				foreach ($meta_value as $key => $single_meta_value) {
					$search_results[] = $wpdb->get_results( "SELECT * FROM `{$prefix}usermeta` WHERE meta_key = '{$meta_key}' AND `meta_value` LIKE '%{$single_meta_value}%'", OBJECT );
				}

			}

//SELECT * FROM `wpgq_usermeta` WHERE `meta_key` = 'institution' AND `meta_value` = 'brac'

		}

			$search_results = array_filter($search_results);

			if (empty($search_results)) {
				echo json_encode(['err' => 1, 'msg' => "Nothing found!"]);
				wp_die();

			}


			if (strcmp($search_type, "intersect") === 0)
				$final_result = self::intersectSearchResult($search_results);
			else
				$final_result = self::unionSearchResult($search_results);

				$titan = TitanFramework::getInstance( 'pmpro_nhpa_opts' );
				$directory_page_id = ( empty($titan->getOption( 'page_id_for_filter' )) ? "" : $titan->getOption( 'page_id_for_filter' ) );
				$wp_page_id = ( empty($_POST['wp_page_id']) ? "" : $_POST['wp_page_id'] );

				if ($wp_page_id == $directory_page_id) {

					$final_result = ( is_array($final_result) ? $final_result : array() );

					$filtered_user_id = array();

					$exclude_membership_levels = ( empty($titan->getOption( 'exclude_membership_levels' )) ? "" : $titan->getOption( 'exclude_membership_levels' ) );
					$exclude_membership_levels = explode(",", $exclude_membership_levels);
					//if (pmpro_hasMembershipLevel($membership_level , $current_user_id))
					$exclude_membership_levels = ( is_array($exclude_membership_levels) ? $exclude_membership_levels : array() );
					$exclude_membership_levels = array_map('intval', $exclude_membership_levels);


					foreach ($final_result as $key => $user_id) {

						$user_id = (int) $user_id;

						$user_id_not_exists = 0;

						$user_id_not_exists = NHPA_Directory::user_is_not_specified_member($exclude_membership_levels, $user_id);


						if ($user_id_not_exists)
							$filtered_user_id[] = $user_id;


					}

					$final_result = $filtered_user_id;

				}


				//$number = ( empty($_POST['limit']) ? "" : $_POST['limit'] );
				$number = "";
		    $offset = ( empty($_POST['offset']) ? "" : $_POST['offset'] );

				$users_array = self::get_users_by_include( array( 'include' => $final_result, 'fields' => ['ID'] ) );

				echo json_encode(self::get_users_by_include([ 'include' => $final_result, 'number' => $number, 'offset' => $offset,'fields' => ['ID'], 'orderby' => 'registered' ]));

			//echo json_encode($output);
			wp_die();


	}

	private static function get_users_by_include( $args = array() ) {
    $blogusers = get_users( $args );

    if( isset( $args['include'] ) ){
        $include = $args['include'];
        usort($blogusers, function ($a, $b) use( $include ){
            $q = array_flip( $include );
            return $q[$a->ID] - $q[$b->ID];
        });
    }

    return $blogusers;
	}

	private static function intersectSearchResult($searchResult = "") {

		if (empty($searchResult))
			return;

			$user_ids = array();

			foreach ($searchResult as $key => $search) {

				if (!is_array($search))
					continue;

					foreach ($search as $key => $singleSearch) {

						//$meta_keys[$singleSearch->meta_key][] = $singleSearch->user_id;

						if (!in_array($singleSearch->meta_key, $meta_keys))
							$meta_keys[] = $singleSearch->meta_key;

							$user_ids[$singleSearch->meta_key][] = $singleSearch->user_id;
					}
			}


			if (empty($user_ids))
				return;

				$ArrayStore = array();


				foreach ($user_ids as $key => $user_id) {

					$ArrayStore[] = $user_id;

				}

				if (count($ArrayStore) ===1) {

					return $ArrayStore[0];

				}


				$user_ids_intersect = call_user_func_array('array_intersect', $ArrayStore);
				$user_ids_intersect = array_values($user_ids_intersect);

			return $user_ids_intersect;

	}

	private static function unionSearchResult($searchResult = "") {

		if (empty($searchResult))
			return;

			$user_ids = array();

			foreach ($searchResult as $key => $search) {

				if (!is_array($search))
					continue;

				foreach ($search as $key => $singleSearch) {

					if (empty($singleSearch->user_id))
						continue;

					if (in_array($singleSearch->user_id, $user_ids))
						continue;

					$user_ids[] = $singleSearch->user_id;
				}


			}

			return $user_ids;


	}



	public static function NHPA_Directory_Search_func($atts, $content) {

		$opts = shortcode_atts( array(
        'on_dir' => 0,
				'option_field' => 'dir_search_fields'
    ), $atts );


		if (!empty($_GET['user_id'])) {

			$user_id = (int) $_GET['user_id'];

			if (empty($atts['on_dir']))
				return do_shortcode('[nhpa_user_profile user_id="'.$user_id.'"]');
			else
				return;
		}

		//d($content);
		if (empty($atts['option_field']))
			$atts['option_field'] = 'dir_search_fields';
		//global $wpdb;
		$titan = TitanFramework::getInstance( 'pmpro_nhpa_opts' );
		$search_fields = ( empty($titan->getOption( $atts['option_field'] )) ? "" : $titan->getOption( $atts['option_field'] ) );

		if (empty($search_fields))
			return;

		$search_fields = explode(PHP_EOL, $search_fields);

		if (!is_array($search_fields))
			return;

		ob_start();

		include pmpro_nhpa_PLUGIN_DIR."template".DS."search_template.php";

		$output = ob_get_clean();

		return $output;

	}

	public function view_profile_func() {



	}

	public static function organize_fields($fields_data = "") {

		if (empty($fields_data))
			return;

		ob_start();

		$html = "";

			$search_fields = $fields_data;

			foreach ($search_fields as $key => $search_field) {

				$search_field = explode("|", $search_field);

				array_walk($search_field, function(&$item, $key) {

					if ( $key === 0) {

							$item = str_replace(' ', '', $item);
					    $item = preg_replace('/[^\w,]/', '', $item);
					} elseif ($key === 3) {
							$item = trim($item);
					}

					// if ($key !== 1) {
					// 	//$item = str_replace(' ', '', $item);
					// 	$item = $item;
				  //   // $item = preg_replace('/[^\w,]/', '', $item);
					// }


				});

				if ((strcmp($search_field[0], "text") == 0))
					$html .= self::add_text_type($search_field, $key_count);

				if ((strcmp($search_field[0], "select") == 0))
					$html .= self::add_select_type($search_field, $key_count);

				if ((strcmp($search_field[0], "select_array") == 0))
					$html .= self::add_select_array_type($search_field, $key_count);


			}

			_e($html);

			$output = ob_get_clean();

			return $output;


	}

	private static function add_select_array_type($search_field = "", $key_count) {

		if (empty($search_field))
			return;

		if (count($search_field) != 4)
			return;

			$key_count = (int) $key_count;

			$html_opts = "";

			$html_opts .= "<option></option>";

			if (strcmp($search_field[3], "auto") === 0)
				$html_opts .= self::generate_select_array_auto($search_field[2]);
			else {

				//echo $search_field[3];

				$select_opts = explode(",",  $search_field[3]);

				foreach ($select_opts as $key => $option) {
					$html_opts .= "<option>".$option."</option>";
				}

			}


					$html = '  <div class="form-group row">
					<label for="select-input-'.$key_count.'" class="col-sm-2 col-form-label">'.$search_field[1].'</label>
					<div class="col-sm-10">';

					//$html .= '<select data-test="e" multiple="multiple" data-select_array="1" data-meta_field="'.$search_field[2].'" multiple class="form-control" id="select-input-'.$key_count.'">';
					$html .= '<select data-test="e" data-select_array="1" name="'.$search_field[2].'" data-meta_field="'.$search_field[2].'" class="form-control" id="select-input-'.$key_count.'">';

					$html .= $html_opts;

					$html .='</select>
					</div>
			</div>';



			return $html;

	}

	public static function generate_select_array_auto($meta_key = null) {

		if (empty($meta_key))
			return;

			global $wpdb;

			$prefix = $wpdb->get_blog_prefix();

			$results = $wpdb->get_results( "SELECT * FROM `{$prefix}usermeta` WHERE meta_key = '{$meta_key}' ", OBJECT );

			if (empty($results))
				return;

				$values = array();

				foreach ($results as $key => $result) {

					$result_meta = maybe_unserialize($result->meta_value);
					$result_meta = ( is_array($result_meta) ? $result_meta : array() );
					foreach ($result_meta as $key => $single_meta) {

						$item = preg_replace("/[^ \w]+/", "", $single_meta);
						$item = $single_meta;
						if (in_array($item, $values))
							continue;

						$values[] = $item;

					}

				}

			$values = array_filter($values);

			$html_val = "";

			foreach ($values as $key => $value) {
				$html_val .= "<option>".$value."</option>";
			}

			return $html_val;


	}



	private static function add_text_type($search_field = "", $key_count) {

		if (empty($search_field))
			return;

		if (count($search_field) != 3)
			return;

	$key_count = (int) $key_count;

			$html = '  <div class="form-group row">
			<label for="text-input-'.$key_count.'" class="col-sm-2 col-form-label">'.$search_field[1].'</label>
			<div class="col-sm-10">
		    <input data-meta_field="'.$search_field[2].'" name="'.$search_field[2].'" class="form-control" type="text" value="" id="text-input-'.$key_count.'">
		  </div>
  </div>';


			return $html;


	}



	private static function add_select_type($search_field = "", $key_count) {

		if (empty($search_field))
			return;

		if (count($search_field) != 4)
			return;

			$key_count = (int) $key_count;

			$html_opts = "";
			if (strcmp($search_field[3], "auto") === 0)
				$html_opts = self::generate_select_auto($search_field[2]);
			else {

				//echo $search_field[3];

				$select_opts = explode(",",  $search_field[3]);
				$html_opts .= "<option></option>";

				foreach ($select_opts as $key => $option) {
					$html_opts .= "<option>".$option."</option>";
				}

			}


					$html = '  <div class="form-group row">
					<label for="select-input-'.$key_count.'" class="col-sm-2 col-form-label">'.$search_field[1].'</label>
					<div class="col-sm-10">

					<select name="'.$search_field[2].'" data-meta_field="'.$search_field[2].'" class="form-control" id="select-input-'.$key_count.'">';

					$html .= $html_opts;

    			$html .='</select>
				  </div>
		  </div>';



			return $html;

	}
	private static function generate_select_auto($meta_key = null) {

		if (empty($meta_key))
			return;

			global $wpdb;

			$prefix = $wpdb->get_blog_prefix();

			$results = $wpdb->get_results( "SELECT * FROM `{$prefix}usermeta` WHERE meta_key = '{$meta_key}' ", OBJECT );

			if (empty($results))
				return;

				$values = array();

				foreach ($results as $key => $result) {

					//$item = preg_replace("/[^ \w]+/", "", $result->meta_value);
					$item = $result->meta_value;

					if (in_array($item, $values))
						continue;

					$values[] = $item;
				}

			$values = array_filter($values);

			$html_val = "";
			$html_val .= "<option></option>";

			foreach ($values as $key => $value) {
				$html_val .= "<option>".$value."</option>";
			}

			return $html_val;


	}

	public static function search_func_psychology($post = null) {

		if (empty($post))
			return;

		if (empty($post['search_trigger']))
			return;

			$search_params = $post;
			$search_results = array();
			$search_type = "";

			global $wpdb;
			$prefix = $wpdb->get_blog_prefix();

			$search_type = $search_params['search_type'];
			$searched_data = [];

			foreach ($search_params as $key => $search_param) {


			/*  if (empty($search_param['value'])) {
					unset($search_params[$key]);
					continue;
				}

				if ( strcmp($search_param['meta'], "search_type") === 0)
					$search_type = $search_param['value'];
			*/
				$meta_key = trim($key);
				$meta_value = $search_param;

				if (empty($meta_value))
					continue;

					$searched_data[$meta_key] = $meta_value;

					$search_results[] = $wpdb->get_results( "SELECT * FROM `{$prefix}usermeta` WHERE meta_key = '{$meta_key}' AND `meta_value` LIKE '%{$meta_value}%'", OBJECT );

			//SELECT * FROM `wpgq_usermeta` WHERE `meta_key` = 'institution' AND `meta_value` = 'brac'

			}

				$search_results = array_filter($search_results);

				if (empty($search_results))
					return;

					if (strcmp($search_type, "intersect") === 0)
						$final_result = self::intersectSearchResult($search_results);
					else
						$final_result = self::unionSearchResult($search_results);


				return [ 'result' => $final_result, 'searched_data' => $searched_data ];

	}

	public static function search_func_general($post = null) {

		if (empty($post))
			return;

		if (empty($post['search_trigger']))
			return;

			$search_params = $post;
			$search_results = array();
			$search_type = "";

			global $wpdb;
			$prefix = $wpdb->get_blog_prefix();

			$search_type = $search_params['search_type'];
			$searched_data = [];

			foreach ($search_params as $key => $search_param) {


			/*  if (empty($search_param['value'])) {
					unset($search_params[$key]);
					continue;
				}

				if ( strcmp($search_param['meta'], "search_type") === 0)
					$search_type = $search_param['value'];
			*/
				$meta_key = trim($key);
				$meta_value = $search_param;

				if (empty($meta_value))
					continue;

					$searched_data[$meta_key] = $meta_value;

					$search_results[] = $wpdb->get_results( "SELECT * FROM `{$prefix}usermeta` WHERE meta_key = '{$meta_key}' AND `meta_value` LIKE '%{$meta_value}%'", OBJECT );

			//SELECT * FROM `wpgq_usermeta` WHERE `meta_key` = 'institution' AND `meta_value` = 'brac'

			}

				$search_results = array_filter($search_results);

				if (empty($search_results))
					return;

					if (strcmp($search_type, "intersect") === 0)
						$final_result = self::intersectSearchResult($search_results);
					else
						$final_result = self::unionSearchResult($search_results);


				return [ 'result' => $final_result, 'searched_data' => $searched_data ];

	}


}



 ?>
