<?php
namespace Application\Model;
use Application\Model\SerializeArrayToJson;

class SerializeArrayToJson implements JsonSerializable {
	
	public function __construct(array $array) {
		$this->array = $array;
	}

	public function jsonSerialize() {
		return $this->array;
	}
}