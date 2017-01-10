<?php

if (!defined('ABSPATH'))
  exit;


add_action( 'tf_create_options', 'wp_expert_custom_options_pmpro_nhpa_opts', 150 );

function wp_expert_custom_options_pmpro_nhpa_opts() {


	$titan = TitanFramework::getInstance( 'pmpro_nhpa_opts' );
	$section = $titan->createAdminPanel( array(
		    'name' => __( 'NHPA Members Directory Options', 'pmpro_nhpa' ),
		    'icon'	=> 'dashicons-networking'
		) );

	$tab = $section->createTab( array(
    		'name' => 'General Options'
		) );

    $tab->createOption([
      'name' => 'Member Biography Word Limit',
      'id' => 'nhpa_bio_limit_word',
      'type' => 'text',
      'desc' => ' Maximum words to show.'
      ]);

      $tab->createOption([
        'name' => 'Member Directory Phone To Display',
        'id' => 'user_phone_meta',
        'type' => 'text',
        'desc' => 'Values - office_phone , homephone , preferredphone , phone',
        'default' => 'office_phone'
        ]);

        $tab->createOption([
          'name' => 'Search Fields To Show',
          'id' => 'dir_search_fields',
          'type' => 'textarea',
          'desc' => 'Text: text|title|meta_field</br>Dropdown: select|title|meta_field|value1,value2,value3 <strong><u>OR</u></strong> dropdown|title|meta_field|auto <br/>Please put options in separate lines.',
          'default' => ''
          ]);

        $tab->createOption([
          'name' => 'Member Data To Restrict',
          'id' => 'dir_restrict_member_data',
          'type' => 'textarea',
          'desc' => 'meta_field|pmpro_level_id|error_message<br/>meta_field : User meta field need to be restricted, pmpro_level_id : PMPro level ID that should not see the data, error_message: any error message, empty to display nothing.',
          'default' => ''
          ]);

        $dir_single_member_profile_html = "section_title_any|section_class_any|restriction_level_ids, separated by comma (level_id : if only this level user should be able to view the section , -level id : if only this level user should not be able to view the section, keep empty if this should be visible for all levels)";
        $dir_single_member_profile_html .= "<br>profile_field_title|meta_field";
        $dir_single_member_profile_html .= "<br>profile_field_title|meta_field";
        $dir_single_member_profile_html .= "<br>...";
        $dir_single_member_profile_html .= "<br>profile_field_title|meta_field";
        $dir_single_member_profile_html .= "<br>--section_end--<br>";
        $dir_single_member_profile_html .= "<br><br><b>Example:</b><br>";
        $dir_single_member_profile_html .= "Basic Profile Information|basic_profile";
        $dir_single_member_profile_html .= "<br>First Name|first_name";
        $dir_single_member_profile_html .= "<br>Last Name|last_name";
        $dir_single_member_profile_html .= "<br>Biography|description";
        $dir_single_member_profile_html .= "<br>--section_end--<br>";
        $dir_single_member_profile_html .= "Professional Profile Information|user_pro_profile|3,4,7";
        $dir_single_member_profile_html .= "<br>Profession|profession";
        $dir_single_member_profile_html .= "<br>Degree|degree";
        $dir_single_member_profile_html .= "<br>License Year|yearoflicensure";
        $dir_single_member_profile_html .= "<br>Board Membership|boardmembership";
        $dir_single_member_profile_html .= "<br>--section_end--<br>";
        $dir_single_member_profile_html .= "Professional Profile Information Restricted|user_pro_profile_2|-3,-8,-9";
        $dir_single_member_profile_html .= "<br>Map Location|map|preferredmailingaddress";

        $dir_single_member_profile_html .= "<br>Profession|profession";
        $dir_single_member_profile_html .= "<br>Degree|degree";
        $dir_single_member_profile_html .= "<br>License Year|yearoflicensure";
        $dir_single_member_profile_html .= "<br>Board Membership|boardmembership";
        $dir_single_member_profile_html .= "<br>--section_end--<br>";
        $dir_single_member_profile_html .= "<br><br>Note for map: use this format - Map title|map|meta_field_for_location";


      $tab->createOption([
        'name' => 'Single Profile Structure',
        'id' => 'dir_single_member_profile',
        'type' => 'textarea',
        'desc' => $dir_single_member_profile_html,
        'default' => '',
        'is_code' => true
        ]);




	/*			$tab = $section->createTab( array(
    		'name' => 'Product Field Options'
		) );

		$tab->createOption([
			'name' => 'Availibility Options',
			'id' => 'availability_opts',
			'type' => 'textarea',
			'desc' => 'Availibility Value|Availibility Title'
			]);

		$tab->createOption([
			'name' => 'Durations',
			'id' => 'var_price_opts',
			'type' => 'textarea',
			'desc' => 'Default Durations'
			]);


*/
		$section->createOption( array(
  			  'type' => 'save',
		) );


		/////////////New

/*		$embroidery_sub = $section->createAdminPanel(array('name' => 'Embroidering Pricing'));


		$embroidery_tab = $embroidery_sub->createTab( array(
    		'name' => 'Profiles'
		) );


		$wp_expert_custom_options['embroidery_tab'] = $embroidery_tab;

		return $wp_expert_custom_options;
*/
}


 ?>
