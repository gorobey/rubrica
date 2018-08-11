SELECT e.id_estudiante, 
       pa.id_paralelo, 
       pa.id_asignatura, 
       e.es_apellidos, 
       e.es_nombres, 
       as_nombre, 
       cu_nombre, 
       pa_nombre
  FROM sw_paralelo_asignatura pa, 
       sw_estudiante_periodo_lectivo ep, 
       sw_estudiante e, 
       sw_asignatura a, 
       sw_curso c, 
       sw_paralelo p
 WHERE pa.id_paralelo = ep.id_paralelo
   AND pa.id_periodo_lectivo = ep.id_periodo_lectivo
   AND ep.id_estudiante = e.id_estudiante
   AND pa.id_asignatura = a.id_asignatura
   AND pa.id_paralelo = p.id_paralelo
   AND p.id_curso = c.id_curso
   AND pa.id_paralelo =10
   AND pa.id_asignatura =14
 ORDER BY es_apellidos, es_nombres ASC