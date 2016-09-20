# -*- mode: ruby -*-
# vi: set ft=ruby :

# All Vagrant configuration is done below. The "2" in Vagrant.configure
# configures the configuration version (we support older styles for
# backwards compatibility). Please don't change it unless you know what
# you're doing.
Vagrant.configure(2) do |config|
  config.vm.box = "ubuntu/trusty64"
  config.vm.network "private_network", ip: "192.168.34.10"
  config.vm.synced_folder ".", "/home/vagrant/directus"
  config.vm.provision "shell", inline: <<-SHELL
    set -e
    apt update -y
    apt install -y git php5 php5-mcrypt
    php5enmod mcrypt
su vagrant <<NONROOT
  set -e
  cd directus
  curl -s https://getcomposer.org/installer | php
  php composer.phar install
NONROOT
  SHELL
end
