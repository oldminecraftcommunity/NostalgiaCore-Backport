<?php

class CarrotBlock extends FlowableBlock{
	public static $blockID;
	public function __construct($meta = 0){
		parent::__construct(CARROT_BLOCK, $meta, "Carrot Block");
		$this->isActivable = true;
		$this->hardness = 0;
	}

	public function place(Item $item, Player $player, Block $block, Block $target, $face, $fx, $fy, $fz){
		$down = $this->getSide(0);
		if($down->getID() === FARMLAND){
			$this->level->setBlock($block, $this, true, false, true);
			$this->level->scheduleBlockUpdate(new Position($this, 0, 0, $this->level), Utils::getRandomUpdateTicks(), BLOCK_UPDATE_RANDOM);
			return true;
		}
		return false;
	}
	
	public function onActivate(Item $item, Player $player){
		if($item->getID() === DYE and $item->getMetadata() === 0x0F){ //Bonemeal
			$this->meta += mt_rand(0, 3) + 2;
			if ($this->meta > 7) {
				$this->meta = 7;
			}
			$this->level->setBlock($this, $this, true, false, true);
			if(($player->gamemode & 0x01) === 0){
				$player->removeItem(DYE,0x0F,1);
			}
			return true;
		}
		return false;
	}
	public static function onRandomTick(Level $level, $x, $y, $z){
		if(mt_rand(0, 2) == 1){
			$block = $level->level->getBlock($x, $y, $z);
			if($block[1] < 0x07){
				//++$this->meta;
				//$this->level->setBlock($this, $this, true, false, true);
				$level->fastSetBlockUpdate($x, $y, $z, $block[0], $block[1] + 1);
			}
		}
	}
	public static function neighborChanged(Level $level, $x, $y, $z, $nX, $nY, $nZ, $oldID){
		if($level->level->getBlockID($x, $y - 1, $z) != FARMLAND){
			ServerAPI::request()->api->entity->drop(new Position($x + 0.5, $y, $z + 0.5, $level), BlockAPI::getItem(CARROT, 0, 1), 10);
			$level->fastSetBlockUpdate($x, $y, $z, 0, 0);
		}
	}
	
	public function getDrops(Item $item, Player $player){
		$drops = [];
		if($this->meta >= 0x07){
			$drops[] = [CARROT, 0, mt_rand(1, 4)];
		}
		else{
			$drops[] = [CARROT, 0, 1];
		}
		return $drops;
	}
}