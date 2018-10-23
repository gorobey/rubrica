 SELECT id_horario, 
        ho.id_hora_clase, 
        hc_nombre, 
        DATE_FORMAT(hc_hora_inicio,'%H:%i') AS hora_inicio, 
        DATE_FORMAT(hc_hora_fin,'%H:%i') AS hora_fin, 
        as_nombre, 
        CONCAT(us_titulo,' ',us_apellidos,' ',us_nombres) AS docente 
   FROM sw_horario ho, 
        sw_hora_clase hc, 
        sw_asignatura a,
        sw_distributivo d,
        sw_usuario u
  WHERE ho.id_hora_clase = hc.id_hora_clase 
    AND ho.id_asignatura = a.id_asignatura 
    AND a.id_asignatura = d.id_asignatura
    AND u.id_usuario = d.id_usuario
    AND ho.id_dia_semana = 22 
    AND ho.id_hora_clase = 8 
    AND ho.id_paralelo = 86