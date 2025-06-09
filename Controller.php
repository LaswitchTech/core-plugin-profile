<?php

/**
 * Core Framework - ProfileController
 *
 * @license    MIT (https://mit-license.org/)
 * @author     Louis Ouellet <louis@laswitchtech.com>
 */

// Import additionnal class into the global namespace
use \LaswitchTech\Core\Objects;
use \LaswitchTech\Core\Abstracts\Controller;

class ProfileController extends Controller {

    /**
     * Constructor
     */
    public function __construct()
    {

        // Call Parent Constructor
        parent::__construct();

        // Retrieve the namespace
        $namespace = $this->Request->getNamespace();

        // Set Global access
        $this->Public = false;

        // Set Properties
        switch($namespace){
            case "/profile/avatar":
                $this->Public = true;
                $this->Level = 0;
                break;
        }
    }

    /**
     * Fetch a Profile's Avatar
     */
    public function avatarAction(): array
    {
        // Retrieve the parameters
        $username = $this->Request->getParams('GET', 'username') ?? null;
        $size = $this->Request->getParams('GET', 'size') ?? 128;

        // Retrieve the user
        $user = $this->Model->Profile->avatar($username);

        // Check if user was retrieved
        if(isset($user['vcard'])){

            // Check if the user has an avatar
            if($user['vcard']['avatar']['uuid']){

                // Retrieve the file content
                $user['vcard']['avatar']['content'] = $this->Helper->Files->get($user['vcard']['avatar']['path'] . DIRECTORY_SEPARATOR . $user['vcard']['avatar']['uuid']);

                // Return the file
                return $user['vcard']['avatar'];
            }
        }

        // Create the default logo from the img folder
        $avatar = [
            'type' => $this->Helper->Gravatar->mimeType($username, $size),
            'content' => $this->Helper->Gravatar->content($username, $size)
        ];

        // Return the default logo
        return $avatar;
    }
}
