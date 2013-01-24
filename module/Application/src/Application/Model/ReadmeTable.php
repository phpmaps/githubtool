<?php
namespace Application\Model;

use Zend\Db\TableGateway\TableGateway;

class ReadmeTable
{
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll()
    {
        $resultSet = $this->tableGateway->select();
        return $resultSet;
    }

    public function getReadme($id)
    {
        $id  = (int) $id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function saveReadme(Readme $readme)
    {
        $data = array(
        	'user' => $readme->user,
            'reponame' => $readme->reponame,
        	'type' => $readme->type,
        	'status' => $readme->status,
            'title'  => $readme->title,
        	'description' => $readme->description,
        	'livelink' => $readme->livelink,
        	'features' => $readme->features,
        	'instructions' => $readme->instructions,
        	'requirements' => $readme->requirements,
        	'resourcelinks' => $readme->resourcelinks
        );

        $id = (int)$readme->id;
        if ($id == 0) {
            $r = $this->tableGateway->insert($data);
            return array("success" => true, "type" => "insert", "transid" => $this->tableGateway->getLastInsertValue());
        } else {
            if ($this->getReadme($id)) {
                $r = $this->tableGateway->update($data, array('id' => $id));
                return array("success" => true, "type" => "update", "transid" => $readme->id);
            } else {
                throw new \Exception('Form id does not exist');
            }
        }
        return;
    }

    public function deleteReadme($id)
    {
        $this->tableGateway->delete(array('id' => $id));
    }
}