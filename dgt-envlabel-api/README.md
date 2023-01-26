# DGT Environmental Label API

Este componente tiene dos partes bien diferenciadas:

* Una API para obtener los datos de distintivos ambientales de vehículos.
* Un conjunto de [comandos de Symfony](https://symfony.com/doc/5.4/console.html), encargados de descargar el fichero de datos y procesarlo, y así poder servir los datos del punto anterior.

## Estructura

```text
├── dgt-envlabel-api
│   ├── api
│   └── docker
│       ├── nginx
│       └── php
│           └── supervisor
└── tmp
    └── splits
```

Donde:

* *api* → Proyecto de *Symfony*.
* *docker* → Ficheros de *Docker* para construir los contenedores de *Nginx* y *PHP-FPM*, y todo lo que les cuelga.
* *tmp* → Volumen de trabajo con los ficheros que se monta en el contenedor *PHP-FPM*.

## Despliegue

### Docker

Como es obvio, es posible usar los comandos de *Docker* a calzón quitao, pero para facilitar la cosa existe la posibilidad de usar un fichero `Makefile` al efecto:

```shell
❯ make
usage: make [target]

targets:
Makefile  help                      Show this help message
Makefile  build                     (Re)Builds all the containers
Makefile  run                       Starts the containers
Makefile  stop                      Stops the containers
Makefile  terminate                 Stops the containers (with --remove-orphans option set)
Makefile  restart                   Restarts the containers
Makefile  code-style                Runs php-cs to fix code styling following Symfony rules
Makefile  ssh-php                   SSHs into the PHP container as unprivileged user
Makefile  ssh-web                   SSHs into the web server container as unprivileged user
Makefile  ssh-php-root              SSHs into the PHP container as root
Makefile  ssh-web-root              SSHs into the web server container as root
Makefile  api-logs-dev              Tails the Symfony dev log
Makefile  api-logs-prod             Tails the Symfony prod log
Makefile  restart-supervisord       (Re)Starts Supervisor demon
Makefile  update-worker             Prepares Supervisor demon due to manage Symfony worker
Makefile  run-worker                Runs Symfony worker via supervisor demon
Makefile  workers-status            Shows the Supervisor demon managed Symfony workers status
Makefile  docker-log-php            Tails the PHP container log
Makefile  docker-log-web            Tails the WEB container log
```

Podemos personalizar los argumentos de `docker compose` para adecuarlos a nuestras necesidades; dichos valores se indican en un fichero `.env` que se invoca desde el `Makefile`:

* `DOCKER_PHP` → Nombre del contenedor donde reside la aplicación (por defecto, `envlabel-api-php`).
* `DOCKER_XDEBUG_LOG_LEVEL`, `DOCKER_XDEBUG_HOST` y `DOCKER_XDEBUG_PORT` → Parámetros de configuración para [Xdebug 3.x](https://xdebug.org/) (por defecto, `0`, `172.17.0.1` y `9003`, respectivamente).
* `DOCKER_WEB`, `DOCKER_WEB_PORT` y `DOCKER_WEB_TLS_PORT` → Nombre del contenedor de *Nginx* y puertos que expone (por defecto, `envlabel-api-web`, `250` y `8443`, respectivamente).
* `DOCKER_DB` y `DOCKER_DB` → Nombre del contenedor de *PostgreSQL* y puerto que expone (por defecto, `envlabel-api-db` y `5432`, respectivamente).
* `DOCKER_DB_NAME` → Nombre de la base de datos de distintivos ambientales (por defecto, `envlabel`).
* `DOCKER_DB_USER` y `DOCKER_DB_PWD` → Credenciales de la base de datos de distintivos ambientales (por defecto, `elabelu` y `ulebale`, respectivamente).

Las modificaciones en algunos parámetros pueden conllevar actualizaciones en cascada de variables de entorno de *Symfony*, por ejemplo, los obligatorios cambios en las credenciales de la base de datos.

También es perfectamente posible «capar» el fichero `docker-compose.yml` para levantar únicamente los contenedores deseados, por ejemplo, en un contexto en el que la base de datos se encuentra en otra máquina.

### Symfony

Las variables de entorno de *Symfony* deben ser revisadas en un `.env.local` remoto. Aparte de las clásicas cadenas de conexión para *Doctrine* y *Messenger*, destacamos las siguientes:

* `TMP_STORAGE_DIR` → Directorio de trabajo de los ficheros descargados; debe ser coherente con los volúmenes de *Docker* que se montan en los contenedores de *Symfony*, pues ambos operan sobre este directorio.
* `DGT_ENVIRONMENTAL_LABELS_URL` → URL de descarga oficial del fichero de distintivos ambientales (por defecto `https://sedeapl.dgt.gob.es/IEST_INTER/MICRODATOS/salida/distintivoAmbiental/`).
* `DGT_ENVIRONMENTAL_LABELS_ZIP_FILE` → Nombre del fichero a descargar (por defecto, `export_dist_ambiental.zip`).
* `DGT_ENVIRONMENTAL_LABELS_CSV_FILE` → Nombre del fichero CSV una vez descomprimido (por defect, `export_distintivo_ambiental.txt`).
* `SPLIT_COMMAND_LINES_ARG` → Número de líneas que debe emplear el comando `split` para fragmentar el CSV de distintivos ambientales (por defecto, `20 k).
* `SPLIT_COMMAND_STORAGE_DIR` → Directorio en el que el comando `split` deposita los *n* fragmentos resultantes de trocear el CSV de distintivos ambientales; para el correcto funcionamiento del comando de persistencia de datos, debe estar en consonancia con `TMP_STORAGE_DIR`.

## Uso

### API de distintivos ambientales

Esta API no prevee ningún tipo de autenticación, y cuenta con unos *EndPoints* muy básicos, todos bajo el verbo `GET`, pues las altas se realizan en procesos por lotes, y no hay actualizaciones ni borrados.

La documentación de la API está disponible en `/v1/docs`, siguiendo la [especificación OpenAPI de Swagger](https://swagger.io/specification/):

![Swagger OpenAPI](./swagger-openapi.png?raw=true "API docs via Swagger OpenAPI")

#### Dame los tipos de distintivos ambientales

`GET /v1/labels`

```json
[
    {
        "id": 1,
        "tag": "16T0",
        "description": "CERO EMISIONES"
    },
    {
        "id": 2,
        "tag": "16TE",
        "description": "ECO"
    },
    {
        "id": 3,
        "tag": "16TC",
        "description": "C"
    },
    {
        "id": 4,
        "tag": "16TB",
        "description": "B"
    },
    {
        "id": 5,
        "tag": "16M0",
        "description": "CERO EMISIONES"
    },
    {
        "id": 6,
        "tag": "16ME",
        "description": "ECO"
    },
    {
        "id": 7,
        "tag": "16MC",
        "description": "C"
    },
    {
        "id": 8,
        "tag": "16MB",
        "description": "B"
    },
    {
        "id": 9,
        "tag": "SIN DISTINTIVO",
        "description": "SIN DISTINTIVO"
    }
]
```

#### Toma esta matrícula; dame su distintivo ambiental más reciente

`GET /v1/vehicles/latest/0000KKK`

```json
{
    "id": "72dd88f8-7ffb-4482-82f8-f5d785257fd1",
    "plate": "000KKK",
    "createdAt": "2023-01-20T09:31:43+01:00",
    "label": {
        "id": 6,
        "tag": "16ME",
        "description": "ECO"
    }
}
```

Atención: el miembro «createdAt» muestra el *timestamp* de registro en el sistema, no al de definición del distintivo en la DGT. La DGT solo nos ofrece la matrícula y el valor del distintivo en el momento en el que generan el fichero, y un fichero puede haber sido generado y subido varios días antes de ser descargado y procesado.

#### Toma esta matrícula; dame todos sus distintivos ambientales que tengas registrados

`GET /v1/vehicles/all/0000BBB`

```json
{
    "plate": "0000BBB",
    "labels": [
        {
            "createdAt": "2023-01-20T15:07:32+01:00",
            "label": {
                "id": 2,
                "tag": "16TE",
                "description": "ECO"
            }
        },
        {
            "createdAt": "2022-12-08T08:04:40+01:00",
            "label": {
                "id": 8,
                "tag": "16MB",
                "description": "B"
            }
        }
    ]
}
```

Si la matrícula solo tiene un distintivo, la respuesta es similar a la del *EndPoint*  anterior. Con más de un distintivo, los bloques se ordenarán por la fecha de registro *(createdAt)*, del más reciente al más antiguo.

#### Dime qué ficheros se han procesado

`GET /v1/downloaded_file_logs`

```json
[
    {
        "id": "246d767d-765c-4d50-b652-5fcfb9d8de63",
        "createdAt": "2023-01-20T08:02:54+01:00",
        "fileHash": "e8458fa1ac87dd741a488fa3219dcfc7"
    },
    {
        "id": "b976b852-65b0-4f21-bb0b-70f439da4540",
        "createdAt": "2023-01-20T07:44:52+01:00",
        "fileHash": "e8458fa1ac87dd741a488fa3219dcfc1"
    }
]
```

Es posible paginar y establecer el número de elementos por página, con `page` e `itemsPerPage`, respectivamente.

`GET /v1/downloaded_file_logs?itemsPerPage=1&page=2`

```json
[
    {
        "id": "b976b852-65b0-4f21-bb0b-70f439da4540",
        "createdAt": "2023-01-20T07:44:52+01:00",
        "fileHash": "e8458fa1ac87dd741a488fa3219dcfc1"
    }
]
```

### Carga de datos

La carga de datos requiere tres comandos de *Symfony*, en este componente, y un *listener* en el componente [dgt-envlabel-setter](../dgt-envlabel-settler/README.md), que es el que lleva a cabo la persistencia de los datos.

Los comandos son accesibles desde la consola de *Symfony* (`sf` es un alias de `/bin/console` ):

```text
❯ make ssh-php
U_ID=1000 docker exec -it -u 1000 'envlabel-api-php' bash
appuser@11946118b407:/appdata/www$ 
appuser@11946118b407:/appdata/www/api$ sf | grep app:
  app:download-environmental-labels-file      [app:download-file] Gets the complete official file of vehicles' environmental labels.
  app:process-environmental-label-file        [app:process-file] Processes the huge file of environmental labels.
  app:send-environmental-labels-csv-messages  [app:send-csv-msg] Sends one message per each CSV chopped file to RabbitMQ.
appuser@11946118b407:/appdata/www$ 
```

Donde:

* `download_dgt_envlabel_file_command` → Realiza la descarga del fichero comprimido del portal oficial, cuya URL y nombre de fichero se encentran en sendas variables de entorno. La descarga correcta del fichero provoca la invocación del siguiente comando.
* `process-environmental-label-file` → Se encarga de descomprimir y troquelar el fichero CSV, de acuerdo al número de líneas indicado en la correspondiente variable de entorno. El procesado exitoso del fichero provoca la invocacón del siguiente comando.
* `send-environmental-labels-csv-messages` → Envía un mensaje a *RabbitMQ* por cada uno de los trozos de fichero CSV, indicando la ruta completa en la que se encuentra.

El proceso completo de carga de datos puede desencadenarse de forma manual, pero lo ideal es que sea desatendido, y para ello se ha instalado [supervisord](http://supervisord.org/) para controlar el proceso.

Cuenta la leyenda que la DGT sube una nueva versión del fichero de distintivos cada semana, por lo que en la configuración del *worker* de *supervisord* se define dicha periodicidad *(sleep 7d)*:

```ini
[program:download-labels-file-command]
command=/bin/bash -c 'while true; do /appdata/www/api/bin/download_dgt_envlabel_file_command; sleep 7d; done'
user=root
numprocs=1
startsecs=0
autostart=true
autorestart=true
process_name=%(program_name)s_%(process_num)02d
```

#### Puesta en marcha del proceso desatendido

*supervisord* y su *worker* no están arrancados por defecto, así que, una vez levantado el contenedor, hay que proceder de la siguiente manera:

1. Arrancamos *supervisord*:

    ```shell
    ❯ make restart-supervisor
    U_ID=1000 docker exec -it -u root 'envlabel-api-php' /etc/init.d/supervisor stop
    Stopping supervisor: supervisord.
    U_ID=1000 docker exec -it -u root 'envlabel-api-php' /etc/init.d/supervisor start
    Starting supervisor: supervisord.
    U_ID=1000 docker exec -it -u root 'envlabel-api-php' /etc/init.d/supervisor status
    supervisord is running
    ```

2. Comprobamos si el *worker* también esta corriendo:

    ```shell
    ❯ make workers-status 
    U_ID=1000 docker exec -it -u root 'envlabel-api-php' supervisorctl status
    download-labels-file-command:download-labels-file-command_00   RUNNING   pid 142 uptime 0:00:53
    ```

Hay dos opciones más en el `Makefile` relacionadas con *supervisord*:

* `make update-worker` → Actualiza el *worker* si ha habido algún cambio en su configuraciones (`supervisorctl update`).
* `make run-worker` → Arranca el *worker* (`supervisorctl start`).

#### Prevención contra ficheros ya procesados

El proceso de carga es muy costoso en tiempo, por lo que sería un desperdicio completo procesar un fichero que la DGT no ha actualizado en el plazo pertinente. La forma de evitarlo es ir registrando un sencillo *hash* [md5](https://en.wikipedia.org/wiki/MD5) del fichero, una vez cargados los datos.

Así, una vez descargado un nuevo fichero comprimido, se calcula un de nuevo el *hash* y se comprueba que es diferente al del último fichero procesado exitosamente, abortándose el proceso si se trata del mismo valor.

## Testing

→ TODO

## Licencia

DO WHAT THE FUCK YOU WANT TO PUBLIC LICENSE (WTFPL). Por favor, revisad el [fichero de licencia](./LICENSE) para más información.

## TODO

* Añadir otro *EndPoint* para poder recuperar el último fichero procesado con éxito.
* Añadir filtros de fecha.
* Añadir los logs de *supervisord* como opción del `Makefile`.
