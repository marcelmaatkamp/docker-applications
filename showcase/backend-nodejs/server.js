if (process.env.DEVELOP) {
  // keep docker container open for development, but do not start server
  console.log("Starting alive at " + new Date().toLocaleString());
  setInterval(function () {
    console.log("Still alive at " + new Date().toLocaleString());
  }, 60000);
} else {
  // start the message processing for ShowCase
  require("./result/code/processMessages");
}