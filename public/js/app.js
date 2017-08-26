
var board = new Vue({
  el: '#board',
  data: {
    spaBag: spaBag
  }
});

var start = new Vue({
  el: '#start',
  data: {
    spaBag: spaBag
  },
  methods: {
    createTeam: function(event){
      spaBag.status = 1;
    },
    joinTeam: function(event){
      spaBag.status = 2;
    },
  }
});
var teamCreate = new Vue({
  el: '#team-create',
  data: {
    spaBag: spaBag,
    tourPin: '',
    teamName: '',
    memberName: ''
  },
  methods: {
    submit: function (event)
    {
      formData = new FormData();
      formData.append('tourPin', this.tourPin);
      formData.append('teamName', this.teamName);
      formData.append('memberName', this.memberName);
      formData.append('csrf', spaBag.csrf);
      ajax('/team/create', {
        method: 'POST',
        data: formData,
        success: function(req){
          document.getElementById('message').innerHTML = req.response;
          var result = JSON.parse(req.response);
          if (result){
            for (prop in result){
              if (spaBag[prop]) spaBag[prop] = result[prop];
            }
            spaBag.status = (result.success)?3:1;
          }
        }
      });
    },
    back: function(event){
      spaBag.feedback = ' ';
      spaBag.status = (spaBag.memberToken>'')?3:0;
    },
  }

});
var teamJoin = new Vue({
  el: '#team-join',
  data: {
    spaBag: spaBag,
    teamPin: '',
    memberName: ''
  },
  methods: {
    submit: function (event)
    {
      formData = new FormData();
      formData.append('teamPin', this.teamPin);
      formData.append('memberName', this.memberName);
      formData.append('csrf', spaBag.csrf);
      ajax('/team/join', {
        method: 'POST',
        data: formData,
        success: function(req){
          var result = JSON.parse(req.response);
          if (result){
            for (prop in result){
              if (spaBag[prop]) spaBag[prop] = result[prop];
            }
            spaBag.status = (result.success)?3:2;
          }
        }
      });
    },
    back: function(event){
      spaBag.feedback = ' ';
      spaBag.status = (spaBag.memberToken>'')?3:0;

    }
  } 
});
var team = new Vue({
  el: '#team',
  data: {
    spaBag: spaBag,    
  },
  methods: {
    scan: function(){
      spaBag.status = 4;
    },
    rejoin: function (event)
    {
      spaBag.status = 2;
      spaBag.feedback = ' ';
      teamJoin.teamPin = '';
    }
  }

});

var scan = new Vue({
  el: '#scan',
  data: {
    scanner: null,
    activeCameraId: null,
    cameras: [],
    scanning: false
  },
  mounted: function () {
    var self = this;
    self.scanner = new Instascan.Scanner({ video: document.getElementById('preview'), scanPeriod: 5, captureImage: false, backgroundScan: false });
    self.scanner.addListener('scan', function (questionCode) {
      //self.scans.unshift({ date: +(Date.now()), content: content });
      /* TEST */ questionCode = 'abcde';
      formData = new FormData();
      formData.append('questionCode', questionCode);
      ajax('/question/get', {
        method: 'POST',
        data: formData,
        success: function(req){
          document.getElementById('debug').innerHTML = req.response;
          var result = JSON.parse(req.response);
          if (result){
            spaBag.feedback = result.feedback;
            if (result.success){
              question.code = questionCode;
              question.title = result.title;
              question.location = result.location;
              question.options = result.options;
              question.description = result.description;
              spaBag.status = 5;
            }
          }
        }
      });
    });
    Instascan.Camera.getCameras().then(function (cameras) {
      self.cameras = cameras;
      if (cameras.length > 0) {
        self.activeCameraId = cameras[0].id;
        self.scanner.start(cameras[0]);
      } else {
        console.error('No cameras found.');
      }
    }).catch(function (e) {
      console.error(e);
    });
  },
  methods: {
    formatName: function (name) {
      return name || '(unknown)';
    },
    selectCamera: function (camera) {
      this.activeCameraId = camera.id;
      this.scanner.start(camera);
    },
    back: function(event){
      spaBag.status = 3;
    }
  }
});

var question = new Vue({
  el: '#question',
  data: {
    spaBag: spaBag,
    code: null,
    title: '',
    location: '',
    description: '',
    answer: null,
    options: [
    ]
  },
  methods: {
    submit: function(event){
      if (!this.answer) return;
      formData = new FormData();
      formData.append('questionCode', this.code);
      formData.append('optionId',this.answer);
      ajax('/question/answer', {
        method: 'POST',
        data: formData,
        success: function(req){
          document.getElementById('debug').innerHTML = req.response;
          var result = JSON.parse(req.response);
          if (result){
            spaBag.feedback = result.feedback;
            spaBag.status = 3;
            if (result.success){
              question.code = null;
              question.title = '';
              question.location = '';
              question.options = [];
              question.description = '';
            }
          }
        }
      });      
    }

  }
});

