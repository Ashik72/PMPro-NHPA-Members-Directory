<div class="search_dir_nhpa">


  <div class="container">
      <div class="row">
          <div class="col-sm-12">

            <form id="searchDirForm">

              <?php

              _e(NHPA_Directory_Search::organize_fields($search_fields));

               ?>

               <div class="form-group row">
               <label for="radio-input-search-type" class="col-sm-2 col-form-label">Search Type: </label>
               <div class="col-sm-10">
                 <span class="col-sm-5"><input data-meta_field="search_type" class="form-control" name="search_type" type="radio" value="union" id="radio-input-search-type" checked>
                 Users that match any search criteria.</span>
                 <span class="col-sm-5"><input data-meta_field="search_type" class="form-control" name="search_type" type="radio" value="intersect" id="radio-input-search-type">
                 Only Specifically Matched Users</span>



               </div>
               </div>

              <button type="submit" class="btn btn-primary">Submit</button>
            </form>


          </div>
      </div>
  </div>
</div>


<?php

$html = "";

$html .= "<div class='load_nhpa_pmpro_members' data-searchStatus='1' data-limit='1'>";
$html .= '<div class="container"><div class="row block_input">';
$html .=  '<div class="col-sm-12"><div class="single_member_profile_load"></div></div>';

$html .= '</div></div>';
$html .= '<div class="container"><div class="row navigate_dir">        <div class="col-xs-2 preList"><a href="#">Previous</a></div>
    <div class="col-xs-2 nextList"><a href="?get_next=">Next</a></div></div></div>';
$html .= "</div>";

_e($html);

 ?>
