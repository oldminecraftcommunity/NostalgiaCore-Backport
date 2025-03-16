<?php


abstract class ProtocolInfo{
	public static $CURRENT_PROTOCOL;

	const PING_PACKET = 0x00;

	const PONG_PACKET = 0x03;

	const CLIENT_CONNECT_PACKET = 0x09;
	const SERVER_HANDSHAKE_PACKET = 0x10;

	const CLIENT_HANDSHAKE_PACKET = 0x13;
	//const SERVER_FULL_PACKET = 0x14;
	const DISCONNECT_PACKET = 0x15;
	const LOGIN_PACKET = 0x82;
	const LOGIN_STATUS_PACKET = 0x83;
	const READY_PACKET = 0x84;
	const MESSAGE_PACKET = 0x85;
	const SET_TIME_PACKET = 0x86;
	const START_GAME_PACKET = 0x87;
	const ADD_MOB_PACKET = 0x88;
	const ADD_PLAYER_PACKET = 0x89;
	const REMOVE_PLAYER_PACKET = 0x8a;

	const ADD_ENTITY_PACKET = 0x8c;
	const REMOVE_ENTITY_PACKET = 0x8d;
	const ADD_ITEM_ENTITY_PACKET = 0x8e;
	const TAKE_ITEM_ENTITY_PACKET = 0x8f;
	const MOVE_ENTITY_PACKET = 0x90;

	const MOVE_ENTITY_PACKET_POSROT = 0x93;
	const MOVE_PLAYER_PACKET = 0x94;
	//const PLACE_BLOCK_PACKET = 0x95;
	const REMOVE_BLOCK_PACKET = 0x96;
	const UPDATE_BLOCK_PACKET = 0x97;
	const ADD_PAINTING_PACKET = 0x98;
	const EXPLODE_PACKET = 0x99;
	const LEVEL_EVENT_PACKET = 0x9a;
	const TILE_EVENT_PACKET = 0x9b;
	const ENTITY_EVENT_PACKET = 0x9c;
	const REQUEST_CHUNK_PACKET = 0x9d;
	const CHUNK_DATA_PACKET = 0x9e;
	const PLAYER_EQUIPMENT_PACKET = 0x9f;
	const PLAYER_ARMOR_EQUIPMENT_PACKET = 0xa0;
	const INTERACT_PACKET = 0xa1;
	const USE_ITEM_PACKET = 0xa2;
	const PLAYER_ACTION_PACKET = 0xa3;

	const HURT_ARMOR_PACKET = 0xa5;
	const SET_ENTITY_DATA_PACKET = 0xa6;
	const SET_ENTITY_MOTION_PACKET = 0xa7;
	//const SET_ENTITY_LINK_PACKET = 0xa?;
	const SET_HEALTH_PACKET = 0xa8;
	const SET_SPAWN_POSITION_PACKET = 0xa9;
	const ANIMATE_PACKET = 0xaa;
	const RESPAWN_PACKET = 0xab;
	const SEND_INVENTORY_PACKET = 0xac;
	const DROP_ITEM_PACKET = 0xad;
	const CONTAINER_OPEN_PACKET = 0xae;
	const CONTAINER_CLOSE_PACKET = 0xaf;
	const CONTAINER_SET_SLOT_PACKET = 0xb0;
	const CONTAINER_SET_DATA_PACKET = 0xb1;
	const CONTAINER_SET_CONTENT_PACKET = 0xb2;
	//const CONTAINER_ACK_PACKET = 0xb3;
	const CHAT_PACKET = 0xb4;
	const ADVENTURE_SETTINGS_PACKET = 0xb6;
	const ENTITY_DATA_PACKET = 0xb7;
	const PLAYER_INPUT_PACKET = 0xb9;

	const ROTATE_HEAD_PACKET = 0xff;
	const SET_ENTITY_LINK_PACKET = 0xff;
}
/*Unused:
 * 0xb5
 * 0xb9
 * 0x96
 * 0x17
 * 0x14
 */
