#!/bin/bash
echo $1 >> /usr/local/bin/test.log
echo "$(whoami)" >> /usr/local/bin/test.log
sudo -u cacti `/usr/bin/php -q /var/www/html/cacti/plugins/impb/cli_impb.php "$1"`
