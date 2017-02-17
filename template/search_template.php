<div class="search_dir_nhpa">


  <div class="container">
      <div class="row">
          <div class="col-sm-12">

<?php

$titan = TitanFramework::getInstance( 'pmpro_nhpa_opts' );
$titan_ajax = $titan->getOption( 'enable_dir_ajax' );

if ($titan_ajax)
  _e('<form id="searchDirForm">');
else
  _e('<form id="searchDirForm" action="." method="get">');

 ?>



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

<?php

if ($titan_ajax)
  _e('<button type="submit" class="btn btn-primary">Submit</button>');
else
  _e('<button name="search_trigger" value="do_search" type="submit" class="btn btn-primary">Submit</button>');


 ?>

              <button type="reset" class="btn btn-primary">Reset</button>

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

if (empty($opts['on_dir']))
  _e($html);

 ?>
