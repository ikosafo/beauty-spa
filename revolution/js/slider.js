/*------------------------------------------------------------------------------*/
/*  Home_Page Slider
/*------------------------------------------------------------------------------*/

var revapi1,
  tpj;
jQuery(function() {
  tpj = jQuery;
  if(tpj("#rev_slider_2_1").revolution == undefined){
    revslider_showDoubleJqueryError("#rev_slider_1_1");
  }else{
    revapi1 = tpj("#rev_slider_2_1").show().revolution({
      sliderLayout:"fullwidth",
      visibilityLevels:"1240,1240,778,480",
      gridwidth:"1240,1240,778,480",
      gridheight:"750,750,450,330",
      minHeight:"",
      spinner:"spinner0",
      editorheight:"750,768,450,330",
      responsiveLevels:"1240,1240,778,480",
      disableProgressBar:"on",
      navigation: {
          onHoverStop:false,
          arrows: {
              enable:true,
              style:"uranus",
              hide_onmobile:true,
              hide_under:"768px",
              hide_onleave:true,
              left: {
                  h_offset:30
              },
              right: {
                  h_offset:30
              }
          }
      },
      fallbacks: {
        allowHTML5AutoPlayOnAndroid:true
      },
    });
  }
  
});




var revapi3,
    tpj;
jQuery(function() {
    tpj = jQuery;
    if(tpj("#rev_slider_1_1").revolution == undefined){
        revslider_showDoubleJqueryError("#rev_slider_1_1");
    }else{
        revapi3 = tpj("#rev_slider_1_1").show().revolution({
          sliderLayout:"fullwidth",
          visibilityLevels:"1240,1240,778,480",
          gridwidth:"1240,1240,778,480",
          gridheight:"750,750,450,330",
          minHeight:"",
          spinner:"spinner0",
          editorheight:"750,768,450,330",
          responsiveLevels:"1240,1240,778,480",
          disableProgressBar:"on",
          navigation: {
              onHoverStop:false,
              arrows: {
                  enable:true,
                  style:"uranus",
                  hide_onmobile:true,
                  hide_under:"768px",
                  hide_onleave:true,
                  left: {
                      h_offset:30
                  },
                  right: {
                      h_offset:30
                  }
              }
          },
          fallbacks: {
            allowHTML5AutoPlayOnAndroid:true
          },
        });
    }
    
});