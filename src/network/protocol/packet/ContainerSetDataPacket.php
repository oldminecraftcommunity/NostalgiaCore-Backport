<?php

class ContainerSetDataPacket extends RakNetDataPacket{
	public $windowid;
	public $property;
	public $value;
	
	public function pid(){
		return ProtocolInfo::getCurrentProtocolInfo()::CONTAINER_SET_DATA_PACKET;
	}
	
	public function decode(){

	}
	
	public function encode(){
		$this->reset();
		$this->putByte($this->windowid);
		$this->putShort($this->property);
		$this->putShort($this->value);
	}

}