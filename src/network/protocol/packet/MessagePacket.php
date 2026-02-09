<?php

class MessagePacket extends RakNetDataPacket{
	public $source;
	public $message;
	
	public function pid(){
		return ProtocolInfo::MESSAGE_PACKET;
	}
	
	public function decode(){
		if(ProtocolInfo::$CURRENT_PROTOCOL > 11){
			$this->source = $this->getString();
		}else{
			$this->source = "";
		}
		
		$this->message = $this->getString();
	}	
	
	public function encode(){
		$this->reset();
		if(ProtocolInfo::$CURRENT_PROTOCOL > 11){
			$this->putString($this->source);
		}
		$this->putString($this->message);
	}

}