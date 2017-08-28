    <script type="text/javascript" src="<?php echo Config::get('URL'); ?>js/adapter.min.js"></script>
    <script type="text/javascript" src="<?php echo Config::get('URL'); ?>js/vue.min.js"></script>
    <script type="text/javascript" src="<?php echo Config::get('URL'); ?>js/instascan.min.js"></script>
    <script type="text/javascript" src="<?php echo Config::get('URL'); ?>js/ajax.js"></script>
    <div id="debug"></div>
    <div id="board"  class="w3-panel w3-lime">
      <h1>{{spaBag.tourName}}</h1>
      <h2>{{spaBag.teamName}}, {{spaBag.memberName}}</h2>
      <p class="feedback">{{spaBag.feedback}}</p>
    </div>
    <section id="actions" class="w3-panel w3-khaki">
      <div id="start" v-bind:class="{ focus: (spaBag.status == 0) }">
        <h1>Start</h1>
        <p>Je gaat dit gebouw en het leerpark verkennen in een team. Overal kan je qr-codes scannen. Zo'n qr-code geeft je een vraag. Een goed antwoord levert punten. Deze app werkt alleen als je <strong>cookies accepteert</strong>!</p>
        <button id="create-team" v-on:click="createTeam" class="w3-button w3-orange">Maak een nieuw team</button><br>
        <button id="join-team" v-on:click="joinTeam" class="w3-button w3-orange">Sluit je aan bij een team</button>
      </div>
      <div id="team-create" v-bind:class="{ focus: (spaBag.status == 1) }">
        <h1>Maak je eigen team</h1>
        <label for="tour-pin" title="Je hebt ">Tocht-pincode:</label><br>
        <input id="tour-pin" v-model="tourPin" name="tour-pin" required placeholder="de pincode is nodig voor toegang..." class="w3-input" v-on:keypress="inputPinCharactersOnly" v-on:change="sanitize" maxlength="10">
        <label for="team-name" title="Je hebt een geschikte naam nodig voor jouw team!">Teamnaam:</label><br>
        <input id="team-name" v-model="teamName" name="team-name" placeholder="verzin een geschikte naam..." class="w3-input" v-on:keypress="inputNameCharactersOnly" v-on:change="sanitize" maxlength="20">
        <label for="member-name" title="Jouw naam in het team">Jouw naam:</label><br>
        <input id="member-name" name="member-name" v-model="memberName" placeholder="geef jouw eigen naam..." class="w3-input" v-on:keypress="inputNameCharactersOnly" v-on:change="sanitize" maxlength="20"><br>
        <button id="team-create-submit" v-on:click="submit" class="w3-button w3-orange" :disabled="(tourPin=='')||(teamName=='')||(memberName=='')">Verder</button>
        <button id="team-create-cancel" v-on:click="back" class="w3-button w3-orange">Terug</button>
      </div>
      <div id="team-join" v-bind:class="{ focus: (spaBag.status == 2) }">
        <h1>Sluit je aan bij een team</h1>
        <p>Je hebt daarvoor een speciale team-pincode nodig. Dat kan een ander teamlid je geven.</p>
        <label for="team-pin" title="Je hebt een pincode nodig voor toegang tot het team!">Team pincode: </label>
        <input id="team-pin" name="team-pin" v-model="teamPin" placeholder="de pincode is nodig voor toegang tot het team..." class="w3-input" v-on:keypress="inputPinCharactersOnly" v-on:change="sanitize" maxlength="10">
        <label for="member-name" title="Jouw naam in het team">Naam: </label>
        <input id="member-name" name="member-name" v-model="memberName" placeholder="geef jouw eigen naam..." class="w3-input" v-on:keypress="inputNameCharactersOnly" v-on:change="sanitize" maxlength="20"><br>
        <button id="team-join-submit" v-on:click="submit" class="w3-button w3-orange" :disabled="(teamPin=='')||(memberName=='')">Verder</button>
        <button id="team-join-cancel" v-on:click="back" class="w3-button w3-orange">Terug</button>
      </div>
      <div id="team" v-bind:class="{ focus: (spaBag.status == 3) }">
        <br>
        <button id="qr-scan" v-on:click="scan" class="w3-button w3-orange">Scan QR-code</button><br>
        <button id="pin-show" v-on:click="showPin" class="w3-button w3-orange">Toon pincode</button><br>
        <button id="team-leave" v-on:click="rejoin" class="w3-button w3-orange">Team verlaten</button>
      </div>
      <div id="scan" v-bind:class="{ focus: (spaBag.status == 4) }">
        <h1>Scanning...</h1>
        <button id="scan-cancel" v-on:click="back" class="w3-button w3-orange">Stop scannen</button>
        <div class="preview-container">
          <video id="preview"></video>
        </div>
        <section class="cameras" v-if="cameras.length > 1">
          <h2>Cameras</h2>
          <ul>
            <li v-if="cameras.length === 0" class="empty">No cameras found</li>
            <li v-for="camera in cameras">
              <span v-if="camera.id == activeCameraId" :title="formatName(camera.name)" class="active">{{ formatName(camera.name) }}</span>
              <button v-if="camera.id != activeCameraId" :title="formatName(camera.name)" class="w3-button w3-orange" v-on:click="selectCamera(camera)">{{ formatName(camera.name) }}</button>
            </li>
          </ul>
        </section>
      </div>
      <div id="question" v-bind:class="{ focus: (spaBag.status == 5) }">
        <h2>{{title}}</h2>
        <p>Je bent nu: {{location}}</p>
        <h2>Vraag</h2>
        <p>{{description}}</p>
        <ul class="options">
          <li v-for="(option, index) in options">
            <input type="radio" v-bind:id="option.id" v-bind:value="option.id" v-model="answer">
            <label v-bind:for="option.id">{{option.description}}</label>
          </li>
        </ul>
        <button id="answer" v-on:click="submit" class="w3-button w3-orange">Antwoord sturen</button><br>
      </div>
      <div id="show-pin" v-bind:class="{ focus: (spaBag.status == 9) }">
        <h3>Aansluiten bij jullie team met pincode:</h3>
        <h2>{{spaBag.teamPin}}</h2>
        <button id="cancel-show-pin" v-on:click="back" class="w3-button w3-orange">Terug</button><br>
      </div>
    </section>
    <script type="text/javascript">
      spaBag = {
        memberToken: '<?php if($this->member) echo $this->member->token; ?>',
        memberName: '<?php if($this->member) echo $this->member->name; ?>',
        teamName: '<?php echo ($this->team)?$this->team->name:'Je hebt nog geen team...'; ?>',
        teamPin: '<?php if($this->team) echo $this->team->pin; ?>',
        tourName: '<?php echo ($this->tour)?$this->tour->name:'Nieuwe tocht'; ?>',
        csrf: '<?php echo $this->csrf; ?>',
        status: <?php echo ($this->member)?3:0 ?>,
        feedback: '<?php echo ($this->feedback)?$this->feedback:(($this->member)?'welkom terug...':'Welkom op deze tocht!'); ?>'
      } 
    </script>
    <script type="text/javascript" src="<?php echo Config::get('URL'); ?>js/app.js"></script>

