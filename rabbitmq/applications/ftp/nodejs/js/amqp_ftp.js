var JSFtp = require("jsftp");

var ftp_hostname = process.env.FTP_HOSTNAME;
var ftp_username = process.env.FTP_USERNAME;
var ftp_password = process.env.FTP_PASSWORD;

console.log("connecting to ftp://"+ftp_username+":"+ftp_password+"@"+ftp_hostname+" ... ");

var ftp = new JSFtp({
  host: ftp_hostname
});

ftp.auth(ftp_username, ftp_password, function(err, res) {
 if (err) throw err;

 var buffer = new Buffer("I'm a string!", "utf-8")
 ftp.put(buffer, 'file.txt', function(hadError) {
  if (!hadError)
    console.log("File transferred successfully!");
  else 
    console.log("error: " + hadError);
 });
 
 ftp.ls(".", function(err, res) {
  res.forEach(function(file) {
    console.log(file.name);
  });
 });

});

ftp.raw.quit(function(err, res) {
 if (err) throw err;
 console.log("Bye!");
});

