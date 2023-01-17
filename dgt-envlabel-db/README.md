# dgt-envlabel-db

Modelo de datos de la API `dgt-envlabel-api`

El modelo de datos es muy simple:

* **tt_envlabel** → Tabla maestra de tipos distintivos ambientales (se incluyen *data-fixtures*).
* **t_vehicle** → Tabla de matrículas de vehículos con su distintivo correspondiente, según la DGT.
* **tmp_file** → Tabla auxiliar para cargar el pedazo de fichero CSV de la DGT.

![envlabel-schema](./doc/dgt-envlabel-db.png?raw=true "envlabel-schema")
