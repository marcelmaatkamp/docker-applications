console.log ("process.env.DEVELOP: " + process.env.DEVELOP);

if (process.env.DEVELOP == 1) {
  // keep docker container open for development, but do not start server
  console.log("Starting alive at " + new Date().toLocaleString());
  setInterval(function () {
    console.log("Still alive at " + new Date().toLocaleString());
  }, 60000);
} else {
  // start the message processing for ShowCase
  require("./result/code/processMessages");
}