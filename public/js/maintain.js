


questions = new Vue({
	el: '#questions',
	data: {
		questions: [],
		spaBag: spaBag
	},
	mounted: function(){
		var self = this;
		this.questions = [{id:1,title:'testtitle',location:'testlocation',description:'testdescription'},
		{id:2,title:'testtitle2',location:'testlocation2',description:'testdescription2'}];
		self.refresh(self);
	},
	methods: {
		add: function(event){},
		edit: function(question){
			if (spaBag.status != 0) return;
			editQuestion.origin = question;
			editQuestion.question = clone(question);
			spaBag.status = 1;
		},
		archive: function(event){
			if (spaBag.status != 0) return;
		},
		refresh: function(self){
			if (spaBag.status == 0){
	      		ajax('/question/index', {
	        		method: 'GET',
	        		success: function(req){
	        			self.questions = JSON.parse(req.response);
	        			//document.getElementById('debug').innerHTML = req.response;
	        		}
	        	});
			}
			console.log('refreshing');
			setTimeout(self.refresh,1000,self);
		}
	}
});

editQuestion = new Vue({
	el: '#edit-question',
	data: {
		origin: null,
		question: null,//{id:'', title:'', location:'',description:''},
		spaBag: spaBag
	},
	mounted: function(){
		self = this;
	},
	methods: {
		cancel: function(){
			spaBag.status = 0;
		},
		submit: function(){
			formData = new FormData();
      		formData.append('id', this.question.id);
      		formData.append('title', this.question.title);
      		formData.append('location', this.question.location);
      		formData.append('description', this.question.description);
      		formData.append('csrf', spaBag.csrf);
      		ajax('/question/editSave', {
        		method: 'POST',
        		data: formData,
        		success: function(req){
          			document.getElementById('debug').innerHTML = req.response;
					for(key in self.question){
						self.origin[key] = self.question[key];
					}
					spaBag.status = 0;
          		}
          	});
         /* var result = JSON.parse(req.response);
          if (result){
            spaBag.feedback = result.feedback;
            if (result.success){
              question.code = questionCode;
              question.title = result.title;
              question.location = result.location;
              question.options = result.options;
              question.description = result.description;
              spaBag.status = 5;
            } else {
              setTimeout(function(){spaBag.status = 3;},1000);
              setTimeout(function(){spaBag.feedback = '';},3000);
            }
          }
        }*/



		}
	}
});


