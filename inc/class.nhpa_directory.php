<?php

if(!defined('WPINC')) // MUST have WordPress.
	exit('Do NOT access this file directly: '.basename(__FILE__));

/**
 * NHPA_Directory
 */
class NHPA_Directory
{

private static $instance;

public static function get_instance() {
	if ( ! isset( self::$instance ) ) {
		self::$instance = new self();
	}

	return self::$instance;
}


  function __construct()  {


    add_action( 'wp_enqueue_scripts', array($this, 'load_custom_wp_frontend_style') );


    add_shortcode( 'nhpa_members_dir', [$this, 'nhpa_members_dir_func'] );

    add_action( 'wp_footer', array($this, 'wp_footer_test') );

    add_action( 'wp_ajax_get_nhpa_users_id', array($this, 'get_nhpa_users_id_callback') );
    add_action( 'wp_ajax_nopriv_get_nhpa_users_id', array($this, 'get_nhpa_users_id_callback') );

    add_action( 'wp_ajax_get_single_basic_profile', array($this, 'get_single_basic_profile_callback') );
    add_action( 'wp_ajax_nopriv_get_single_basic_profile', array($this, 'get_single_basic_profile_callback') );

    add_action( 'wp_ajax_request_detail_single_user', array($this, 'request_detail_single_user_callback') );
    add_action( 'wp_ajax_nopriv_request_detail_single_user', array($this, 'request_detail_single_user_callback') );

		add_shortcode( 'nhpa_members_dir_search', ['NHPA_Directory_Search', 'NHPA_Directory_Search_func'] );

		add_action( 'wp_ajax_search_grab_matched_users', array('NHPA_Directory_Search', 'search_grab_matched_users_func') );
    add_action( 'wp_ajax_nopriv_search_grab_matched_users', array('NHPA_Directory_Search', 'search_grab_matched_users_func') );

		add_action('template_redirect', [$this, 'debug_user']);

		add_action( 'wp_enqueue_scripts', array($this, 'map_script') );

		add_shortcode( 'nhpa_user_profile', ['NHPA_User_Profile', 'NHPA_User_Profile_func'] );
		add_shortcode( 'nhpa_map', ['NHPA_Map', 'NHPA_Map_func'] );


		add_action( 'wp_ajax_do_pagination_check', array($this, 'do_pagination_check_callback') );
    add_action( 'wp_ajax_nopriv_do_pagination_check', array($this, 'do_pagination_check_callback') );

		add_action( 'wp_ajax_geocode_location', array('NHPA_Map', 'geoCoderJSON') );
    add_action( 'wp_ajax_nopriv_geocode_location', array('NHPA_Map', 'geoCoderJSON') );


		add_shortcode( 'nhpa_members_dir_psychologist', ['NHPA_Directory_Search_psychologist', 'nhpa_members_dir_func'] );

  }

	public function map_script() {


    wp_register_script( 'pmpro-nhpa-map-script', 'http://maps.google.com/maps/api/js?key=AIzaSyBQa0l6mp5VWIqyWwEyDsu_x1QG0vwsutc', array( 'jquery' ), '', true );

    //wp_localize_script( 'pmpro-nhpa-map-script', 'nhpa_plugin_map_data', array( 'ajax_url' => admin_url('admin-ajax.php'), 'pmpro_nhpa_PLUGIN_URL' => pmpro_nhpa_PLUGIN_URL ));

		wp_register_script( 'pmpro-nhpa-gmaps-script', pmpro_nhpa_PLUGIN_URL.'js/gmaps.js', array( 'jquery' ), '', true );
		wp_register_script( 'pmpro-nhpa-gmaps-script-custom', pmpro_nhpa_PLUGIN_URL.'js/map_custom.js', array( 'jquery' ), '', true );
		wp_localize_script( 'pmpro-nhpa-gmaps-script-custom', 'nhpa_plugin_map_custom_data', array( 'ajax_url' => admin_url('admin-ajax.php'), 'pmpro_nhpa_PLUGIN_URL' => pmpro_nhpa_PLUGIN_URL ));

    wp_enqueue_script( 'pmpro-nhpa-map-script' );
		wp_enqueue_script( 'pmpro-nhpa-gmaps-script' );
		wp_enqueue_script( 'pmpro-nhpa-gmaps-script-custom' );

  }

