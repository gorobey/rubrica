SELECT e.id_estudiante, 
        c.id_curso, 
        d.id_paralelo, 
        d.id_asignatura, 
        e.es_apellidos, 
        e.es_nombres, 
        es_retirado, 
        as_nombre, 
        cu_nombre, 
        pa_nombre,
        a.id_tipo_asignatura 
    FROM sw_distributivo d, 
        sw_estudiante_periodo_lectivo ep, 
        sw_estudiante e, 
        sw_asignatura a, 
        sw_curso c, 
        sw_paralelo p 
    WHERE d.id_paralelo = ep.id_paralelo 
    AND d.id_periodo_lectivo = ep.id_periodo_lectivo 
    AND ep.id_estudiante = e.id_estudiante 
    AND d.id_asignatura = a.id_asignatura 
    AND d.id_paralelo = p.id_paralelo 
    AND p.id_curso = c.id_curso 
    AND d.id_paralelo = 87
    AND d.id_asignatura = 172 
    AND es_retirado <> 'S' ORDER BY es_apellidos, es_nombres ASC