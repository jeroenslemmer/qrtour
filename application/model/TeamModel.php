<?php
require_once(Config::get('PATH_HELPER') . 'pin.php');
require_once(Config::get('PATH_HELPER') . 'censor.php');

/**
 * NoteModel
 * This is basically a simple CRUD (Create/Read/Update/Delete) demonstration.
 */
class TeamModel
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
    public static function getTeam($team_id)
    {
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT * FROM teams WHERE id = :team_id LIMIT 1";
        $query = $database->prepare($sql);
        $query->execute(array(':team_id' => $team_id));

        // fetch() is the PDO method that gets a single result
        return $query->fetch();
    }

    public static function getTeamByPin($pin)
    {
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT * FROM teams WHERE pin = :pin LIMIT 1";
        $query = $database->prepare($sql);
        $query->execute(array(':pin' => $pin));

        // fetch() is the PDO method that gets a single result
        return $query->fetch();
    }

    public static function getTeamByNameAndTourId($name,$tourId)
    {
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT * FROM teams WHERE name = :name and tour_id = :tour_id LIMIT 1";
        $query = $database->prepare($sql);
        $query->execute(array(':name' => $name,'tour_id'=>$tourId));

        // fetch() is the PDO method that gets a single result
        return $query->fetch();
    }

    public static function getNewPin(){
        $database = DatabaseFactory::getFactory()->getConnection();
        $sql = "SELECT * FROM teams WHERE pin = :pin LIMIT 1";
        $query = $database->prepare($sql);
        do {
            $pin = generatePin(6);
            $query->execute(array(':pin' => $pin));
            $team = $query->fetch();

        } while ($team);
        return $pin;
    }


    public static function validateNewNameOK($name, $tourId){
        $clean = censorNameOK($name);
        if ($clean){
            $team = self::getTeamByNameAndTourId($name, $tourId);
            return !$team;
       }
       return false;
    }

    /**
     * Set a note (create a new one)
     * @param string $note_text note text that will be created
     * @return bool feedback (id of newly created team or false)
     */
    public static function createTeam($tour_id, $name)
    {
        if (!$name || strlen($name) == 0 || !$tour_id) {
            Session::add('feedback_negative', Text::get('FEEDBACK_TEAM_CREATION_FAILED'));
            return false;
        }

        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "INSERT INTO teams (name, tour_id, pin) VALUES (:name, :tour_id, :pin)";
        $query = $database->prepare($sql);
        $pin = self::getNewPin();
        $query->execute(array(':name' => $name, ':tour_id' => $tour_id,':pin'=>$pin));

        if ($query->rowCount() == 1) {
            return $database->lastInsertId();
        }

        // default return
        Session::add('feedback_negative', Text::get('FEEDBACK_TEAM_CREATION_FAILED'));
        return false;
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
