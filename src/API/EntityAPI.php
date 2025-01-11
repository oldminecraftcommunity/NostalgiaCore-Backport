<?php

class EntityAPI{
	public $entities;
	private $server;
	private $eCnt = 1;
	private $serverSpawnAnimals, $serverSpawnMobs;
	function __construct(){
		$this->entities = [];
		$this->server = ServerAPI::request();
		
		$this->serverSpawnAnimals = $this->server->api->getProperty("spawn-animals");
		$this->serverSpawnMobs = $this->server->api->getProperty("spawn-mobs");
	}
	
	public function init(){
		$this->server->api->console->register("summon", "<mob>", [$this, "commandHandler"]);
		$this->server->api->console->register("spawnmob", "<mob>", [$this, "commandHandler"]);
		$this->server->api->console->register("despawn", "", [$this, "CommandHandler"]);
		$this->server->api->console->register("entcnt", "", [$this, "CommandHandler"]);
	}
	
	
	public function commandHandler($cmd, $args, $issuer, $alias){
		$mob = [
			"chicken" => 10,
			"cow" => 11,
			"pig" => 12,
			"sheep" => 13,
			
			"zombie" => 32,
			"creeper" => 33,
			"skeleton" => 34,
			"spider" => 35,
			"pigman" => 36
		];
		$output = "";
		switch($cmd){
			case "entcnt":
				return "Total amount of entities: ". count($this->entities);
			case "summon":
			case "spawnmob":
				if(!($issuer instanceof Player)){
					return "Please run this command in-game.";
				}
				if((count($args) < 1) or (count($args) > 3)){
					return "Usage: /$cmd <mob> [amount] [baby]";
				}
				
				if(is_int($args[0])) $type = $args[0];
				else $type = $mob[strtolower($args[0])] ?? 0;
				if($type < 10 || $type > 36){
					return "Unknown mob.";
				}
				$mobName = ucfirst(array_flip($mob)[$type]);
				
				if(((isset($args[1]) && strtolower($args[1]) === "baby") || (isset($args[2]) && strtolower($args[2]) === "baby")) && !Utils::in_range($type, 10, 13)){
					return "$mobName cannot be a baby!";
				}
				
				$x = round($issuer->entity->x, 2, PHP_ROUND_HALF_UP);
				$y = round($issuer->entity->y, 2, PHP_ROUND_HALF_UP);
				$z = round($issuer->entity->z, 2, PHP_ROUND_HALF_UP);
				$level = $issuer->entity->level;
				$pos = new Position($x, $y, $z, $level);
				
				if(count($args) === 1){//summon <mob>
					$this->summon($pos, ENTITY_MOB, $type);
					return "$mobName spawned in $x, $y, $z.";
				}
				elseif(is_numeric($args[1])){//summon <mob> [amount]
					$amount = (int) $args[1];
					if($amount > 1000){
						return "Cannot spawn > 1000 mobs";
					}
					$isBaby = false;
					if(isset($args[2]) and strtolower($args[2]) === 'baby'){//summon <mob> [amount] [baby]
						$isBaby = true;
					}
					
					for($cnt = $amount; $cnt > 0; --$cnt){
						$this->summon($pos, ENTITY_MOB, $type, ["IsBaby" => $isBaby]);
					}
					
					return "$amount ".($isBaby ? "Baby " : "")."$mobName(s) spawned in $x, $y, $z.";
				}
				elseif(strtolower($args[1]) == "baby"){//summon <mob> [baby]
					$this->summon($pos, ENTITY_MOB, $type, ["IsBaby" => 1]);
					return "Baby $mobName spawned in $x, $y, $z.";
				}
				break;
			case "despawn":
				$cnt = 0;
				if(!isset($args[0])){
					return "/despawn <all|mobs|objects|items|fallings|minecarts>";
				}
				
				$despawnclass = 0;
				$despawntype = 0;
				switch($args[0]){
					case "all":
						foreach($this->entities as $entity){
							if($entity instanceof Entity && !$entity->isPlayer()){
								$entity->close();
								++$cnt;
							}
						}
						break;
					case "minecarts":
						$despawntype = OBJECT_MINECART;
						break;
					case "mobs":
						$despawnclass = ENTITY_MOB;
						break;
					case "objects":
						$despawnclass = ENTITY_OBJECT;
						break;
					case "items":
						$despawnclass = ENTITY_ITEM;
						break;
					case "fallings":
						$despawnclass = ENTITY_FALLING;
						break;
				}
				if($despawnclass){
					foreach($this->entities as $entity){
						if($entity instanceof Entity && !$entity->isPlayer() && $entity->class === $despawnclass){
							$entity->close();
							++$cnt;
						}
					}
				}
				if($despawntype){
					foreach($this->entities as $entity){
						if($entity instanceof Entity && !$entity->isPlayer() && $entity->type === $despawntype){
							$entity->close();
							++$cnt;
						}
					}
				}
				
				return "$cnt entities have been despawned!";
		}
		return $output;
	}
	
