# EPG API

## Requirements

To be able to install and the application you will need : 

* (Docker)[https://docs.docker.com/engine/install/debian/]
* (Docker Compose)[https://docs.docker.com/compose/install/]

## Installation

Start the application by running :

```bash
docker compose up
```

The application requires some dependencies. `Composer` is used as package manager.

Install dependencies with composer install command.

If you don't have php/composer installed on you host machine; you can run it into a docker :

```bash
docker compose exec -it debug sh -c "composer install"
```

## Usage

Verify if it is working by running

```bash
curl -X GET --location "http://localhost:8080"
```

you should receive the following response :

```json
{
  "name": "epg-api",
  "version": "dev"
}
```

To run the tests :

```bash
docker compose run test
```
