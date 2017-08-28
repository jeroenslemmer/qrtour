<?php
require_once(Config::get('PATH_HELPER') . 'censor.php');
require_once(Config::get('PATH_HELPER') . 'pin.php');
/**
 * NoteModel
 * This is basically a simple CRUD (Create/Read/Update/Delete) demonstration.
 */
class MemberModel
{
    /**
     * Get all notes (notes are just example data that the user has created)
     * @return array an array with several objects (the results)
     */
    public static function getAllMemberByTeam($teamId)
    {
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT name FROM members WHERE team_id = :team_id";
        $query = $database->prepare($sql);
        $query->execute(array(':team_id' => $teamId));

        // fetchAll() is the PDO method that gets all result rows
        return $query->fetchAll();
    }

    public static function getMember($id)
    {
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT * FROM members WHERE id = :id LIMIT 1";
        $query = $database->prepare($sql);
        $query->execute(array(':id' => $id));

        // fetch() is the PDO method that gets a single result
        return $query->fetch();
    }


    /**
     * Get a single member
     * @param int $note_id id of the specific note
     * @return object a single object (the result)
     */
    public static function getMemberByToken($memberToken)
    {
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT * FROM members WHERE token = :token LIMIT 1";
        $query = $database->prepare($sql);
        $query->execute(array(':token' => $memberToken));

        // fetch() is the PDO method that gets a single result
        return $query->fetch();
    }

    public static function getMemberByNameAndTeamId($name, $teamId)
    {
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT * FROM members WHERE name = :name and team_id = :team_id LIMIT 1";
        $query = $database->prepare($sql);
        $query->execute(array(':name' => $name, ':team_id'=>$teamId));

        // fetch() is the PDO method that gets a single result
        return $query->fetch();
    }

    public static function getMemberByCheckedToken()
    {
        //Cookie::set('memberToken','abcdefg');
        $memberToken = Cookie::get('memberToken');
        // TEST
        //$memberToken = '1234';
        if ($memberToken){
            $member = self::getMemberByToken($memberToken);
            if (!$member){
                Cookie::delete('memberToken');
                return false;
            } 
            return $member;        
        }
        return false;
    }

    public static function validateNewNameOK($name, $teamId = false){
        $clean = censorNameOK($name);
        if ($clean && $teamId){
            $member = self::getMemberByNameAndTeamId($name, $teamId);
            return (!$member);
        }
        return $clean;
    }

    /**
     * Set a member for a team (create a new one)
     * @param string $note_text note text that will be created
     * @return bool feedback (was the note created properly ?)
     */
    public static function createMember($teamId, $memberName)
    {
        if (!$memberName || strlen($memberName) == 0) {
            Session::add('feedback_negative', Text::get('FEEDBACK_MEMBER_CREATION_FAILED'));
            return false;
        }

        $pin = generatePin(40);

        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "INSERT INTO members (name, team_id, token) VALUES (:name, :team_id, :token)";
        $query = $database->prepare($sql);
        $query->execute(array(':name' => $memberName, ':team_id' => $teamId,':token'=>$pin));

        if ($query->rowCount() == 1) {
            return $database->lastInsertId();
        }

        // default return
        Session::add('feedback_negative', Text::get('FEEDBACK_MEMBER_CREATION_FAILED'));
        return false;
    }

    /**
     * Update an existing note
     * @param int $note_id id of the specific note
     * @param string $note_text new text of the specific note
     * @return bool feedback (was the update successful ?)
     */
    public static function updateMember($id, $teamId, $name)
    {
        if (!$teamId || !$name) {
            return false;
        }

        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "UPDATE members SET team_id = :team_id, name = :name WHERE id = :id LIMIT 1";
        $query = $database->prepare($sql);
        $query->execute(array(':id' => $id, ':team_id' => $teamId, ':name' => $name));

        if ($query->rowCount() == 1) {
            return true;
        }

        Session::add('feedback_negative', Text::get('FEEDBACK_MEMBER_EDITING_FAILED'));
        return false;
    }

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