	  public function request_detail_single_user_callback() {

	    if(empty($_POST['user_id']))
	      wp_die();

	      //$html_detail = self::get_single_profile_detail($_POST);

			ob_start();

			_e(do_shortcode('[nhpa_user_profile user_id="'.$_POST['user_id'].'"]'));

	    $output = ob_get_clean();


	    echo json_encode($output);


	    wp_die();

	  }

	public function debug_user() {

		if (empty($_GET['show_user_data']))
			return;

			global $wpdb;

		// if (!current_user_can( 'manage_options' ))
		// 	return;

			$user_id = (int) $_GET['show_user_data'];



		//d(pmpro_hasMembershipLevel(9 , $user_id));


		//d(get_user_meta($user_id));

		//d(pmpro_hasMembershipLevel(14 , $user_id));

		//d(self::get_single_profile_basic($user_id));
		//d(self::get_single_profile_detail( ['user_id' => $user_id] ));


		$user_data = self::get_filtered_user_meta($user_id);
		d($user_data);

		//$nhregion = ( empty($user_data['nhregion'][0]) ? "" : maybe_unserialize($user_data['nhregion'][0]) );
		//d($nhregion);why_joining2
		//d(NHPA_Directory_Search::generate_select_array_auto('_line2'));
		d(NHPA_Directory_Search::generate_select_array_auto('why_joining2'));

		//echo self::get_single_profile_detail(['user_id' => $user_id]);
		//$avatar_id = ( empty($user_data[$wpdb->get_blog_prefix().'user_avatar']) ? "" : $user_data[$wpdb->get_blog_prefix().'user_avatar'] );
		//d($user_data);

		wp_die("", "show_user_data");
	}

	public static function get_filtered_user_meta($user_id = null) {

		if (empty($user_id))
			$user_id = get_current_user_id();

			$user_data = ( empty(get_user_meta($user_id)) ? array() : get_user_meta($user_id) );

			if (current_user_can( 'manage_options' ))
				return $user_data;



			$current_user_id = get_current_user_id();

			$titan = TitanFramework::getInstance( 'pmpro_nhpa_opts' );

			$restrict_struct = $titan->getOption( 'dir_restrict_member_data' );

			if ( empty($restrict_struct) )
				return $user_data;

			$restrict_struct = explode(PHP_EOL, $restrict_struct);

			$restrict_struct =  ( is_array($restrict_struct) ? $restrict_struct : [] );

			$store_meta = [];

			foreach ($restrict_struct as $key => $single_structure) {
				$single_structure = explode("|", $single_structure);

				if (empty($single_structure[0]) || empty($single_structure[1]))
					continue;

				$membership_level = (int) $single_structure[1];

				if (pmpro_hasMembershipLevel($membership_level , $current_user_id))
					$store_meta[] = [ 'meta' => $single_structure[0] , 'err' => ( empty($single_structure[2]) ? "" : $single_structure[2]) ];

			}

			if (empty($store_meta))
				return $user_data;

				foreach ($store_meta as $key => $single_meta_info) {

					if (ctype_space($single_meta_info['err']) || empty($single_meta_info['err']))
						unset($user_data[$single_meta_info['meta']]);
					else
						$user_data[$single_meta_info['meta']] = $single_meta_info['err'];

				}

			return $user_data;
	}



  public function get_single_basic_profile_callback() {

    if (empty($_POST['ID']))
      echo json_encode("");

      $html_single = self::get_single_profile_basic($_POST['ID']);

      echo json_encode($html_single);

      wp_die();

  }

