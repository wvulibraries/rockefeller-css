#!/bin/bash

#title           :init.sh
#description     :This script will install laravel on PHP 7
#author		       :Ajay Krishna Teja Kavuri
#date            :20161014
#version         :0.1
#==============================================================================

# Remove if something already exists
rm -rf /var/www/html/css

# Change the directory
cd /home/vagrant

# Set up laravel installer
composer global require "laravel/installer"

# Set the path variable
export PATH=~/.config/composer/vendor/bin:$PATH

#create a sample laravel project
laravel new css

#Move into Apache
sudo mv /home/vagrant/css /var/www/html

# Set the configurations
chmod 775 /var/www/html/css/storage
sudo chown -R apache:apache /var/www/html/css
sudo chmod 755 /var/www
sudo rm -R /etc/httpd/conf/httpd.conf
sudo ln -s /vagrant/serverConfiguration/httpd.conf /etc/httpd/conf
sudo systemctl restart httpd

#Set up MYSQL Stuff
# mysql -u root < /vagrant/sqlFiles/setup.sql