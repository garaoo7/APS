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
        self.prepareAutoSuggestor(response);
	  		});	
    	}
	}

  this.prepareAutoSuggestor = function(response){
    if(response != ''){
        var recommendateQuestions = JSON.parse(response);
        var recommendateQuestionsData = '';
        for(var question in recommendateQuestions){
          recommendateQuestionsData += "<li class ='recommdated-question' id='recommendateQuestions'><span>"+recommendateQuestions[question]+"</span></li>";
        }
        $("#recommdated-questions").css('display','block');
        $("#recommdated-questions").html(recommendateQuestionsData);
        apsObj.bindEvents('click','.recommdated-question');
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
    $(elementSelector).on(eventName,function(event) {
      switch(elementSelector) {
        case '#searchBox':
          	autoSuggestionObj.getSuggestions($(this).val());
        break;

        case '.recommdated-question':
            alert('go to question detail page');
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