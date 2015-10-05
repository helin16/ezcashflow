@echo off
:: Remove Existing databases
c:\wamp\bin\mysql\mysql5.6.17\bin\mysql.exe -u root -proot -e "DROP DATABASE IF EXISTS ezcashflow;"

:: Create new databases
c:\wamp\bin\mysql\mysql5.6.17\bin\mysql.exe -u root -proot -e "CREATE DATABASE `ezcashflow` DEFAULT CHARACTER SET utf8 DEFAULT COLLATE utf8_general_ci;"

Pause

:: Import sql files
c:\wamp\bin\mysql\mysql5.6.17\bin\mysql.exe -u root -proot ezcashflow < structure.sql
c:\wamp\bin\mysql\mysql5.6.17\bin\mysql.exe -u root -proot ezcashflow < data.sql
Pause