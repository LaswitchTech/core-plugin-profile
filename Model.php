<?php

// Import additionnal class into the global namespace
use \LaswitchTech\Core\Abstracts\Model;

class ProfileModel extends Model {

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
