<?php

class IronOreBlock extends SolidBlock{
	public static $blockID;
	public function __construct(){
		parent::__construct(IRON_ORE, 0, "Iron Ore");
		$this->hardness = 15;
	}

	public function getBreakTime(Item $item, Player $player){
		if(($player->gamemode & 0x01) === 0x01){
			return 0.20;
		}
        return match ($item->getPickaxeLevel()) {
            5 => 0.6,
            4 => 0.75,
            3 => 1.15,
            default => 15,
        };
	}
	
	public function getDrops(Item $item, Player $player){
		if($item->getPickaxeLevel() >= ItemTool::STONE_LEVEL){
			return array(
				array(IRON_ORE, 0, 1),
			);
		}else{
			return array();
		}
	}
}