<?php

$itemclasses = array(
  array('id'=>'0','name'=>'Consumable'),
  array('id'=>'1','name'=>'Container'),
  array('id'=>'2','name'=>'Weapon'),
  array('id'=>'3','name'=>'Gem'),
  array('id'=>'4','name'=>'Armor'),
  array('id'=>'5','name'=>'Reagent'),
  array('id'=>'6','name'=>'Projectile'),
  array('id'=>'7','name'=>'Trade Goods'),
  array('id'=>'8','name'=>'Generic(OBSOLETE)'),
  array('id'=>'9','name'=>'Recipe'),
  array('id'=>'10','name'=>'Money'),
  array('id'=>'11','name'=>'Quiver'),
  array('id'=>'12','name'=>'Quest'),
  array('id'=>'13','name'=>'Key'),
  array('id'=>'14','name'=>'Permanent(OBSOLETE)'),
  array('id'=>'15','name'=>'Miscellaneous'),
  array('id'=>'16','name'=>'Glyph')
);

function Item_FindClass($class)
{
	global $itemclasses;
	
	foreach ($itemclasses as $i => $data)
	{
		if ((int)$data['id'] == (int)$class)
		{
			return $data['name'];
		}
	}
	
	return 'Unknown';
}

$itemsubclasses = array(
  array('class'=>'0','subclass'=>'0','name'=>'Consumable','name2'=>''),
  array('class'=>'0','subclass'=>'1','name'=>'Potion','name2'=>''),
  array('class'=>'0','subclass'=>'2','name'=>'Elixir','name2'=>''),
  array('class'=>'0','subclass'=>'3','name'=>'Flask','name2'=>''),
  array('class'=>'0','subclass'=>'4','name'=>'Scroll','name2'=>''),
  array('class'=>'0','subclass'=>'5','name'=>'Food & Drink','name2'=>''),
  array('class'=>'0','subclass'=>'6','name'=>'Item Enhancement','name2'=>''),
  array('class'=>'0','subclass'=>'7','name'=>'Bandage','name2'=>''),
  array('class'=>'0','subclass'=>'8','name'=>'Other','name2'=>''),
  array('class'=>'1','subclass'=>'0','name'=>'Bag','name2'=>''),
  array('class'=>'1','subclass'=>'1','name'=>'Soul Bag','name2'=>''),
  array('class'=>'1','subclass'=>'2','name'=>'Herb Bag','name2'=>''),
  array('class'=>'1','subclass'=>'3','name'=>'Enchanting Bag','name2'=>''),
  array('class'=>'1','subclass'=>'4','name'=>'Engineering Bag','name2'=>''),
  array('class'=>'1','subclass'=>'5','name'=>'Gem Bag','name2'=>''),
  array('class'=>'1','subclass'=>'6','name'=>'Mining Bag','name2'=>''),
  array('class'=>'1','subclass'=>'7','name'=>'Leatherworking Bag','name2'=>''),
  array('class'=>'1','subclass'=>'8','name'=>'Inscription Bag','name2'=>''),
  array('class'=>'2','subclass'=>'0','name'=>'Axe','name2'=>'One-Handed Axes'),
  array('class'=>'2','subclass'=>'1','name'=>'Axe','name2'=>'Two-Handed Axes'),
  array('class'=>'2','subclass'=>'2','name'=>'Bow','name2'=>'Bows'),
  array('class'=>'2','subclass'=>'3','name'=>'Gun','name2'=>'Guns'),
  array('class'=>'2','subclass'=>'4','name'=>'Mace','name2'=>'One-Handed Maces'),
  array('class'=>'2','subclass'=>'5','name'=>'Mace','name2'=>'Two-Handed Maces'),
  array('class'=>'2','subclass'=>'6','name'=>'Polearm','name2'=>'Polearms'),
  array('class'=>'2','subclass'=>'7','name'=>'Sword','name2'=>'One-Handed Swords'),
  array('class'=>'2','subclass'=>'8','name'=>'Sword','name2'=>'Two-Handed Swords'),
  array('class'=>'2','subclass'=>'9','name'=>'Obsolete','name2'=>''),
  array('class'=>'2','subclass'=>'10','name'=>'Staff','name2'=>'Staves'),
  array('class'=>'2','subclass'=>'11','name'=>'Exotic','name2'=>'One-Handed Exotics'),
  array('class'=>'2','subclass'=>'12','name'=>'Exotic','name2'=>'Two-Handed Exotics'),
  array('class'=>'2','subclass'=>'13','name'=>'Fist Weapon','name2'=>'Fist Weapons'),
  array('class'=>'2','subclass'=>'14','name'=>'Miscellaneous','name2'=>''),
  array('class'=>'2','subclass'=>'15','name'=>'Dagger','name2'=>'Daggers'),
  array('class'=>'2','subclass'=>'16','name'=>'Thrown','name2'=>'Thrown'),
  array('class'=>'2','subclass'=>'17','name'=>'Spear','name2'=>'Spears'),
  array('class'=>'2','subclass'=>'18','name'=>'Crossbow','name2'=>'Crossbows'),
  array('class'=>'2','subclass'=>'19','name'=>'Wand','name2'=>'Wands'),
  array('class'=>'2','subclass'=>'20','name'=>'Fishing Pole','name2'=>'Fishing Poles'),
  array('class'=>'3','subclass'=>'0','name'=>'Red','name2'=>''),
  array('class'=>'3','subclass'=>'1','name'=>'Blue','name2'=>''),
  array('class'=>'3','subclass'=>'2','name'=>'Yellow','name2'=>''),
  array('class'=>'3','subclass'=>'3','name'=>'Purple','name2'=>''),
  array('class'=>'3','subclass'=>'4','name'=>'Green','name2'=>''),
  array('class'=>'3','subclass'=>'5','name'=>'Orange','name2'=>''),
  array('class'=>'3','subclass'=>'6','name'=>'Meta','name2'=>''),
  array('class'=>'3','subclass'=>'7','name'=>'Simple','name2'=>''),
  array('class'=>'3','subclass'=>'8','name'=>'Prismatic','name2'=>''),
  array('class'=>'4','subclass'=>'0','name'=>'Miscellaneous','name2'=>''),
  array('class'=>'4','subclass'=>'1','name'=>'Cloth','name2'=>'Cloth'),
  array('class'=>'4','subclass'=>'2','name'=>'Leather','name2'=>'Leather'),
  array('class'=>'4','subclass'=>'3','name'=>'Mail','name2'=>'Mail'),
  array('class'=>'4','subclass'=>'4','name'=>'Plate','name2'=>'Plate'),
  array('class'=>'4','subclass'=>'5','name'=>'Buckler(OBSOLETE)','name2'=>'Bucklers'),
  array('class'=>'4','subclass'=>'6','name'=>'Shield','name2'=>'Shields'),
  array('class'=>'4','subclass'=>'7','name'=>'Libram','name2'=>'Librams'),
  array('class'=>'4','subclass'=>'8','name'=>'Idol','name2'=>'Idols'),
  array('class'=>'4','subclass'=>'9','name'=>'Totem','name2'=>'Totems'),
  array('class'=>'4','subclass'=>'10','name'=>'Sigil','name2'=>'Sigils'),
  array('class'=>'5','subclass'=>'0','name'=>'Reagent','name2'=>''),
  array('class'=>'6','subclass'=>'0','name'=>'Wand(OBSOLETE)','name2'=>''),
  array('class'=>'6','subclass'=>'1','name'=>'Bolt(OBSOLETE)','name2'=>''),
  array('class'=>'6','subclass'=>'2','name'=>'Arrow','name2'=>''),
  array('class'=>'6','subclass'=>'3','name'=>'Bullet','name2'=>''),
  array('class'=>'6','subclass'=>'4','name'=>'Thrown(OBSOLETE)','name2'=>''),
  array('class'=>'7','subclass'=>'0','name'=>'Trade Goods','name2'=>''),
  array('class'=>'7','subclass'=>'1','name'=>'Parts','name2'=>''),
  array('class'=>'7','subclass'=>'2','name'=>'Explosives','name2'=>''),
  array('class'=>'7','subclass'=>'3','name'=>'Devices','name2'=>''),
  array('class'=>'7','subclass'=>'4','name'=>'Jewelcrafting','name2'=>''),
  array('class'=>'7','subclass'=>'5','name'=>'Cloth','name2'=>''),
  array('class'=>'7','subclass'=>'6','name'=>'Leather','name2'=>''),
  array('class'=>'7','subclass'=>'7','name'=>'Metal & Stone','name2'=>''),
  array('class'=>'7','subclass'=>'8','name'=>'Meat','name2'=>''),
  array('class'=>'7','subclass'=>'9','name'=>'Herb','name2'=>''),
  array('class'=>'7','subclass'=>'10','name'=>'Elemental','name2'=>''),
  array('class'=>'7','subclass'=>'11','name'=>'Other','name2'=>''),
  array('class'=>'7','subclass'=>'12','name'=>'Enchanting','name2'=>''),
  array('class'=>'7','subclass'=>'13','name'=>'Materials','name2'=>''),
  array('class'=>'7','subclass'=>'14','name'=>'Armor Enchantment','name2'=>'Armor Enchantment'),
  array('class'=>'7','subclass'=>'15','name'=>'Weapon Enchantment','name2'=>'Weapon Enchantment'),
  array('class'=>'8','subclass'=>'0','name'=>'Generic(OBSOLETE)','name2'=>''),
  array('class'=>'9','subclass'=>'0','name'=>'Book','name2'=>''),
  array('class'=>'9','subclass'=>'1','name'=>'Leatherworking','name2'=>''),
  array('class'=>'9','subclass'=>'2','name'=>'Tailoring','name2'=>''),
  array('class'=>'9','subclass'=>'3','name'=>'Engineering','name2'=>''),
  array('class'=>'9','subclass'=>'4','name'=>'Blacksmithing','name2'=>''),
  array('class'=>'9','subclass'=>'5','name'=>'Cooking','name2'=>''),
  array('class'=>'9','subclass'=>'6','name'=>'Alchemy','name2'=>''),
  array('class'=>'9','subclass'=>'7','name'=>'First Aid','name2'=>''),
  array('class'=>'9','subclass'=>'8','name'=>'Enchanting','name2'=>''),
  array('class'=>'9','subclass'=>'9','name'=>'Fishing','name2'=>''),
  array('class'=>'9','subclass'=>'10','name'=>'Jewelcrafting','name2'=>''),
  array('class'=>'9','subclass'=>'11','name'=>'Inscription','name2'=>'Inscription'),
  array('class'=>'10','subclass'=>'0','name'=>'Money(OBSOLETE)','name2'=>''),
  array('class'=>'11','subclass'=>'0','name'=>'Quiver(OBSOLETE)','name2'=>''),
  array('class'=>'11','subclass'=>'1','name'=>'Quiver(OBSOLETE)','name2'=>''),
  array('class'=>'11','subclass'=>'2','name'=>'Quiver','name2'=>''),
  array('class'=>'11','subclass'=>'3','name'=>'Ammo Pouch','name2'=>''),
  array('class'=>'12','subclass'=>'0','name'=>'Quest','name2'=>''),
  array('class'=>'13','subclass'=>'0','name'=>'Key','name2'=>''),
  array('class'=>'13','subclass'=>'1','name'=>'Lockpick','name2'=>''),
  array('class'=>'14','subclass'=>'0','name'=>'Permanent','name2'=>''),
  array('class'=>'15','subclass'=>'0','name'=>'Junk','name2'=>''),
  array('class'=>'15','subclass'=>'1','name'=>'Reagent','name2'=>''),
  array('class'=>'15','subclass'=>'2','name'=>'Pet','name2'=>''),
  array('class'=>'15','subclass'=>'3','name'=>'Holiday','name2'=>''),
  array('class'=>'15','subclass'=>'4','name'=>'Other','name2'=>''),
  array('class'=>'15','subclass'=>'5','name'=>'Mount','name2'=>'Mount'),
  array('class'=>'16','subclass'=>'1','name'=>'Warrior','name2'=>'Warrior'),
  array('class'=>'16','subclass'=>'2','name'=>'Paladin','name2'=>'Paladin'),
  array('class'=>'16','subclass'=>'3','name'=>'Hunter','name2'=>'Hunter'),
  array('class'=>'16','subclass'=>'4','name'=>'Rogue','name2'=>'Rogue'),
  array('class'=>'16','subclass'=>'5','name'=>'Priest','name2'=>'Priest'),
  array('class'=>'16','subclass'=>'6','name'=>'Death Knight','name2'=>'Death Knight'),
  array('class'=>'16','subclass'=>'7','name'=>'Shaman','name2'=>'Shaman'),
  array('class'=>'16','subclass'=>'8','name'=>'Mage','name2'=>'Mage'),
  array('class'=>'16','subclass'=>'9','name'=>'Warlock','name2'=>'Warlock'),
  array('class'=>'16','subclass'=>'11','name'=>'Druid','name2'=>'Druid')
);

function Item_FindSubclass($class, $subclass)
{
	global $itemsubclasses;
	
	foreach ($itemsubclasses as $i => $data)
	{
		if ((int)$data['class'] == (int)$class && (int)$data['subclass'] == (int)$subclass)
		{
			return $data['name'];
		}
	}
	
	return 'Unknown';
}