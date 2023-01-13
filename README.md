# KnowleCloud

Store and share

## Public knowlecloud.ru

Заведены ssh пользователи **matvei** и **arsenii**, с возможностью `sudo su www-data`

Сайт knowlecloud.ru смотрит на ветку main. Для обновления нужно сделать:

    cd /var/www/knowlecloud.ru
    sudo su www-data
    git pull

Использование `git status`, `composer install --no-dev`, `php yii migrate`, итд - по необходимости.

Для общего понимания: директорию /var/www/knowlecloud.ru может править только пользователь www-data. От его имени совершаются все действия с загружаемыми
файлами, бд, и всем на сайте

## Создание _dev_ пошагово

### Подготовка

Должны быть установлены:

* Virtualbox 5
* Vagrant 2
* git client
* ssh client
* VPN с эндпоинтом вне РФ(обход санкций)

### Vagrant-виртуалка

* Клонируем репозитарий на хост-машине `git clone git@github.com:xclassicx/KnowleCloud.git`
* Кладем в корень клонированного проекта скрипты из архива `_pt-slug.zip`
* При первом запуске `vagrant up` в любом случае включаем VPN - нужно для скачивания бокса и плагинов
* Устанавливаем плагин для NFS `vagrant plugin install vagrant-winnfsd`(Если его нет в списке `vagrant plugin list`)
* В корне клонированного проекта запускаем установку виртуалки `vagrant up`
* Как закончит - выключаем(перезагружаем) виртуалку `vagrant halt`
* Если был включен VPN - можно отключать
* Добавляем в hosts на хост-машине `192.168.56.99  knowlecloud.test`
* Опять запускаем виртуалку `vagrant up`
* Можно проверить в браузере http://knowlecloud.test/ - Должно ответить, но с ошибкой отсутствующих зависимостей
* Авторизуемся по SSH на `vagrant@knowlecloud.test` с паролем **vagrant**
* Переходим в каталог `cd /var/www/knowlecloud.test`
* Устанавливаем пакеты `composer install --prefer-dist`
* Инициализируем SQLite `php yii migrate`
* Инициализируем индексы эластики `php yii elastica/run`

#### База данных

SQLite, живет в `knowlecloud.test/db/db.sqlite`
Все обновления схемы устанавливаются через `php yii migrate`

#### Elasticsearch

Тк. Офицальный репозитарий недоступен с Ру ip и VPN помогает слабо, в виртуалку Elasticsearch устанавливаем из скачанного пакета -
см. `_pt-slug.zip\data\elasticsearch-8.1.1-amd64.deb`

Краткий перечень полезных команд для Elasticsearch:

    curl 'localhost:9200/' - информация о установленом Elasticsearch
    curl 'localhost:9200/_cat/indices?v' - информация о созданых индексах
    curl -XGET 'localhost:9200/knowlecloud.document.dev/_mapping?pretty' - информация о меппингах в указаном индексе
    curl -XPOST 'localhost:9200/knowlecloud.document.dev/_search?pretty' - 10 последних записйе в указанном индексе
    curl -XDELETE 'localhost:9200/knowlecloud.document.dev/' - удаление индекса

##### SASS -> CSS

compass Готов к использованию после установки пакетов композером(Для упрощения пока не используется).

Запустить компиляцию css:

    $ compass compile

#### Тесты

**Для упрощения выпилены**