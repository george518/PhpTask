#!/bin/bash
COUNTER=0
while [ "${COUNTER}" -lt 500 ]
do
	sleep 5
    #COUNTER=`expr "${COUNTER}"+1`
    echo "${COUNTER}" & > /tmp/testx.log
done