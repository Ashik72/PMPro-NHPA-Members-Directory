<?php

if(!defined('WPINC')) // MUST have WordPress.
  exit('Do NOT access this file directly: '.basename(__FILE__));

/**
 * User Profile
 */
class NHPA_User_Profile
{

  private static $user_id;

  public static function NHPA_User_Profile_func($atts) {

    $atts = shortcode_atts( array(
      'user_id' => get_current_user_id(),
    ), $atts, 'nhpa_user_profile' );

    self::$user_id = $atts['user_id'];
    //global $wpdb;
    $titan = TitanFramework::getInstance( 'pmpro_nhpa_opts' );


    $profile_structure = ( empty($titan->getOption( 'dir_single_member_profile' )) ? "" : $titan->getOption( 'dir_single_member_profile' ) );

    if (empty($profile_structure))
      return;

    ob_start();


    //include pmpro_nhpa_PLUGIN_DIR."template".DS."search_template.php";
    $profile_structure = explode("--section_end--", $profile_structure);
    $profile_structure = array_filter($profile_structure);
    if (empty($profile_structure))
      return;

    $profile_structure = ( is_array($profile_structure) ? $profile_structure : array() );

    $html = "";

    $html .= '<div class="container showUserProfile">';

    $html .= self::attach_image($user_id);


    foreach ($profile_structure as $key => $single_profile_structure) {

      $single_profile_structure = explode(PHP_EOL, $single_profile_structure);

      $single_profile_structure = array_filter($single_profile_structure, 'trim');

      $single_profile_structure = array_values($single_profile_structure);

      $temp_class_section = explode("|", $single_profile_structure[0]);

      $html .= '<div class="container showUserProfileSection '.( empty($temp_class_section[1]) ? "" : $temp_class_section[1] ).'">';

      //$html .= self::Single_Profile_Section_RenderHeader($single_profile_structure[0]);

      $html .= self::Single_Profile_Section_Render($single_profile_structure);

      $html .= "</div>";

    }

    $html .= "</div>";

    _e($html);

    $output = ob_get_clean();

    return $output;

  }

