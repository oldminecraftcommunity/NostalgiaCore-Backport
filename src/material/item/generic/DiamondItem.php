<?php

class DiamondItem extends Item{
	public function __construct($meta = 0, $count = 1){
		parent::__construct(DIAMOND, 0, $count, "Diamond");
	}

}