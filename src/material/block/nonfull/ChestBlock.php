<?php

class ChestBlock extends TransparentBlock{
	public static $blockID;
	public function __construct($meta = 0){
		parent::__construct(CHEST, $meta, "Chest");
		$this->isActivable = true;
		$this->isFullBlock = false;
		$this->hardness = 15;
		$this->breakTime = 2.5;
		$this->material = Material::$wood;
	}
	public static function getCollisionBoundingBoxes(Level $level, $x, $y, $z, Entity $entity){
		return [new AxisAlignedBB($x + 0.025, $y, $z + 0.025, $x + 0.975, $y + 0.95, $z + 0.975)];
	}
	
	public function place(Item $item, Player $player, Block $block, Block $target, $face, $fx, $fy, $fz){
		$server = ServerAPI::request();
		$faces = array(
			0 => 4,
			1 => 2,
			2 => 5,
			3 => 3,
		);

		$chest = false;
		$this->meta = $faces[$player->entity->getDirection()];
		
		for($side = 2; $side <= 5; ++$side){
			if(($this->meta === 4 or $this->meta === 5) and ($side === 4 or $side === 5)){
				continue;
			}elseif(($this->meta === 3 or $this->meta === 2) and ($side === 2 or $side === 3)){
				continue;
			}
			$c = $this->getSide($side);
			if(($c instanceof ChestBlock) and $c->getMetadata() === $this->meta){
				if((($tile = $server->api->tile->get($c)) instanceof Tile) and !$tile->isPaired()){
					$chest = $tile;
					break;
				}
			}
		}

		$this->level->setBlock($block, $this, true, false, true);
		$tile = $server->api->tile->add($this->level, TILE_CHEST, $this->x, $this->y, $this->z, array(
			"Items" => array(),
			"id" => TILE_CHEST,
			"x" => $this->x,
			"y" => $this->y,
			"z" => $this->z
		));
		$server->api->tile->spawnToAll($tile);
		
		if($chest instanceof Tile){
			$chest->pairWith($tile);
			$tile->pairWith($chest);
		}
		return true;
	}
	
	public function onBreak(Item $item, Player $player){
		$t = ServerAPI::request()->api->tile->get($this);
		if($t !== false){
			$t->unpair();
		}
		$this->level->setBlock($this, new AirBlock(), true, true, true);
		return true;
	}
	
	public function onActivate(Item $item, Player $player){
		$top = $this->getSide(1);
		if($top->isTransparent !== true){
			return true;
		}
	
		$server = ServerAPI::request();
		$t = $server->api->tile->get($this);
		$chest = false;
		if($t !== false){
			$chest = $t;
		}else{
			$chest = $server->api->tile->add($this->level, TILE_CHEST, $this->x, $this->y, $this->z, array(
				"Items" => array(),
				"id" => TILE_CHEST,
				"x" => $this->x,
				"y" => $this->y,
				"z" => $this->z			
			));
		}
		
		
		
		if(($player->gamemode & 0x01) === 0x01){
			return true;
		}
		
		$chest->openInventory($player);		
		return true;
	}

	public function getDrops(Item $item, Player $player){
		$drops = array(
			array($this->id, 0, 1),
		);
		$t = ServerAPI::request()->api->tile->get($this);
		if($t !== false and $t->class === TILE_CHEST){
			for($s = 0; $s < CHEST_SLOTS; ++$s){
				$slot = $t->getSlot($s);
				if($slot->getID() > AIR and $slot->count > 0){
					$drops[] = array($slot->getID(), $slot->getMetadata(), $slot->count);
				}
			}
		}
		return $drops;
	}
}