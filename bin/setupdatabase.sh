#! /usr/bin/env bash
#
# Copyright (C) 2011 by iRail vzw/asbl 
# Author: Jan Vansteenlandt <jan at iRail.be>
# License: AGPLv3


# This script uses your MySQL to initialize some tables used for logging purposes -> errors and requests


NUMBER_OF_ARGS=2;
if [ $# -eq $NUMBER_OF_ARGS ]
then

    Q1=" use $2; CREATE TABLE IF NOT EXISTS errors (
  id bigint(20) NOT NULL AUTO_INCREMENT,
  time bigint(20) DEFAULT NULL,
  user_agent varchar(255) DEFAULT NULL,
  ip varchar(255) DEFAULT NULL,
  url_request varchar(255) DEFAULT NULL,
  error_message varchar(255) DEFAULT NULL,
  error_code varchar(255) DEFAULT NULL,
  PRIMARY KEY (id)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;"

    Q2="CREATE TABLE IF NOT EXISTS requests (
  id bigint(20) NOT NULL AUTO_INCREMENT,
  time bigint(20) DEFAULT NULL,
  user_agent varchar(255) DEFAULT NULL,
  ip varchar(40) DEFAULT NULL,
  url_request varchar(512) DEFAULT NULL,
  module varchar(64) DEFAULT NULL,
  resource varchar(64) DEFAULT NULL,
  format varchar(24) DEFAULT NULL,
  subresources varchar(128) DEFAULT NULL,
  reqparameters varchar(128) DEFAULT NULL,
  allparameters varchar(164) DEFAULT NULL,
  PRIMARY KEY (id)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;"

    Q3="CREATE TABLE IF NOT EXISTS feedback_messages (
  id bigint(20) NOT NULL AUTO_INCREMENT,
  url_request varchar(255) DEFAULT NULL,
  msg text NOT NULL,
  PRIMARY KEY (id)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;"

    SQL="${Q1}${Q2}${Q3}"

    mysql -u "$1" -p -e "$SQL"

else
    echo -e "$NUMBER_OF_ARGS arguments should be passed along with the script. 
Usage : bash set_up_database.sh MySQL_USERNAME MySQL_DATABASE";
fi

