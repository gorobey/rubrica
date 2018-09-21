SELECT hc_nombre, 
       ds_nombre,
       hc_hora_inicio,
       CONCAT(cu_shortname," ",'"',pa_nombre,'"') AS paralelo,
       as_shortname,
       us_shortname
  FROM sw_hora_clase hc, 
       sw_dia_semana ds, 
       sw_hora_dia hd, 
       sw_curso c, 
       sw_paralelo p, 
       sw_horario ho,
       sw_asignatura a,
       sw_distributivo d,
       sw_usuario u
 WHERE hc.id_hora_clase = hd.id_hora_clase 
   AND ds.id_dia_semana = hd.id_dia_semana 
   AND c.id_curso = p.id_curso
   AND p.id_paralelo = ho.id_paralelo
   AND hd.id_hora_clase = ho.id_hora_clase
   AND hd.id_dia_semana = ho.id_dia_semana
   AND a.id_asignatura = ho.id_asignatura
   AND a.id_asignatura = d.id_asignatura
   AND u.id_usuario = d.id_usuario
   AND p.id_paralelo = d.id_paralelo
   AND d.id_paralelo = ho.id_paralelo
   AND ds.id_periodo_lectivo = 6