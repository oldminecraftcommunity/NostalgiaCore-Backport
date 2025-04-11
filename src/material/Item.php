<?php

class Item{
	const TOOL_SWORD = 0;
	const TOOL_PICKAXE = 1;
	const TOOL_AXE = 2;
	const TOOL_SHOVEL = 3;
	const TOOL_HOE = 4;	
	
	const DEF_DAMAGE = 1;
	
	public static $class = array(
	
		//armor
		LEATHER_CAP => "LeatherCapItem",
		LEATHER_TUNIC => "LeatherTunicItem",
		LEATHER_PANTS => "LeatherPantsItem",
		LEATHER_BOOTS => "LeatherBootsItem",
		CHAIN_HELMET => "ChainHelmetItem",
		CHAIN_CHESTPLATE => "ChainChestplateItem",
		CHAIN_LEGGINGS => "ChainLeggingsItem",
		CHAIN_BOOTS => "ChainBootsItem",
		IRON_HELMET => "IronHelmetItem",
		IRON_CHESTPLATE => "IronChestplateItem",
		IRON_LEGGINGS => "IronLeggingsItem",
		IRON_BOOTS => "IronBootsItem",
		DIAMOND_HELMET => "DiamondHelmetItem",
		DIAMOND_CHESTPLATE => "DiamondChestplateItem",
		DIAMOND_LEGGINGS => "DiamondLeggingsItem",
		DIAMOND_BOOTS => "DiamondBootsItem",
		GOLDEN_HELMET => "GoldenHelmetItem",
		GOLDEN_CHESTPLATE => "GoldenChestplateItem",
		GOLDEN_LEGGINGS => "GoldenLeggingsItem",
		GOLDEN_BOOTS => "GoldenBootsItem",
		
		//food
		APPLE => "AppleItem",
		MUSHROOM_STEW => "MushroomStewItem",
		BREAD => "BreadItem",
		RAW_PORKCHOP => "RawPorkchopItem",
		COOKED_PORKCHOP => "CookedPorkchopItem",
		CAKE => "CakeItem",
		MELON => "MelonItem",
		BEEF => "BeefItem",
		STEAK => "SteakItem",
		RAW_CHICKEN => "RawChickenItem",
		COOKED_CHICKEN => "CookedChickenItem",
		CARROT => "CarrotItem",
		POTATO => "PotatoItem",
		BAKED_POTATO => "BakedPotatoItem",
		PUMPKIN_PIE => "PumpkinPieItem",
		BEETROOT => "BeetrootItem",
		BEETROOT_SOUP => "BeetrootSoupItem",
	
		//generic
		ARROW => "ArrowItem",
		COAL => "CoalItem",
		DIAMOND => "DiamondItem",
		IRON_INGOT => "IronIngotItem",
		GOLD_INGOT => "GoldIngotItem",
		STICK => "StickItem",
		BOWL => "BowlItem",
		STRING => "StringItem",
		FEATHER => "FeatherItem",
		GUNPOWDER => "GunpowderItem",
		WHEAT_SEEDS => "WheatSeedsItem",
		WHEAT => "WheatItem",
		FLINT => "FlintItem",
		PAINTING => "PaintingItem",
		SIGN => "SignItem",
		WOODEN_DOOR => "WoodenDoorItem",
		BUCKET => "BucketItem",
		MINECART => "MinecartItem",
		SADDLE => "SaddleItem",
		IRON_DOOR => "IronDoorItem",
		REDSTONE => "RedstoneItem",
		SNOWBALL => "SnowballItem",
		LEATHER => "LeatherItem",
		BRICK => "BrickItem",
		CLAY => "ClayItem",
		SUGARCANE => "SugarCaneItem",
		PAPER => "PaperItem",
		BOOK => "BookItem",
		SLIMEBALL => "SlimeballItem",
		EGG => "EggItem",
		GLOWSTONE_DUST => "GlowstoneDustItem",
		DYE => "DyeItem",
		BONE => "BoneItem",
		SUGAR => "SugarItem",
		BED => "BedItem",
		PUMPKIN_SEEDS => "PumpkinSeedsItem",
		MELON_SEEDS => "MelonSeedsItem",
		SPAWN_EGG => "SpawnEggItem",
		NETHER_BRICK => "NetherBrickItem",
		QUARTZ => "QuartzItem",
		CAMERA => "CameraItem",
		BEETROOT_SEEDS => "BeetrootSeedsItem",
		
		//tool
		IRON_SHOVEL => "IronShovelItem",
		IRON_PICKAXE => "IronPickaxeItem",
		IRON_AXE => "IronAxeItem",
		FLINT_STEEL => "FlintSteelItem",
		BOW => "BowItem",
		IRON_SWORD => "IronSwordItem",
		WOODEN_SWORD => "WoodenSwordItem",
		WOODEN_SHOVEL => "WoodenShovelItem",
		WOODEN_PICKAXE => "WoodenPickaxeItem",
		WOODEN_AXE => "WoodenAxeItem",
		STONE_SWORD => "StoneSwordItem",
		STONE_SHOVEL => "StoneShovelItem",
		STONE_PICKAXE => "StonePickaxeItem",
		STONE_AXE => "StoneAxeItem",
		DIAMOND_SWORD => "DiamondSwordItem",
		DIAMOND_SHOVEL => "DiamondShovelItem",
		DIAMOND_PICKAXE => "DiamondPickaxeItem",
		DIAMOND_AXE => "DiamondAxeItem",
		GOLDEN_SWORD => "GoldenSwordItem",
		GOLDEN_SHOVEL => "GoldenShovelItem",
		GOLDEN_PICKAXE => "GoldenPickaxeItem",
		GOLDEN_AXE => "GoldenAxeItem",
		WOODEN_HOE => "WoodenHoeItem",
		STONE_HOE => "StoneHoeItem",
		IRON_HOE => "IronHoeItem",
		DIAMOND_HOE => "DiamondHoeItem",
		GOLDEN_HOE => "GoldenHoeItem",
		COMPASS => "CompassItem",
		CLOCK => "ClockItem",
		SHEARS => "ShearsItem",
		
	);
	public $block;
	public $id;
	public $meta;
	public $count;
	/**
	 * @var int 
	 * Max stack size of the item. Use Item::getMaxStackSize to get stack size for specific item.
	 */
	public $maxStackSize = 64;
	public $durability = 0;
	public $name;
	public $isActivable = false;
	
