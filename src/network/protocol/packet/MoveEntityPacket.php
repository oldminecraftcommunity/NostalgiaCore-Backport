<?php

class MoveEntityPacket extends RakNetDataPacket{

	public function pid(){
		return ProtocolInfo::getCurrentProtocolInfo()::MOVE_ENTITY_PACKET;
	}
	
	public function decode(){

	}
	
	public function encode(){
		$this->reset();
	}

}