<?php

class TrapdoorBlock extends TransparentBlock{
	public static $blockID;
	public function __construct($meta = 0){
		parent::__construct(TRAPDOOR, $meta, "Trapdoor");
		$this->isActivable = true;
		if(($this->meta & 0x04) === 0x04){
			$this->isFullBlock = false;
		}else{
			$this->isFullBlock = true;
		}
		$this->hardness = 15;
	}

	public function canAttachTo(Block $target){
		$id = $target->getID();
		return $id === SLAB || $id === GLOWSTONE || $id === SLAB || $id === WOOD_SLAB || (!$target->isTransparent || $target instanceof StairBlock);
	}
	
	public static function isOpen($meta){
		return ($meta >> 2) & 1;
	}
	
	public static function getAABB(Level $level, $x, $y, $z){
		static::updateShape($level, $x, $y, $z);
		return StaticBlock::getAABB(static::$blockID, $x, $y, $z);
	}
	
	public static function getCollisionBoundingBoxes(Level $level, $x, $y, $z, Entity $entity){
		return [static::getAABB($level, $x, $y, $z)];
	}
	
	public static function updateShape(Level $level, $x, $y, $z){
		[$id, $meta] = $level->level->getBlock($x, $y, $z);
		
		StaticBlock::setBlockBounds($id, 0.0, 0.0, 0.0, 1.0, 0.1875, 1.0);
		
		if(static::isOpen($meta)){
			$facing = $meta & 3;
			switch($facing){
				case 0:
					StaticBlock::setBlockBounds($id, 0.0, 0.0, 0.8125, 1.0, 1.0, 1.0);
					break;
				case 1:
					StaticBlock::setBlockBounds($id, 0.0, 0.0, 0.0, 1.0, 1.0, 0.1875);
					break;
				case 2:
					StaticBlock::setBlockBounds($id, 0.8125, 0.0, 0.0, 1.0, 1.0, 1.0);
					break;
				case 3:
					StaticBlock::setBlockBounds($id, 0.0, 0.0, 0.0, 0.1875, 1.0, 1.0);
					break;
				default:
					ConsoleAPI::error("wat");
			}
		}
	}
	
	public function place(Item $item, Player $player, Block $block, Block $target, $face, $fx, $fy, $fz){
			if(($this->canAttachTo($target)) and $face !== 0 and $face !== 1){
				$faces = array(
					2 => 0,
					3 => 1,
					4 => 2,
					5 => 3,
				);
				$this->meta = $faces[$face] & 0x03;
				$this->level->setBlock($block, $this, true, false, true);
				return true;
			}
	}
	public function getDrops(Item $item, Player $player){
		return array(
			array($this->id, 0, 1),
		);
	}
	public static function neighborChanged(Level $level, $x, $y, $z, $nX, $nY, $nZ, $oldID){
		$attach = match($level->level->getBlockDamage($x, $y, $z)){
			0, 4 => $level->level->getBlockID($x, $y, $z + 1),
			1, 5 => $level->level->getBlockID($x, $y, $z - 1),
			2, 6 => $level->level->getBlockID($x + 1, $y, $z),
			3, 7 => $level->level->getBlockID($x - 1, $y, $z),
			default => 0
		};
			
		if($attach == AIR){ //Replace with common break method
			ServerAPI::request()->api->entity->drop(new Position($x, $y, $z, $level), BlockAPI::getItem(TRAPDOOR, 0, 1));
			$level->fastSetBlockUpdate($x, $y, $z, 0, 0, true);
		}
	}
	
	public function onActivate(Item $item, Player $player){
		$this->meta ^= 0x04;
		$this->level->setBlock($this, $this, true, false, true);
		return true;
	}
}