	public function summon(Position $pos, $class, $type, array $data = []){
		$entity = $this->add($pos->level, $class, $type, [
			"x" => $pos->x,
			"y" => $pos->y,
			"z" => $pos->z
		] + $data);
		$this->spawnToAll($entity);
	}
	
	public function getNextEID(){
		return $this->eCnt++;
	}
	
	public function addRaw(Entity $e){
		$eid = $e->eid;
		$this->entities[$eid] = $e;
		$cX = (int)$this->entities[$eid]->x >> 4;
		$cZ = (int)$this->entities[$eid]->z >> 4;
		$e->level->entityListPositioned["$cX $cZ"][$eid] = $eid;
		$e->level->entityList[$eid] = &$this->entities[$eid];
		$this->server->handle("entity.add", $this->entities[$eid]);
		return $e;
	}
	public function add(Level $level, $class, $type = 0, $data = []){
		$eid = $this->getNextEID();
		$efl = EntityRegistry::$entityList->getEntityFromTypeAndClass($type, $class);
		if($efl instanceof PropertyEntity){
			$class = $efl->getEntityName();
			$e = new $class($level, $eid, $efl->getEntityClass(), $efl->getEntityType(), $data);
		}else{
			$e = new Entity($level, $eid, $class, $type, $data);
		}
		return $this->addRaw($e);
	}
	
	public function spawnToAll(Entity $e){
		foreach($this->server->api->player->getAll($e->level) as $player){
			if($player->eid !== false and $player->eid !== $e->eid and $e->class !== ENTITY_PLAYER and $e instanceof Entity){
				$e->spawn($player);
			}
		}
	}
	
	public function get($eid){
		return $this->entities[$eid] ?? false;
	}
	
	public function remove($eid){
		if(isset($this->entities[$eid])){
			$level = $this->entities[$eid]->level;
			$this->entities[$eid]->closed = true;
			if($level instanceof Level){
				$cX = (int)$this->entities[$eid]->x >> 4;
				$cZ = (int)$this->entities[$eid]->z >> 4;
				$index = "$cX $cZ";
				unset($level->entityListPositioned[$index][$eid]);
				if(isset($level->mobSpawner->entityAffectedPlayers[$eid])){
					$pid = $level->mobSpawner->entityAffectedPlayers[$eid];
					unset($level->mobSpawner->entityAffectedPlayers[$eid]);
					unset($level->mobSpawner->playerAffectedEIDS[$pid][$eid]);
				}
			}
			if($this->entities[$eid]->isPlayer()){
				$pk = new RemovePlayerPacket;
				$pk->eid = $eid;
				$pk->clientID = 0;
				$this->server->api->player->broadcastPacket($this->server->api->player->getAll(), $pk);
			}else{
				$pk = new RemoveEntityPacket;
				$pk->eid = $eid;
				$this->server->api->player->broadcastPacket($this->entities[$eid]->level->players, $pk);
			}
			$this->server->api->dhandle("entity.remove", $this->entities[$eid]);
			unset($this->entities[$eid]->level->entityList[$eid]);
			unset($this->entities[$eid]);
		}
	}
	
