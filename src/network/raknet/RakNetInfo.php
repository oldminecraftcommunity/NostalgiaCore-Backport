<?php

abstract class RakNetInfo{

	const STRUCTURE = 5;
	const MAGIC = "\x00\xff\xff\x00\xfe\xfe\xfe\xfe\xfd\xfd\xfd\xfd\x12\x34\x56\x78";
	const UNCONNECTED_PING = 0x01;
	const UNCONNECTED_PING_OPEN_CONNECTIONS = 0x02;

	const OPEN_CONNECTION_REQUEST_1 = 0x05;
	const OPEN_CONNECTION_REPLY_1 = 0x06;
	const OPEN_CONNECTION_REQUEST_2 = 0x07;
	const OPEN_CONNECTION_REPLY_2 = 0x08;

	const INCOMPATIBLE_PROTOCOL_VERSION = 0x1a; //CHECK THIS

	const UNCONNECTED_PONG = 0x1c;
	const ADVERTISE_SYSTEM = 0x1d;

	const DATA_PACKET_0 = 0x80;
	const DATA_PACKET_1 = 0x81;
	const DATA_PACKET_2 = 0x82;
	const DATA_PACKET_3 = 0x83;
	const DATA_PACKET_4 = 0x84;
	const DATA_PACKET_5 = 0x85;
	const DATA_PACKET_6 = 0x86;
	const DATA_PACKET_7 = 0x87;
	const DATA_PACKET_8 = 0x88;
	const DATA_PACKET_9 = 0x89;
	const DATA_PACKET_A = 0x8a;
	const DATA_PACKET_B = 0x8b;
	const DATA_PACKET_C = 0x8c;
	const DATA_PACKET_D = 0x8d;
	const DATA_PACKET_E = 0x8e;
	const DATA_PACKET_F = 0x8f;

	const NACK = 0xa0;
	const ACK = 0xc0;

	public static function isValid($pid){
        return match ((int)$pid) {
            RakNetInfo::UNCONNECTED_PING, RakNetInfo::UNCONNECTED_PING_OPEN_CONNECTIONS, RakNetInfo::OPEN_CONNECTION_REQUEST_1, RakNetInfo::OPEN_CONNECTION_REPLY_1, RakNetInfo::OPEN_CONNECTION_REQUEST_2, RakNetInfo::OPEN_CONNECTION_REPLY_2, RakNetInfo::INCOMPATIBLE_PROTOCOL_VERSION, RakNetInfo::UNCONNECTED_PONG, RakNetInfo::ADVERTISE_SYSTEM, RakNetInfo::DATA_PACKET_0, RakNetInfo::DATA_PACKET_1, RakNetInfo::DATA_PACKET_2, RakNetInfo::DATA_PACKET_3, RakNetInfo::DATA_PACKET_4, RakNetInfo::DATA_PACKET_5, RakNetInfo::DATA_PACKET_6, RakNetInfo::DATA_PACKET_7, RakNetInfo::DATA_PACKET_8, RakNetInfo::DATA_PACKET_9, RakNetInfo::DATA_PACKET_A, RakNetInfo::DATA_PACKET_B, RakNetInfo::DATA_PACKET_C, RakNetInfo::DATA_PACKET_D, RakNetInfo::DATA_PACKET_E, RakNetInfo::DATA_PACKET_F, RakNetInfo::NACK, RakNetInfo::ACK => true,
            default => false,
        };
	}
}