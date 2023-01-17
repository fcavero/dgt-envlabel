# API de distintivos ambientales de la DGT

API para consultar los distintivos ambientales del parque móvil de España.

Los datos tienen su origen en un fichero espantoso que la DGT, esto es, la **Dirección General de Tráfico**, genera de manera periódica —con una periodicidad que solo ellos conocen—, publica, y luego esconde en su espeluznante portal de datos estadísticos (esta [*horrendez*](https://sedeapl.dgt.gob.es/WEB_IEST_CONSULTA/categoria.faces)).

La gracia del asunto no estiba en ofrecer algo tan simple como la etiqueta correspondiente a una matrícula, sino en que se realiza un mantenimiento automático de los datos. Así, de manera por completo desatendida:
1. Se descarga un fichero ZIP del portal de la DGT.
2. Se descomprime y el CSV se trocea, pues son más de 30 millones de líneas.
3. Se importan en una tabla temporal de PostgreSQL.
4. ???
5. Profit!!
