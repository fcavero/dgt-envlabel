# API de distintivos ambientales de la DGT

API vitaminada para consultar los [distintivos ambientales](https://revista.dgt.es/es/multimedia/infografia/2018/0219distintivos-ambientales.shtml) del parque móvil de España; a partir de su matrícula, podemos saber qué distintivo ambiental tiene un vehículo, o, incluso, qué distintivos ambientales ha ido teniendo a lo largo del tiempo. Sí, amiguitos, los distintivos ambientales mutan, de acuerdo a las mejoras que los propietarios hagan a sus vehículos (v.g. cambiar un motor diesel [Euro5](https://en.wikipedia.org/wiki/European_emission_standards#Euro5) por un [GNC](https://en.wikipedia.org/wiki/Compressed_natural_gas) o [GLP](https://en.wikipedia.org/wiki/Liquefied_petroleum_gas))[¹].

Pero, ¿esto no es lo que ya se hace [aquí](https://sede.dgt.gob.es/es/vehiculos/distintivo-ambiental/)? Sí, pero ahora sin *web scrapping*, y accediendo al histórico, y [porque podemos, y si podemos, lo hacemos](https://www.youtube.com/watch?v=swvKEzKIGwY).

Los datos servidos tienen su origen en un fichero espantoso que la DGT, esto es, la **Dirección General de Tráfico**, genera de manera periódica —con una periodicidad que solo ellos conocen—, publica, y luego esconde en su espeluznante portal de datos estadísticos (esta [*horrendez*](https://sedeapl.dgt.gob.es/WEB_IEST_CONSULTA/categoria.faces), que pagan nuestros impuestos).

El fichero no es más que un CSV con este formato: `MATRÍCULA|ETIQUETA AMBIENTAL`

Ahí va una cata del fichero:

```txt
0003LWZ|16MC
0003LXB|16TC
0003LXC|16TC
0003LXD|16TC
0003LXF|16TC
0003LXG|16TC
0003LXH|16MC
0003LXJ|16M0
0003LXK|16TC
0003LXL|16TC
[...]
```

Los literales de las etiquetas tienen su propia semántica atroz para llegar a diferenciar los siguientes tipos, de menos a más contaminante:

* *CERO EMISIONES*: Etiquetas `16M0` y `16T0`
* *ECO*: Etiquetas `16ME` y `16TE`
* *C*: Etiquetas `16MC` y `16TC`
* *B*: Etiquetas `16MB` y `16TB`
* *SIN DISTINTIVO*: No existe etiqueta, o etiqueta `SIN DISTINTIVO`, conocidos a pie de calle como *vehículos A*.

[¹]: En ocasiones es importante tener clara la evolución cronológica de cambios, pues la DGT no practica la *sublimación* de un ditintivo *C* o *B* al gas natural o el petróleo licuado, esto es, al *ECO*. El flujo, por poco lógico que parezca, es el siguiente: de un *C* o un *B* pasa a ser, por un tiempo indeterminado, un vehículo *SIN DISTINTIVO*, y ya desde este «estado-intermedio-sin-etiqueta», a un vehículo *ECO*. Imaginaos ahora que en un municipio cualquiera ([pongamos que hablo de Madrid](https://www.youtube.com/watch?v=4_HbXgtd0N0)), los vehículos *SIN DISTINTIVO* tienen el acceso restringido y son sancionados; el propietario del vehículo, que ya es tecnológicamente *ECO*, ignora por completo que, oficialmente, su vehículo es un paria *SIN DISTINTIVO*, y si no somos cuidadosos, podría ser injustamente sancionado (o más injustamente sancionado, *quicir*).

## Pero, ¿por qué carallo se hace esto?

Es una funcionalidad añadida para los sistemas de control de accesos que emplean los policías locales y municipales en el correctísimo desempeño de su importante labor. ¿Les importan los distintivos ambientales a las fuerzas y cuerpos de seguridad del Estado? Lo mismo que a mí: nada de nada. Al menos, hasta que circular con un vehículo SIN DISTINTIVO por su jurisdicción sea constitutivo de delito. *How dare you?*

Sin embargo, a mis queridos y estimados concejales de movilidad ─un saludo afectuoso desde aquí; Fernando Cavero, un admirador, un amigo, un esclavo, un sieeervo─, no es que les gusten los distintivos ambientales, no. Es que les erotizan. Imaginaos un Excel de accesos a su municipio con los datos agregados por «etiqueta de la DGT». Por días. Semanas. Meses. Años. Mmmmm

En algunos municipios, además, siguiendo a pies juntillas las agendas europeas del pensamiento correctísimamente correcto, se veta la entrada en determinadas calles a vehículos de contribuyentes cuyas emisiones no sean «las adecuadas» (de los vehículos, no de los contribuyentes), llegando a sancionar administrativamente a los ciudadanos a la par que violan el [artículo 19 de la Constitución Española de 1978](https://app.congreso.es/consti/constitucion/indice/sinopsis/sinopsis.jsp?art=19&tipo=2) 🤡

Recapitulando, contar con un mecanismo automático que realice el mantenimiento de los datos de los distintivos ambientales, parece que viene a ser necesario. La alternativa manual es tan pesadillesca que, cuando lo has tenido que hacer un par de veces, solo piensas en sacártelo de encima.

## Stack tecnológico detrás de este tinglado

Este pequeño proyecto utiliza, todo convenientemente *dockerificado*, los siguientes componentes:

* La API está desarrollada en PHP 8.2 con [Symfony](https://symfony.com/) (versión 5.4 LTS, de acuerdo al [*roadmap*](https://symfony.com/releases/5.4) oficial) y [API Platform](https://api-platform.com/).
* Los *scripts* que manejan el fichero son comandos de *Symfony*, esto es, más PHP.
* La base de datos es [PostgreSQL](https://www.postgresql.org/), con un modelo de datos bien sencillo a la par de eficaz.
* Para la comunicación asíncrona entre servicios se utiliza [RabbitMQ](https://www.rabbitmq.com/).

## Descripción general del proceso de mantenimiento de distintivos ambientales

Los pasos, *grosso modo*, son los siguientes:

1. Un comando descarga el fichero ZIP de la página de estadísticas de la DGT. El fichero es de alrededor de 90 MB.
2. Otro comando descomprime el fichero (más de 500 MB), e invoca al comando `split` del sistema operativo con el fin de poder manejar *n* ficheros pequeños en lugar de uno enorme. *Divide et impera.*
3. Un tercer comando es el encargado de enviar al bróker de mensajería un mensaje con la ruta de cada fichero a importar en la base de datos.
4. Una aplicación se encuentra en permanente escucha de mensajes de ficheros, procesándolos de a poquito; son muchos millones de registros, y no queremos quedarnos sin memoria.

Como no hay prisa y el número de registros a tratar es muy grande (más de 30 millones), el proceso completo se demora varias horas, durante las cuales la API sigue siendo por completo funcional.

## Hablar es fácil. Enséñame la API

Esta API no prevee ningún tipo de autenticación, y cuenta con unos *EndPoints* muy básicos.

### Dame los tipos de distintivos ambientales

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

### Toma esta matrícula; dame su distintivo ambiental más reciente

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

### Toma esta matrícula; dame todos sus distintivos ambientales

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

## Estructura

```txt
.
├── dgt-envlabel-api
├── dgt-envlabel-db
├── dgt-envlabel-settler
├── rabbitmq
└── tmp
    └── splits
```

Donde:

* [dgt-envlabel-api](./dgt-envlabel-api/README.md) → API de Symfony/API Platform y comandos de descarga y procesado del fichero de datos.
* [dgt-envlabel-db](./dgt-envlabel-db/README.md) → *Scripts* que despliegan, con [Sqitch](https://sqitch.org/), el modelo de datos.
* [dgt-envlabel-settler](./dgt-envlabel-settler/README.md) → Aplicación que procesa los mensajes y ejecuta la persistencia de datos en *PostgreSQL*.
* [rabbitmq](./rabbitmq/README.md) → *RabbitMQ dockerificado*, con el [*plugin Management*](https://www.rabbitmq.com/management.html) para mayor comodidad.
* *tmp/splits* → Rutas donde se descarga el fichero de datos, se descomprime y se trocea. Son convenientemente limpiadas tras cada ejecución.

## Licencia

DO WHAT THE FUCK YOU WANT TO PUBLIC LICENSE (WTFPL). Por favor, revisad el [fichero de licencia](./LICENSE) para más información.
