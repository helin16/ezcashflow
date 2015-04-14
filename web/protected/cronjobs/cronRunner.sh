#!/bin/bash

# run all the message sending 
/usr/bin/php /var/www/ezbk/web/protected/cronjobs/MessageSender.php >> /tmp/message_`date +"%d_%b_%y"`.log
