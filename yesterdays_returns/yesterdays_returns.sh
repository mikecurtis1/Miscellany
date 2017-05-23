#!/usr/bin/ksh
## /UPS_PROD/sny_scripts/yesterdays_returns.sh
## RUN the plus script in the database
sqlplus $ALEPH_ADMIN @/app/exlibris/aleph/prod/ups/sny_scripts/yesterdays_returns.sql;
