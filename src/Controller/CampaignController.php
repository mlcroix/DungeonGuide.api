<?php
namespace Src\Controller;

use Src\TableGateways\CampaignGateway;

class CampaignController {

    private $db;
    private $requestMethod;
    private $Id;

    private $campaignGateway;

    public function __construct($db, $requestMethod, $Id)
    {
        $this->db = $db;
        $this->requestMethod = $requestMethod;
        $this->Id = $Id;

        $this->campaignGateway = new CampaignGateway($db);
    }

    public function processRequest()
    {
        switch ($this->requestMethod) {
            case 'GET':
                if ($this->Id) {
                    $response = $this->getCampaign($this->Id);
                } else {
                    $response = $this->getAllCampaigns();
                };
                break;
            case 'POST':
                $response = $this->createCampaignFromRequest();
                break;
            case 'PUT':
                $response = $this->updateCampaign($this->Id);
                break;
            case 'DELETE':
                $response = $this->deleteCampaign($this->Id);
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

    private function getAllCampaigns()
    {
        $result = $this->campaignGateway->findAll();
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    private function getCampaign($id)
    {
        $result = $this->campaignGateway->find($id);
        if (! $result) {
            return $this->notFoundResponse();
        }
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    private function createCampaignFromRequest()
    {
        $input = (array) json_decode(file_get_contents('php://input'), TRUE);

        $this->campaignGateway->insert($input);
        $response['status_code_header'] = 'HTTP/1.1 201 Created';
        $response['body'] = null;
        return $response;
    }

    private function updateCampaign($id)
    {
        $result = $this->campaignGateway->find($id);
        if (! $result) {
            return $this->notFoundResponse();
        }
        $input = (array) json_decode(file_get_contents('php://input'), TRUE);

        $this->campaignGateway->update($id, $input);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = null;
        return $response;
    }

    private function deleteCampaign($id)
    {
        $result = $this->campaignGateway->find($id);
        if (! $result) {
            return $this->notFoundResponse();
        }
        $this->campaignGateway->delete($id);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = null;
        return $response;
    }

    private function unprocessableEntityResponse()
    {
        $response['status_code_header'] = 'HTTP/1.1 422 Unprocessable Entity';
        $response['body'] = json_encode([
            'error' => 'Invalid input'
        ]);
        return $response;
    }

    private function notFoundResponse()
    {
        $response['status_code_header'] = 'HTTP/1.1 404 Not Found';
        $response['body'] = null;
        return $response;
    }
}
?>