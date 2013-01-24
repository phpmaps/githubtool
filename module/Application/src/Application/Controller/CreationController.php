<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Application\Model\Readme;
use Zend\Session\Container;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Json\Json;

class CreationController extends AbstractActionController
{
	protected $readmeTable;
	protected $serializer;
    public function indexAction()
    {
    	return new ViewModel(array(
    			'readmes' => $this->getReadmeTable()->fetchAll(),
    	));
    }
    
    public function readmeAction()
    {
    	$id = (int)$this->params('id');
    	if (!$id) {
    		return $this->redirect()->toRoute('application', array('action'=>'index'));
    	}
    	
    	//Get the readme document from the readme table
    	$row = $this->getReadmeTable()->getReadme($id);
    	
    	//Convert the returned table value from Json to a php Array
    	$phpNative = \Zend\Json\Json::decode($row->requirements, \Zend\Json\Json::TYPE_ARRAY);
    	
    	$obj = (object) array('id' => $row->id, 'requirements' => $phpNative);
    	return new ViewModel(array(
    			'models' => array($obj),
    	));
    }
    
    public function removeAction()
    {
    	$request = $this->getRequest();
    	$response = $this->getResponse();
    	if ($request->isPost()) {
    		$post_data = $request->getPost();
    		$row = $this->getReadmeTable()->getReadme($post_data['rid']);
    		$data = array(
    				'id' => $row->id,
    				'user' => $row->user,
    				'reponame' => $row->reponame,
    				'title'  => $row->title,
    				'description' => $row->description,
    				'livelink' => $row->livelink,
    				'features' => $row->features,
    				'instructions' => $row->instructions,
    				'requirements' => $row->requirements,
    				'resourcelinks' => $row->resourcelinks
    		);
    
    		//Convert the returned table value from Json to a php Array
    		$phpNative = \Zend\Json\Json::decode($row->requirements, \Zend\Json\Json::TYPE_ARRAY);
    		$cleanArray = array();
    		foreach ($phpNative as $key => $value) {
    			if ($value == $post_data['requirements']) {
    				//do nothing
    				$v = "";
    			}else{
    				array_push($cleanArray, $value);
    			}
    		}
    		
    		$rjson = \Zend\Json\Json::encode($cleanArray);
    		$data['requirements'] = $rjson;
    
    		$rm = new Readme();
    		$rm->exchangeArray($data);
    		$r = $this->getReadmeTable()->saveReadme($rm);
    
    		$response->setContent(\Zend\Json\Json::encode(array('response' => true, 'details'=> array("type" => "remove", "transid" =>$post_data['rid']), 'results' => array())));
    	}else{
    		$response->setContent(\Zend\Json\Json::encode(array('response' => false, 'details' => array("error" => "Post not recognized."))));
    	}
    	return $response;
    }
    
    public function instructionsAction()
    {
    	$id = (int)$this->params('id');
    	if (!$id) {
    		return $this->redirect()->toRoute('application', array('action'=>'index'));
    	}
    	
    	//Get the readme document from the readme table
    	$row = $this->getReadmeTable()->getReadme($id);
    	
    	//Convert the returned table value from Json to a php Array
    	$phpNative = \Zend\Json\Json::decode($row->instructions, \Zend\Json\Json::TYPE_ARRAY);
    	
    	$obj = (object) array('id' => $row->id, 'instructions' => $phpNative);
    	return new ViewModel(array(
    			'models' => array($obj),
    	));
    }
    
    public function statusAction()
    {
    	$id = (int)$this->params('id');
    	$session = new Container('Default');
    	if (!$id) {
    		$obj = (object) array('id' => "", 'type' => "", 'status' => "Pending", "sess" => $session->type);
    	}else{
    		$row = $this->getReadmeTable()->getReadme($id);
    		$type = \Zend\Json\Json::decode($row->type, \Zend\Json\Json::TYPE_ARRAY);
    		$status = \Zend\Json\Json::decode($row->status, \Zend\Json\Json::TYPE_ARRAY);
    		$obj = (object) array('id' => $row->id, 'type' => $type[0], 'status' => $status[0], "sess" => $session->type);
    	}
    	return new ViewModel(array(
    			'models' => $obj,
    	));
    }
    
