<?php

class AdventureSettingsPacket extends RakNetDataPacket{
	public $flags;
	
	public function pid(){
		return ProtocolInfo::getCurrentProtocolInfo()::ADVENTURE_SETTINGS_PACKET;
	}
	
	public function decode(){

	}
	
	public function encode(){
		$this->reset();
		$this->putInt($this->flags);
	}

}