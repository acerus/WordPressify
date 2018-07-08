# WordPressify

Сайт (https://wordpressify.ru/), который объединяет русскоязычное сообщество WordPress. Позволяет Заказчикам найти Специалистов и Команды. А Специалистам найти интересные Проекты и Продукты для участия в их Разработке.

# Разработка

## Требования

* [VirtualBox](https://www.virtualbox.org/)
* [Vagrant](https://www.vagrantup.com/)
* [Vagrant::Hostsupdater](https://github.com/cogitatio/vagrant-hostsupdater)

_Note: Vagrant::Hostsupdater is optional to automatically add the entry to the hosts file. If you skip that, you will need to manually edit the hosts file and add the related entry yourself._

## Использование

1. Сделайте форк
2. Далее склонируйте репо к себе на компьютер
3. Выполните команду `vagrant up`
4. Запустите сайт по адресу http://dev.wordpressify.ru (это локальная копия для разработки)
0. Если будут проблемы, пишите сюда https://github.com/acerus/WordPressify/issues


All Vagrant commands like `vagrant halt`, `vagrant destroy` and `vagrant suspend` are applicable.

## Credentials

MySQL root:

**User**: `root`
**Password**: `password`

Additional MySQL access:

**User**: `vagrant`
**Password**: `password`
**Database**: `vagrant`

## What's Included?

* [Ubuntu 16.04](http://www.ubuntu.com/)
* [nginx (mainline)](http://nginx.org/)
* [php-fpm 7.1.x](http://php-fpm.org/)
* [MariaDB 10.1.x](https://mariadb.org/)
* [phpMyAdmin](https://www.phpmyadmin.net/)
* [Git](https://git-scm.com/)
* [Subversion](https://subversion.apache.org/)
* [Composer](https://getcomposer.org/)
* [Node.js](https://nodejs.org/)
* [WP-CLI](http://wp-cli.org/)
* [MailHog](https://github.com/mailhog/MailHog)

## Directory Structure

* `config` - Contains all services related configuration, please modify it accordingly to your usage.
* `logs` - Contains all the logs generated from nginx as well as PHP errors.
* `www` - The web root of your web application.

## Инструменты

* http://mail.dev.wordpressify.ru - отладка эл почты
* http://db.dev.wordpressify.ru - работа с БД

## Credits

[Vagrant LEMP](https://github.com/uptimizt/vagrant-lemp) awesome Vagrant setup.