	public function __construct($id, $meta = 0, $count = 1, $name = "Unknown"){
		$this->id = (int) $id;
		$this->meta = (int) $meta;
		$this->count = (int) $count;
		$this->name = $name;
		if(!isset($this->block) and $this->id <= 0xff and isset(Block::$class[$this->id])){
			$this->block = BlockAPI::get($this->id, $this->meta);
			$this->name = $this->block->getName();
		}
		if($this->isTool() || $this->isArmor() || $this->getID() === SADDLE){
			$this->maxStackSize = 1;
		}
	}
	
	public function isPickaxe(){
		return false;
	}
	
	public function getName(){
		return $this->name;
	}
	
	public function isPlaceable(){
		return (($this->block instanceof Block) and $this->block->isPlaceable === true);
	}
	
	public function getBlock(){
		if($this->block instanceof Block){
			return $this->block;
		}else{
			return BlockAPI::get(AIR);
		}
	}
	
	public function getID(){
		return $this->id;
	}
	
	public function getMetadata(){
		return $this->meta;
	}	
	
	public function isArmor(){
		return false;
	}
	
	public function getMaxStackSize(){
		return $this->maxStackSize;
	}
	
	public function getFuelTime(){
		if(!isset(FuelData::$duration[$this->id])){
			return false;
		}
		if($this->id !== BUCKET or $this->meta === 10){
			return FuelData::$duration[$this->id];
		}
		return false;
	}
	
	public function getSmeltItem(){
		if(!isset(SmeltingData::$product[$this->id])){
			return false;
		}
		
		if(isset(SmeltingData::$product[$this->id][0]) and !is_array(SmeltingData::$product[$this->id][0])){
			return BlockAPI::getItem(SmeltingData::$product[$this->id][0], SmeltingData::$product[$this->id][1]);
		}
		
		if(!isset(SmeltingData::$product[$this->id][$this->meta])){
			return false;
		}
		
		return BlockAPI::getItem(SmeltingData::$product[$this->id][$this->meta][0], SmeltingData::$product[$this->id][$this->meta][1]);
		
	}
	
	public function useOn($object, $force = false){
		if($force){
			if(($object instanceof Entity) and !$this->isSword()){
				$this->meta += 2;
			}else{
				$this->meta++;
			}
			return true;
		}
		return false;
	}
	
	public function isTool(){
		return false;
	}
	
	public function getMaxDurability(){
		if(!$this->isTool() and $this->id !== BOW){
			return false;
		}
		
		$levels = [ //TODO rewrite(item usage too)
			2 => 33, //GOLD
			1 => 60, //WOODEN
			3 => 132, //STONE
			4 => 251, //IRON
			5 => 1562, //DIAMOND(called EMERALD in disassembled code)
			FLINT_STEEL => 65, //lets assume it is correct
			SHEARS => 239, //x2
			BOW => 385 //x3
		];

		if(($type = $this->getLevel()) === false){
			$type = $this->id;
		}
		return $levels[$type];
	}
	
	public function getLevel(){
		return false;
	}
	
	//TODO remove?
	public function getPickaxeLevel(){ //Returns false or level of the pickaxe
 	   return match ($this->id) {
			WOODEN_PICKAXE => 1,
			GOLDEN_PICKAXE => 2,
			STONE_PICKAXE => 3,
			IRON_PICKAXE => 4,
			DIAMOND_PICKAXE => 5,
			default => false,
		};
	}
	
	public function isAxe(){
		return false;
	}

	public function isSword(){
		return false;
	}
	
	public function isShovel(){
		return false;
	}
	
	public function isHoe(){
		return false;
	}

	public function isShears(){
		return ($this->id === SHEARS);
	}
	
	public function __toString(){
		return "Item ". $this->name ." (".$this->id.":".$this->meta.")";
	}
	
	public function getDamageAgainstOf($e){
		return Item::DEF_DAMAGE;
	}
	
	public function getDestroySpeed(Block $block, Player $player){
		return 1;
	}
	
	public function onActivate(Level $level, Player $player, Block $block, Block $target, $face, $fx, $fy, $fz){
		return false;
	}
	
	public static function getFoodHeal($id){
		return match($id){
			APPLE => 4,
			MUSHROOM_STEW => 10,
			BEETROOT_SOUP => 10,
			BREAD => 5,
			RAW_PORKCHOP => 3,
			COOKED_PORKCHOP => 8,
			BEEF => 3,
			STEAK => 8,
			COOKED_CHICKEN => 6,
			RAW_CHICKEN => 2,
			MELON_SLICE => 2,
			GOLDEN_APPLE => 10,
			PUMPKIN_PIE => 8,
			CARROT => 4,
			POTATO => 1,
			BAKED_POTATO => 6,
			BEETROOT => 1,
			
			default => 0
		};
	}
	
}
