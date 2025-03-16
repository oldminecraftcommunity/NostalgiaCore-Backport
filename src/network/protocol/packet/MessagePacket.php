<?php

class MessagePacket extends RakNetDataPacket{
	public $source;
	public $message;
	
	public function pid(){
		return ProtocolInfo::MESSAGE_PACKET;
	}
	
	public function decode(){
		if(ProtocolInfo::$CURRENT_PROTOCOL > 9){ //0.6.1 and below
			$this->source = $this->getString();
		}
		$this->message = $this->getString();
	}	
	
	public function encode(){
		$this->reset();
		if(ProtocolInfo::$CURRENT_PROTOCOL > 9){ //0.6.1 and below
			$this->putString($this->source);
		}
		$this->putString($this->message);
	}

}
