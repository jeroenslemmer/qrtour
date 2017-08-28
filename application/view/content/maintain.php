    <script type="text/javascript" src="<?php echo Config::get('URL'); ?>js/adapter.min.js"></script>
    <script type="text/javascript" src="<?php echo Config::get('URL'); ?>js/vue.min.js"></script>
    <script type="text/javascript" src="<?php echo Config::get('URL'); ?>js/ajax.js"></script>
    <script type="text/javascript" src="<?php echo Config::get('URL'); ?>js/clone.js"></script>
    <div id="debug"></div>
    <div id="board">
      <h1>Beheer</h1>
      <section id="questions">
      <table>
      <tr><th>id</th><th>title</th><th>location</th><th>description</th><th><button v-on:click="add">add</button></th><th>options</th><th>tours</th>
        <tr v-for="question in questions"><td>{{question.id}}</td><td>{{question.title}}</td><td>{{question.location}}</td><td>{{question.description}}</td><td><button v-on:click="edit(question)">edit</button><button v-on:click="archive">archive</button></td>
        </tr> 
      </table>
      </section>
      <section id="edit-question" v-if="question" v-bind:class="{ focus: (spaBag.status == 1) }" >
        <div><label for="question-id">Id:</label><span id="question-id">{{question.id}}</span></div>
        <div><label for="question-title">Title:</label><input id="question-title" v-model="question.title"></div>
        <div><label for="question-location">Location:</label><textarea id="question-location" v-model="question.location"></textarea></div>
        <div><label for="question-description">Description:</label><textarea id="question-description" v-model="question.description"></textarea></div>
        <button v-on:click="submit">submit</button><button v-on:click="cancel">cancel</button>
      </section>
      <section id="edit-question" v-if="question" v-bind:class="{ focus: (spaBag.status == 1) }" >
        <div><label for="question-id">Id:</label><span id="question-id">{{question.id}}</span></div>
        <div><label for="question-title">Title:</label><input id="question-title" v-model="question.title"></div>
        <div><label for="question-location">Location:</label><textarea id="question-location" v-model="question.location"></textarea></div>
        <div><label for="question-description">Description:</label><textarea id="question-description" v-model="question.description"></textarea></div>
        <button v-on:click="submit">submit</button><button v-on:click="cancel">cancel</button>
      </section>
    </div>
    <script>
      spaBag = {
        status: 0, // 0:doing nothing; 1:editing
        csrf: '<?php echo $this->csrf; ?>'
      }
    </script>

    <script type="text/javascript" src="<?php echo Config::get('URL'); ?>js/maintain.js"></script>

