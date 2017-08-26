var board = new Vue({
  el: '#board',
  data: {
    spaBag: spaBag,
  }
})

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
    scans: []
  },
  mounted: function () {
    var self = this;
    self.scanner = new Instascan.Scanner({ video: document.getElementById('preview'), scanPeriod: 5 });
    self.scanner.addListener('scan', function (content, image) {
      //self.scans.unshift({ date: +(Date.now()), content: content });


      console.log(content);
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
    }
  }
});
