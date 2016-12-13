var Cap = require('cap').Cap,
    decoders = require('cap').decoders,
    PROTOCOL = decoders.PROTOCOL;

console.log("decoders: " + JSON.stringify(decoders));
var c = new Cap(),
    device = 'mon0',
    filter = '',
    bufSize = 10 * 1024 * 1024,
    buffer = new Buffer(65535);

var linkType = c.open(device, filter, bufSize, buffer);

c.setMinBytes && c.setMinBytes(0);

c.on('packet', function(nbytes, trunc) {
  console.log('packet' +linkType+', length ' + nbytes + ' bytes, truncated? ' + (trunc ? 'yes' : 'no'));
  var ret = decoders.Ethernet(buffer);
  console.log(ret.info.type + "ret: " + JSON.stringify(ret) + ", json: " + JSON.stringify(ret));
});
