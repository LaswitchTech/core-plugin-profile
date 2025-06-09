<?php

/**
 * Core Framework - ProfileModel
 *
 * @license    MIT (https://mit-license.org/)
 * @author     Louis Ouellet <louis@laswitchtech.com>
 */

// Import additionnal class into the global namespace
use \LaswitchTech\Core\Abstracts\Model;

class ProfileModel extends Model {

    /**
     * Retrieve Profile's Details
     *
     * @param int $id
     * @param string $username
     * @return array
     */
    public function get(int $id, string $username): array
    {
        // Initialize the Objects
        $objects = [];

        // Retrieve the Events
        $Query = $this->Database->query()
            ->table('events')
            ->select('*')
            ->join('owner', 'users', 'username')
            ->filter()
            ->where('owner', $username)
            ->filter('OR')
            ->where('message', '%'.$username.'%', 'LIKE')
            ->filter('OR')
            ->where('targetTable', 'users')
            ->where('targetId', $id)
            ->index('id');
        $objects['events'] = $Query->result();

        // Retrieve the Files
        $Query = $this->Database->query()
            ->table('files')
            ->select('*')
            ->join('owner', 'users', 'username')
            ->filter()
            ->where('targetTable', 'users')
            ->where('targetId', $id)
            ->index('id');
        $objects['files'] = $Query->result();

        // Retrieve the Notes
        $Query = $this->Database->query()
            ->table('notes')
            ->select('*')
            ->join('owner', 'users', 'username')
            ->filter()
            ->where('targetTable', 'users')
            ->where('targetId', $id)
            ->index('id');
        foreach($Query->result() as $note){
            $note['sharedWith'] = json_decode($note['sharedWith'] ?? '[]', true);
            $objects['notes'][$note['id']] = $note;
        }

        // Retrieve the Contacts
        $Query = $this->Database->query()
            ->table('contacts')
            ->select('*')
            ->join('owner', 'users', 'username')
            ->join('vcard', 'vcards', 'id')
            ->filter()
            ->where('targetTable', 'users')
            ->where('targetId', $id)
            ->index('id');
        $objects['contacts'] = $Query->result();

        // Return the objects
        return $objects;
    }

    /**
     * Retrieve a Profile's Avatar
     *
     * @param string $username
     * @return array
     */
    public function avatar(string $username): array
    {
        // Create a Query
        $Query = $this->Database->query()
            ->table('users')
            ->select('*')
            ->filter()
            ->where('username', $username)
            ->limit(1);

        // Retrieve the User
        $user = $Query->result();

        // Check if the user exists
        if($user){

            // Select the user
            $user = $user[array_key_first($user)];

            // Create the Query
            $Query = $this->Database->query()
                ->table('vcards')
                ->select('*')
                ->join('avatar', 'files', 'id')
                ->order('id', 'ASC')
                ->filter()
                ->where('id', 9999, '<>')
                ->filter()
                ->where('id', $user['vcard'])
                ->limit(1);

            // Retrieve the vCard
            $vCard = $Query->result();

            // Return the vCard
            $user['vcard'] = $vCard[array_key_first($vCard)] ?? [];
        }

        // Return the User
        return $user;
    }
}
