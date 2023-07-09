# REST API-Documentation

[V5 Documentation](/docs/v5/DOCS.md)

[V4 & V5 List of Endpoints](https://github.com/AlphaSoundZ/apollo/issues/4)

---

V3 Api documentation (incomplete and may have some errors):
https://documenter.getpostman.com/view/20621332/Uz59MyeK

## Setup

- Install PHP (8.0 works)
- Install MySql server (MariaDB for example)
- Install composer
- Navigate into ```/api/v5``` and run
```console
composer install
```
- configure .env

## Run
```console
php -S localhost:8080
```

Start MySql Server

Open in your browser to check if the API is running:
[http://localhost:8080/api/v5](http://localhost:8080/api/v5)

The response should look something like:
```json
{
  "status":"API_RUNNING",
  "message":"API ist aktiv",
  "code":200,
  "version":"v5",
  "timestamp":1688850752,
  "request":"/api/v5",
  "method":"GET"
}
```
