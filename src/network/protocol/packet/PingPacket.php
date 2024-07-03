<?php

class PingPacket extends RakNetDataPacket{
	public $time = 0;

	public function pid(){
		return ProtocolInfo::getCurrentProtocolInfo()::PING_PACKET;
	}
	
	public function decode(){
		$this->time = $this->getLong();
	}	
	
	public function encode(){
		$this->reset();
		$this->putLong($this->time);
	}

}