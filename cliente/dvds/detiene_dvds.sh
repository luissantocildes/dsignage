#! /bin/sh

    if [ -f /tmp/dvds.lock ] ; then
	ACTIVO=`ps h -p \`cat /tmp/dvds.lock\` | wc -l`
	if [ $ACTIVO -eq "1" ] ; then
	    killall omxplayer.bin
	    kill -10 `cat /tmp/dvds.lock`
	fi
    fi
