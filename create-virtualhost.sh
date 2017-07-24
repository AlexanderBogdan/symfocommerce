#!/bin/bash

echo -n "Enter name of host: "
read newHost

echo -n "Enter path from / to files of site (without / in the end): "
read newPath

#Add new record in hosts
file="/etc/hosts"
b=$(cat $file)
newContent="127.0.0.1 ${newHost}"$'\n'$b
sudo bash -c "echo '${newContent}' > $file"

#Add site in sites-available and add needed directives
sap=/etc/apache2/sites-available/$newHost.conf
sudo touch $sap
sudo chmod 777 $sap
directives="<VirtualHost *:80>
        ServerName ${newHost}
        DirectoryIndex app_dev.php
        DocumentRoot ${newPath}/web/
        ErrorLog /var/log/apache2/${newHost}.symfony.log
        <Directory ${newPath}/web/>
                Options Indexes FollowSymLinks MultiViews
                AllowOverride None
                Order allow,deny
                allow from all
                <IfModule mod_rewrite.c>
                        RewriteEngine On
                        RewriteCond %{REQUEST_FILENAME} !-f
                        RewriteRule ^(.*)$ /app_dev.php [QSA,L]
                </IfModule>
        </Directory>
</VirtualHost>"
echo "$directives">$sap

#Enable virtual host
sudo a2ensite $newHost.conf

#Enable rewrite
sudo a2enmod rewrite

#Restarting apache2
sudo /usr/sbin/apache2ctl restart
