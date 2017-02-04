function temp(){
	$('#suggestions').show();
	$('#suggestions').html("nice");
}


var autoSuggestorClass = function(){
	var self = this;
	this.getSuggestions = function(searchTerm) {
    	processedSearchTerm = self.processSearchTerm(searchTerm);
    	if(processedSearchTerm!=''){
    		$.ajax ({
				method: "GET",
				data:{'searchTerm':processedSearchTerm},
				url: base_url +'search/search/getSuggestion/',
			})
			.done(function(response) {
				console.log(response);
	  		});	
    	}
	}
	this.processSearchTerm = function(searchTerm){
		return searchTerm;
	}
}


var apsSearchClass = function(obj){
  var self = this, bindElements = {};
  bindElements['keyup'] = ['#searchBox'];
  this.bindPageElements = function() {
    for(var eventName in bindElements) {
          for(var elementSelector in bindElements[eventName]) {
            self.bindEvents(eventName,bindElements[eventName][elementSelector]);
          }
        }
  }
  
  this.bindEvents = function(eventName, elementSelector) {
    $(document).on(eventName, elementSelector,function(event) {
      switch(elementSelector) {
        case '#searchBox':
          	autoSuggestionObj.getSuggestions($(this).val());
        break;
      }
    });
  }
};

$(document).ready(function(){
	apsObj = new apsSearchClass();
	autoSuggestionObj = new autoSuggestorClass();
	apsObj.bindPageElements();
})