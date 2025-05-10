<?php

class StonecutterBlock extends SolidBlock{
	public static $blockID;
	public function __construct($meta = 0){
		parent::__construct(STONECUTTER, $meta, "Stonecutter");
		$this->isActivable = true;
	}
	
	public function onActivate(Item $item, Player $player){
		$player->craftingType = CraftingRecipes::TYPE_STONECUTTER;
		return true;
	}

	public function getDrops(Item $item, Player $player){
		if($item->getPickaxeLevel() >= ItemTool::WOODEN_LEVEL){
			return [
				[$this->id, 0, 1],
			];
		}else{
			return [];
		}
		
	}	
}