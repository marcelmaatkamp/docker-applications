#!/bin/bash
USERNAME=${FTP_USERNAME:-ftpuser}
PASSWORD=${FTP_PASSWORD:-ftpuser}
SERVER=${FTP_SERVER:-pureftpd}

TMPFILE=`mktemp`
read line
echo "$line" > $TMPFILE
curl -s -T $TMPFILE ftp://${SERVER} --user ${USERNAME}:${PASSWORD}
rm $TMPFILE
