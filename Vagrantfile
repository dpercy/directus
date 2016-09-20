# -*- mode: ruby -*-
# vi: set ft=ruby :

Vagrant.configure(2) do |config|
  config.vm.box = "ubuntu/trusty64"
  config.vm.network "private_network", ip: "192.168.34.10"
  config.vm.synced_folder ".", "/home/vagrant/directus"
  config.vm.provision "shell", inline: <<-SHELL
    set -e
    apt update -y
    apt install -y \
        git \
        php5 php5-mcrypt php5-gd php5-mysql \
        apache2 \
        mysql-client mysql-server \
        imagemagick
    php5enmod mcrypt
su vagrant <<NONROOT
  set -e
  cd directus
  curl -s https://getcomposer.org/installer | php
  php composer.phar install
NONROOT
    rm -rf /var/www/html
    ln -s /home/vagrant/directus /var/www/html
    service apache2 restart
  SHELL
end
