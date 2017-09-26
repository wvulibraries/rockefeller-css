#!/bin/bash

#title           :bootstrap.sh
#description     :This script will install LAMP stack and Composer on centos 7.2
#author		       :Ajay Krishna Teja Kavuri
#date            :20161014
#updated by      :Tracy A McCormick
#date            :20170926
#version         :0.4
#==============================================================================

#Formal update for no reason
yum -y update

#Setup Yum messages
rpm -Uvh http://repo.mysql.com/mysql-community-release-el7-5.noarch.rpm
rpm -Uvh https://dl.fedoraproject.org/pub/epel/epel-release-latest-7.noarch.rpm
rpm -Uvh https://mirror.webtatic.com/yum/el7/webtatic-release.rpm
yum -y update
echo -e "----Added RPM's----\n\n"

# Install apache
yum -y install httpd httpd-devel httpd-manual httpd-tools
echo -e "----Installed Apache----\n\n"

# Install MySQL
yum -y install mysql-connector-java mysql-connector-odbc mysql-devel mysql-lib mysql-server
echo -e "----Installed MySQL----\n\n"

# Install MySQL mods
yum -y install mod_auth_kerb mod_auth_mysql mod_authz_ldap mod_evasive mod_perl mod_security mod_ssl mod_wsgi
echo -e "----Installed Auth Plugins for MySQL----\n\n"

# Install PHP 7
yum -y install php70w php70w-bcmath php70w-cli php70w-common php70w-gd php70w-ldap php70w-mbstring php70w-mcrypt php70w-mysql php70w-odbc php70w-pdo php70w-pear php70w-pear-Benchmark php70w-pecl-apc php70w-pecl-imagick php70w-pecl-memcache php70w-soap php70w-xml php70w-xmlrpc
echo -e "----Installed PHP 7----\n\n"

# Install Xdebug
yum -y install php70w-pecl-xdebug.x86_64
echo -e "----Installed Xdebug----\n\n"

# remove httpd.conf and link our custom file if a custom vagrant_httpd.conf if it exists
if [ -e /vagrant/serverConfiguration/vagrant_httpd.conf ]
then
   rm -f /etc/httpd/conf/httpd.conf
   ln -s /vagrant/serverConfiguration/vagrant_httpd.conf /etc/httpd/conf/httpd.conf
fi

# remove php.ini and link our custom file if a custom php.ini if it exists
if [ -e /vagrant/serverConfiguration/php.ini ]
then
   rm -f /etc/php.ini
   ln -s /vagrant/serverConfiguration/php.ini /etc/php.ini
fi

# remove mod_security.conf and link our custom file
if [ -e /vagrant/serverConfiguration/mod_security.conf ]
then
   rm -f /etc/httpd/conf.d/mod_security.conf
   ln -s /vagrant/serverConfiguration/mod_security.conf /etc/httpd/conf.d/mod_security.conf
fi

# create access log
touch /vagrant/serverConfiguration/logs/access_log

# create error log
touch /vagrant/serverConfiguration/logs/error_log

# Start and set apache
sudo systemctl start httpd
sudo systemctl enable httpd
echo -e "----Started Apache----\n\n"

# Start and set MySQl
sudo systemctl start mysqld
sudo systemctl enable mysqld
echo -e "----Started MySQL----\n\n"

# Install Node and gulp
rpm -ivh https://kojipkgs.fedoraproject.org//packages/http-parser/2.7.1/3.el7/x86_64/http-parser-2.7.1-3.el7.x86_64.rpm
yum install -y gcc-c++ make
curl -sL https://rpm.nodesource.com/setup_8.x | bash -
yum install -y nodejs
npm install --global gulp-cli
echo -e "----Installed Node and Gulp----\n\n"

yum -y install libnotify
echo -e "----Installed libnotify----\n\n"

# Install git
yum -y install git
echo -e "----Installed Git----\n\n"

# Install composer
curl -sS https://getcomposer.org/installer | php
sudo chmod +x composer.phar
mv composer.phar /usr/bin/composer
echo -e "----Installed composer----\n\n"
