<?php

class comportamientos extends MySQL
{
    var $code = "";
    var $ec_cualitativa = "";
    var $ec_cuantitativa = "";
    var $ec_nota_minima = "";
    var $ec_nota_maxima = "";
    var $ec_equivalencia = "";
    
    function equiv_comportamiento($n)
    {
        $consulta = parent::consulta("SELECT ec_equivalencia FROM sw_escala_comportamiento WHERE ec_nota_minima >= $n AND ec_nota_maxima <= $n");
        $resultado = parent::fetch_array($consulta);
        return $resultado["ec_equivalencia"];
    }
}

