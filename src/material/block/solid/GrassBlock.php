<?php

class GrassBlock extends SolidBlock{
	public static $blockID;
	public function __construct(){
		parent::__construct(GRASS, 0, "Grass");
		$this->isActivable = true;
		$this->hardness = 3;
		$this->breakTime = 0.6;
		$this->material = Material::$dirt;
		$this->lightBlock = 255;
	}

	public function getDrops(Item $item, Player $player){
		return array(
			array(DIRT, 0, 1),
		);
	}

	public function onActivate(Item $item, Player $player){
		if($item->getID() === DYE and $item->getMetadata() === 0x0F){
			$player->consumeSingleItem(send:true);
			TallGrassObject::growGrass($this->level, $this, new Random(), 8, 2);
			return true;
		}elseif($item->isHoe()){
			if($this->getSide(1)->isTransparent === false) return false;
			$item->hurtAndBreak(1, $player); //TODO move to ItemHoe
			$this->level->fastSetBlockUpdate($this->x, $this->y, $this->z, FARMLAND, 0, true);
			$this->seedsDrop();
			return true;
		}
		return false;
	}
	
	public function seedsDrop(){
		$chance = lcg_value() * 100;
		if($chance <= 1){
			ServerAPI::request()->api->entity->drop(new Position($this->x+0.5, $this->y+1, $this->z+0.5, $this->level), BlockAPI::getItem(BEETROOT_SEEDS,0,1), 10);
		}elseif($chance > 1 and $chance <= 16){
			ServerAPI::request()->api->entity->drop(new Position($this->x+0.5, $this->y+1, $this->z+0.5, $this->level), BlockAPI::getItem(SEEDS,0,1), 10);
		}
	}
	public static function onRandomTick(Level $level, $x, $y, $z){
		if(!StaticBlock::getIsTransparent($level->level->getBlockID($x, $y + 1, $z)) && mt_rand(0, 2) == 1){
			$level->fastSetBlockUpdate($x, $y, $z, DIRT, 0);
		}else{
			//for($cnt = 0; $cnt < 4; ++$cnt){
			$x = $x + mt_rand(0, 2) - 1;
			$y = $y + mt_rand(0, 4) - 3;
			$z = $z + mt_rand(0, 2) - 1;
			
			$blockUp = $level->level->getBlockID($x, $y + 1, $z);
			if(StaticBlock::getIsTransparent($blockUp) && !StaticBlock::getIsLiquid($blockUp) && !($blockUp == FARMLAND) && $level->level->getBlockID($x, $y, $z) === DIRT){
				$level->fastSetBlockUpdate($x, $y, $z, GRASS, 0);
			}
				
			//}
		}
	}

}
