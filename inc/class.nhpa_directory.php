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

    //add_action( 'wp_footer', array($this, 'wp_footer_test') );

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

  }

	public function debug_user() {

		if (empty($_GET['show_user_data']))
			return;

		if (!current_user_can( 'manage_options' ))
			return;

			$user_id = (int) $_GET['show_user_data'];

		d(get_user_meta($user_id));
		wp_die("", "show_user_data");
	}


  public function request_detail_single_user_callback() {

    if(empty($_POST['user_id']))
      wp_die();

      $html_detail = self::get_single_profile_detail($_POST);

      echo json_encode($html_detail);


    wp_die();

  }


  public function get_single_basic_profile_callback() {

    if (empty($_POST['ID']))
      echo json_encode("");

      $html_single = self::get_single_profile_basic($_POST['ID']);

      echo json_encode($html_single);

      wp_die();

  }

  public function get_nhpa_users_id_callback() {

    $number = ( empty($_POST['limit']) ? "" : $_POST['limit'] );
    $offset = ( empty($_POST['offset']) ? "" : $_POST['offset'] );

    echo json_encode(get_users([ 'number' => $number, 'offset' => $offset,'fields' => ['ID'], 'orderby' => 'registered' ]));
    wp_die();

  }

  public function nhpa_members_dir_func($atts) {

    $atts = shortcode_atts( array(
      'limit' => '10',
    ), $atts, 'nhpa_members_dir' );

    //$html_single = self::get_single_profile_basic(379);

    $html = "";

    $html .= "<div class='load_nhpa_pmpro_members' data-limit='".$atts['limit']."'>";
    $html .= '<div class="container"><div class="row block_input">';
    $html .=  '<div class="col-sm-12"><div class="single_member_profile_load"><img src="'.pmpro_nhpa_PLUGIN_URL.'img/ajax-loader.gif'.'"></div></div>';
    // $html .=  '<div class="col-sm-12"><div class="single_member_profile">'.$html_single.'</div></div>';
    // $html .=  '<div class="col-sm-12"><div class="single_member_profile">'.$html_single.'</div></div>';


    $html .= '</div></div>';
    $html .= '<div class="container"><div class="row navigate_dir">        <div class="col-xs-2 preList"><a href="#">Previous</a></div>
        <div class="col-xs-2 nextList"><a href="?get_next=">Next</a></div></div></div>';
    $html .= "</div>";


      return $html;

  }

  public static function get_single_profile_basic($user_id = null) {

    if (empty($user_id))
      return;

    global $wpdb;
    $titan = TitanFramework::getInstance( 'pmpro_nhpa_opts' );

    $uid = $user_id;
    $user_data = get_user_meta($uid);
    $avatar_id = get_user_meta($uid, $wpdb->get_blog_prefix().'user_avatar', true);
    $image_url = wp_get_attachment_url($avatar_id);
    $user_instituion = ( empty($user_data['institutiongraduatedfrom'][0]) ? $user_data['institution'][0] : $user_data['institutiongraduatedfrom'][0] );
    $image_url = ( empty($image_url) ? pmpro_nhpa_PLUGIN_URL.'img/propic.png' : $image_url );
    $user_bio = ( empty($user_data['description'][0]) ? "" : $user_data['description'][0] );
    $user_bio = ( empty($titan->getOption( 'nhpa_bio_limit_word' )) ? $user_bio : wp_trim_words($user_bio, $titan->getOption( 'nhpa_bio_limit_word' )) );
    $avatar_id = get_user_meta($uid, 'profession', true);
    $user_profession = ( empty($user_data['profession'][0]) ? "" : $user_data['profession'][0] );
    $user_degree = ( empty($user_data['degree'][0]) ? "" : $user_data['degree'][0] );
    $user_phone_meta =  $titan->getOption( 'user_phone_meta' );
    $user_phone = ( empty($user_phone_meta) ? "" : $user_data[$user_phone_meta][0]);
    $user_homeaddress = ( empty($user_data['homeaddress'][0]) ? "" : $user_data['homeaddress'][0]);
    $user_homecityaddress = ( empty($user_data["homecityaddress"][0]) ? "" : $user_data["homecityaddress"][0]);

    // d($user_data);
    // d($image_url);

    $html_single = '<div class="single_member_profile">
<div class="container">
    <div class="row">
        <div class="col-xs-3"><img data-uid="'.$user_id.'" src="'.$image_url.'" class="nhpa_profile_avatar"></div>
        <div class="col-xs-5">

          <div class="full_name"><strong>'.$user_data['first_name'][0].' '.$user_data['last_name'][0].'</strong></div>
          <div class="user_pro_degree"><span><strong>'.$user_profession.'</strong></span>, <span><strong>'.$user_degree.'</strong></span></div>
          <div class="user_bio"><strong>'.$user_bio.'</strong></div>

        </div>
        <div class="col-xs-4">

        <div class="user_phone"><strong>'.$user_phone.'</strong></div>
        <div class="user_address">'.$user_homeaddress.', '.$user_homecityaddress.'</div>
        <div class="view_profile"><button data-user="'.$uid.'" type="button" class="btn btn-primary btn-sm">'.__("View Profile", "pmpro_nhpa").'</button></div>


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

    $user_data = get_user_meta($uid);

    $avatar_id = get_user_meta($uid, $wpdb->get_blog_prefix().'user_avatar', true);
    $image_url = wp_get_attachment_url($avatar_id);
    $image_url = ( empty($image_url) ? pmpro_nhpa_PLUGIN_URL.'img/propic.png' : $image_url );

    $titan = TitanFramework::getInstance( 'pmpro_nhpa_opts' );

    include pmpro_nhpa_PLUGIN_DIR."template".DS."single_detail.php";

    $output = ob_get_clean();

    return $output;

  }



  public function wp_footer_test() {

    d(get_user_meta(379));
  }

  public function load_custom_wp_frontend_style() {

    wp_register_script( 'pmpro-nhpa-script', pmpro_nhpa_PLUGIN_URL.'js/script_custom.js', array( 'jquery' ), '', true );

    wp_localize_script( 'pmpro-nhpa-script', 'nhpa_plugin_data', array( 'ajax_url' => admin_url('admin-ajax.php') ));

    wp_enqueue_script( 'pmpro-nhpa-script' );

    wp_enqueue_style( 'pmpro-nhpa-script-style', pmpro_nhpa_PLUGIN_URL.'css/style.css' );

  }

}


 ?>