  private static function attach_image($user_id) {

    $user_id = (empty(self::$user_id) ? $user_id : self::$user_id);

    if (empty($user_id))
      return;

      global $wpdb;

      $avatar_id = get_user_meta($user_id, $wpdb->get_blog_prefix().'user_avatar', true);
      $image_url = wp_get_attachment_url($avatar_id);

      $image_url = ( empty($image_url) ? pmpro_nhpa_PLUGIN_URL.'img/propic.png' : $image_url );
      $first_name = get_user_meta($user_id, 'first_name', true);
      $last_name = get_user_meta($user_id, 'last_name', true);
	  $user_info = get_userdata($user_id);
         $email = $user_info->user_email;

      $address = get_user_meta($user_id, 'line1', true);
      $county = get_user_meta($user_id, 'line2', true);
      $city = get_user_meta($user_id, 'city', true);
      $zip = get_user_meta($user_id, 'zip', true);
      $state = get_user_meta($user_id, 'state', true);
      $nhregion = get_user_meta($user_id, 'nhregion', true);
      $phone = get_user_meta($user_id, 'phone', true);
      $cellphone = get_user_meta($user_id, 'cellphone', true);
      $fax = get_user_meta($user_id, 'fax', true);
      $areasofpractice = get_user_meta($user_id, 'areasofpractice', true);
      $approachorientation = get_user_meta($user_id, 'approachorientation', true);
      $populationserved = get_user_meta($user_id, 'populationserved', true);
      $languagesspoken = get_user_meta($user_id, 'languagesspoken', true);
      $accepted_fee_arrangements = get_user_meta($user_id, 'accepted_fee_arrangements', true);
      $yearofgraduation = get_user_meta($user_id, 'yearofgraduation', true);
      $institutiongraduatedfrom = get_user_meta($user_id, 'institutiongraduatedfrom', true);
      $yearoflicensure = get_user_meta($user_id, 'yearoflicensure', true);
      $degree = get_user_meta($user_id, 'degree', true);
      $highest_degree = get_user_meta($user_id, 'highest_degree', true);
      $licensed = get_user_meta($user_id, 'licensed', true);
      $profession = get_user_meta($user_id, 'profession', true);
      $languagesspoken = get_user_meta($user_id, 'languagesspoken', true);
      $preferredphone = get_user_meta($user_id, 'preferredphone', true);
      $preferredmailingaddress = get_user_meta($user_id, 'preferredmailingaddress', true);
      $description = get_user_meta($user_id, 'description', true);
      $website = get_user_meta($user_id, 'website', true);
      $boardmembership = get_user_meta($user_id, 'boardmembership', true);
     // $city = get_user_meta($user_id, 'city', true);

    global $current_user;
    $current_user->membership_level = pmpro_getMembershipLevelForUser($user_id);
    $member_level_id =   $current_user->membership_level->id;

$originationURL = (isset($_POST['prePage']) && !empty($_POST['prePage'])) ? $_POST['prePage'] : "a-default-page.php";

$a = $originationURL;

if (strpos($a, 'find-a-psychologist') !== false) {
    $pageis = 'find';
}

 $regular_members = array('3','21','20','13','24','25','4','26','27','5','30','31','6','34','35','7','8','9','10','11','12');
 $psycho_members = array('3','21','20','13','24','25','4','26','27','5','30','31','6','34','35');

 $upgrade_members = array('14','23','22','15','28','29','17','32','33','18','36','37','19');


// = pmpro_getMembershipLevelForUser($user_id);
      $html_pic = "";

$html_pic .= '<div class="container member">
        <div class="row">';




if($pageis == 'find'){   // Find A Psycho

  if (in_array($member_level_id, $regular_members)) :
       $html_pic .= '
        <div class="col-sm-3">
            <img data-uid="'.$user_id.'" src="'.$image_url.'" class="nhpa_profile_avatar img-responsive">
        </div>
        <div class="col-sm-9 namediv">
          <h2> '. $first_name .' '. $last_name .'</h2>
          <p><strong>Profession:</strong> '. $profession . '</p>

          <p><strong>Description</strong>:<br>'. $description .'</p>
        </div>';

       $html_pic .= '<div class="col-sm-9 col-sm-offset-3"><h4 class="separatordiv">Business Address & Professional Information</h4></div>';
     //endif;


   //  if($address || $state || $city) :

      $html_pic .= '
      <div class="col-sm-9 col-sm-offset-3 addresdiv">
        <p><strong>Work County:</strong> <br> '. $county .' </p>
      </div>';

   //  endif;

   //  if($nhregion) :
      $html_pic .= '
      <div class="col-sm-9 col-sm-offset-3 phonediv">
         <p><strong>Work Phone:</strong>  ' . $phone . '</p>
      </div>';

   //  endif;

  elseif (in_array($member_level_id, $upgrade_members)) :


    // if($first_name || $last_name) :
        $html_pic .= '
        <div class="col-sm-3">
            <img data-uid="'.$user_id.'" src="'.$image_url.'" class="nhpa_profile_avatar img-responsive">
        </div>
        <div class="col-sm-9 namediv">
          <h2> '. $first_name .' '. $last_name .'</h2>
          <p><strong>Profession:</strong> '. $profession . '</p>

          <p><strong>Description</strong>:<br>'. $description .'</p>
        </div>';

      //endif;
       $html_pic .= '<div class="col-sm-9 col-sm-offset-3"><h4 class="separatordiv">Business Address & Professional Information</h4></div>';

     // if($address || $county || $state || $city || $zip) :
        $html_pic .= '
        <div class="col-sm-9 col-sm-offset-3 addresdiv">
          <div class="row">
            <div class="col-md-6">
              <p><strong>Work Address:</strong> <br>' . $address .'<br>' . $city . ', ' . $state .', '. $zip .'</p>
            </div>
            <div class="col-md-6">
                <p><strong>Work Phone:</strong>  ' . $phone . '</p>
                <p><strong>Fax:</strong>  ' . $fax . '</p>
                <p><strong>Email:</strong>  ' . $email . '</p>
            </div>
          </div>
        </div>';
     //endif;

      //if($nhregion) :

        $html_pic .= '
        <div class="col-sm-9 col-sm-offset-3 countydiv">
          <p><strong>Work County:</strong>  ' . $county . '</p>
        </div>';
      //endif;

       /* $html_pic .= '
        <div class="col-sm-9 col-sm-offset-3 statediv">
          <p><strong>State:</strong>  ' . $state . '</p>
        </div>';

         $html_pic .= '
        <div class="col-sm-9 col-sm-offset-3 zipdiv">
          <p><strong>Postal Code:</strong>  ' . $zip . '</p>
        </div>';*/


        if (is_array($nhregion)) {

          $html_pic .= '
          <div class="col-sm-9 col-sm-offset-3 regiondiv">
           <p><strong>NH Region:</strong> '. $nhregion = implode(', ', $nhregion). ' </p>
          </div>';
        }

        if (is_array($populationserved)) {

          $html_pic .= '
          <div class="col-sm-9 col-sm-offset-3 populationserveddiv">
           <p><strong>Population Served:</strong> '. $populationserved = implode(', ', $populationserved). ' </p>
          </div>';
        }

        if (is_array($areasofpractice)) {

          $html_pic .= '
          <div class="col-sm-9 col-sm-offset-3 areasofpracticediv">
           <p><strong>Treatment & Practice Areas:</strong> '. $areasofpractice = implode(', ', $areasofpractice). ' </p>
          </div>';
        }


        if (is_array($accepted_fee_arrangements)) {

          $html_pic .= '
          <div class="col-sm-9 col-sm-offset-3 accepted_fee_arrangementsdiv">
           <p><strong>Accepted Fee arrangements:</strong> '. $accepted_fee_arrangements  = implode(', ', $$accepted_fee_arrangements). ' </p>
          </div>';
        }


          $html_pic .= '
        <div class="col-sm-9 col-sm-offset-3 languagesspokendiv">
          <p><strong>Languages Spoken:</strong>  ' . $languagesspoken . '</p>
        </div>';

  endif;

}else{

    // if($first_name || $last_name) :
        $html_pic .= '
        <div class="col-sm-3">
            <img data-uid="'.$user_id.'" src="'.$image_url.'" class="nhpa_profile_avatar img-responsive">
        </div>
        <div class="col-sm-9 namediv">
          <h2> '. $first_name .' '. $last_name .'</h2>
          <p><strong>Profession:</strong> '. $profession . '</p>

          <p><strong>Description</strong>:<br>'. $description .'</p>
        </div>';

      //endif;
   $html_pic .= '<div class="col-sm-9 col-sm-offset-3"><h4 class="separatordiv">Business Address & Professional Information</h4></div>';
     // if($address || $county || $state || $city || $zip) :
        $html_pic .= '
        <div class="col-sm-9 col-sm-offset-3 addresdiv">
          <div class="row">
            <div class="col-md-6">
              <p><strong>Work Address:</strong> <br>' . $address .'<br>' . $city . ', ' . $state .', '. $zip .'</p>
            </div>
            <div class="col-md-6">
                <p><strong>Work Phone:</strong>  ' . $phone . '</p>
                <p><strong>Fax:</strong>  ' . $fax . '</p>
                <p><strong>Cell Phone:</strong>  ' . $cellphone . '</p>
                <p><strong>Email:</strong>  ' . $email . '</p>
            </div>
          </div>
        </div>



        ';
     //endif;

      //if($nhregion) :

        $html_pic .= '
        <div class="col-sm-9 col-sm-offset-3 countydiv">
          <p><strong>Work County:</strong>  ' . $county . '</p>
        </div>';

        if (is_array($nhregion)) {

          $html_pic .= '
          <div class="col-sm-9 col-sm-offset-3 regiondiv">
           <p><strong>NH Region:</strong> '. $nhregion = implode(', ', $nhregion). ' </p>
          </div>

          <div class="gap"></div>

          ';


        }




          $html_pic .= '
        <div class="col-sm-9 col-sm-offset-3 highest_degreediv">
          <p><strong>Highest Degree:</strong>  ' . $highest_degree . '</p>
        </div>';

          $html_pic .= '
        <div class="col-sm-9 col-sm-offset-3 licenseddiv">
          <p><strong>Are you licensed to practice psychology in NH?:</strong>  ' . $licensed . '</p>
        </div>';

         $html_pic .= '
        <div class="col-sm-9 col-sm-offset-3 professiondiv">
          <p><strong>Profession:</strong>  ' . $profession . '</p>
        </div>';

        /*$html_pic .= '
        <div class="col-sm-9 degreediv">
          <p><strong>Degree:</strong> '. $degree .'</p>
        </div>';*/

        $html_pic .= '
        <div class="col-sm-9 col-sm-offset-3 languagesspokendiv">
          <p><strong>Languages Spoken:</strong>  ' . $languagesspoken . '</p>
        </div>';

        $html_pic .= '
        <div class="col-sm-9 col-sm-offset-3 preferredphonediv">
          <p><strong>Preferred Phone:</strong>  ' . $preferredphone . '</p>
        </div>';

        $html_pic .= '
        <div class="col-sm-9 col-sm-offset-3 preferredmailingaddressdiv">
          <p><strong>Preferred Mailing Address:</strong>  ' . $preferredmailingaddress . '</p>
        </div>';

        if (is_array($populationserved)) {

          $html_pic .= '
          <div class="col-sm-9 col-sm-offset-3 populationserveddiv">
           <p><strong>Population Served:</strong> '. $populationserved = implode(', ', $populationserved). ' </p>
          </div>';

        }

        if (is_array($areasofpractice)) {

          $html_pic .= '
          <div class="col-sm-9 col-sm-offset-3 areasofpracticediv">
           <p><strong>Treatment & Practice Areas:</strong> '. $areasofpractice = implode(', ', $areasofpractice). ' </p>
          </div>';


        }


        if (is_array($accepted_fee_arrangements)) {

          $html_pic .= '
          <div class="col-sm-9 col-sm-offset-3 accepted_fee_arrangementsdiv">
           <p><strong>Accepted Fee Arrangements:</strong> '. $accepted_fee_arrangements = implode(', ', $accepted_fee_arrangements). ' </p>
          </div>';

        }

         $html_pic .= '

          <div class="gap"></div>

         <div class="col-sm-9 col-sm-offset-3"><h4 class="separatordiv">Education</h4></div>';

        $html_pic .= '
        <div class="col-sm-9 col-sm-offset-3 yearofgraduationdiv">
         <p><strong>Year of Graduation:</strong> '. $yearofgraduation . ' </p>
        </div>';

        $html_pic .= '
        <div class="col-sm-9 col-sm-offset-3 institutiongraduatedfromdiv">
         <p><strong>Institution Graduated From:</strong> '. $institutiongraduatedfrom .' </p>
        </div>';

        $html_pic .= '
        <div class="col-sm-9 col-sm-offset-3 yearoflicensurediv">
         <p><strong>Year of Licensure:</strong> '. $yearoflicensure .' </p>
        </div>';

         $html_pic .= '
        <div class="col-sm-9 col-sm-offset-3 boardmembershipdiv">
         <p><strong>NHPA Board/Committee Membership:</strong> '. $boardmembership .' </p>
        </div>';




  }

  $html_pic .= '</div></div>';

  return $html_pic;
}

