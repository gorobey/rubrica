SELECT pe.id_periodo_lectivo, 
       COUNT(*) AS num_paralelos 
  FROM sw_periodo_lectivo pe, 
       sw_paralelo p, 
       sw_curso c, 
       sw_especialidad e,
       sw_tipo_educacion te
 WHERE pe.id_periodo_lectivo = te.id_periodo_lectivo
   AND te.id_tipo_educacion = e.id_tipo_educacion
   AND e.id_especialidad = c.id_especialidad 
   AND c.id_curso = p.id_curso
   AND pe.id_periodo_lectivo = 5
 GROUP BY pe.id_periodo_lectivo 
 ORDER BY pe.id_periodo_lectivo