  public function get_nhpa_users_id_callback() {

		$titan = TitanFramework::getInstance( 'pmpro_nhpa_opts' );
		$directory_page_id = ( empty($titan->getOption( 'page_id_for_filter' )) ? "" : $titan->getOption( 'page_id_for_filter' ) );

    $number = ( empty($_POST['limit']) ? "" : $_POST['limit'] );
    $offset = ( empty($_POST['limit']) ? "" : $_POST['limit'] );
		$page = ( empty($_POST['page']) ? "" : $_POST['page'] );
		$wp_page_id = ( empty($_POST['wp_page_id']) ? "" : $_POST['wp_page_id'] );

		$offset = ( $page - 1 ) * $offset;

		$wp_page_id = (int) $wp_page_id;
		$directory_page_id = (int) $directory_page_id;

		if ($wp_page_id !== $directory_page_id) {
			echo json_encode([ 'users' => get_users([ 'number' => $number, 'offset' => $offset,'fields' => ['ID'], 'orderby' => 'registered' ]), 'total' => count_users(), 'total_get_users' => get_users(), 'offset' => $offset ]);
			wp_die();
		}


		$the_users = get_users([ 'fields' => ['ID'], 'orderby' => 'registered' ]);


		$the_users = ( is_array($the_users) ? $the_users : array() );

		//echo json_encode($the_users);

		$filtered_user_id = array();

		$exclude_membership_levels = ( empty($titan->getOption( 'exclude_membership_levels' )) ? "" : $titan->getOption( 'exclude_membership_levels' ) );
		$exclude_membership_levels = explode(",", $exclude_membership_levels);
		//if (pmpro_hasMembershipLevel($membership_level , $current_user_id))
		$exclude_membership_levels = ( is_array($exclude_membership_levels) ? $exclude_membership_levels : array() );
		$exclude_membership_levels = array_map('intval', $exclude_membership_levels);


		foreach ($the_users as $key => $user_id) {

			$user_id = (int) $user_id->ID;

			$user_id_not_exists = 0;

			$user_id_not_exists = self::user_is_not_specified_member($exclude_membership_levels, $user_id);


			if ($user_id_not_exists)
				$filtered_user_id[] = $user_id;


		}

		//echo json_encode($filtered_user_id);

		$filtered_user_id = array_unique($filtered_user_id);
		$filtered_user_id_without_offset = get_users([ 'exclude' => $filtered_user_id, 'fields' => ['ID'], 'orderby' => 'registered' ]);

		$filtered_user_id = get_users([ 'number' => $number, 'exclude' => $filtered_user_id, 'offset' => $offset,'fields' => ['ID'], 'orderby' => 'registered' ]);

		//echo json_encode(count(get_users()));

		//echo json_encode(count($filtered_user_id_without_offset));

		echo json_encode([ 'users' => $filtered_user_id, 'total' => count_users(), 'total_get_users' => $filtered_user_id_without_offset, 'offset' => $offset ]);



    wp_die();

  }


