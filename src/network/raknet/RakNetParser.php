<?php

class RakNetParser{

	public $packet;
	private $buffer;
	private $offset;

	public function __construct(&$buffer){
		$this->buffer =& $buffer;
		$this->offset = 0;
		if(strlen($this->buffer) > 0){
			$this->parse();
		}else{
			$this->packet = false;
		}
	}

	private function parse(){
		$this->packet = new RakNetPacket(ord($this->get(1)));
		$this->packet->buffer =& $this->buffer;
		$this->packet->length = strlen($this->buffer);
		switch($this->packet->pid()){
			case RakNetInfo::UNCONNECTED_PING:
			case RakNetInfo::UNCONNECTED_PING_OPEN_CONNECTIONS:
				$this->packet->pingID = $this->getLong();
				$this->offset += 16; //Magic
				break;
			case RakNetInfo::OPEN_CONNECTION_REQUEST_1:
				$this->offset += 16; //Magic
				$this->packet->structure = $this->getByte();
				$this->packet->mtuSize = strlen($this->get(true));
				break;
			case RakNetInfo::OPEN_CONNECTION_REQUEST_2:
				$this->offset += 16; //Magic
				$this->packet->security = $this->get(5);
				$this->packet->port = $this->getShort(false);
				$this->packet->mtuSize = $this->getShort(false);
				$this->packet->clientID = $this->getLong();
				break;
			case RakNetInfo::DATA_PACKET_0:
			case RakNetInfo::DATA_PACKET_1:
			case RakNetInfo::DATA_PACKET_2:
			case RakNetInfo::DATA_PACKET_3:
			case RakNetInfo::DATA_PACKET_4:
			case RakNetInfo::DATA_PACKET_5:
			case RakNetInfo::DATA_PACKET_6:
			case RakNetInfo::DATA_PACKET_7:
			case RakNetInfo::DATA_PACKET_8:
			case RakNetInfo::DATA_PACKET_9:
			case RakNetInfo::DATA_PACKET_A:
			case RakNetInfo::DATA_PACKET_B:
			case RakNetInfo::DATA_PACKET_C:
			case RakNetInfo::DATA_PACKET_D:
			case RakNetInfo::DATA_PACKET_E:
			case RakNetInfo::DATA_PACKET_F:
				$this->packet->seqNumber = $this->getLTriad();
				$this->packet->data = [];
				while(!$this->feof() and ($pk = $this->parseDataPacket()) instanceof RakNetDataPacket){
					$this->packet->data[] = $pk;
				}
				break;
			case RakNetInfo::NACK:
			case RakNetInfo::ACK:
				$count = $this->getShort();
				$this->packet->packets = [];
				for($i = 0; $i < $count and !$this->feof(); ++$i){
					if($this->getByte() === 0){
						$start = $this->getLTriad();
						$end = $this->getLTriad();
						if(($end - $start) > 4096){
							$end = $start + 4096;
						}
						for($c = $start; $c <= $end; ++$c){
							$this->packet->packets[] = $c;
						}
					}else{
						$this->packet->packets[] = $this->getLTriad();
					}
				}
				break;
			default:
				$this->packet = false;
				break;
		}
	}

	private function get($len){
		if($len <= 0){
			$this->offset = strlen($this->buffer) - 1;
			return "";
		}
		if($len === true){
			return substr($this->buffer, $this->offset);
		}
		$this->offset += $len;
		return substr($this->buffer, $this->offset - $len, $len);
	}

	private function getLong($unsigned = false){
		return Utils::readLong($this->get(8), $unsigned);
	}

	private function getByte(){
		return ord($this->get(1));
	}

	private function getShort($unsigned = false){
		return Utils::readShort($this->get(2), $unsigned);
	}

	private function getLTriad(){
		return Utils::readTriad(strrev($this->get(3)));
	}

	private function feof(){
		return !isset($this->buffer[$this->offset]);
	}

