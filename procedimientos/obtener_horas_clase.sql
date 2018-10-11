SELECT hc.id_hora_clase,
       hc_nombre,
       DATE_FORMAT(hc_hora_inicio,'%H:%i') AS hora_inicio,
       DATE_FORMAT(hc_hora_fin,'%H:%i') AS hora_fin,
       ds_nombre
  FROM sw_hora_clase hc,
       sw_hora_dia hd,
       sw_horario ho,
       sw_dia_semana di
 WHERE hc.id_hora_clase = hd.id_hora_clase
   AND hc.id_hora_clase = ho.id_hora_clase
   AND di.id_dia_semana = hd.id_dia_semana
   AND ho.id_asignatura = 158
   AND ho.id_paralelo = 95
   AND ho.id_dia_semana = 21
   AND hd.id_dia_semana = 21
 ORDER BY hc.hc_ordinal