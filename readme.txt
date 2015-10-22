####################
SETUP MANUAL
####################


Recommended environment (tested): Ubuntu 12.04, PHP 5.5.5 (self compiled with pthreads for multi-core readiness)


1. Step: Install required dependencies

apt-get install \
libxml2 \
libxml2-dev \
libssl-dev \
pkg-config \
curl \
libcurl4-nss-dev \
enchant \
libenchant-dev \
libjpeg8 \
libjpeg8-dev \
libpng12-0 \
libpng12-dev \
libvpx1 \
libvpx-dev \
libfreetype6 \
libfreetype6-dev \
libt1-5 \
libt1-dev \
libgmp10 \
libgmp-dev \
libicu48 \
libicu-dev \
mcrypt \
libmcrypt4 \
libmcrypt-dev \
libpspell-dev \
libedit2 \
libedit-dev \
libsnmp15 \
libsnmp-dev \
libxslt1.1 \
libxslt1-dev \
checkinstall \
autoconf \
subversion \
php-pear \
wordnet \
curl \
libcurl4-openssl-dev \
php5-curl \
php5-cli

2. Prepare, Compile, Install PHP 5.5.5 with pthreads

### look for the correct link at http://php.net/downloads.php
wget http://us3.php.net/get/php-5.5.5.tar.bz2/from/de3.php.net/mirror -O php.tar.bz2
tar xvjf php.tar.bz2

cd php-5.5.5
./configure --enable-debug --enable-maintainer-zts --enable-pthreads --with-curl --with-zlib-dir --with-gd --enable-zip --with-bz2 --with-jpeg-dir=/usr --with-jpeg-dir=/usr --enable-calendar --enable-calendar
make
checkinstall

cp php.ini-production /usr/local/lib/php.ini
pear config-set php_ini /usr/local/lib/php.ini
pecl config-set php_ini /usr/local/lib/php.ini
pecl config-set bin_dir /usr/local/bin/
pecl install pthreads
pecl install zip
### update extension path in php.ini to somethink like /usr/local/lib/php/extensions/debug-zts-20121212/ (you need to detect the correct directory name under extensions)
### php.ini for apache is: /etc/php5/apache2/php.ini
### php.ini for cli is: /usr/local/lib/php.ini

3. Step: Install apache
apt-get install apache2
apt-get install libapache2-mod-php5
# it might be meaningful to apadt the max_execution_time, max_input_time and allowed_memory 
# to your preference (depending on the task, that is very important!
/etc/init.d/apache2 restart

4. Step: Install mysql-server
apt-get install mysql-server
apt-get install php5-mysql
apt-get install curl libcurl4-openssl-dev php5-curl php5-cli
/etc/init.d/apache2 restart

4.1. Install PHPMyAdmin
sudo apt-get install phpmyadmin
# If you're using Ubuntu 7.10 (Gutsy) or later select Apache2 from the "Configuring phpmyadmin" dialog box. 
# PHPMyAdmin is now available at: http://yourdomain/phpmyadmin

5. Step: Copy the files to the webdir
svn co https://github.com/tomson2001/refmodmine/trunk /var/www
chown -R www-data:www-data /var/www
chmod -R 777 /var/www/repository
chmod -R 777 /var/www/workspace
chmod -R 777 /var/www/files
chmod -R 777 /var/www/log


6. Step: Inject RefMod-Miner(Java-Version)

# Install Java
apt-get install python-software-properties
add-apt-repository ppa:webupd8team/java
apt-get update
apt-get install oracle-java8-installer

# Get RefMod-Miner (Java)
# copy RefMod-Miner (Java) jar and ressources to /var/www/lib/refmod-miner
# rename the executable jar file to master.jar

# DEPENDENCIES
sudo apt-get install r-base r-base-dev 

#Configure RefMod-Miner (JAVA)
cd /var/www/lib/refmod-miner
java -jar master.jar CREATECONFIG