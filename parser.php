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

        // partido
        $array_partidos = array(
            0 => 'Sin partido', 
            1 => 'PAN', 
            2 => 'PRI', 
            3 => 'PRD', 
            4 => 'MC', 
            5 => 'PVEM', 
            6 => 'Morena' 
        );
        // estado
        $array_estados = array( 
            1 => 'Aguascalientes',
            2 => 'Baja California',
            3 => 'Baja California Sur',
            4 => 'Campeche',
            5 => 'Chiapas',
            6 => 'Chihuahua',
            7 => 'Ciudad de México',
            8 => 'Coahuila',
            9 => 'Colima',
            10 => 'Durango',
            11 => 'Estado de México',
            12 => 'Guanajuato',
            13 => 'Guerrero',
            14 => 'Hidalgo',
            15 => 'Jalisco',
            16 => 'Michoacán',
            17 => 'Morelos',
            18 => 'Nayarit',
            19 => 'Nuevo León',
            20 => 'Oaxaca',
            21 => 'Puebla',
            22 => 'Querétaro',
            23 => 'Quintana Roo',
            24 => 'San Luis Potosí',
            25 => 'Sinaloa',
            26 => 'Sonora',
            27 => 'Tabasco',
            28 => 'Tamaulipas',
            29 => 'Tlaxcala',
            30 => 'Veracruz',
            31 => 'Yucatán',
            32 => 'Zacatecas'
        );

        // si hay una imagen en el csv y si elegimos guardar las imagenes entones las descargamos
        if (!empty($imagen) && $storeImages == true) {
            file_put_contents($destination . basename($imagen), file_get_contents($imagen));
        }

        // remover el dominio para hacer referencia al archivo descargado local
        $imagen = str_replace("http://sitl.diputados.gob.mx/LXV_leg/fotos_lxvconfondo/", "", $columns[6]);

        // cambiamos los ids por los nombres de partido y estado
        $partido_string = $array_partidos[$partido];
        $estado_string = $array_estados[$estado];

        // crear el registro en la tabla con una referencia al filename de las fotos descargadas
        $query = "INSERT INTO traidores VALUES ('" . $nombre . "', '" . $partido_string . "', '" . $imagen . "', '" . $link . "', '" . $periodo . "', '" . $estado_string . "', '" . $distrito . "', '" . $dob . "')";
        $mysqli->query($query) OR die($mysqli->connect_error . "<br>" . $query);
    }
    fclose($handle);
}

?>