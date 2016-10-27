/**
 * decode ProtocolBuffer messages
 *
 * 2016-10-11 Ab Reitsma
 */
"use strict";
// initialize protocol buffers
var winston = require("winston");
var fs = require("fs");
var path = require("path");
var protoBuf = require("protocol-buffers");
var protobufFileLocation = path.join(__dirname, "..", "..", "data", "sensor.proto");
var protoBufMsg = protoBuf(fs.readFileSync(protobufFileLocation));
function decodeProtoBuff(encodedMessage) {
    "use strict";
    try {
        return protoBufMsg.NodeMessage.decode(encodedMessage).reading;
    }
    catch (err) {
        winston.error("Error decoding protocol buffers: " + err.message);
        throw err;
    }
}
Object.defineProperty(exports, "__esModule", { value: true });
exports.default = decodeProtoBuff;

//# sourceMappingURL=decodeProtobuf.js.map
