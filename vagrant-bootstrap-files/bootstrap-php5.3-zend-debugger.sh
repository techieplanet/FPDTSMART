# add package index for git
rpm -Uvh http://dl.fedoraproject.org/pub/epel/5/i386/epel-release-5-4.noarch.rpm

# add package index for php 5.3
rpm -Uvh http://mirror.webtatic.com/yum/centos/5/latest.rpm

# update base packages
yum update -y

# install our custom packages
yum install nano mysql-server git-core yum-versionlock -y
yum --enablerepo=webtatic install php php-mysql php-devel -y

# install the zend debugger for php 5.3 from http://www.zend.com/en/download/534?start=true
mv /home/vagrant/ZendDebugger.so /usr/lib64/php/modules/ZendDebugger.so
ln -s /lib64/libssl.so.0.9.8e /lib64/libssl.so.0.9.8
ln -s /lib64/libcrypto.so.0.9.8e /lib64/libcrypto.so.0.9.8
/sbin/ldconfig

mv /home/vagrant/php-debugger.ini /etc/php.d/php-debugger.ini

# set up apache to point to shared /vagrant folder and start it
mv /home/vagrant/vagrant-trainsmart-httpd.conf /etc/httpd/conf.d/vagrant-trainsmart-httpd.conf
chkconfig httpd on
service httpd start

# start up mysql, import data, grant remote access
chkconfig mysqld on
service mysqld start
mysql -u root </home/vagrant/grant-privileges.sql
mysql -u root </home/vagrant/data.sql
rm /home/vagrant/data.sql
service mysqld restart


# let's make it so we can look at the log files without being root
chmod -R a+rX /var/log


