# RabbitMQ en Docker

Levanta un contenedor *Docker* con una instancia de [RabbitMQ](https://www.rabbitmq.com/) de manera rápida, fácil y para toda la familia.

La imagen empleada por defecto es `rabbitmq:3-management`, que trae de serie el *plugin Management*, que facilita mucho la administración a través de un navegador web.

## Despliegue

Como es obvio, es posible usar los comandos de *Docker* a calzón quitao, pero para facilitar la cosa existe la posibilidad de usar un fichero `Makefile` al efecto:

```shell
❯ make
usage: make [target]

targets:
Makefile  help                   Show this help message
Makefile  run                    Start the RabbitMQ container
Makefile  stop                   Stop the RabbitMQ container
Makefile  restart                Restart the RabbitMQ container
Makefile  ssh-rabbit             SSH into the RabbitMQ container
Makefile  ssh-rabbit-root        SSH into the RabbitMQ container as root
Makefile  logs-rabbit            Show logs of the RabbitMQ container (with --follow option set)
```

Podemos personalizar los argumentos de `docker run` para adecuarlos a nuestras necesidades; dichos valores se indican en un fichero `.env` que se invoca desde el `Makefile`:

* `RABBIT_NAME` → Nombre del contenedor (por defecto, `rabbitmq`).
* `RABBIT_PORT` → Puerto de la instancia de *RabbitMQ* (por defecto, `5672`).
* `RABBIT_MGMT_PORT` → Puerto usado por el *plugin Management* (por defecto, `15672`).

Es posible, además, habilitar más *plugins* de *RabbitMQ* en la construcción del contenedor. Para ello, crearemos un fichero `plugins_enabled` con una lista simple de los que necesitamos; sí, el punto del final debe mantenerse:

```erlang
[rabbitmq_shovel,rabbitmq_shovel_management,rabbitmq_management,rabbitmq_stomp].
```

Con todo preparado, podremos lanzar la creación del contenedor:

```shell
❯ make run
docker rm 'rabbitmq' || true
Error: No such container: rabbitmq
docker run -d \
        --hostname 'rabbitmq' \
        --name 'rabbitmq' \
        -p 5672:5672 \
        -p 15672:15672 \
        -v `pwd`/'rabbitmq'-mnesia:/var/lib/rabbitmq/mnesia/rabbit@'rabbitmq' \
        --mount type=bind,source=`pwd`/enabled_plugins,target=/etc/rabbitmq/enabled_plugins \
        --restart=on-failure \
        rabbitmq:'3-management'
Unable to find image 'rabbitmq:3-management' locally
3-management-alpine: Pulling from library/rabbitmq
213ec9aee27d: Pull complete 
6ceb39faa1e0: Pull complete 
51364b0a48f4: Pull complete 
f6345f7f0557: Pull complete 
c735ccc70ee5: Pull complete 
f76e78226ad4: Pull complete 
1685526f1a4f: Pull complete 
e4f48928d6c0: Pull complete 
1533d9991711: Pull complete 
8ade942fbaa8: Pull complete 
Digest: sha256:f3c2135623f691218f70baffd9118c5b4b1ad57564f6bed5616d550ecc66bae7
Status: Downloaded newer image for rabbitmq:3-management
8497c9dfe4f2edf4165034dcc2347f2ee587f8ad6a184ffddc33456dbe1f5294
```

Vamos echando un ojo al proceso de creación y arranque:

```shell
❯ make logs-rabbit
docker logs --follow 'rabbitmq'
2022-10-03 14:48:45.864271+00:00 [info] <0.221.0> Feature flags: list of feature flags found:
2022-10-03 14:48:45.869443+00:00 [info] <0.221.0> Feature flags:   [ ] classic_mirrored_queue_version
2022-10-03 14:48:45.869475+00:00 [info] <0.221.0> Feature flags:   [ ] implicit_default_bindings
2022-10-03 14:48:45.869500+00:00 [info] <0.221.0> Feature flags:   [ ] maintenance_mode_status
2022-10-03 14:48:45.869545+00:00 [info] <0.221.0> Feature flags:   [ ] quorum_queue
2022-10-03 14:48:45.869565+00:00 [info] <0.221.0> Feature flags:   [ ] stream_queue
2022-10-03 14:48:45.869585+00:00 [info] <0.221.0> Feature flags:   [ ] user_limits
2022-10-03 14:48:45.869603+00:00 [info] <0.221.0> Feature flags:   [ ] virtual_host_metadata
2022-10-03 14:48:45.869641+00:00 [info] <0.221.0> Feature flags: feature flag states written to disk: yes
2022-10-03 14:48:46.012389+00:00 [notice] <0.44.0> Application syslog exited with reason: stopped
2022-10-03 14:48:46.012470+00:00 [notice] <0.221.0> Logging: switching to configured handler(s); following messages may not be visible in this log output
2022-10-03 14:48:46.018627+00:00 [notice] <0.221.0> Logging: configured log handlers are now ACTIVE
2022-10-03 14:48:47.023417+00:00 [info] <0.221.0> ra: starting system quorum_queues
2022-10-03 14:48:47.023483+00:00 [info] <0.221.0> starting Ra system: quorum_queues in directory: /var/lib/rabbitmq/mnesia/rabbit@rabbitmq/quorum/rabbit@rabbitmq
2022-10-03 14:48:47.024084+00:00 [info] <0.283.0> ra system 'quorum_queues' running pre init for 0 registered servers
2022-10-03 14:48:47.024574+00:00 [info] <0.287.0> ra: meta data store initialised for system quorum_queues. 0 record(s) recovered
2022-10-03 14:48:47.024714+00:00 [notice] <0.298.0> WAL: ra_log_wal init, open tbls: ra_log_open_mem_tables, closed tbls: ra_log_closed_mem_tables
2022-10-03 14:48:47.025859+00:00 [info] <0.221.0> ra: starting system coordination
2022-10-03 14:48:47.025892+00:00 [info] <0.221.0> starting Ra system: coordination in directory: /var/lib/rabbitmq/mnesia/rabbit@rabbitmq/coordination/rabbit@rabbitmq
2022-10-03 14:48:47.026408+00:00 [info] <0.329.0> ra system 'coordination' running pre init for 0 registered servers
2022-10-03 14:48:47.026764+00:00 [info] <0.331.0> ra: meta data store initialised for system coordination. 0 record(s) recovered
2022-10-03 14:48:47.026866+00:00 [notice] <0.336.0> WAL: ra_coordination_log_wal init, open tbls: ra_coordination_log_open_mem_tables, closed tbls: ra_coordination_log_closed_mem_tables
2022-10-03 14:48:47.028105+00:00 [info] <0.221.0> 
2022-10-03 14:48:47.028105+00:00 [info] <0.221.0>  Starting RabbitMQ 3.10.5 on Erlang 24.3.4.1 [jit]
2022-10-03 14:48:47.028105+00:00 [info] <0.221.0>  Copyright (c) 2007-2022 VMware, Inc. or its affiliates.
2022-10-03 14:48:47.028105+00:00 [info] <0.221.0>  Licensed under the MPL 2.0. Website: https://rabbitmq.com

  ##  ##      RabbitMQ 3.10.5
  ##  ##
  ##########  Copyright (c) 2007-2022 VMware, Inc. or its affiliates.
  ######  ##
  ##########  Licensed under the MPL 2.0. Website: https://rabbitmq.com

  Erlang:      24.3.4.1 [jit]
  TLS Library: OpenSSL - OpenSSL 1.1.1o  3 May 2022

  Doc guides:  https://rabbitmq.com/documentation.html
  Support:     https://rabbitmq.com/contact.html
  Tutorials:   https://rabbitmq.com/getstarted.html
  Monitoring:  https://rabbitmq.com/monitoring.html

  Logs: /var/log/rabbitmq/rabbit@rabbitmq_upgrade.log
        <stdout>

  Config file(s): /etc/rabbitmq/conf.d/10-defaults.conf

  Starting broker...2022-10-03 14:48:47.028643+00:00 [info] <0.221.0>

[...]

2022-10-03 14:48:47.359640+00:00 [info] <0.473.0> Ready to start client connection listeners
2022-10-03 14:48:47.360394+00:00 [info] <0.620.0> started TCP listener on [::]:5672
 completed with 6 plugins.
2022-10-03 14:48:47.397556+00:00 [info] <0.473.0> Server startup complete; 6 plugins started.
2022-10-03 14:48:47.397556+00:00 [info] <0.473.0>  * rabbitmq_stomp
2022-10-03 14:48:47.397556+00:00 [info] <0.473.0>  * rabbitmq_shovel_management
2022-10-03 14:48:47.397556+00:00 [info] <0.473.0>  * rabbitmq_shovel
2022-10-03 14:48:47.397556+00:00 [info] <0.473.0>  * rabbitmq_management
2022-10-03 14:48:47.397556+00:00 [info] <0.473.0>  * rabbitmq_web_dispatch
2022-10-03 14:48:47.397556+00:00 [info] <0.473.0>  * rabbitmq_management_agent
```

En las últimas líneas veremos el estado de los *plugins* que hemos habilitado.

Entre ellos estará el *Management* a través del navegador web, en la URL `http://<mi-sitio>:15672/`:

![Empty RabbitMQ](./doc/rabbitmq-out-of-the-box.png?raw=true "RabbitMQ out of the box")

El usuario por defecto es `guest / guest`, pero es algo que, en un entorno distinto del de «desarrollo», cambiaremos nomás hayamos entrado, ¿verdad?

## Acceso al *Management* de RabbitMQ por TLS

La manera más sencilla de proteger nuestro *Management* bajo el paraguas TLS es servirlo con *Nginx*. En el fichero de configuración del sitio añadimos un *upstream* y un par de *locations* (aunque, en sentido estricto, solo haremos uso de una de ellas):

```shell
upstream rabbitmq {        
    least_conn;
    server localhost:15672 weight=10 max_fails=3 fail_timeout=30s;
}

server {
[...]

    location /rabbitmq/api/ {
        rewrite ^ $request_uri;
        rewrite ^/rabbitmq/api/(.*) /api/$1 break;
        return 400;
        proxy_pass http://rabbitmq$uri;
    }

    location /rabbitmq {
        rewrite ^/rabbitmq$ /rabbitmq/ permanent;
        rewrite ^/rabbitmq/(.*)$ /$1 break;
        proxy_pass http://rabbitmq;
        proxy_buffering                    off;
        proxy_set_header Host              $http_host;
        proxy_set_header X-Real-IP         $remote_addr;
        proxy_set_header X-Forwarded-For   $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }
[...]
}
```

Ahora el acceso a la administración de *RabbitMQ* será: `https://<mi-sitio>/rabbitmq`
