#!/bin/bash

set -e 

shutdown() {
  kill -s SIGTERM $chromedriver
  wait $chromedriver
  kill -s SIGTERM $xvfb
  wait $xvfb
}

# Get the server number for xvfb
function get_server_num() {
  echo $(echo $DISPLAY | sed -r -e 's/([^:]+)?:([0-9]+)(\.[0-9]+)?/\2/')
}

# Prep xvfb
SERVERNUM=$(get_server_num)
rm -f /tmp/.X*lock

# Run xvfb
Xvfb :$SERVERNUM -screen 0 1920x1200x16 &
xvfb=$!
# Run chromedriver
/usr/bin/chromedriver &
chromedriver=$!

# Setup a trap to catch SIGTERM and relay it to child processes
trap shutdown SIGTERM SIGINT

while [ true ]
do
    php artisan schedule:run --verbose --no-interaction &
    sleep 60
done
