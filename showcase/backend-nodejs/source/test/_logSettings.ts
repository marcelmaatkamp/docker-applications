/**
 * default winston log settings for testing
 */

import * as winston from "winston";

winston.remove(winston.transports.Console);
winston.add(winston.transports.Console, {
  level: "error",
  timestamp: () => {
    return "[" + new Date().toLocaleTimeString([], { hour12: false }) + "]";
  },
  formatter: (options) => {
    return options.timestamp() + " " +
      options.level.toUpperCase() + " " +
      (options.message === undefined ? "" : options.message);
      // (options.meta && Object.keys(options.meta).length ? '\n\t'+ JSON.stringify(options.meta) : '' );
  }
});
//winston.add(winston.transports.Console, { timestamp: true, level: "debug", prettyPrint: true });