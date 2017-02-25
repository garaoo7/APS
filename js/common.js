var apsSearchClass = function(obj){
  var self = this, bindElements = {};
  bindElements['keyup'] = ['#searchBox'];
  bindElements['click'] = ['#searchButton'];
  this.bindPageElements = function() {
    for(var eventName in bindElements) {
          for(var elementSelector in bindElements[eventName]) {
            self.bindEvents(eventName,bindElements[eventName][elementSelector]);
          }
        }
  }
  
  this.bindEvents = function(eventName, elementSelector) {
    $(elementSelector).on(eventName,function(event) {
      switch(elementSelector) {
        case '#searchBox':
          	//autoSuggestionObj.getSuggestions($(this).val());
        break;

        case '.recommdated-question':
            alert('go to question detail page');
            break;
        case '#searchButton':
            searchResultPage();
      }
    });
  }
};

function searchResultPage(){
}
$(document).ready(function(){
  apsObj = new apsSearchClass();
  autoSuggestionObj = new autoSuggestorClass();
  apsObj.bindPageElements();
})


function initializeFilters(){
  $("#questionFilters input[type=checkbox]").on("click",function(){
    getFilteredResults();
  });
}

function getFilteredResults(){
  console.log($("#questionFilters").serialize());
  $.ajax({
        type  : "POST",
        url   : base_url+'/search/Search/getFilteredResult',
        data  : {'data': $("#questionFilters").serialize()},
        beforeSend  : function(){
            //showFilterLoader();
        }
    })
    .done(function( res ) {
        res = JSON.parse(res);
    });
}

function showFilterLoader() {
        //disable background
  /*var body = document.getElementsByTagName('body')[0];
  if(typeof $j('#iframe_div') != "undefined") {
    $j('#iframe_div').css({backgroundColor: "#000", opacity: "0.4" });
    $j('#iframe_div').attr("allowTransparency", "true");
  }
  
  // get the position of the current viewport so as to position the loader image in the center
  var top   = $j(window).scrollTop();
  var left  = $j('html').offset().left;
  var imageheight = $j("#loadingImage").height();
  var imagewidth  = $j("#loadingImage").width();
  
  /*var smallLoader = $('#'+splitAspect.id).closest('div.x_content').next('div.loader_small_overlay');
    smallLoader.css({"background": "rgba(0, 0, 0, 0.18)"}).show();*/
    $("#loadingImage").css("top",top + ($(window).height() /2) - (imageheight/2));
  $("#loadingImage").css("left",left + ($(window).width() /2) - (imagewidth/2));
  $("#loadingImage").css({"background": "rgba(0, 0, 0, 0.18)"}).show();

}