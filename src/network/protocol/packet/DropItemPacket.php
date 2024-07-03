<?php

class DropItemPacket extends RakNetDataPacket{
	public $eid;
	public $unknown;
	public $item;
	
	public function pid(){
		return ProtocolInfo::getCurrentProtocolInfo()::DROP_ITEM_PACKET;
	}
	
	public function decode(){
		$this->eid = $this->getInt();
		$this->unknown = $this->getByte();
		$this->item = $this->getSlot();
	}
	
	public function encode(){

	}

}