    public function updateStatusAction()
    {
    	$session = new Container('Default');
    	$request = $this->getRequest();
    	$response = $this->getResponse();
    	if ($request->isPost()) {
    		$post_data = $request->getPost();
    		if(isset($post_data['rid']) && $post_data['rid'] != ""){
    			//check to see if the type in the database is the same as what's being posted
    			$row = $this->getReadmeTable()->getReadme($post_data['rid']);
    			if($row->type == $post_data['type']) {
    				$session->typechanged = false;
    			}else{
    				$session->typechanged = true;
    				$typejson = \Zend\Json\Json::encode(array($post_data['type']));
    				$statusjson = \Zend\Json\Json::encode(array("Pending"));
    				$data = array(
    						'id' => $row->id,
    						'user' => $row->user,
    						'reponame' => $row->reponame,
    						'type' => $typejson,
    						'status' => $statusjson,
    						'title'  => $row->title,
    						'description' => $row->description,
    						'livelink' => $row->livelink,
    						'features' => $row->features,
    						'instructions' => $row->instructions,
    						'requirements' => $row->requirements,
    						'resourcelinks' => $row->resourcelinks
    				);
    				
    				$rm = new Readme();
    				$rm->exchangeArray($data);
    				$r = $this->getReadmeTable()->saveReadme($rm);
    				
    				$response->setContent(\Zend\Json\Json::encode(array('response' => true, 'details'=> $r, 'results' => array())));
    				return $response;
    			}
    			
    		}else{
    			//DONT Store anything because we don't want garbage stored in a table without a reponame
    			$session->type = $post_data['type'];
    			$session->status = "Pending";
    			$r = array("success" => true, "type" => "session", "transid" => "");
    			$response->setContent(\Zend\Json\Json::encode(array('response' => true, 'details'=> $r, 'results' => array())));
    			return $response;
    		}
    	}
    }
    
    public function updateDetailsAction()
    {
    	$session = new Container('Default');
    	$request = $this->getRequest();
    	$response = $this->getResponse();
    	if ($request->isPost()) {
    		$post_data = $request->getPost();
    		if(isset($post_data['rid']) && $post_data['rid'] != ""){
    			//check to see if the type in the database is the same as what's being posted
    			$row = $this->getReadmeTable()->getReadme($post_data['rid']);
    			$reponame = (isset($post_data['reponame'])) ? \Zend\Json\Json::encode(array($post_data['reponame'])) : $row->reponame;
    			$title = (isset($post_data['title'])) ? \Zend\Json\Json::encode(array($post_data['title'])) : $row->title;
    			$description = (isset($post_data['description'])) ? \Zend\Json\Json::encode(array($post_data['description'])) : $row->description;
    			$features = (isset($post_data['features'])) ? $post_data['features'] : $row->features;
    			//$reponameJson = \Zend\Json\Json::encode(array($post_data['reponame']));
    			//$statusjson = \Zend\Json\Json::encode(array("Pending"));
    				$data = array(
    						'id' => $row->id,
    						'user' => $row->user,
    						'reponame' => $reponame,
    						'type' => $row->type,
    						'status' => $row->status,
    						'title'  => $title,
    						'description' => $description,
    						'livelink' => $row->livelink,
    						'features' => $features,
    						'instructions' => $row->instructions,
    						'requirements' => $row->requirements,
    						'resourcelinks' => $row->resourcelinks
    				);
    
    				$rm = new Readme();
    				$rm->exchangeArray($data);
    				$r = $this->getReadmeTable()->saveReadme($rm);
    
    				$response->setContent(\Zend\Json\Json::encode(array('response' => true, 'details'=> $r, 'results' => array())));
    				return $response;
    			 
    		}else{
    			//DONT Store anything because we don't want garbage stored in a table without a reponame
    			$reponame = (isset($post_data['reponame'])) ? \Zend\Json\Json::encode(array($post_data['reponame'])) : \Zend\Json\Json::encode(array());
    			$title = (isset($post_data['title'])) ? \Zend\Json\Json::encode(array($post_data['title'])) : \Zend\Json\Json::encode(array());
    			$description = (isset($post_data['description'])) ? \Zend\Json\Json::encode(array($post_data['description'])) : \Zend\Json\Json::encode(array());
    			$features = (isset($post_data['features'])) ? $post_data['features'] : \Zend\Json\Json::encode(array());
    			$type = $session->type;
    			$status = "Pending";
    			
    			$data = array(
    					'id' => "",
    					'user' => \Zend\Json\Json::encode(array()),
    					'reponame' => $reponame,
    					'type' => \Zend\Json\Json::encode(array($type)),
    					'status' => \Zend\Json\Json::encode(array($status)),
    					'title'  => $title,
    					'description' => $description,
    					'members' => \Zend\Json\Json::encode(array()),
    					'livelink' => \Zend\Json\Json::encode(array()),
    					'features' => $features,
    					'instructions' => \Zend\Json\Json::encode(array()),
    					'requirements' => \Zend\Json\Json::encode(array()),
    					'resourcelinks' => \Zend\Json\Json::encode(array())
    			);
    			
    			$rm = new Readme();
    			$rm->exchangeArray($data);
    			$r = $this->getReadmeTable()->saveReadme($rm);
    			
    			//$r = array("success" => true, "type" => "session", "transid" => "");
    			$response->setContent(\Zend\Json\Json::encode(array('response' => true, 'details'=> $r, 'results' => array())));
    			return $response;
    		}
    	}
    }
    
