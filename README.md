# KnowleCloud
Store and share

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
* Проверяем доступен ли https://artifacts.elastic.co/GPG-KEY-elasticsearch. Если нет - включаем VPN
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
* (Пока нет - будет после загрузки файлов) Инициализируем индексы эластики `php yii elastica-init/init`

#### База данных

SQLite, живет в `knowlecloud.test/db/db.sqlite` 
Все обновления схемы устанавливаются через `php yii migrate`

##### SASS -> CSS

compass Готов к использованию после установки пакетов композером(Для упрощения пока не используется).

Запустить компиляцию css:

    $ compass compile

#### Тесты

**Для упрощения выпилены**