#!/bin/bash
# RECONFIGURE LOGROTATE
/opt/keexybox/keexyapp/bin/cake config logrotate main

# Clean upload and download dir :
rm -f /opt/keexybox/keexyapp/webroot/upload/*
rm -f /opt/keexybox/keexyapp/webroot/download/*
