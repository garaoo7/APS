function temp(){
	$('#suggestions').show();
	$('#suggestions').html("nice");
}
 

var autoSuggestorClass = function(){
	var self = this;
	this.getSuggestions = function(searchTerm) {
        $("#recommdated-questions").css('display','hide');
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




