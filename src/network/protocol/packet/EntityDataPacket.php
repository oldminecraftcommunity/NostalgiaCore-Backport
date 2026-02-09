<?php

class EntityDataPacket extends RakNetDataPacket{
	public $x;
	public $y;
	public $z;
	public $namedtag;
	
	public function pid(){
		return ProtocolInfo::ENTITY_DATA_PACKET;
	}
	
	public function decode(){
		$this->x = $this->getShort();
		$this->y = $this->getByte();
		$this->z = $this->getShort();
		if(ProtocolInfo::$CURRENT_PROTOCOL <= 11) {
			$this->line1 = $this->getString();
			$this->line2 = $this->getString();
			$this->line3 = $this->getString();
			$this->line4 = $this->getString();
		}else{
			$this->namedtag = $this->get(true);
		}
	}
	
	public function encode(){
		$this->reset();
		$this->putShort($this->x);
		$this->putByte($this->y);
		$this->putShort($this->z);
		if(ProtocolInfo::$CURRENT_PROTOCOL <= 11) {
			$this->putString($this->line1);
			$this->putString($this->line2);
			$this->putString($this->line3);
			$this->putString($this->line4);
		}else{
			$this->put($this->namedtag);
		}
	}

}