	private function parseDataPacket(){
		$packetFlags = $this->getByte();
		$reliability = ($packetFlags & 0b11100000) >> 5;
		$hasSplit = ($packetFlags & 0b00010000) > 0;
		$length = (int) ceil($this->getShort() / 8);
		if($reliability === 2
			or $reliability === 3
			or $reliability === 4
			or $reliability === 6
			or $reliability === 7){
			$messageIndex = $this->getLTriad();
		}else{
			$messageIndex = false;
		}

		if($reliability === 1
			or $reliability === 3
			or $reliability === 4
			or $reliability === 7){
			$orderIndex = $this->getLTriad();
			$orderChannel = $this->getByte();
		}else{
			$orderIndex = false;
			$orderChannel = false;
		}

		if($hasSplit == true){
			$splitCount = $this->getInt();
			$splitID = $this->getShort();
			$splitIndex = $this->getInt();
		}else{
			$splitCount = false;
			$splitID = false;
			$splitIndex = false;
		}

		if($length <= 0
			or $orderChannel >= 32
			or ($hasSplit === true and $splitIndex >= $splitCount)){
			return false;
		}else{
			$pid = $this->getByte();
			$buffer = $this->get($length - 1);
			if(strlen($buffer) < ($length - 1)){
				return false;
			}
			switch($pid){
				case ProtocolInfo::getCurrentProtocolInfo()::PING_PACKET:
					$data = new PingPacket;
					break;
				case ProtocolInfo::getCurrentProtocolInfo()::PONG_PACKET:
					$data = new PongPacket;
					break;
				case ProtocolInfo::getCurrentProtocolInfo()::CLIENT_CONNECT_PACKET:
					$data = new ClientConnectPacket;
					break;
				case ProtocolInfo::getCurrentProtocolInfo()::SERVER_HANDSHAKE_PACKET:
					$data = new ServerHandshakePacket;
					break;
				case ProtocolInfo::getCurrentProtocolInfo()::DISCONNECT_PACKET:
					$data = new DisconnectPacket;
					break;
				case ProtocolInfo::getCurrentProtocolInfo()::LOGIN_PACKET:
					$data = new LoginPacket;
					break;
				case ProtocolInfo::getCurrentProtocolInfo()::LOGIN_STATUS_PACKET:
					$data = new LoginStatusPacket;
					break;
				case ProtocolInfo::getCurrentProtocolInfo()::READY_PACKET:
					$data = new ReadyPacket;
					break;
				case ProtocolInfo::getCurrentProtocolInfo()::MESSAGE_PACKET:
					$data = new MessagePacket;
					break;
				case ProtocolInfo::getCurrentProtocolInfo()::SET_TIME_PACKET:
					$data = new SetTimePacket;
					break;
				case ProtocolInfo::getCurrentProtocolInfo()::START_GAME_PACKET:
					$data = new StartGamePacket;
					break;
				case ProtocolInfo::getCurrentProtocolInfo()::ADD_MOB_PACKET:
					$data = new AddMobPacket;
					break;
				case ProtocolInfo::getCurrentProtocolInfo()::ADD_PLAYER_PACKET:
					$data = new AddPlayerPacket;
					break;
				case ProtocolInfo::getCurrentProtocolInfo()::REMOVE_PLAYER_PACKET:
					$data = new RemovePlayerPacket;
					break;
				case ProtocolInfo::getCurrentProtocolInfo()::ADD_ENTITY_PACKET:
					$data = new AddEntityPacket;
					break;
				case ProtocolInfo::getCurrentProtocolInfo()::REMOVE_ENTITY_PACKET:
					$data = new RemoveEntityPacket;
					break;
				case ProtocolInfo::getCurrentProtocolInfo()::ADD_ITEM_ENTITY_PACKET:
					$data = new AddItemEntityPacket;
					break;
				case ProtocolInfo::getCurrentProtocolInfo()::TAKE_ITEM_ENTITY_PACKET:
					$data = new TakeItemEntityPacket;
					break;
				case ProtocolInfo::getCurrentProtocolInfo()::MOVE_ENTITY_PACKET:
					$data = new MoveEntityPacket;
					break;
				case ProtocolInfo::getCurrentProtocolInfo()::MOVE_ENTITY_PACKET_POSROT:
					$data = new MoveEntityPacket_PosRot;
					break;
				case ProtocolInfo::getCurrentProtocolInfo()::ROTATE_HEAD_PACKET:
					$data = new RotateHeadPacket;
					break;
				case ProtocolInfo::getCurrentProtocolInfo()::MOVE_PLAYER_PACKET:
					$data = new MovePlayerPacket;
					break;
				case ProtocolInfo::getCurrentProtocolInfo()::REMOVE_BLOCK_PACKET:
					$data = new RemoveBlockPacket;
					break;
				case ProtocolInfo::getCurrentProtocolInfo()::UPDATE_BLOCK_PACKET:
					$data = new UpdateBlockPacket;
					break;
				case ProtocolInfo::getCurrentProtocolInfo()::ADD_PAINTING_PACKET:
					$data = new AddPaintingPacket;
					break;
				case ProtocolInfo::getCurrentProtocolInfo()::EXPLODE_PACKET:
					$data = new ExplodePacket;
					break;
				case ProtocolInfo::getCurrentProtocolInfo()::LEVEL_EVENT_PACKET:
					$data = new LevelEventPacket;
					break;
				case ProtocolInfo::getCurrentProtocolInfo()::TILE_EVENT_PACKET:
					$data = new TileEventPacket;
					break;
				case ProtocolInfo::getCurrentProtocolInfo()::ENTITY_EVENT_PACKET:
					$data = new EntityEventPacket;
					break;
				case ProtocolInfo::getCurrentProtocolInfo()::REQUEST_CHUNK_PACKET:
					$data = new RequestChunkPacket;
					break;
				case ProtocolInfo::getCurrentProtocolInfo()::CHUNK_DATA_PACKET:
					$data = new ChunkDataPacket;
					break;
				case ProtocolInfo::getCurrentProtocolInfo()::PLAYER_EQUIPMENT_PACKET:
					$data = new PlayerEquipmentPacket;
					break;
				case ProtocolInfo::getCurrentProtocolInfo()::PLAYER_ARMOR_EQUIPMENT_PACKET:
					$data = new PlayerArmorEquipmentPacket;
					break;
				case ProtocolInfo::getCurrentProtocolInfo()::INTERACT_PACKET:
					$data = new InteractPacket;
					break;
				case ProtocolInfo::getCurrentProtocolInfo()::USE_ITEM_PACKET:
					$data = new UseItemPacket;
					break;
				case ProtocolInfo::getCurrentProtocolInfo()::PLAYER_ACTION_PACKET:
					$data = new PlayerActionPacket;
					break;
				case ProtocolInfo::getCurrentProtocolInfo()::HURT_ARMOR_PACKET:
					$data = new HurtArmorPacket;
					break;
				case ProtocolInfo::getCurrentProtocolInfo()::SET_ENTITY_DATA_PACKET:
					$data = new SetEntityDataPacket;
					break;
				case ProtocolInfo::getCurrentProtocolInfo()::SET_ENTITY_MOTION_PACKET:
					$data = new SetEntityMotionPacket;
					break;
				case ProtocolInfo::getCurrentProtocolInfo()::SET_HEALTH_PACKET:
					$data = new SetHealthPacket;
					break;
				case ProtocolInfo::getCurrentProtocolInfo()::SET_SPAWN_POSITION_PACKET:
					$data = new SetSpawnPositionPacket;
					break;
				case ProtocolInfo::getCurrentProtocolInfo()::ANIMATE_PACKET:
					$data = new AnimatePacket;
					break;
				case ProtocolInfo::getCurrentProtocolInfo()::RESPAWN_PACKET:
					$data = new RespawnPacket;
					break;
				case ProtocolInfo::getCurrentProtocolInfo()::SEND_INVENTORY_PACKET:
					$data = new SendInventoryPacket;
					break;
				case ProtocolInfo::getCurrentProtocolInfo()::DROP_ITEM_PACKET:
					$data = new DropItemPacket;
					break;
				case ProtocolInfo::getCurrentProtocolInfo()::CONTAINER_OPEN_PACKET:
					$data = new ContainerOpenPacket;
					break;
				case ProtocolInfo::getCurrentProtocolInfo()::CONTAINER_CLOSE_PACKET:
					$data = new ContainerClosePacket;
					break;
				case ProtocolInfo::getCurrentProtocolInfo()::CONTAINER_SET_SLOT_PACKET:
					$data = new ContainerSetSlotPacket;
					break;
				case ProtocolInfo::getCurrentProtocolInfo()::CONTAINER_SET_DATA_PACKET:
					$data = new ContainerSetDataPacket;
					break;
				case ProtocolInfo::getCurrentProtocolInfo()::CONTAINER_SET_CONTENT_PACKET:
					$data = new ContainerSetContentPacket;
					break;
				case ProtocolInfo::getCurrentProtocolInfo()::CHAT_PACKET:
					$data = new ChatPacket;
					break;
				case ProtocolInfo::getCurrentProtocolInfo()::ADVENTURE_SETTINGS_PACKET:
					$data = new AdventureSettingsPacket;
					break;
				case ProtocolInfo::getCurrentProtocolInfo()::ENTITY_DATA_PACKET:
					$data = new EntityDataPacket;
					break;
				case ProtocolInfo::getCurrentProtocolInfo()::SET_ENTITY_LINK_PACKET:
					$data = new SetEntityLinkPacket;
				case ProtocolInfo::getCurrentProtocolInfo()::PLAYER_INPUT_PACKET:
					$data = new PlayerInputPacket;
					break;
				default:
					$data = new UnknownPacket();
					$data->packetID = $pid;
					break;
			}
			$data->reliability = $reliability;
			$data->hasSplit = $hasSplit;
			$data->messageIndex = $messageIndex;
			$data->orderIndex = $orderIndex;
			$data->orderChannel = $orderChannel;
			$data->splitCount = $splitCount;
			$data->splitID = $splitID;
			$data->splitIndex = $splitIndex;
			$data->setBuffer($buffer);
		}
		return $data;
	}

	private function getInt($unsigned = false){
		return Utils::readInt($this->get(4), $unsigned);
	}

}