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
