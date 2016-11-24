#!/bin/bash
for i in {1..50}
do
   #/usr/bin/time -v inkscape -z -e temp/$i.png -w 750 temp/bar.svg
   /usr/bin/time ./test_curl.sh
   echo "Welcome $i times"
done

