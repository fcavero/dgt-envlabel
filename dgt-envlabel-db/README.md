# Modelo de datos de la API de distintivos ambientales de la DGT

El modelo de datos de esta API es absurdamente simple:

* `tt_envlabel` → Tabla maestra de tipos distintivos ambientales (se incluyen *data-fixtures* con los valores «oficiales» de la DGT).
* `t_vehicle` → Tabla de matrículas de vehículos con su distintivo correspondiente, según la DGT.
* `t_downloaded_file` → Tabla que almacena la fecha y un *hash* de los ficheros cuyos datos se han cargado exitosamente.

## Detalles del modelo de datos a tener en cuenta

* La peculiaridad de la persistencia de datos es que no hay sentencias *UPDATE* ni *DELETE*. La clave primaria de la tabla de vehículos no es la matrícula, sino un `uuid`, y eso permite que una misma matrícula pueda tener varias tuplas, una por cada distintivo.
* Sí existe una clave única que evita que una matrícula esté asociada varias veces al mismo distintivo, y es la clave que salta cuando hacemos la inserción y el distintivo no ha cambiado:

    ```sql
    INSERT INTO envlabel.t_vehicle (txt_plate, envlabel_id)
        SELECT :plate, id 
            FROM envlabel.tt_envlabel
            WHERE txt_dgt_tag = :tag
            ON CONFLICT DO NOTHING
    ```

## Estructura

La estructura es la clásica de [Sqitch](https://sqitch.org/):

```text
└── dgt-envlabel-db
    └── sqitch
        ├── deploy
        ├── revert
        └── verify
```

Donde:

* *sqitch/deploy* → *Scripts* de PL/pgSQL para desplegar el modelo de datos.
* *sqitch/revert* → *Scripts* de PL/pgSQL para revertir los cambios cuando el despliegue falla miserablemente.
* *sqitch/verify* → *Scripts* de PL/pgSQL para verificar el modelo de datos.

## Licencia

DO WHAT THE FUCK YOU WANT TO PUBLIC LICENSE (WTFPL). Por favor, revisad el [fichero de licencia](./LICENSE) para más información.
