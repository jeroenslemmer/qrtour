    <script type="text/javascript" src="<?php echo Config::get('URL'); ?>js/adapter.min.js"></script>
    <script type="text/javascript" src="<?php echo Config::get('URL'); ?>js/vue.min.js"></script>
    <script type="text/javascript" src="<?php echo Config::get('URL'); ?>js/instascan.min.js"></script>
    <script type="text/javascript" src="<?php echo Config::get('URL'); ?>js/ajax.js"></script>
    <div id="debug"></div>
    <div id="board">
      <h1>{{spaBag.tourName}}</h1>
      <h2>{{spaBag.teamName}}</h2>
      <h3>{{spaBag.feedback}}</h3>
    </div>

    <div id="start" v-bind:class="{ focus: (spaBag.status == 0) }">
      <h2>Start</h2>
      <p>Je gaat de tocht op het leerpark lopen in een team. Waar is de kantine? Waar kan je een nieuwe pas aanvragen? Waar moet je heen voor het vak Engels? Overal op het leerpark kan je qr-codes tegenkomen. Als je zo'n qr-code scant dan krijg je een vraag die je kunt beantwoorden met je team. Een goed antwoord levert punten op voor jouw team.<br>Let op: deze app werkt alleen goed als je cookies accepteert!</p>
      <button id="create-team" v-on:click="createTeam">Maak een nieuw team</button><br>
      <button id="join-team" v-on:click="joinTeam">Sluit je aan bij een team</button>
    </div>
    <div id="team-create" v-bind:class="{ focus: (spaBag.status == 1) }">
      <h2>Nieuw team</h2>
      <p>Maak je eigen team. Je hebt een <strong>pincode</strong> (cijfers of kleine letters) nodig voor toegang tot de tocht met jouw nieuwe team. Deze tocht-pincode heb je gekregen tijdens de algemene instructie.<br>Je hebt ook een goede naam nodig voor jouw team! Verzin een beschaafde naam.</p>
      <label for="tour-pin" title="Je hebt ">Tocht pincode: <input id="tour-pin" v-model="tourPin" name="tour-pin" required></label><br>
      <label for="team-name" title="Je hebt een geschikte naam nodig voor jouw team!">Teamnaam: <input id="team-name" v-model="teamName" name="team-name" required></label><br>
      <label for="member-name" title="Jouw naam in het team">Naam: </label><input id="member-name" name="member-name" v-model="memberName"><br>
      <button id="team-create-submit" v-on:click="submit">start</button>
      <button id="team-create-cancel" v-on:click="back">terug</button>
    </div>
    <div id="team-join" v-bind:class="{ focus: (spaBag.status == 2) }">
      <h2>Sluit je aan bij een team</h2>
      <p>Je aansluiten bij een team. Je hebt daarvoor een team-pincode (cijfers of kleine letters) nodig. Dat kan een ander lid je geven. Je moet ook jouw naam invoeren.</p>
      <label for="team-pin" title="Je hebt een pincode nodig voor toegang tot het team!">Team pincode: </label><input id="team-pin" name="team-pin" v-model="teamPin"><br>
      <label for="member-name" title="Jouw naam in het team">Naam: </label><input id="member-name" name="member-name" v-model="memberName"><br>
      <button id="team-join-submit" v-on:click="submit">start</button>
      <button id="team-join-cancel" v-on:click="back">terug</button>
    </div>
    <div id="team" v-bind:class="{ focus: (spaBag.status == 3) }">
      <button id="qr-scan" v-on:click="scan">Scan QR-code</button><br>
      <button id="pin-show" v-on:click="showPin">Toon pincode</button><br>
      <button id="team-leave" v-on:click="rejoin">Ga naar een ander team</button>
    </div>
    <div id="scan" v-bind:class="{ focus: (spaBag.status == 4) }">
      <h2>Scanning</h2>
      <button id="scan-cancel" v-on:click="back">stop scannen</button>
      <div class="preview-container">
        <video id="preview"></video>
      </div>
      
      <div class="sidebar">
        <section class="cameras">
          <h2>Cameras</h2>
          <ul>
            <li v-if="cameras.length === 0" class="empty">No cameras found</li>
            <li v-for="camera in cameras">
              <span v-if="camera.id == activeCameraId" :title="formatName(camera.name)" class="active">{{ formatName(camera.name) }}</span>
              <span v-if="camera.id != activeCameraId" :title="formatName(camera.name)">
                <a @click.stop="selectCamera(camera)">{{ formatName(camera.name) }}</a>
              </span>
            </li>
          </ul>
        </section>
      </div>
    </div>
    <div id="question" v-bind:class="{ focus: (spaBag.status == 5) }">
      <h2>{{title}}</h2>
      <p>{{location}}</p>
      <h2>Vraag</h2>
      <p>{{description}}</p>
      <ul class="options">
        <li v-for="(option, index) in options">
          <input type="radio" v-bind:id="option.id" v-bind:value="option.id" v-model="answer">
          <label v-bind:for="option.id">{{option.description}}</label>
        </li>
      </ul>
      <button id="answer" v-on:click="submit">Antwoord sturen</button><br>
      <h1>{{answer}}</h1>
    </div>
    <div id="show-pin" v-bind:class="{ focus: (spaBag.status == 9) }">
      <h3>Aansluiten bij jullie team met pincode:</h3>
      <h2>{{spaBag.teamPin}}</h2>
      <button id="cancel-show-pin" v-on:click="back">Terug</button><br>
    </div>

    <script type="text/javascript">
      spaBag = {
        memberToken: '<?php if($this->member) echo $this->member->token; ?>',
        memberName: '<?php if($this->member) echo $this->member->name; ?>',
        teamName: '<?php if($this->team) echo $this->team->name; ?>',
        teamPin: '<?php if($this->team) echo $this->team->pin; ?>',
        tourName: '<?php if($this->tour) echo $this->tour->name; ?>',
        csrf: '<?php echo $this->csrf; ?>',
        status: <?php echo ($this->member)?3:0 ?>,
        feedback: '<?php echo ($this->feedback)?$this->feedback:(($this->member)?'welkom terug...':'Welkom op deze tocht!'); ?>'
      } 
    </script>
    <script type="text/javascript" src="<?php echo Config::get('URL'); ?>js/app.js"></script>

