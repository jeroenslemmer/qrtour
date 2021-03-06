    <script type="text/javascript" src="<?php echo Config::get('URL'); ?>js/adapter.min.js"></script>
    <script type="text/javascript" src="<?php echo Config::get('URL'); ?>js/vue.min.js"></script>
    <script type="text/javascript" src="<?php echo Config::get('URL'); ?>js/ajax.js"></script>
    <div id="debug"></div>
    <div id="board">
      <h1>{{tour.name}} {{tour.year}}</h1>
      <h3>{{tour.feedback}}</h3>
      <table class="w3-table w3-bordered w3-white w3-striped">
        <tr><th class="column-team">Team (leden)</th><th>Punten</th><th>van maximaal</th></tr>
        <tr v-for="teamResult in tour.results"><td>{{teamResult.name}} ({{teamResult.members}})</td><td><strong>{{teamResult.result.score}}</strong></td><td>{{teamResult.result.max}}</td></tr>
      </table>
    </div>

    <script type="text/javascript">
      tour = {
        id: '<?php if($this->tour) echo $this->tour->id; ?>',
        name: '<?php if($this->tour) echo $this->tour->name; ?>',
        status: '<?php if($this->tour) echo $this->tour->status; ?>',
        year: '<?php if($this->tour) echo $this->tour->year; ?>',
        feedback: '',
        results: [{name: 'Jantjes', result: {score: 12, max: 15}}]
      } 
    </script>
    <script type="text/javascript" src="<?php echo Config::get('URL'); ?>js/board.js"></script>

