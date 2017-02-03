<div class="map_template">

  <div class="container" style="width: 100%; height: <?php _e($atts['height']); ?>">
      <div class="row" style="height: inherit">
          <div class="col-sm-8 nhpa_map_page" id="map_canvas_nhpa"></div>
          <div class="col-sm-4" id="map_canvas_list_data" data-initial_load="<?php _e( (!empty($atts['initial_load']) ? $atts['initial_load'] : 10 ) ); ?>" data-locate_user_pos="<?php _e( (!empty($atts['locate_user_pos']) ? $atts['locate_user_pos'] : 0 ) ); ?>" data-initial_address="<?php _e( (!empty($atts['initial_address']) ? $atts['initial_address'] : ( !(empty($user_address_array[0])) ? $user_address_array[0]['address'] : "" ) ) ); ?>">

<?php

$user_address_array = ( is_array($user_address_array) ? $user_address_array : [] );

foreach ($user_address_array as $key => $single_user_address_array) {
?>

<div class="single_address" data-geocode="<?php _e( ($single_user_address_array['geocode'] == 1) ? '' : $single_user_address_array['geocode']['lat'].'|'.$single_user_address_array['geocode']['long'] ); ?>" data-addr="<?php _e($single_user_address_array['address']); ?>" data-uid="<?php _e($single_user_address_array['id']); ?>" style="">

<?php //_e($single_user_address_array['address']);

$sidebar_meta = explode("|", $atts['sidebar_meta']);



foreach ($sidebar_meta as $key => $single_sidebar_meta) {

  $single_sidebar_meta = explode(",", $single_sidebar_meta);

  if (empty($single_sidebar_meta[0]) || empty($single_sidebar_meta[1]))
    continue;

    $single_sidebar_meta_field = trim($single_sidebar_meta[1]);
    $single_sidebar_meta_title = trim($single_sidebar_meta[0]);

    if (strcmp($single_sidebar_meta_field, "name") === 0) {

      $html = "";


      $single_sidebar_meta_value = get_user_meta($single_user_address_array['id'], 'first_name', true) . " " . get_user_meta($single_user_address_array['id'], 'last_name', true);

      $html .= $single_sidebar_meta_title . " : " . $single_sidebar_meta_value . "<br>";

      echo $html;

    } elseif (strcmp($single_sidebar_meta_field, "address") === 0) {

      $html = "";

      $html .= $single_sidebar_meta_title . " : " . $single_user_address_array['address'] . "<br>";

      echo $html;

    } else {

      $html = "";

      $single_sidebar_meta_value = get_user_meta($single_user_address_array['id'], $single_sidebar_meta_field, true);

      if (empty($single_sidebar_meta_value))
        continue;

      $html .= $single_sidebar_meta_title . " : " . $single_sidebar_meta_value . "<br>";

      echo $html;


    }

}

?>

</div>


<?php
}

 ?>


          </div>
      </div>
  </div>

</div>
