
var refresh = function(self){
  ajax('/tour/boardscores/'+self.tour.id, {
    method: 'GET',
    success: function(req){
      //document.getElementById('debug').innerHTML = req.response;
      self.tour.results = JSON.parse(req.response);
    }
  });
  setTimeout(refresh,10000,self);
}



var board = new Vue({
  el: '#board',
  data: {
    tour: tour
  },
  mounted: function(){
    var self = this;
    refresh(self);
  }
});

