#!/usr/bin/env bash

SSHPASS="${SFTP_PASS}" sshpass -e sftp -oBatchMode=no -b - ${SFTP_USER}@${SFTP_HOST} << !
  cd montmartre
  put -r ./*
  bye
!