  private static function Single_Profile_Section_RenderHeader($structure_0) {


    $structure_0 = explode("|", $structure_0);

    $html = "";

    $html .= '<div class="row">
        <div class="col-sm-12 section_title">

        <p class="navbar-text"><b>'.( empty($structure_0[0]) ? "</b>" : $structure_0[0] ).'</p>

        </div>

        </div>
';

   // return $html;
  }



  private static function Single_Profile_Section_Render($structure = "") {

    if (empty($structure))
      return;

    $structure_parent = ( empty($structure[0]) ? "" : explode("|", $structure[0]) );
    $structure_parent = array_filter($structure_parent, 'trim');

    //return self::SingleProfileHTML($structure);
    $html_header = self::Single_Profile_Section_RenderHeader($structure[0]);

    if (current_user_can( 'manage_options' ))
      return $html_header.self::SingleProfileHTML($structure);

    if (!empty($structure_parent[2])) {

      $restriction_levels = ( empty($structure_parent[2]) ? "" : explode(",", $structure_parent[2]) );

      return self::levelRestrictionRender($restriction_levels, $structure, $html_header);

    }

    //return $html_header.self::SingleProfileHTML($structure);


  }

  private static function levelRestrictionRender($levels = "", $structure_parent = "", $header = "") {

    if (empty($levels))
      return $structure_parent;

      $levels = array_map('intval', $levels);

      $show_stat = 0;
      $sign = 0;

      $current_user = get_current_user_id();
      $member_is_there = 0;

      foreach ($levels as $key => $level_val) {

        $level_val_abs = abs($level_val);

        $member_is_there = pmpro_hasMembershipLevel($level_val_abs , $current_user);

        if ($member_is_there) {

          $show_stat = $level_val;
          $sign = $level_val;
          break;
        } else {

          $sign = $level_val;

        }

      }


      if (!$member_is_there) {

        if ($sign > 0 )
          return;
        else
          return $header.self::SingleProfileHTML($structure_parent);

      } else {

        if ($sign > 0 )
          return $header.self::SingleProfileHTML($structure_parent);
        else
          return;

      }

  }


