#!/bin/sh

sudo chcon -R -t httpd_sys_content_t /var/www/html/bourseauxjoeuts
sudo chcon -R -t httpd_sys_rw_content_t /var/www/html/bourseauxjoeuts
sudo chcon -t httpd_sys_script_exec_t /var/www/html/bourseauxjoeuts/git-update.sh
sudo setsebool -P httpd_can_network_connect=1
sudo chown -R apache:apache /var/www/html/bourseauxjoeuts