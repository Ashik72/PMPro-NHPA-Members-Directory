jQuery(document).ready(function($) {

var mapWorks = {

  init: function() {

    this.report_map_area_px();
    this.init_gmap();
    this.click_on_addr();

  },

  report_map_area_px: function() {

    if ($(".map_template .col-sm-8").length < 1)
      return;

    $(".map_template .col-sm-8").attr('data-width', $(".map_template .col-sm-8").width());

  },

  init_gmap: function() {

    var data_initial_address = $("#map_canvas_list_data").data("initial_address");
    var locate_user_pos = $("#map_canvas_list_data").data("locate_user_pos");
    var initial_load = $("#map_canvas_list_data").data("initial_load");

    locate_user_pos = parseInt(locate_user_pos);
    initial_load = parseInt(initial_load);

    console.log(data_initial_address);
    console.log(initial_load);


    var data = {
      'action' : 'geocode_location',
      'address' : data_initial_address
    };

    jQuery.post(nhpa_plugin_data.ajax_url, data, function(response) {


      response = $.parseJSON(response);

      console.log(response);

      var map = "";

      if (response == null) {
        map = new GMaps({
          div: '#map_canvas_nhpa',
          lat: 90,
          lng: 23

        });

        window.gmap = map;

      } else {

        map = new GMaps({
          div: '#map_canvas_nhpa',
          lat: response.lat,
          lng: response.long
        });


        window.gmap = map;

      }


      var centerSet = 0;
      if (locate_user_pos > 0) {

        GMaps.geolocate({
          success: function(position) {
            map.setCenter(position.coords.latitude, position.coords.longitude);
          }
        });

        centerSet++;
      }

      var total_addr = $("#map_canvas_list_data .single_address").length;

      total_addr = ( (initial_load == -1) ? total_addr : initial_load);

      $("#map_canvas_list_data .single_address").each(function(i, el) {

        if (i > total_addr)
          return;

        var data = {
          'action' : 'geocode_location',
          'address' : $(this).data("addr")
        };

        jQuery.post(nhpa_plugin_data.ajax_url, data, function(response_marker) {

          response_marker = $.parseJSON(response_marker);

          if (response_marker == null)
            return;

          if (centerSet == 0) {

            // GMaps.geolocate({
            //   success: function(position) {
            //     map.setCenter(response.lat, response.long);
            //   }
            // });

              map.setCenter(response.lat, response.long);

            centerSet++;

          }


            $("#map_canvas_list_data .single_address").each(function(i, el) {

              var geocode = $(this).data('geocode');

              if (geocode.length == 0)
                return;

                geocode = geocode.split('|');

              var htmlInfo = $(this).html();

              map.addMarker({
                lat: geocode[0],
                lng: geocode[1],
                title: ""+i+"",
                click: function(e) {
                  //alert('You clicked in this marker');
                  //console.log(this);

                },
                infoWindow: {
                    content: htmlInfo
                }
              });

            })


            window.gmap.setZoom(1);

        })

      });



    });



  },

  click_on_addr: function() {

    $(document).on("click", "#map_canvas_list_data .single_address", function(evt) {

      var data = {
        'action' : 'geocode_location',
        'address' : $(this).data("addr")
      };

      var clickThis = $(this);

      var thisHtml = clickThis.html();

      clickThis.html("Loading...");
      clickThis.removeClass("single_addresshovered");

      jQuery.post(nhpa_plugin_data.ajax_url, data, function(response_marker) {

        response_marker = $.parseJSON(response_marker);

        if (response_marker == null) {

          alert("No address found on map!");

          return;

        }

          console.log(response_marker);


          window.gmap.setCenter(response_marker.lat, response_marker.long);

          window.gmap.setZoom(20);

          window.gmap.addMarker({
            lat: response_marker.lat,
            lng: response_marker.long,
            title: response_marker.lat+" , "+response_marker.long,
            click: function(e) {
              //alert('You clicked in this marker');
              //console.log(this);

            },
            infoWindow: {
                content: thisHtml
            }
          });


      }).complete(function() {

        clickThis.html(thisHtml);

        clickThis.addClass("single_addresshovered");

      })


     })


  }

}

mapWorks.init();

});
