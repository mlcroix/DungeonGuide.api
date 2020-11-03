<?php
namespace Src\Controller;

use Src\TableGateways\PlayerGateway;

class PlayerController {
    private $db;
    private $requestMethod;
    private $Id;

    private $playerGateway;

    public function __construct($db, $requestMethod, $Id)
    {
        $this->db = $db;
        $this->requestMethod = $requestMethod;
        $this->Id = $Id;

        $this->playerGateway = new PlayerGateway($db);
    }

    public function processRequest()
    {
        
        switch ($this->requestMethod) {
            case 'GET':
                if ($this->Id) {
                    $response = $this->getPlayer($this->Id);
                } else {
                    $response = $this->getAllPlayers();
                };
                break;
            case 'POST':
                break;
            case 'PUT':
                $response = $this->updatePlayer($this->Id);
                break;
            case 'DELETE':
                $response = $this->deletePlayer($this->Id);
                break;
            default:
                $response = $this->notFoundResponse();
                break;
        }
        header($response['status_code_header']);
        if ($response['body']) {
            echo $response['body'];
        }
    }

    private function getAllPlayers()
    {
        $result = $this->playerGateway->findAll();
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    private function getPlayer($id)
    {
        $result = $this->playerGateway->find($id);
        if (! $result) {
            return $this->notFoundResponse();
        }
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    private function updatePlayer($id)
    {
        $result = $this->playerGateway->find($id);
        if (! $result) {
            return $this->notFoundResponse();
        }
        $input = (array) json_decode(file_get_contents('php://input'), TRUE);

        $this->campaignGateway->update($id, $input);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = null;
        return $response;
    }

    private function deletePlayer($id)
    {
        $result = $this->playerGateway->find($id);
        if (! $result) {
            return $this->notFoundResponse();
        }
        $this->campaignGateway->delete($id);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = null;
        return $response;
    }
}
?>