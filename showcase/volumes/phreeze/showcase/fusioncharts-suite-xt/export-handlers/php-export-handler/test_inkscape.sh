#!/bin/bash
for i in {1..100}
do
   /usr/bin/time -v inkscape -z -e temp/$i.png -w 750 temp/bar.svg
   echo "Welcome $i times"
done