	public function do_pagination_check_callback() {

		if (empty($_POST['page_id']) || empty($_POST['count']))
			return;

			$titan = TitanFramework::getInstance( 'pmpro_nhpa_opts' );
			$directory_page_id = ( empty($titan->getOption( 'page_id_for_filter' )) ? "" : $titan->getOption( 'page_id_for_filter' ) );

			$wp_page_id = ( empty($_POST['wp_page_id']) ? "" : $_POST['wp_page_id'] );

			$wp_page_id = (int) $wp_page_id;
			$directory_page_id = (int) $directory_page_id;


			$number = ( empty($_POST['count']) ? "" : $_POST['count'] );

			$offset = ( empty($_POST['count']) ? "" : $_POST['count'] );
			$page = ( empty($_POST['page_id']) ? "" : $_POST['page_id'] );

			$offset = ( $page - 1 ) * $offset;


			if ($wp_page_id !== $directory_page_id) {
				echo json_encode(count(get_users([ 'number' => $number, 'offset' => $offset,'fields' => ['ID'], 'orderby' => 'registered' ])));
				wp_die();
			}

			$the_users = get_users([ 'fields' => ['ID'], 'orderby' => 'registered' ]);


			$the_users = ( is_array($the_users) ? $the_users : array() );

			//echo json_encode($the_users);

			$filtered_user_id = array();

			$exclude_membership_levels = ( empty($titan->getOption( 'exclude_membership_levels' )) ? "" : $titan->getOption( 'exclude_membership_levels' ) );
			$exclude_membership_levels = explode(",", $exclude_membership_levels);
			//if (pmpro_hasMembershipLevel($membership_level , $current_user_id))
			$exclude_membership_levels = ( is_array($exclude_membership_levels) ? $exclude_membership_levels : array() );
			$exclude_membership_levels = array_map('intval', $exclude_membership_levels);


			foreach ($the_users as $key => $user_id) {

				$user_id = (int) $user_id->ID;

				$user_id_not_exists = 0;

				$user_id_not_exists = self::user_is_not_specified_member($exclude_membership_levels, $user_id);


				if ($user_id_not_exists)
					$filtered_user_id[] = $user_id;


			}

			//echo json_encode($filtered_user_id);

			$filtered_user_id = array_unique($filtered_user_id);

			$filtered_user_id = get_users([ 'number' => $number, 'exclude' => $filtered_user_id, 'offset' => $offset,'fields' => ['ID'], 'orderby' => 'registered' ]);

			echo json_encode(count($filtered_user_id));



			wp_die();

	}


	  public function wp_footer_test() {

	    //d(self::user_is_not_specified_member([3,14], 340));
	  }

	public function user_is_not_specified_member($membership_levels = NULL, $user_id = NULL) {

		if (empty($membership_levels) || empty($user_id))
			return 0;

		$user_id = (int) $user_id;

		foreach ($membership_levels as $key => $membership_level) {

			if (pmpro_hasMembershipLevel($membership_level, $user_id))
				return 0;


		}

		return 1;

	}

  public function nhpa_members_dir_func($atts) {

    $atts = shortcode_atts( array(
      'limit' => '10',
    ), $atts, 'nhpa_members_dir' );

    //$html_single = self::get_single_profile_basic(379);

		if (!empty($_GET['user_id'])) {

			$user_id = (int) $_GET['user_id'];

			return do_shortcode('[nhpa_user_profile user_id="'.$user_id.'"]');

		}

		$titan = TitanFramework::getInstance( 'pmpro_nhpa_opts' );
		$titan_ajax = $titan->getOption( 'enable_dir_ajax' );

		$html = "";

			if ($titan_ajax)
				$html = $this->return_for_ajax($atts, $user_id, $_GET);
			else
				$html = $this->return_for_php($atts, $user_id, $_GET);


      return $html;

  }

	public function return_for_php($atts = null, $user_id = null, $get = null) {

			ob_start();

			include pmpro_nhpa_PLUGIN_DIR."template".DS."php_dir.php";

			$output = ob_get_clean();
      return $output;

	}

