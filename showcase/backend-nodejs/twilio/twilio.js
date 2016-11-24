var accountSid = 'AC600a293801150c7c3af3a5747a3ba4ae'; 
var authToken = 'ad1f82c56f5b9f048e72558ae984edf8'; 
var client = require('twilio')(accountSid, authToken); 
 
client.messages.create({ 
    to: "+31652358975", 
    from: "+15017250604", 
    body: "This is the ship that made the Kessel Run in fourteen parsecs?", 
}, function(err, message) { 
    console.log("error: " + JSON.stringify(err) + ", " + message); 
});
