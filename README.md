# Music Stat

Этот репозиторий содержит сайт "Music Stat".

# Инициализация приложения

Склонируйте репозиторий на локальную машину:

``` bash
git clone https://github.com/DaniilVarezkin/MusicStat.git
```

Перейдите в папку проекта:

``` bash
cd MusicStat
```

Установите все зависимости проекта:

``` bash
composer install 
```

Для работы с базой данных выполните миграции:

``` bash
php bin/console doctrine:migrations:migrate
```

Для локального тестирования запустите сервер Symfony:

``` bash
symfony server:start --port=8080
```

Убедитесь, что приложение доступно по адресу [http://localhost:8080](http://localhost:8080]).
