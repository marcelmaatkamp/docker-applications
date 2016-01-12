#!/bin/bash
cat hs_total_clean.txt | sed -e 's/, \[/, \{/g' | sed -e 's/], None).*/}/g' | sed -e 's/\\r\\n//g' | sed -e "s/'//g" | sed -e "s/,/\",/g" | sed -e "s/, /, \"/g" | sed -e "s/, \"{/, {/g" | sed -e "s/: /\": \"/g"
