<?php

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
     * Retrieve a record
     */
    public function fetchAction(): array
    {
        // Set the default message
        $message = ["status" => 200, "message" => "OK", "data" => []];

        // Check if the records is accessible
        if($message['status'] == 200){

            // Check the request method
            if($this->Request->getMethod() == "GET"){

                // Retrieve the record
                $record = $this->Model->Users->fetch($this->Auth->user()->id);

                // Check if the record was found
                if(!empty($record)){

                    // Set the record in the message
                    $message['data']['record'] = $record;

                    // Check if the vCards Plugin is accessible
                    if($this->Helper->Core->isInstalled('vcards')){
                        $message['data']['record']['vcard'] = $this->Model->Vcards->fetch(intval($message['data']['record']['vcard']['id']));
                    }

                    // Check if the Organizations Plugin is accessible
                    if($this->Helper->Core->isInstalled('organizations')){
                        $message['data']['record']['organization'] = $this->Model->Organizations->fetch(intval($message['data']['record']['organization']['id']));
                    }

                    // Check if the Relationship Plugin is accessible
                    if($this->Helper->Core->isInstalled('relationship')){
                        $message['data']['dependencies']['relationship'] = $this->Model->Relationship->get('users', $message['data']['record']['id']);
                        if($this->Helper->Core->isInstalled('vcards')){
                            $message['data']['dependencies']['relationship'] = array_merge(
                                $message['data']['dependencies']['relationship'],
                                $this->Model->Relationship->get('vcards', $message['data']['record']['vcard']['id'])
                            );
                        }
                    }

                    // Check if the Contacts is accessible
                    if($this->Helper->Core->isInstalled('contacts')){
                        $message['data']['dependencies']['contacts'] = $this->Model->Contacts->fetchAll([
                            ["key" => "targetTable", "operator" => "=", "value" => 'users'],
                            ["key" => "targetId", "operator" => "=", "value" => $message['data']['record']['id']],
                            ["key" => "isArchived", "operator" => "<>", "value" => 1],
                        ]);
                    }

                    // Check if the Events is accessible
                    if($this->Helper->Core->isInstalled('event')){
                        $message['data']['dependencies']['event'] = $this->Model->Event->fetchAll([
                            ["key" => "targetTable", "operator" => "=", "value" => 'users'],
                            ["key" => "targetId", "operator" => "=", "value" => $message['data']['record']['id']],
                            ["key" => "isArchived", "operator" => "<>", "value" => 1],
                        ]);
                    }

                    // Check if the Files is accessible
                    if($this->Helper->Core->isInstalled('files')){
                        $message['data']['dependencies']['files'] = $this->Model->Files->fetchAll([
                            ["key" => "targetTable", "operator" => "=", "value" => 'users'],
                            ["key" => "targetId", "operator" => "=", "value" => $message['data']['record']['id']],
                            ["key" => "isArchived", "operator" => "<>", "value" => 1],
                        ]);
                    }

                    // Check if the Notes is accessible
                    if($this->Helper->Core->isInstalled('notes')){
                        $message['data']['dependencies']['notes'] = $this->Model->Notes->fetchAll([
                            ["key" => "targetTable", "operator" => "=", "value" => 'users'],
                            ["key" => "targetId", "operator" => "=", "value" => $message['data']['record']['id']],
                            ["key" => "isArchived", "operator" => "<>", "value" => 1],
                        ]);
                    }
                } else {
                    $message = ["status" => 404,"message" => "Record not found","data" => "Could not find the requested record.",];
                }
            } else {
                $message = ["status" => 405, "message" => "Method Not Allowed", "data" => "The requested method is not allowed for this endpoint."];
            }
        }

        // Return the message
        return $message;
    }
}
