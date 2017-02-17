<?php

if(!defined('WPINC')) // MUST have WordPress.
	exit('Do NOT access this file directly: '.basename(__FILE__));
  $titan = TitanFramework::getInstance( 'pmpro_nhpa_opts' );

  //

  $_POST = $_GET;
  $_POST['limit'] = $atts['limit'];
  $_POST['page'] = $_POST['nhpa_page'];

  $number = ( empty($_POST['limit']) ? "" : $_POST['limit'] );
  $offset = ( empty($_POST['limit']) ? "" : $_POST['limit'] );
  $page = ( empty($_POST['page']) ? 1 : $_POST['page'] );
  $wp_page_id = ( empty($_POST['wp_page_id']) ? "" : $_POST['wp_page_id'] );
  $offset = ( $page - 1 ) * $offset;

  //
  $users = get_users([ 'number' => $number, 'offset' => $offset,'fields' => ['ID'], 'orderby' => 'registered' ]);
  //$users = get_users([ 'fields' => ['ID'], 'orderby' => 'registered' ]);
  $directory_page_id = ( empty($titan->getOption( 'page_id_for_filter' )) ? "" : $titan->getOption( 'page_id_for_filter' ) );

  $_GET = $get;

  $html = "";

  $html .= "<div class='load_nhpa_pmpro_members' data-wp_page_id='".get_the_ID()."' data-limit='".$atts['limit']."'>";

	$html .= "<div class='container searchParams'>";
	$html .= "<div class='row'>Total members : ".count(get_users())."</div>";
	$html .= "<div class='row'>Showing from : ".($offset+1)."</div>";
	$html .= "</div>";


  $html .= '<div class="container"><div class="row block_input">';

  foreach ($users as $key => $user) {
    $html_single = self::get_single_profile_basic($user->ID);

    $html .= '<div class="col-sm-12">'.$html_single.'</div>';

  }

  $html .=  '<div class="col-sm-12"><div class="single_member_profile_load">';


  $html .=  '</div></div>';
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

  echo $html;
 ?>
