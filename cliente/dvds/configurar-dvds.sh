#!/bin/bash

######
# configura-dvds.sh
#
# Script que configura el cliente de DVDS
#
# (C) 2015 Data Control Tecnologías de la Información S.A.
######

# Carpeta base del script
FOLDER=/opt/dvds

#########
# Muestra el menú del script de configuración
########
menu() {
	echo -e "\r\nConfiguración de cliente DVDS\r\n"
	echo "1) Configurar el servidor"
	echo "2) Registrar raspberry en un servidor"
	echo "3) Actualizar medios"
	echo "4) Borrar medios"
	echo "5) Lanzar DVDS"
	echo "6) Detener DVDS"
	echo "7) Salir"
	echo -en "\r\nEscoja una opción:"
}

########
# Solicita los valores de la configuración del programa y los
# almacena en un fichero
########
configurar() {
    echo -n "Servidor [$SERVIDOR]: "
    read nuevo;
    if [ -n "$nuevo" ] ; then
	SERVIDOR=$nuevo
    fi

    echo -n "Puerto [$PUERTO]: "
    read nuevo;
    if [ -n "$nuevo" ] ; then
	PUERTO=$nuevo
    fi

    echo -n "Proxy [$PROXY]: "
    read nuevo
    if [ -n "$nuevo" ] ; then
	PROXY=$nuevo
    fi

    echo -n "Nombre [$NOMBRE]: "
    read nuevo
    if [ -n "$nuevo" ] ; then
	NOMBRE=$nuevo
    fi

    echo -n "Ubicación [$UBICACION]: "
    read nuevo
    if [ -n "$nuevo" ] ; then
	UBICACION=$nuevo
    fi

    echo -n "Comentario [$COMENTARIO]: "
    read nuevo
    if [ -n "$nuevo" ] ; then
	COMENTARIO=$nuevo
    fi

    echo -n "¿Datos correctos? (s/N)"
    read nuevo
    case $nuevo in
	s|S) graba_configuracion
	    . $FOLDER/dvds.conf
	    ;;
	*) . $FOLDER/dvds.conf
	    ;;
    esac
}

#######
# Almacena la configuración actual en un fichero
#######
graba_configuracion() {
    echo "SERVIDOR=$SERVIDOR" > $FOLDER/dvds.conf
    echo "PUERTO=$PUERTO" >> $FOLDER/dvds.conf
    echo "PROXY=$PROXY" >> $FOLDER/dvds.conf
    echo "NOMBRE=\"$NOMBRE\"" >> $FOLDER/dvds.conf
    echo "UBICACION=\"$UBICACION\"" >> $FOLDER/dvds.conf
    echo "COMENTARIO=\"$COMENTARIO\"" >> $FOLDER/dvds.conf
    echo "ID=$ID" >> $FOLDER/dvds.conf
}

########
# Registra el cliente en el servidor
########
registrar() {
    echo "Registrando el cliente [$NOMBRE] en el servidor $SERVIDOR:$PUERTO"
    if [ ! -z $ID ] ; then
	echo "El cliente ya está registrado..."
	echo -n "Pulse ENTER para continuar...";
	read cosa
	return 1
    fi

    if [ -z "$NOMBRE" ] ; then
	echo "Configure el nombre del cliente en la opción de configuración."
	echo -n "Pulse ENTER para continuar...";
	read cosa
	return 1
    fi
    wget -O /tmp/salida.txt "http://$SERVIDOR:$PUERTO/api/?f=register&nombre=\"$NOMBRE\"&ubicacion=\"$UBICACION\"&comentario=\"$COMENTARIO\""

    if (( $? > 0 )) ; then
	echo "Ha ocurrido un error al intentar registrar el cliente... Inténtelo más tarde"
    else
	if [ ! -s /tmp/salida ] ; then
	    ID=`cat /tmp/salida.txt`
	    rm -f /tmp/salida.txt
	    graba_configuracion

	    echo "Cliente registrado con ID: $ID"
	else
	    echo "No se ha obtenido un id del servidor"
	fi
    fi
    echo -n "Pulse ENTER para continuar...";
    read cosa
}

########
# Borra los archivos descargados
########

########
# Inicia el script de reproduccion
########
iniciar() {
    if [ ! -f /tmp/dvds.lock ] ; then
	/usr/bin/php $FOLDER/dvds.php > /dev/null &
    else
	echo "El script ya está en funcionamiento. Si no es así,"
	echo "elimine el archivo /tmp/dvds.lock y pruebe de nuevo."
	echo -n "Pulse Enter para continuar..."
	read cosa
    fi
}

########
# Detiene el script de reproduccion
# Si existe el archivo de bloqueo, se mata el proceso del reproductor
# y envía la señal SIGUSR1 al script
########
detener() {
    if [ -f /tmp/dvds.lock ] ; then
	ACTIVO=`ps h -p \`cat /tmp/dvds.lock\` | wc -l`
	if [ $ACTIVO -eq "1" ] ; then
	    killall omxplayer.bin
	    kill -s SIGUSR1 `cat /tmp/dvds.lock`
	else
	    echo "El sistema DVDS no está funcionando,"
	    echo -e "aunque se encuentra el archivo /tmp/dvds.lock.\r\n"
	    echo -n "Pulse ENTER para continuar..."
	    read cosa
	fi
    else
	echo "El sistema DVDS no está funcionando."
	echo -e "No se encuentra el archivo /tmp/dvds.lock.\r\n"
	echo -n "Pulse ENTER para continuar..."
	read cosa
    fi
}

######################################################
# Cuerpo principal del script
# Muestra el menú y ejecuta las diferentes opciones seleccionadas por el usuario
######################################################

if [ ! -f $FOLDER/dvds.conf ] ; then
    cat <<FIN > $FOLDER/dvds.conf
SERVIDOR=dvd.datacontrol.es
PUERTO=80
PROXY=
ID=
NOMBRE=
UBICACION=
COMENTARIO=
FIN
    chmod 755 $FOLDER/dvds.conf
fi

. $FOLDER/dvds.conf

while true; do
    clear
    menu
    read opcion

    case $opcion in
	1) configurar
	    ;;
	2) registrar
	    ;;
	3) actualizar
	    ;;
	4) borrar
	    ;;
	5) iniciar
	    ;;
	6) detener
	    ;;
	7) exit
	    ;;
	*) echo "Opción incorrecta";
	    ;;
    esac
done