	public function return_for_ajax($atts = null, $user_id = null, $get = null) {

		$_GET = $get;

		$html = "";

    $html .= "<div class='load_nhpa_pmpro_members' data-wp_page_id='".get_the_ID()."' data-limit='".$atts['limit']."'>";
    $html .= '<div class="container"><div class="row block_input">';
    $html .=  '<div class="col-sm-12"><div class="single_member_profile_load"><img src="'.pmpro_nhpa_PLUGIN_URL.'img/ajax-loader.gif'.'"></div></div>';
    // $html .=  '<div class="col-sm-12"><div class="single_member_profile">'.$html_single.'</div></div>';
    // $html .=  '<div class="col-sm-12"><div class="single_member_profile">'.$html_single.'</div></div>';
		$current_page_id = (int) ( empty($_GET['nhpa_page']) ? 1 : $_GET['nhpa_page'] );
    $html .= '</div></div>';
    $html .= '<div class="container"><div class="row navigate_dir" >';

		//$html .= '       <div class="col-xs-2 preList"><a href="#">Previous</a></div>    <div class="col-xs-2 nextList"><a href="?get_next=">Next</a></div></div></div>';
		$html .= '    <div class="col-xs-12">';
		//<a href="?get_next=">Next</a>

		$cpi_2 = $current_page_id-2;
		$cpi_1 = $current_page_id-1;


		if ( $cpi_2 > 0 )
			$html .= '<a href="?get_next='.$atts['limit'].'&nhpa_page='.$cpi_2.'">'.$cpi_2.'</a>';

			if ( $cpi_1 > 0 )
				$html .= '<a href="?get_next='.$atts['limit'].'&nhpa_page='.$cpi_1.'">'.$cpi_1.'</a>';



		$html .= '<a class="cpid" href="?get_next='.$atts['limit'].'&nhpa_page='.$current_page_id.'">'.$current_page_id.'</a>';

		//$html .= '<a href="?get_next='.$atts['limit'].'&nhpa_page='.($current_page_id+1).'">'.($current_page_id+1).'</a>';
		//$html .= '<a href="?get_next='.$atts['limit'].'&nhpa_page='.($current_page_id+2).'">'.($current_page_id+2).'</a>';
		//$html .= '<a href="?get_next='.$atts['limit'].'&nhpa_page='.($current_page_id+3).'">'.($current_page_id+3).'</a>';


		$html .= '</div>   </div></div>';
    $html .= "</div>";

		return $html;

	}

  public static function get_single_profile_basic($user_id = null) {

    if (empty($user_id))
      return;

    global $wpdb;
    $titan = TitanFramework::getInstance( 'pmpro_nhpa_opts' );

		$titan_ajax = $titan->getOption( 'enable_dir_ajax' );

    $uid = $user_id;
    $user_data = self::get_filtered_user_meta($uid);

		$avatar_id = ( empty($user_data[$wpdb->get_blog_prefix().'user_avatar']) ? "" : $user_data[$wpdb->get_blog_prefix().'user_avatar'] );

		$avatar_id = ( ( is_array($avatar_id) && !empty($avatar_id[0]) ) ? $avatar_id[0] : ""  );

		$image_url = wp_get_attachment_url($avatar_id);

  	$user_instituion = ( empty($user_data['institutiongraduatedfrom'][0]) ? $user_data['institution'][0] : $user_data['institutiongraduatedfrom'][0] );
    $image_url = ( empty($image_url) ? pmpro_nhpa_PLUGIN_URL.'img/propic.png' : $image_url );
    $user_bio = ( empty($user_data['description'][0]) ? "" : $user_data['description'][0] );
    $user_bio = ( empty($titan->getOption( 'nhpa_bio_limit_word' )) ? $user_bio : wp_trim_words($user_bio, $titan->getOption( 'nhpa_bio_limit_word' )) );
    //$avatar_id = get_user_meta($uid, 'profession', true);

    $user_profession = ( empty($user_data['profession'][0]) ? "" : $user_data['profession'][0] );
    $user_degree = ( empty($user_data['degree'][0]) ? "" : $user_data['degree'][0] );
    $user_phone_meta =  $titan->getOption( 'user_phone_meta' );
    $user_phone = ( empty($user_phone_meta) ? "" : $user_data[$user_phone_meta][0]);
    $user_homeaddress = ( empty($user_data['homeaddress'][0]) ? "" : $user_data['homeaddress'][0]);
    $user_homecityaddress = ( empty($user_data["homecityaddress"][0]) ? "" : $user_data["homecityaddress"][0]);
		$nhregion = ( empty($user_data['nhregion'][0]) ? "" : maybe_unserialize($user_data['nhregion'][0]) );

		if (!empty($nhregion))
			$nhregion = implode(" , ", $nhregion);

	  // d($user_data);
    // d($image_url);

    $html_single = '<div class="single_member_profile">
<div class="container">
    <div class="row">
        <div class="col-xs-3"><img data-uid="'.$user_id.'" src="'.$image_url.'" class="nhpa_profile_avatar"></div>
        <div class="col-xs-5">

          <div class="full_name"><strong>'.$user_data['first_name'][0].' '.$user_data['last_name'][0].'</strong></div>
          <div class="user_pro_degree"><span><strong>'.$user_profession.'</strong></span>'. ( empty($user_degree) ? "" : "," ) .' <span><strong>'.$user_degree.'</strong></span></div>
					<div class="user_pro_degree"><span><strong>'.$user_data['_line1'][0].'</strong></span>'. ( empty($user_data['city'][0]) ? "" : "," ) .' <span><strong>'.$user_data['city'][0].'</strong></span></div>
					<div class="user_pro_degree"><span><strong>'.$nhregion.'</strong></span></div>

          <!--<div class="user_bio"><strong>'.$user_bio.'</strong></div>-->

        </div>
        <div class="col-xs-4">

        <div class="user_phone"><strong>'.$user_phone.'</strong></div>
				<div class="user_address">'.$user_data['_state'][0].'</div>
				<div class="user_address">'.$user_homeaddress. ( empty($user_homecityaddress) ? "" : ", " ) .$user_homecityaddress.'</div>

';

if (!empty($titan_ajax))
	$html_single .= '<div class="view_profile"><button data-user="'.$uid.'" type="button" class="btn btn-primary btn-sm">'.__("View Profile", "pmpro_nhpa").'</button></div>';
else
	$html_single .= '<a href="'.'?user_id='.$uid.'"><div class="view_profile_link"><button data-user="'.$uid.'" type="button" class="btn btn-primary btn-sm">'.__("View Profile", "pmpro_nhpa").'</button></div></a>';

$html_single .= '


        </div>

    </div>
</div></div>';

      return $html_single;
  }