	public function getRadius(Position $center, $radius = 15, $class = false){
		$minChunkX = ((int)($center->x - $radius)) >> 4;
		$minChunkZ = ((int)($center->z - $radius)) >> 4;
		$maxChunkX = ((int)($center->x + $radius)) >> 4;
		$maxChunkZ = ((int)($center->z + $radius)) >> 4;
		$ents = [];
		//TODO also index by chunkY?
		for($chunkX = $minChunkX; $chunkX <= $maxChunkX; ++$chunkX){
			for($chunkZ = $minChunkZ; $chunkZ <= $maxChunkZ; ++$chunkZ){
				$ind = "$chunkX $chunkZ";
				foreach($center->level->entityListPositioned[$ind] ?? [] as $ind2 => $entid){
					if(isset($this->entities[$entid]) && $this->entities[$entid] instanceof Entity && ($class === false || $this->entities[$entid]->class == $class)){
						$ents[$entid] = $this->entities[$entid];
					}elseif(!isset($this->entities[$entid])){
						ConsoleAPI::debug("Removing entity from level array at index $ind/$ind2: $entid");
						unset($center->level->entityListPositioned[$ind][$ind2]);
					}
				}
			}
		}
		return $ents;
	}
	
	public function heal($eid, $heal, $cause){
		$this->harm($eid, -$heal, $cause);
	}
	
	public function harm($eid, $attack, $cause, $force = false){
		$e = $this->get($eid);
		if($e === false or $e->dead === true){
			return false;
		}
		$e->setHealth($e->getHealth() - $attack, $cause, $force);
	}
	
	public function dropRawPos(Level $level, $x, $y, $z, $item, $speedX, $speedY, $speedZ){
		if($item->getID() === AIR or $item->count <= 0){
			return;
		}
		$data = [
			"x" => $x,
			"y" => $y,
			"z" => $z,
			"level" => $level,
			"speedX" => $speedX,
			"speedY" => $speedY,
			"speedZ" => $speedZ,
			"item" => $item,
			"itemID" => $item->getID()
		];
		
		if($this->server->api->handle("item.drop", $data) !== false){
			for($count = $item->count; $count > 0;){
				$item->count = min($item->getMaxStackSize(), $count);
				$count -= $item->count;
				$e = $this->add($level, ENTITY_ITEM, ENTITY_ITEM_TYPE, $data);
				$this->spawnToAll($e);
				$this->server->api->handle("entity.motion", $e);
			}
		}
	}
	
	public function drop(Position $pos, Item $item, $pickupDelay = 10){
		if($item->getID() === AIR or $item->count <= 0){
			return;
		}
		$data = [
			"x" => $pos->x + mt_rand(-10, 10) / 50,
			"y" => $pos->y + 0.19,
			"z" => $pos->z + mt_rand(-10, 10) / 50,
			"level" => $pos->level,
			"speedX" => lcg_value() * 0.2 - 0.1,
			"speedY" => 0.2,
			"speedZ" => lcg_value() * 0.2 - 0.1,
			"item" => $item,
			"itemID" => $item->getID()
		];
		if($this->server->api->handle("item.drop", $data) !== false){
			for($count = $item->count; $count > 0;){
				$item->count = min($item->getMaxStackSize(), $count);
				$count -= $item->count;
				$e = $this->add($pos->level, ENTITY_ITEM, ENTITY_ITEM_TYPE, $data);
				$e->delayBeforePickup = $pickupDelay;
				$this->spawnToAll($e);
				$this->server->api->handle("entity.motion", $e);
			}
		}
	}
	
	public function spawnAll(Player $player){
		foreach($player->level->entityList as $e){
			if($e->class !== ENTITY_PLAYER){
				$e->spawn($player);
			}
		}
	}
	
	public function getAll($level = null){
		if($level instanceof Level){
			return $level->entityList;
		}
		return $this->entities;
	}
	
	/**
	 * @deprecated this function doesnt do anything
	 */
	public function updateRadius(Position $center, $radius = 15, $class = false){
		
	}
}
