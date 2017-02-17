<?php

if(!defined('WPINC')) // MUST have WordPress.
	exit('Do NOT access this file directly: '.basename(__FILE__));

/**
 * Directory Search Psychologist
 */
class NHPA_Directory_Search_psychologist {

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

		$html = self::return_for_php($atts, $user_id, $_GET);


      return $html;

  }

  private static function return_for_php($atts = null, $user_id = null, $get = null) {

      ob_start();

      include pmpro_nhpa_PLUGIN_DIR."template".DS."php_dir_psychologist.php";

      $output = ob_get_clean();
      return $output;

  }





}
