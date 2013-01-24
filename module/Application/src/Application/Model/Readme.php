<?php
namespace Application\Model;

use Application\Model\Readme;
use Application\Model\ReadmeTable;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;

class Readme
{
    public $id;
    public $user;
    public $reponame;
    public $type;
    public $status;
    public $title;
    public $description;
    public $livelink;
    public $features;
    public $members;
    public $instructions;
    public $requirements;
    public $resourcelinks;
    

    public function exchangeArray($data)
    {
        $this->id     = (isset($data['id'])) ? $data['id'] : null;
        $this->user = (isset($data['user'])) ? $data['user'] : null;
        $this->title     = (isset($data['title'])) ? $data['title'] : null;
        $this->reponame  = (isset($data['reponame'])) ? $data['reponame'] : null;
        $this->type  = (isset($data['type'])) ? $data['type'] : null;
        $this->status  = (isset($data['status'])) ? $data['status'] : null;
        $this->description     = (isset($data['description'])) ? $data['description'] : null;
        $this->livelink     = (isset($data['livelink'])) ? $data['livelink'] : null;
        $this->features     = (isset($data['features'])) ? $data['features'] : null;
        $this->members     = (isset($data['members'])) ? $data['members'] : null;
        $this->instructions     = (isset($data['instructions'])) ? $data['instructions'] : null;
        $this->requirements     = (isset($data['requirements'])) ? $data['requirements'] : null;
        $this->resourcelinks     = (isset($data['resourcelinks'])) ? $data['resourcelinks'] : null;
    }
}