  private static function SingleProfileHTML ($structure = "") {

    if (empty($structure))
      return;

    $user_id = ( empty(self::$user_id) ? get_current_user_id() : ((int) self::$user_id) );

    $html = "";


    foreach ($structure as $key => $single_structure) {

      if ($key === 0)
        continue;
      //d($single_structure);

      $single_structure = explode("|", $single_structure);



      if (empty($single_structure[1]))
        return;

      $single_structure[1] = trim($single_structure[1]);

      $get_data = get_user_meta($user_id, $single_structure[1], true);


      if (strcmp($single_structure[1], "map") === 0) {

        $location = ( empty($single_structure[2]) ? "" : $single_structure[2] );

        $html .= '<div class="row section_data_row user_map" data-user_id="'.$user_id.'">
            <div class="col-sm-12 section_data">

            <div class="single_data_title user_map">'.$single_structure[0].' : </div>
            ';

        $html .= self::locate_map($user_id, $location);

        $html .= '
            </div>

            </div>
            ';

        continue;
      }

      if (strcmp($single_structure[1], "email") === 0) {
        $user_info = get_userdata($user_id);
        $get_data = $user_info->user_email;
      }


      if (empty($get_data))
         continue;



      if (strcmp($single_structure[1], "description") === 0)
        $get_data = wpautop($get_data);


      $data_class = preg_replace('/[^A-Za-z0-9\-]/', '', $single_structure[1]);

      if (is_array($get_data))
        $get_data = implode(" , ", $get_data);





      $html .= '<div class="row section_data_row  '.$data_class.'">
          <div class="col-sm-12 section_data">

          <span class="single_data_title '.$data_class.'">'.$single_structure[0].'</span> : <span class="single_data_value" >'.$get_data.'</span>

          </div>


          </div>
          ';


    }


   // return $html;
  }

public static function geocode($address){

    $address = urlencode($address);

    $url = "http://maps.google.com/maps/api/geocode/json?address={$address}";

    $resp_json = file_get_contents($url);

    $resp = json_decode($resp_json, true);

    if($resp['status']=='OK'){

        $lati = $resp['results'][0]['geometry']['location']['lat'];
        $longi = $resp['results'][0]['geometry']['location']['lng'];
        $formatted_address = $resp['results'][0]['formatted_address'];

        if($lati && $longi && $formatted_address){

            $data_arr = array();

            array_push(
                $data_arr,
                    $lati,
                    $longi,
                    $formatted_address
                );

            return $data_arr;

        }else{
            return false;
        }

    }else{
        return false;
    }
}

