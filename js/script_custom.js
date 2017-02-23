jQuery(document).ready(function($) {


  var nhpa_dir = {

    init: function() {

      this.load_nhpa_pmpro_members();
      this.adjust_height();
      this.search_dir();
      this.load_pagination();
      //this.select_multiple_limit();

      console.log(nhpa_plugin_data);

    },

    load_nhpa_pmpro_members: function(limitF, offsetF, doSearch) {

      var enable_disable_ajax = parseInt(nhpa_plugin_data.enable_disable_ajax);


      if ((isNaN(enable_disable_ajax))) {

        nhpa_dir.view_profile();

        return;

      }

      if ($(".load_nhpa_pmpro_members").length === 0)
        return;



      if (limitF == null)
        var limit = $(".load_nhpa_pmpro_members").data("limit");
      else
        var limit = limitF;

      limit = parseInt(limit);
      limit = (limit == -1 || limit == null) ? 10 : limit;

      var getData = nhpa_dir.parseURLParams(window.location.href);

      if (typeof getData !== 'undefined' && typeof getData.view_profile !== 'undefined') {

        var user_id = parseInt(getData.view_profile);
        var location_prev = window.location.href;

        var data = {
          'action' : 'view_profile',
          'user_id' : user_id,
          'location_prev' : location_prev
        };

        // jQuery.post(nhpa_plugin_data.ajax_url, data, function(response) {
        //
        // })


        console.log(data);

        return;

      }


      var nhpa_page = 1;

      if (typeof getData === 'undefined' || typeof getData.get_next === 'undefined')
        offsetF = "";
      else
        offsetF = getData.get_next[0];

        if (typeof getData === 'undefined' || typeof getData.nhpa_page === 'undefined')
          offsetF = "";
        else {
          //offsetF = offsetF * getData.nhpa_page[0];
          offsetF = offsetF * getData.nhpa_page[0];
          nhpa_page = getData.nhpa_page[0];

          //$(".navigate_dir .preList").css("display", "block");



        }


        var searchStatus = $(".load_nhpa_pmpro_members").data("searchstatus");

        if (searchStatus == 1) {

          $(".navigate_dir").css("display", "none");

          if (typeof doSearch == 'undefined')
            return;

        }


        var wp_page_id = parseInt($(".load_nhpa_pmpro_members").data("wp_page_id"));

              var data = {
                'action' : 'get_nhpa_users_id',
                'limit' : limit,
                'offset' : offsetF,
                'page' : nhpa_page,
                'wp_page_id' : wp_page_id
              };

              //console.log(data);

              // $.ajaxSetup({
              //     async: true
              // });

              jQuery.post(nhpa_plugin_data.ajax_url, data, function(response) {

                //console.log(response);

                response = $.parseJSON(response);

                if (response.users.length == 0) {
                  $(".load_nhpa_pmpro_members .container:first-child .row.block_input").html('<div class="col-sm-12">No user found!</div>');


                }


                //var count_total = response.total.total_users;
                var count_total = response.total_get_users.length;
                var response_offset = parseInt(response.offset);
                  response_offset = response_offset+1;

                response = response.users;

                if (typeof doSearch != 'undefined') {
                  response = doSearch;
                  //console.log(response);

                }


                if ($(".container.searchParams").length == 0) {

                  $(".search_dir_nhpa").append("<div class='container searchParams'></div>");
                  $(".container.searchParams").append("<div class='row'>Total members : "+count_total+"</div>");
                  $(".container.searchParams").append("<div class='row'>Showing from : "+response_offset+"</div>");

                } else {

                  $(".container.searchParams").append("<div class='row'>Total members : "+count_total+"</div>");
                  $(".container.searchParams").append("<div class='row'>Showing from : "+response_offset+"</div>");

                }


                //console.log(response);

                $.each(response, function(i, el) {

                  var count_i = i;

                  var el_id;

                  if (typeof el.ID == 'undefined')
                    el_id = el;
                  else
                    el_id = el.ID;


                  // console.log(el.ID);
                  //
                  // console.log(el.ID.length);

                    var data = {
                      'action' : 'get_single_basic_profile',
                      'ID' : el_id
                    };

                    $.ajaxSetup({
                        async: true
                    });

                    jQuery.post(nhpa_plugin_data.ajax_url, data, function(response) {

                      response = $.parseJSON(response);

                      if (count_i == 0)
                        $(".load_nhpa_pmpro_members .container:first-child .row.block_input").html('<div class="col-sm-12">'+response+'</div>');
                      else
                        $(".load_nhpa_pmpro_members .container:first-child .row.block_input").append('<div class="col-sm-12">'+response+'</div>');

                    });

                    $.ajaxSetup({
                        async: true
                    });


                })

              }).complete(function() {

                if (offsetF.length === 0) {

                  console.log("offsetF.length === 0");

                  var limit = $(".load_nhpa_pmpro_members").data("limit");

                  $(".load_nhpa_pmpro_members .nextList a").attr("href", "?get_next="+limit+"&nhpa_page="+2);
                  $(".load_nhpa_pmpro_members .preList").css("display", "none");
                } else {

                  var limit = $(".load_nhpa_pmpro_members").data("limit");
                  nhpa_page++;
                  $(".load_nhpa_pmpro_members .nextList a").attr("href", "?get_next="+limit+"&nhpa_page="+nhpa_page);
                  //console.log(nhpa_page);
                  $(".load_nhpa_pmpro_members .preList").css("display", "block");
                  $(".load_nhpa_pmpro_members .preList a").attr("href", "?get_next="+limit+"&nhpa_page="+(nhpa_page-1));

                }

                //

                //nhpa_dir.click_next();

                nhpa_dir.view_profile();

              })


    },

    adjust_height: function() {

      $(".single_member_profile").each(function(i, el) {

        //var xs_5 = $(this).find(".col-xs-5").height();
        var xs_5 = $(this).find(".row").height();
        $(this).find(".col-xs-4").css("height", xs_5);

        console.log(xs_5);

      })

    },

    click_next: function() {


      $(document).on("click", ".navigate_dir .nextList", function(evt) {

        //evt.preventDefault();

        //var limit = parseInt($(this).parent(".navigate_dir").data("limit"));
        //var offset = parseInt($(this).parent(".navigate_dir").data("offset"));

        //nhpa_dir.load_nhpa_pmpro_members(limit, offset);



      })


    },

    parseURLParams: function (url) {
    var queryStart = url.indexOf("?") + 1,
        queryEnd   = url.indexOf("#") + 1 || url.length + 1,
        query = url.slice(queryStart, queryEnd - 1),
        pairs = query.replace(/\+/g, " ").split("&"),
        parms = {}, i, n, v, nv;

    if (query === url || query === "") return;

    for (i = 0; i < pairs.length; i++) {
        nv = pairs[i].split("=", 2);
        n = decodeURIComponent(nv[0]);
        v = decodeURIComponent(nv[1]);

        if (!parms.hasOwnProperty(n)) parms[n] = [];
        parms[n].push(nv.length === 2 ? v : null);
    }
    return parms;
  },

  view_profile: function(prev_link) {

    $(document).on("click", ".single_member_profile .view_profile button", function(event) {

      event.preventDefault();


      $(this).html("<img style='width: 10%' src='"+nhpa_plugin_data.pmpro_nhpa_PLUGIN_URL+"img/ajax-loader.gif'>");

      var prePage = window.location.href;
      var user_id = parseInt($(this).data("user"));

      var url = window.location+"?user_id="+user_id;
      //window.open(url, '_blank');
      //return;

      var data = {
        'action' : 'request_detail_single_user',
        'prePage' : prePage,
        'user_id' : user_id
      };

      var this_btn = $(this);

      jQuery.post(nhpa_plugin_data.ajax_url, data, function(response) {

        response = $.parseJSON(response);

        var search_div = $(".search_dir_nhpa");

        if (search_div.length > 0)
          $(".search_dir_nhpa").css("display", "none");

        $(".load_nhpa_pmpro_members .block_input").parent().find(".container.showUserProfile").remove();
        $(".row.navigate_dir").parent().find(".proBack").remove();

        $(".load_nhpa_pmpro_members .block_input").parent().prepend(response);
        $(".load_nhpa_pmpro_members .block_input").css("display", "none");

        //$(".row.navigate_dir").html("<div class='proBack'><a href='"+prePage+"'>Back</a></div>");
        $(".row.navigate_dir").parent().prepend("<div class='proBack'><a href='#'>Back</a></div>")
        //$(".row.navigate_dir").html("<div class='proBack'><a href='#'>Back</a></div>");
        $(".row.navigate_dir").css("display", "none");

      }).always(function() {

        this_btn.html("View Profile");


      });



    })


    $(document).on("click", ".proBack", function(event) {

      event.preventDefault();

      $(".row.navigate_dir").css("display", "block");
      $(".proBack").css("display", "none");

      $(".search_dir_nhpa").css("display", "block");

      $(".container.showUserProfile").css("display", "none");
      $(".row.block_input").css("display", "block");

    });

  },

  search_dir: function() {

    $("#searchDirForm").submit(function(evt) {


      return;
      evt.preventDefault();

      $("#searchDirForm button[type='submit']").html("<img src='"+nhpa_plugin_data.pmpro_nhpa_PLUGIN_URL+"img/ajax-loader.gif'>")
        .css("width", "10%");

      var gather_data = [];
      var input_val, input_meta;

      $(this).find("input").each(function(i, input) {

        input_val = $(this).val();
        input_meta = $(this).data('meta_field');
        input_title = $(this).parent().parent(".form-group.row").find("label").text();

        if ($(this).data('meta_field') == "search_type") {

          input_val = $(this).filter(':checked').val();
          input_meta = $(this).data('meta_field');

          if (typeof input_val == "undefined")
            return;

          gather_data.push({ 'meta' : input_meta, 'value' : input_val, 'title' : input_title });

          return;
        }

        gather_data.push({ 'meta' : input_meta, 'value' : input_val, 'title' : input_title });

      })

      $(this).find("select").each(function(i, input) {

        select_val = $(this).find("option:selected").text();
        select_meta = $(this).data('meta_field');
        input_title = $(this).parent().parent().find("label").text();

        var select_arr_stat = $(this).data("select_array");
        select_arr_stat = parseInt(select_arr_stat);
        if (select_arr_stat)
          select_val = $(this).val();

        gather_data.push({ 'meta' : select_meta, 'value' : select_val, 'title' : input_title });

      })
      var wp_page_id = parseInt($(".load_nhpa_pmpro_members").data("wp_page_id"));
      var data = {
        'action' : 'search_grab_matched_users',
        'search_params' : gather_data,
        'wp_page_id' : wp_page_id
      };

      if ($(".container.searchParams").length == 0)
        $(".search_dir_nhpa").append("<div class='container searchParams'></div>");

        $(".container.searchParams").html("");

      if ($(".searchParamsInfo.row").length == 0)
        $(".container.searchParams").append("<div class='searchParamsInfo row'>Search parameters:</div>");

      $.each(gather_data, function(i, el) {
        //console.log(el);

        if (el == null) {
          el = "";
          el.value = "";
        }

        if ((el.value != null) && (el.value.length != 0)) {

          if (el.title.length != 0)
            $(".container.searchParams").append("<div class='row'>"+el.title+" : "+el.value+"</div>");
        }


      })

      jQuery.post(nhpa_plugin_data.ajax_url, data, function(response) {
        console.log(response);
        response = $.parseJSON(response);

        $(".container.searchParams").append("<div class='row'>Total Results: "+response.length+"</div>")

        if (typeof response.err != 'undefined') {

          $(".load_nhpa_pmpro_members .single_member_profile_load").append(response.msg);
          $(".row.block_input").html(response.msg);
          $(".nextList").css("display", "none");
          return;
        };

        if (typeof response != 'undefined')
          nhpa_dir.load_nhpa_pmpro_members( "", "",response);


          $(".nextList").css("display", "block");

        // console.log(response);
        // console.log(typeof response);

        $(".row.navigate_dir").css("display", "none");

      }).always(function() {

        $("#searchDirForm button[type='submit']").html("Submit")
          .css("width", "auto");



      });

    })


    $("#searchDirForm").on('reset', function(e) {

      $(".container.searchParams").remove();
    })


  },

  load_pagination: function() {


    if ($(".cpid").length == 0)
      return;

    var current_id = parseInt($(".cpid").text());

    var id_next_1 = current_id + 1;
    var id_next_2 = current_id + 2;
    var id_next_3 = current_id + 3;

    var count = parseInt($(".load_nhpa_pmpro_members").data("limit"));
    var wp_page_id = parseInt($(".load_nhpa_pmpro_members").data("wp_page_id"));
    var psychologist_dir = parseInt($(".load_nhpa_pmpro_members").data("psychologist_dir"));

    console.log(  );

    var data_1 = {
      'action' : ((psychologist_dir == 1) ? "do_pagination_check_psy" : "do_pagination_check"),
      'page_id' : id_next_1,
      'count' : count,
      'wp_page_id' : wp_page_id
    };

    var data_2 = {
      'action' : ((psychologist_dir == 1) ? "do_pagination_check_psy" : "do_pagination_check"),
      'page_id' : id_next_2,
      'count' : count,
      'wp_page_id' : wp_page_id
    };

    var data_3 = {
      'action' : ((psychologist_dir == 1) ? "do_pagination_check_psy" : "do_pagination_check"),
      'page_id' : id_next_3,
      'count' : count,
      'wp_page_id' : wp_page_id
    };

    var cpid_parent = $(".cpid");

    jQuery.post(nhpa_plugin_data.ajax_url, data_1, function(response) {

      response = $.parseJSON(response);

      if (response > 0)
        cpid_parent.parent("div").append('<a href="?get_next='+data_1.count+'&amp;nhpa_page='+id_next_1+'">'+id_next_1+'</a>');

    }).done(function() {

      jQuery.post(nhpa_plugin_data.ajax_url, data_2, function(response) {

        response = $.parseJSON(response);

        if (response > 0)
          cpid_parent.parent("div").append('<a href="?get_next='+data_2.count+'&amp;nhpa_page='+id_next_2+'">'+id_next_2+'</a>');

      }).done(function() {

        jQuery.post(nhpa_plugin_data.ajax_url, data_3, function(response) {

          response = $.parseJSON(response);

          if (response > 0)
            cpid_parent.parent("div").append('<a href="?get_next='+data_3.count+'&amp;nhpa_page='+id_next_3+'">'+id_next_3+'</a>');

        });



      });



    });




  },

  select_multiple_limit: function() {

    $(".search_dir_nhpa  select[multiple]").on('change', function() {
        if (this.selectedOptions.length < 2) {
            $(this).find(':selected').addClass('selected');
            $(this).find(':not(:selected)').removeClass('selected');
        }else
            $(this)
            .find(':selected:not(.selected)')
            .prop('selected', false);
    });



  }


  }

  nhpa_dir.init();

});
