
var board = new Vue({
  el: '#board',
  data: {
    spaBag: spaBag
  },
});

var start = new Vue({
  el: '#start',
  data: {
    spaBag: spaBag
  },
  methods: {
    createTeam: function(event){
      spaBag.feedback = '';
      spaBag.status = 1;
    },
    joinTeam: function(event){
      spaBag.feedback = '';
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
      spaBag.feedback = '';
      formData = new FormData();
      formData.append('tourPin', this.tourPin);
      formData.append('teamName', this.teamName.trim().replace(/ +(?= )/g,''));
      formData.append('memberName', this.memberName.trim());
      formData.append('csrf', spaBag.csrf);
      ajax('/team/create', {
        method: 'POST',
        data: formData,
        success: function(req){
          //document.getElementById('message').innerHTML = req.response;
          var result = JSON.parse(req.response);
          if (result){
            for (prop in result){
              if (spaBag[prop]!=null) {
                spaBag[prop] = result[prop];
              }
            }
            spaBag.status = (result.success)?3:1;
            //setTimeout(function(){spaBag.feedback = '';},5000);
          }
        }
      });
    },
    back: function(event){
      spaBag.feedback = '';
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
      spaBag.feedback = '';
      formData = new FormData();
      formData.append('teamPin', this.teamPin);
      formData.append('memberName', this.memberName);//.trim().replace(/ +(?= )/g,''));
      formData.append('csrf', spaBag.csrf);
      ajax('/team/join', {
        method: 'POST',
        data: formData,
        success: function(req){
          var result = JSON.parse(req.response);
          if (result){
            for (prop in result){
              if (spaBag[prop]!=null) {
                spaBag[prop] = result[prop];
              }
            }
            spaBag.status = (result.success)?3:2;
          }
        }
      });
    },
    back: function(event){
      spaBag.feedback = '';
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
      spaBag.feedback = '';
      spaBag.status = 4;
    },
    rejoin: function (event)
    {
      spaBag.feedback = '';
      spaBag.status = 2;
      teamJoin.teamPin = '';
    },
    showPin: function(event){
      spaBag.feedback = '';
      spaBag.status = 9;
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
    self.scanner = new Instascan.Scanner({ video: document.getElementById('preview'), scanPeriod: 5, captureImage: false, backgroundScan: true });
    self.scanner.addListener('scan', function (questionCode) {
      //self.scans.unshift({ date: +(Date.now()), content: content });
      /* TEST  questionCode = 'abcde';*/
      formData = new FormData();
      formData.append('questionCode', questionCode);
      ajax('/question/get', {
        method: 'POST',
        data: formData,
        success: function(req){
          //document.getElementById('debug').innerHTML = req.response;
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
            } else {
              setTimeout(function(){spaBag.status = 3;},2000);
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
      spaBag.feedback = '';
      this.activeCameraId = camera.id;
      this.scanner.start(camera);
    },
    back: function(event){
      spaBag.feedback = '';
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
      spaBag.feedback = '';
      if (!this.answer) return;
      formData = new FormData();
      formData.append('questionCode', this.code);
      formData.append('optionId',this.answer);
      ajax('/question/answer', {
        method: 'POST',
        data: formData,
        success: function(req){
          //document.getElementById('debug').innerHTML = req.response;
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

var showPin = new Vue({
  el: '#show-pin',
  data: {
    spaBag: spaBag
  },
  methods: {
    back: function(event){
      spaBag.feedback = '';
      spaBag.status = 3;
    }
  }
});

function inputCertainCharactersOnly(e, regex) {
  var chrTyped, chrCode=0, evt=e?e:event;
  if (evt.charCode != null) 
    chrCode = evt.charCode;
  else if (evt.which != null)   
    chrCode = evt.which;
  else if (evt.keyCode != null) 
    chrCode = evt.keyCode;

  if (chrCode == 0) 
    chrTyped = 'SPECIAL KEY';
  else 
    chrTyped = String.fromCharCode(chrCode);

  //Digits, special keys & backspace [\b] work as usual:
  if (chrTyped.match(regex)) return true;
  if (evt.altKey || evt.ctrlKey || chrCode<28) return true;

  //Any other input? Prevent the default response:
  if (evt.preventDefault) evt.preventDefault();
  evt.returnValue=false;
  return false;
}

var accentedCharacters = '[éèëêÉÈËÊáàäâåÁÀÄÂÅóòöôÓÒÖÔíìïîÍÌÏÎúùüûÚÙÜÛýÿÝøØœŒÆçÇ]';

var regExpWithAccentedCharacters = function(base){
  return new RegExp(accentedCharacters+'|'+base);
}

function inputPinCharactersOnly(e){
  return inputCertainCharactersOnly(e,/[0-9a-z]|SPECIAL/);
}

function inputNameCharactersOnly(e){
  return inputCertainCharactersOnly(e,regExpWithAccentedCharacters("[\\s]|[0-9a-zA-Z]|SPECIAL"));
}

function sanitize(event){
  //.dir(event);
  event.target.value = event.target.value.trim().replace(/ +(?= )/g,'');
}

document.getElementById('board').className += ' focus';
document.getElementById('actions').className += ' focus';



