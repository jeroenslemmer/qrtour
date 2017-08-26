<?php
require_once(Config::get('PATH_HELPER') . 'pin.php');
require_once(Config::get('PATH_HELPER') . 'censor.php');

/**
 * NoteModel
 * This is basically a simple CRUD (Create/Read/Update/Delete) demonstration.
 */
class QuestionModel
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

    /**
     * Get a single note
     * @param int $note_id id of the specific note
     * @return object a single object (the result)
     */
    public static function getQuestionByTourIdAndCode($tourId, $code)
    {
        $database = DatabaseFactory::getFactory()->getConnection();
        $sql = "SELECT questions.* FROM questions JOIN tourquestions ON tourquestions.question_id = questions.id WHERE questions.code = :code and tourquestions.tour_id = :tour_id LIMIT 1";

        $query = $database->prepare($sql);
        $query->execute(array(':tour_id' => $tourId, ':code'=>$code));

        // fetch() is the PDO method that gets a single result
        return $query->fetch();
    }

    public static function getTeamQuestionAnswer($questionId, $teamId){
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT * FROM answers JOIN options ON answers.option_id = options.id WHERE answers.team_id = :team_id and options.question_id = :question_id LIMIT 1";

        $query = $database->prepare($sql);
        $query->execute(array(':team_id' => $teamId, ':question_id'=>$questionId));

        // fetch() is the PDO method that gets a single result
        return $query->fetch();
    }


    public static function getOptions($questionId){
        $database = DatabaseFactory::getFactory()->getConnection();
        $sql = "SELECT * FROM options WHERE options.question_id = :question_id";

        $query = $database->prepare($sql);
        $query->execute(array(':question_id'=>$questionId));

        // fetch() is the PDO method that gets a single result
        return $query->fetchAll();
    }

    public static function getOption($optionId){
        $database = DatabaseFactory::getFactory()->getConnection();
        $sql = "SELECT * FROM options WHERE id = :option_id LIMIT 1";

        $query = $database->prepare($sql);
        $query->execute(array(':option_id'=>$optionId));

        // fetch() is the PDO method that gets a single result
        return $query->fetch();
    }

/*    public static function teamScore($teamId){
        $database = DatabaseFactory::getFactory()->getConnection();
        $scores = [];
        $sql = "SELECT * FROM questions JOIN tourquestions ON tourquestions.question_id = questions.idWHERE id = :option_id LIMIT 1";

        $scores['max'] 
    }
    */

    public static function answer($optionId, $teamId){
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "INSERT INTO answers (team_id, option_id) VALUES (:team_id, :option_id)";
        $query = $database->prepare($sql);
        $query->execute(array(':team_id' => $teamId, ':option_id' => $optionId));

        if ($query->rowCount() == 1) {
            return $database->lastInsertId();
        }

        // default return
        Session::add('feedback_negative', Text::get('FEEDBACK_ANSWER_CREATION_FAILED'));

    }
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
