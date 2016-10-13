/**
 * iotMessage type definitions
 *
 * 2016-10-11 Ab Reitsma
 */

// protobuf format:
// syntax = "proto2";
//
// message SensorReading {
// 	optional uint32 id = 1;
// 	optional uint32 error = 2;
// 	optional sint32 value1 = 3;
// 	optional sint32 value2 = 4;
// 	optional sint32 value3 = 5;
// 	optional sint32 value4 = 6;
// 	optional sint32 value5 = 7;
// 	optional sint64 value6 = 8;
// 	optional sint64 value7 = 9;
// 	optional sint64 value8 = 10;
// 	optional sint64 value9 = 11;
// 	optional sint64 value10 = 12;
// }
//
// message NodeMessage {
//     repeated SensorReading reading = 1;
// }
export declare interface IotPayload {
    id?: number;
    error?: number;
    value1?: number;
    value2?: number;
    value3?: number;
    value4?: number;
    value5?: number;
    value6?: number;
    value7?: number;
    value8?: number;
    value9?: number;
    value10?: number;
}

// TTN Lora JSON format:
// {
//     "payload": "CgQIARgC",
//     "port": 1,
//     "counter": 8,
//     "dev_eui": "000000007FEE6E5B",
//     "metadata": [
//         {
//             "frequency": 868.5,
//             "datarate": "SF7BW125",
//             "codingrate": "4/5",
//             "gateway_timestamp": 2913536323,
//             "channel": 2,
//             "server_time": "2016-09-09T09:14:32.141349077Z",
//             "rssi": -34,
//             "lsnr": 6.2,
//             "rfchain": 1,
//             "crc": 1,
//             "modulation": "LORA",
//             "gateway_eui": "0000024B0805026F",
//             "altitude": -1,
//             "longitude": 5.26561,
//             "latitude": 52.05755
//         }
//     ]
// }
export declare interface Metadata {
    frequency?: number;
    datarate?: string;
    codingrate?: string;
    gateway_timestamp?: number;
    channel?: number;
    server_time: string; // ISO timestring
    rssi?: number;
    lsnr?: number;
    crc?: number;
    modulation?: number;
    gateway_eui?: string;
    altitude?: number;
    longitude?: number;
    latitude?: number;
}

export declare interface Message {
    payload:  IotPayload;
    port?: number;
    counter: number;
    dev_eui: string;
    metadata: Metadata;
}

export declare interface SensorObservation {
    timestamp: string; // ISO timestring
    nodeId: string;
    sensorId: string;
    sensorError: number;
    sensorValue: number;
    sensorValue2?: number;
    sensorValue3?: number;
    sensorValue4?: number;
    sensorValue5?: number;
    sensorValue6?: number;
    sensorValue7?: number;
    sensorValue8?: number;
    sensorValue9?: number;
    sensorValue10?: number;
}
