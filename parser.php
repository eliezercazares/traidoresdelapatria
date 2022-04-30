<?php
// que corra todo el tiempo necesario
set_time_limit(0);

// conexion sql
$mysqli = new mysqli("localhost","traidores", "tr41d0r35","traidoresdelapatria") OR die(mysqli_error());
if ($mysqli->connect_errno) {
    printf("Connect failed: %s\n", $mysqli->connect_error);
    exit();
}
mysqli_set_charset($mysqli, 'utf8');

// borrar la tabla de la db
$mysqli->query("TRUNCATE traidores");

// donde guardar las imagenes
$destination = 'images/';
// descargar las imagenes?
$storeImages = false;


// abrir el csv
$handle = fopen('diputables.csv', "r");
// un contador para brincar los encabezados del csv
$i = 0;

// si el archivo se abre exitosamente
if ($handle) {
    // iteramos las columnas de cada renglón
    while ($columns = fgetcsv($handle)) {
        $i++;
        // saltar los encabezados en el csv
        if( $i == 1):
            continue;
        endif;

        // mapeo de columnas a variables
        $nombre = $columns[0];
        $partido = $columns[7];
        $imagen = $columns[6];

        $link = $columns[3];
        $periodo = $columns[8];
        $estado = $columns[11];
        $distrito = $columns[12];
        $dob = $columns[13];

        // si hay una imagen en el csv y si elegimos guardar las imagenes entones las descargamos
        if (!empty($imagen) && $storeImages == true) {
            file_put_contents($destination . basename($imagen), file_get_contents($imagen));
        }

        // remover el dominio para hacer referencia al archivo descargado local
        $imagen = str_replace("http://sitl.diputados.gob.mx/LXV_leg/fotos_lxvconfondo/", "", $columns[6]);

        // crear el registro en la tabla con una referencia al filename de las fotos descargadas
        $query = "INSERT INTO traidores VALUES ('" . $nombre . "', '" . $partido . "', '" . $imagen . "', '" . $link . "', '" . $periodo . "', '" . $estado . "', '" . $distrito . "', '" . $dob . "')";
        $mysqli->query($query) OR die($mysqli->connect_error . "<br>" . $query);
    }
    fclose($handle);
}

?>