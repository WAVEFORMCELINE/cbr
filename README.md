# Currency Rates Widget

Это приложение представляет собой виджет курсов валют, который получает данные от ЦБ РФ. Бэкенд реализован на Symfony, фронтенд - на нативном JavaScript. Все упаковано в Docker.

## Установка и запуск 

1.  **Клонируйте репозиторий:**
    git clone 
    cd currency-app


2.  **Сборка и запуск контейнеров Docker:** 
    docker-compose up --build -d

3.  **Установка зависимостей Symfony:**
    docker-compose exec php composer install


4.  **Настройка базы данных и применение миграций:**
    Файл `.env` уже настроен для работы с Docker. Чтобы создать таблицы в базе данных, выполните:

    docker-compose exec php bin/console doctrine:migrations:migrate


5.  **Первичное получение курсов валют:**
    Чтобы заполнить базу данных актуальными курсами, выполните команду:

    docker-compose exec php bin/console app:fetch-rates

6.  **Готово!**
    * Приложение (виджет) будет доступно по адресу `http://localhost:8000`.
    * API доступно по адресу `http://localhost:8000/api/rates`.


**Пример ответа:**
[
    {
        "char_code": "USD",
        "name": "Доллар США",
        "rate": 91.5801,
        "diff": 0.2464
    },
    {
        "char_code": "EUR",
        "name": "Евро",
        "rate": 98.4443,
        "diff": -0.3117
    }
]
