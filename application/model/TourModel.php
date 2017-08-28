<?php
require_once(Config::get('PATH_HELPER') . 'pin.php');
/**
 * NoteModel
 * This is basically a simple CRUD (Create/Read/Update/Delete) demonstration.
 */
class TourModel
{
    /**
     * Get all notes (notes are just example data that the user has created)
     * @return array an array with several objects (the results)
     */
    /*public static function getAllNotes()
    {
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT user_id, note_id, note_text FROM notes WHERE user_id = :user_id";
        $query = $database->prepare($sql);
        $query->execute(array(':user_id' => Session::get('user_id')));

        // fetchAll() is the PDO method that gets all result rows
        return $query->fetchAll();
    }*/

    public static function getTourByPin($pin)
    {
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT * FROM tours WHERE pin = :pin LIMIT 1";
        $query = $database->prepare($sql);
        $query->execute(array(':pin' => $pin));

        // fetch() is the PDO method that gets a single result
        return $query->fetch();
    }

    /**
     * Get a single tour by id
     * @param int $note_id id of the specific note
     * @return object a single object (the result)
     */
    public static function getTour($id)
    {
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT * FROM tours WHERE id = :id LIMIT 1";
        $query = $database->prepare($sql);
        $query->execute(array(':id' => $id));

        // fetch() is the PDO method that gets a single result
        return $query->fetch();
    }

    public static function generateTourQuestions(){
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "DELETE FROM tourquestions";
        $query = $database->prepare($sql);
        $query->execute();


        $sql = "SELECT * FROM tours";
        $query = $database->prepare($sql);
        $query->execute();

        // fetch() is the PDO method that gets a single result
        $tours =  $query->fetchAll();

        $sql = "SELECT * FROM questions";
        $query = $database->prepare($sql);
        $query->execute();

        // fetch() is the PDO method that gets a single result
        $questions =  $query->fetchAll();

        $sql = "INSERT INTO tourquestions(tour_id, question_id) VALUES";
        $values = '';
        foreach($tours as $tour){
            foreach($questions as $question){
                if ($values > '') $values .= ',';
                $values .= '(' . $tour->id . ',' . $question->id . ')';
            }
        }
        $sql .= $values . ';';
        $query = $database->prepare($sql);
        $query->execute();       
        //echo $sql;
    }

    public static function generateQuestionCodes(){
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT * FROM questions";
        $query = $database->prepare($sql);
        $query->execute();

        $questions = $query->fetchAll();

        $sql = "UPDATE questions SET code = :code WHERE id = :id";
        $query = $database->prepare($sql);
        foreach($questions as $question){
            $code = QuestionModel::getUniqueQuestionCode();
            $query->execute(array(':id'=>$question->id,':code'=>$code));
        }
    }

    public static function getUniqueTourPin(){
        while (true) {
            $pin = generatePin(4);
            $tour = self::getTourByPin($pin);
            if (!$tour) return $pin;
        } 
    }

    public static function generateTourPins(){
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT * FROM tours";
        $query = $database->prepare($sql);
        $query->execute();

        $tours = $query->fetchAll();

        $sql = "UPDATE tours SET pin = :pin WHERE id = :id";
        $query = $database->prepare($sql);
        foreach($tours as $tour){
            $pin = self::getUniqueTourPin();
            $query->execute(array(':id'=>$tour->id,':pin'=>$pin));
        }
    }

    public static function cleanupTour($tourId){
        $database = DatabaseFactory::getFactory()->getConnection();
        $sql = "SELECT answers.id FROM answers JOIN teams ON answers.team_id = teams.id WHERE teams.tour_id = :tour_id";

        $query = $database->prepare($sql);
        $query->execute(array(':tour_id'=>$tourId));  
        $answers = $query->fetchAll();

        $sql = "DELETE FROM answers WHERE id = :id";
        $query = $database->prepare($sql);
        foreach ($answers as $answer){
            $query->execute(array(':id'=>$answer->id));
        }

        $sql = "SELECT members.id FROM members JOIN teams ON members.team_id = teams.id WHERE teams.tour_id = :tour_id";

        $query = $database->prepare($sql);
        $query->execute(array(':tour_id'=>$tourId));  

        $members = $query->fetchAll();

        $sql = "DELETE FROM members WHERE id = :id";
        $query = $database->prepare($sql);
        foreach ($members as $member){
            $query->execute(array(':id'=>$member->id));
        }

        $sql = "DELETE FROM teams WHERE tour_id = :tour_id";

        $query = $database->prepare($sql);
        $query->execute(array(':tour_id'=>$tourId));

           
    }


    /**
     * Set a note (create a new one)
     * @param string $note_text note text that will be created
     * @return bool feedback (was the note created properly ?)
     */
    /*public static function createNote($note_text)
    {
        if (!$note_text || strlen($note_text) == 0) {
            Session::add('feedback_negative', Text::get('FEEDBACK_NOTE_CREATION_FAILED'));
            return false;
        }

        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "INSERT INTO notes (note_text, user_id) VALUES (:note_text, :user_id)";
        $query = $database->prepare($sql);
        $query->execute(array(':note_text' => $note_text, ':user_id' => Session::get('user_id')));

        if ($query->rowCount() == 1) {
            return true;
        }

        // default return
        Session::add('feedback_negative', Text::get('FEEDBACK_NOTE_CREATION_FAILED'));
        return false;
    }*/

    /**
     * Update an existing note
     * @param int $note_id id of the specific note
     * @param string $note_text new text of the specific note
     * @return bool feedback (was the update successful ?)
     */
    /*public static function updateNote($note_id, $note_text)
    {
        if (!$note_id || !$note_text) {
            return false;
        }

        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "UPDATE notes SET note_text = :note_text WHERE note_id = :note_id AND user_id = :user_id LIMIT 1";
        $query = $database->prepare($sql);
        $query->execute(array(':note_id' => $note_id, ':note_text' => $note_text, ':user_id' => Session::get('user_id')));

        if ($query->rowCount() == 1) {
            return true;
        }

        Session::add('feedback_negative', Text::get('FEEDBACK_NOTE_EDITING_FAILED'));
        return false;
    }*/

    /**
     * Delete a specific note
     * @param int $note_id id of the note
     * @return bool feedback (was the note deleted properly ?)
     */
    /*public static function deleteNote($note_id)
    {
        if (!$note_id) {
            return false;
        }

        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "DELETE FROM notes WHERE note_id = :note_id AND user_id = :user_id LIMIT 1";
        $query = $database->prepare($sql);
        $query->execute(array(':note_id' => $note_id, ':user_id' => Session::get('user_id')));

        if ($query->rowCount() == 1) {
            return true;
        }

        // default return
        Session::add('feedback_negative', Text::get('FEEDBACK_NOTE_DELETION_FAILED'));
        return false;
    }*/
}
