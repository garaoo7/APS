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
          	autoSuggestionObj.getSuggestions($(this).val());
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