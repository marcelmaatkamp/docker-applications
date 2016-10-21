/**
 * default winston log settings for testing
 */

import * as winston from "winston";

winston.remove(winston.transports.Console);
var formatter = require("winston-console-formatter").config();
formatter.level = "error";
winston.add(winston.transports.Console, formatter);
//winston.add(winston.transports.Console, { timestamp: true, level: "debug", prettyPrint: true });