<?php

/**
 * Core Framework - ProfileEndpoint
 *
 * @license    MIT (https://mit-license.org/)
 * @author     Louis Ouellet <louis@laswitchtech.com>
 */

// Import additionnal class into the global namespace
use \LaswitchTech\Core\Objects;
use \LaswitchTech\Core\Abstracts\Endpoint;

class ProfileEndpoint extends Endpoint {

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
            case "/profile/fetch":
                $this->Level = 0;
                break;
        }
    }

    /**
     * Fetch Profile Information
     */
    public function fetchAction(): array
    {
        // Set the default message
        $message = ["status" => 200, "message" => "OK", "data" => [
            "id" => $this->Auth->user()->id,
            "username" => $this->Auth->user()->username,
            "objects" => $this->Model->Profile->get($this->Auth->user()->id, $this->Auth->user()->username),
            "vcards" => [
                "user" => $this->Model->Vcards->get($this->Auth->user()->vcard['id']),
                "organization" => $this->Model->Vcards->get($this->Auth->user()->organization()->id),
            ],
        ]];

        // Return the message
        return $message;
    }
}
