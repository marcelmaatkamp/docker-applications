FROM svendowideit/screenshot
ENTRYPOINT ["/phantomjs/bin/phantomjs", "--debug=true", "--ignore-ssl-errors=yes", "--ssl-protocol=tlsv1", "--proxy-type=socks5", "--proxy=tor:9050", "/rasterize.js"]