  public static function locate_map($user_id, $location) {

    if (empty($location))
      return "No location_meta specified";

    ob_start();

    $location = trim($location);

    $location = get_user_meta($user_id, $location, true);

    if (empty($location))
      return "No location found!";

    $data_arr = self::geocode($location);


    if($data_arr){

        $latitude = $data_arr[0];
        $longitude = $data_arr[1];
        $formatted_address = $data_arr[2];

    ?>

    <!-- google map will be shown here -->
    <div id="gmap_canvas">Loading map...</div>
    <div id='map-label'><small>(Map shows approximate location.)</small></div>

    <!-- JavaScript to show google map -->
    <script type="text/javascript" src="http://maps.google.com/maps/api/js?key=AIzaSyBQa0l6mp5VWIqyWwEyDsu_x1QG0vwsutc"></script>
    <script type="text/javascript">
        function init_map() {
            var myOptions = {
                zoom: 14,
                center: new google.maps.LatLng(<?php echo $latitude; ?>, <?php echo $longitude; ?>),
                mapTypeId: google.maps.MapTypeId.ROADMAP
            };
            map = new google.maps.Map(document.getElementById("gmap_canvas"), myOptions);
            marker = new google.maps.Marker({
                map: map,
                position: new google.maps.LatLng(<?php echo $latitude; ?>, <?php echo $longitude; ?>)
            });
            infowindow = new google.maps.InfoWindow({
                content: "<?php echo $formatted_address; ?>"
            });
            google.maps.event.addListener(marker, "click", function () {
                infowindow.open(map, marker);
            });
            infowindow.open(map, marker);
        }
        google.maps.event.addDomListener(window, 'load', init_map);
    </script>

    <?php

    // if unable to geocode the address
    }else{
        echo "No map found.";
    }



    $output = ob_get_clean();


    return $output;

  }

