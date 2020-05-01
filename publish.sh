#!/usr/bin/env bash

if [[ ${1} = 'dev' ]]; then
SSHPASS="${SFTP_PASS}" sshpass -e sftp -oBatchMode=no -b - ${SFTP_USER}@${SFTP_HOST} << !
  cd montmartre
  put -r ./config
  put -r ./src
  put dbmodel.sql
  put material.inc.php
  put montmartre.game.php
  put montmartre.view.php
  put states.inc.php
  put version.php
  put gameinfos.inc.php
  put montmartre.action.php
  put montmartre.js
  put stats.inc.php
  put gameoptions.inc.php
  put montmartre.css
  put montmartre_montmartre.tpl
  bye
!
else
SSHPASS="${SFTP_PASS}" sshpass -e sftp -oBatchMode=no -b - ${SFTP_USER}@${SFTP_HOST} << !
  cd montmartre
  put -r ./config
  put -r ./src
  put -r ./vendor
  put dbmodel.sql
  put material.inc.php
  put montmartre.game.php
  put montmartre.view.php
  put states.inc.php
  put version.php
  put gameinfos.inc.php
  put montmartre.action.php
  put montmartre.js
  put stats.inc.php
  put gameoptions.inc.php
  put montmartre.css
  put montmartre_montmartre.tpl
bye
!
fi
