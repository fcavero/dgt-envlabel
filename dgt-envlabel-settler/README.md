# DGT Environmental Label Settler

Este componente va consumiendo los mensajes que le indican qué fichero CSV tiene que procesar, realizando las inserciones de distitivos ambientales de vehículos.

## Estructura

```text
.
├── dgt-envlabel-settler
│   ├── app
│   └── docker
│       └── php
│           └── supervisor
└── tmp
    └── splits
```

Donde:

* *app* → Proyecto de *Symfony*.
* *docker* → Ficheros de *Docker* para construir el contenedor *PHP-FPM*, y todo lo que les cuelga.
* *tmp* → Volumen de trabajo con los ficheros que se monta en el contenedor.

## Despliegue

### Docker

Como es obvio, es posible usar los comandos de *Docker* a calzón quitao, pero para facilitar la cosa existe la posibilidad de usar un fichero `Makefile` al efecto:

```shell
❯ make
usage: make [target]

targets:
Makefile  help                       Show this help message
Makefile  build                      (Re)Builds the Settler container
Makefile  run                        Starts the Settler container
Makefile  stop                       Stops the Settler container
Makefile  terminate                  Stops the Settler container (with --remove-orphans option set)
Makefile  restart                    Restarts the Settler container
Makefile  code-style                 Runs php-cs to fix code styling following Symfony rules
Makefile  ssh-php                    SSHs into the Settler container as unprivileged user
Makefile  ssh-php-root               SSHs into the Settler container as root
Makefile  app-logs-dev               Tails the Symfony dev log
Makefile  app-logs-prod              Tails the Symfony prod log
Makefile  restart-supervisord        (Re)Starts supervisord
Makefile  update-worker              Prepares supervisord due to manage Symfony worker
Makefile  run-worker                 Runs Symfony worker via supervisord
Makefile  worker-status              Shows the supervisord managed Symfony worker status
Makefile  docker-log-php             Tails the Settler container Docker log
```

Podemos personalizar los argumentos de `docker compose` para adecuarlos a nuestras necesidades; dichos valores se indican en un fichero `.env` que se invoca desde el `Makefile`:

* `DOCKER_PHP` → Nombre del contenedor donde reside la aplicación (por defecto, `envlabel-api-php`).
* `DOCKER_XDEBUG_LOG_LEVEL`, `DOCKER_XDEBUG_HOST` y `DOCKER_XDEBUG_PORT` → Parámetros de configuración para [Xdebug 3.x](https://xdebug.org/) (por defecto, `0`, `172.17.0.1` y `9003`, respectivamente).

### Symfony

Las variables de entorno de *Symfony* deben ser revisadas en un `.env.local` remoto. Aparte de las clásicas cadenas de conexión para *Doctrine* y *Messenger*, destacamos las siguientes:

* `DATABASE_FAILED_MESSAGES_URL` → La cadena de conexión de la base de datos de mensajes fallidos, esto es, los mensajes que el manejador no ha sido capaz de recuperar.
* `ES_LICENSE_PLATE_REGEXP` → Expresión regular del formato de matrículas español `/[0-9]{4}[BCDFGHJKLMNPRSTVWXYZ]{3}/`

## Uso

La persistencia de los distintivos ambientales requiere que un *worker* de *supervisord* dispare el consumo de la cola de mensajes.

La configuración del *worker* por defecto limita el consumo a cinco mensajes cinco:

```ini
[program:csv-message-consume]
user=appuser
command=php /appdata/www/app/bin/console messenger:consume amqp_csv --limit=5 -vv
numprocs=1
startsecs=5
autostart=true
autorestart=true
startretries=10
process_name=%(program_name)s_%(process_num)02d
```

Cuando se han consumido dicho número de mensajes, el *worker* vuelve a lanzar el comando de *Messenger*, que volverá a procesar solo cinco mensajes. De esta manera, evitamos que el proceso acabe con la memoria disponible.

### Puesta en marcha del *worker*

*supervisord* y su *worker* no están arrancados por defecto, así que, una vez levantado el contenedor, hay que proceder de la siguiente manera:

1. Arrancamos *supervisord*:

    ```shell
    ❯ make restart-supervisor
    U_ID=1000 docker exec -it -u root 'envlabel-settler-php' /etc/init.d/supervisor stop
    Stopping supervisor: supervisord.
    U_ID=1000 docker exec -it -u root 'envlabel-settler-php' /etc/init.d/supervisor start
    Starting supervisor: supervisord.
    U_ID=1000 docker exec -it -u root 'envlabel-settler-php' /etc/init.d/supervisor status
    supervisord is running
    ```

2. Comprobamos si el *worker* también esta corriendo:

    ```shell
    ❯ make workers-status 
    U_ID=1000 docker exec -it -u root 'envlabel-settler-php' supervisorctl status
    csv-message-consume:csv-message-consume_00   RUNNING   pid 96, uptime 3 days, 6:38:51
    ```

Hay dos opciones más en el `Makefile` relacionadas con *supervisord*:

* `make update-worker` → Actualiza el *worker* si ha habido algún cambio en su configuraciones (`supervisorctl update`).
* `make run-worker` → Arranca el *worker* (`supervisorctl start`).

## Testing

→ TODO

## Licencia

DO WHAT THE FUCK YOU WANT TO PUBLIC LICENSE (WTFPL). Por favor, revisad el [fichero de licencia](./LICENSE) para más información.

## TODO

* Añadir los logs de *supervisord* como opción del `Makefile`.