    public function detailsAction()
    {
    	$id = (int)$this->params('id');
    	$session = new Container('Default');
    	$type = $session->type;
    	if (!$id) {
    		$obj = (object) array('id' => "", 'type' => "", "sess" => $session->type, "features" => array(), "description" => "");
    	}else{
    		$row = $this->getReadmeTable()->getReadme($id);
    		$type = \Zend\Json\Json::decode($row->type, \Zend\Json\Json::TYPE_ARRAY);
    		$reponame = \Zend\Json\Json::decode($row->reponame, \Zend\Json\Json::TYPE_ARRAY);
    		$title = \Zend\Json\Json::decode($row->title, \Zend\Json\Json::TYPE_ARRAY);
    		$description = \Zend\Json\Json::decode($row->description, \Zend\Json\Json::TYPE_ARRAY);
    		$features = \Zend\Json\Json::decode($row->features, \Zend\Json\Json::TYPE_ARRAY);
    		$obj = (object) array('id' => $row->id, 'type' => $type[0], "sess" => $session->type, "reponame" => $reponame, 'title' => $title, 'description' => $description, 'features' => $features);
    	}
    	return new ViewModel(array(
    			'models' => $obj,
    	));
    }
    
    public function ownershipAction()
    {
    	$id = (int)$this->params('id');
    	if (!$id) {
    		return $this->redirect()->toRoute('application', array('action'=>'index'));
    	}
    	 
    	//Get the readme document from the readme table
    	$row = $this->getReadmeTable()->getReadme($id);
    	 
    	//Convert the returned table value from Json to a php Array
    	$phpNative = \Zend\Json\Json::decode($row->members, \Zend\Json\Json::TYPE_ARRAY);
    	 
    	$obj = (object) array('id' => $row->id, 'members' => $phpNative);
    	return new ViewModel(array(
    			'models' => array($obj),
    	));
    }
    
    public function addInstructionsAction()
    {
    	$request = $this->getRequest();
    	$response = $this->getResponse();
    	if ($request->isPost()) {
    		$post_data = $request->getPost();
    		$row = $this->getReadmeTable()->getReadme($post_data['rid']);
    		$data = array(
    				'id' => $row->id,
    				'user' => $row->user,
    				'reponame' => $row->reponame,
    				'title'  => $row->title,
    				'description' => $row->description,
    				'livelink' => $row->livelink,
    				'features' => $row->features,
    				'instructions' => $row->instructions,
    				'requirements' => $row->requirements,
    				'resourcelinks' => $row->resourcelinks
    		);
    
    		//Convert the returned table value from Json to a php Array
    		$phpNative = \Zend\Json\Json::decode($row->instructions, \Zend\Json\Json::TYPE_ARRAY);
    		if(is_array($phpNative)) {
    			array_push($phpNative, $post_data['instructions']);
    			$rjson = \Zend\Json\Json::encode($phpNative);
    			$data['instructions'] = $rjson;
    		}else{
    			$rjson = \Zend\Json\Json::encode(array($post_data['instructions']));
    			$data['instructions'] = $rjson;
    		}
    
    		$rm = new Readme();
    		$rm->exchangeArray($data);
    		$r = $this->getReadmeTable()->saveReadme($rm);
    
    		$response->setContent(\Zend\Json\Json::encode(array('response' => true, 'details'=> $r, 'results' => array())));
    	}else{
    		$response->setContent(\Zend\Json\Json::encode(array('response' => false, 'details' => array("error" => "Post not recognized."))));
    	}
    	return $response;
    }
    
    public function addAction()
    {
    	$request = $this->getRequest();
    	$response = $this->getResponse();
    	if ($request->isPost()) {
    		$post_data = $request->getPost();
    		$row = $this->getReadmeTable()->getReadme($post_data['rid']);
    		$data = array(
    				'id' => $row->id,
    				'user' => $row->user,
    				'reponame' => $row->reponame,
    				'title'  => $row->title,
    				'description' => $row->description,
    				'livelink' => $row->livelink,
    				'features' => $row->features,
    				'instructions' => $row->instructions,
    				'requirements' => $row->requirements,
    				'resourcelinks' => $row->resourcelinks
    		);
    		
    		//Convert the returned table value from Json to a php Array
    		$phpNative = \Zend\Json\Json::decode($row->requirements, \Zend\Json\Json::TYPE_ARRAY);
    		if(is_array($phpNative)) {
    			array_push($phpNative, $post_data['requirements']);
    			$rjson = \Zend\Json\Json::encode($phpNative);
    			$data['requirements'] = $rjson;
    		}else{
    			$rjson = \Zend\Json\Json::encode(array($post_data['requirements']));
    			$data['requirements'] = $rjson;
    		}
    		
    		$rm = new Readme();
    		$rm->exchangeArray($data);
    		$r = $this->getReadmeTable()->saveReadme($rm);
    		
    		$response->setContent(\Zend\Json\Json::encode(array('response' => true, 'details'=> $r, 'results' => array())));
    	}else{
    		$response->setContent(\Zend\Json\Json::encode(array('response' => false, 'details' => array("error" => "Post not recognized."))));
    	}
    	return $response;
    }
    
    public function getReadmeTable()
    {
    	if (!$this->readmeTable) {
    		$sm = $this->getServiceLocator();
    		$this->readmeTable = $sm->get('Application\Model\ReadmeTable');
    	}
    	return $this->readmeTable;
    }
    
    public function getSerializer()
    {
    	if (!$this->serializer) {
    		$sm = $this->getServiceLocator();
    		$this->serializer = $sm->get('Application\Model\SerializeArrayToJson');
    	}
    	return $this->serializer;
    }
}
