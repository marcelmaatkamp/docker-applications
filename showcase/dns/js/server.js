var dns = require('native-dns');
var cst = require('console-stamp')(console, 'HH:MM:ss.l');

server = dns.createServer()
server.on('request', function (request, response) {
  console.log("["+request.address.address+"] class["+request.question[0].class+"] type["+request.question[0].type+"] "+request.question[0].name)

  if(request.question[0].name == "dns.uk.ms") {
    response.answer.push(dns.A({
      name: request.question[0].name,
      address: '93.191.128.252',
      ttl: 60,
    }))

  } else if(request.question[0].name.endsWith(".dns.uk.ms")) { 
     var address = request.question[0].name.substring(0, request.question[0].name.length-".dns.uk.ms".length);
     console.log("address: " + address);

     if (/^(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/.test(address)) {
       console.log("address.address.1: " + address);
     } else if(/^(\w+)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/.test(address)) { 
       console.log("domain.address.2: " + address);
       address = address.substring(address.indexOf('.')+1);
       console.log("domain.address.3: " + address);
     }

     console.log("address.4: " + address);
     response.answer.push(dns.A({
      name: request.question[0].name,
      address: address,
      ttl: 60,
     }))

  } else { 
    console.log("not ending dns: " + request.question[0].name + ", return home!");
    response.answer.push(dns.A({
      name: request.question[0].name,
      address: '127.0.0.1',
      ttl: 60,
    }))
  }

  response.send()

})
server.on('error', function (err, buff, req, res) {
  console.log(err.stack)
})

var dns_port = process.env.DNS_PORT||53
console.log("Starting on port " +dns_port)
server.serve(dns_port)