  public static function show_map_func($user_id, $location) {

    if (empty($location))
      return "No location_meta specified";

    ob_start();

    $location = trim($location);

    $location = get_user_meta($user_id, $location, true);

    if (empty($location))
      return "No location found!";

    $data_arr = self::geocode($location);


    if($data_arr){

        $latitude = $data_arr[0];
        $longitude = $data_arr[1];
        $formatted_address = $data_arr[2];

    ?>

    <!-- google map will be shown here -->
    <div id="gmap_canvas">Loading map...</div>
    <div id='map-label'><small>(Map shows approximate location.)</small></div>

    <!-- JavaScript to show google map -->
    <script type="text/javascript" src="http://maps.google.com/maps/api/js?key=AIzaSyBQa0l6mp5VWIqyWwEyDsu_x1QG0vwsutc"></script>
    <script type="text/javascript">
        function init_map() {
            var myOptions = {
                zoom: 14,
                center: new google.maps.LatLng(<?php echo $latitude; ?>, <?php echo $longitude; ?>),
                mapTypeId: google.maps.MapTypeId.ROADMAP
            };
            map = new google.maps.Map(document.getElementById("gmap_canvas"), myOptions);
            marker = new google.maps.Marker({
                map: map,
                position: new google.maps.LatLng(<?php echo $latitude; ?>, <?php echo $longitude; ?>)
            });
            infowindow = new google.maps.InfoWindow({
                content: "<?php echo $formatted_address; ?>"
            });
            google.maps.event.addListener(marker, "click", function () {
                infowindow.open(map, marker);
            });
            infowindow.open(map, marker);
        }
        google.maps.event.addDomListener(window, 'load', init_map);
    </script>

    <?php

    // if unable to geocode the address
    }else{
        echo "No map found.";
    }



    $output = ob_get_clean();


    return $output;

  }


}


 ?>