  public static function get_single_profile_detail($post_data = null) {

    if (empty($post_data['user_id']))
      return;

      ob_start();

      $user_id = $post_data['user_id'];
      $uid = $user_id;
    global $wpdb;

    $user_data = self::get_filtered_user_meta($uid);

    //$avatar_id = get_user_meta($uid, $wpdb->get_blog_prefix().'user_avatar', true);
		$avatar_id = ( empty($user_data[$wpdb->get_blog_prefix().'user_avatar']) ? "" : $user_data[$wpdb->get_blog_prefix().'user_avatar'] );

    $image_url = wp_get_attachment_url($avatar_id);
    $image_url = ( empty($image_url) ? pmpro_nhpa_PLUGIN_URL.'img/propic.png' : $image_url );

    $titan = TitanFramework::getInstance( 'pmpro_nhpa_opts' );

    //include pmpro_nhpa_PLUGIN_DIR."template".DS."single_detail.php";

		_e(do_shortcode('[nhpa_user_profile user_id="'.$user_id.'"]'));


    $output = ob_get_clean();

    return $output;

  }



  public function load_custom_wp_frontend_style() {

		$titan = TitanFramework::getInstance( 'pmpro_nhpa_opts' );
		$titan_ajax = $titan->getOption( 'enable_dir_ajax' );

    wp_register_script( 'pmpro-nhpa-script', pmpro_nhpa_PLUGIN_URL.'js/script_custom.js', array( 'jquery' ), '', true );


    wp_localize_script( 'pmpro-nhpa-script', 'nhpa_plugin_data', array( 'ajax_url' => admin_url('admin-ajax.php'), 'pmpro_nhpa_PLUGIN_URL' => pmpro_nhpa_PLUGIN_URL, 'enable_disable_ajax' => $titan_ajax ));

    wp_enqueue_script( 'pmpro-nhpa-script' );

    wp_enqueue_style( 'pmpro-nhpa-script-style', pmpro_nhpa_PLUGIN_URL.'css/style.css' );

  }



}


 ?>
