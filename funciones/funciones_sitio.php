<?php

/* Archivo: funciones_sitio.php */

//función para retornar la fecha actual en el formato dia_de_la_sema, dia del mes del año
//e.g. Domingo, 25 de Abril del 2010

//función php para establecer el huso horario que se va a utilizar
date_default_timezone_set('America/Guayaquil');

$meses = array(0, "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
$dias = array("Domingo", "Lunes", "Martes", "Mi&eacute;rcoles", "Jueves", "Viernes", "S&aacute;bado");
$mes = $meses[date("n")];
$dia = $dias[date("w")];

function fecha_actual() {
    global $mes, $dia;
    $fecha_string = "$dia,  " . date("j") . " de $mes del " . date("  Y");
    return $fecha_string;
}

function fecha_formateada($yy, $mm, $dd) {
	global $meses, $dias;
	$fecha_string = (int)substr($dd, 0, 2) . " de " . $meses[(int)$mm] . " del " . $yy;
	return $fecha_string;
}

function equivalencia($n) {
	if ($n==10)
		$equiv = 'S';
	else if ($n >= 9 && $n < 10)
		$equiv = 'D';
	else if ($n >= 7 && $n < 9)
		$equiv = 'A';
	else if ($n > 4 && $n < 7)
		$equiv = 'P';
	else
		$equiv = 'N';
	return $equiv;
}

function equiv_comportamiento($n) {

    if($n>=9 && $n<=10)
            $equiv = 'A';
    else if($n>=7 && $n<8.99)
            $equiv = 'B';
    else if($n>=6 && $n<=6.99)
            $equiv = 'C';
    else if($n>=4 && $n<=5.99)
            $equiv = 'D';
    else if($n==0)
            $equiv = 'S/N';
    else
            $equiv = 'E';

    return $equiv;
}

function equiv_anual($promedio_anual, $es_retirado, $terminacion)
{
	if ($es_retirado == "S")
		$observacion = "RETIRAD" . $terminacion;
	else {
		if ($promedio_anual >= 7 && $promedio_anual <= 10)
			$observacion = "APRUEBA";
		else if ($promedio_anual >= 5 && $promedio_anual < 7)
			$observacion = "SUPLETORIO";
		else if ($promedio_anual > 0 && $promedio_anual < 5)
			$observacion = "REMEDIAL";
		else $observacion = "SIN NOTAS";
	}
	return $observacion;
}

?>