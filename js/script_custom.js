jQuery(document).ready(function($) {


  var nhpa_dir = {

    init: function() {

      this.load_nhpa_pmpro_members();
      this.adjust_height();

    },

    load_nhpa_pmpro_members: function(limitF, offsetF) {

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


        console.log("view_profile");

        return;

      }



      if (typeof getData === 'undefined' || typeof getData.get_next === 'undefined')
        offsetF = "";
      else
        offsetF = getData.get_next[0];

        if (typeof getData === 'undefined' || typeof getData.nhpa_page === 'undefined')
          offsetF = "";
        else {
          offsetF = offsetF * getData.nhpa_page[0];
          var nhpa_page = getData.nhpa_page[0];

          //$(".navigate_dir .preList").css("display", "block");



        }

          console.log(getData);

              var data = {
                'action' : 'get_nhpa_users_id',
                'limit' : limit,
                'offset' : offsetF
              };

              // $.ajaxSetup({
              //     async: true
              // });

              jQuery.post(nhpa_plugin_data.ajax_url, data, function(response) {

                response = $.parseJSON(response)

                $.each(response, function(i, el) {

                  var count_i = i;

                  // console.log(el.ID);

                    var data = {
                      'action' : 'get_single_basic_profile',
                      'ID' : el.ID
                    };

                    $.ajaxSetup({
                        async: false
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

                } else {

                  var limit = $(".load_nhpa_pmpro_members").data("limit");
                  nhpa_page++;
                  $(".load_nhpa_pmpro_members .nextList a").attr("href", "?get_next="+limit+"&nhpa_page="+nhpa_page);
                  //console.log(nhpa_page);
                }

                //

                //nhpa_dir.click_next();

                nhpa_dir.view_profile();

              })


    },

    adjust_height: function() {

      $(".single_member_profile").each(function(i, el) {

        var xs_5 = $(this).find(".col-xs-5").height();
        $(this).find(".col-xs-4").css("height", xs_5);



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

      var prePage = window.location.href;
      var user_id = parseInt($(this).data("user"));


      var data = {
        'action' : 'request_detail_single_user',
        'prePage' : prePage,
        'user_id' : user_id
      };


      jQuery.post(nhpa_plugin_data.ajax_url, data, function(response) {

        response = $.parseJSON(response);

        $(".load_nhpa_pmpro_members .block_input").html(response);


      });



    })

  }


  }

  nhpa_dir.init();

});
