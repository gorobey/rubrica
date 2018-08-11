SELECT c.id_criterio_personalizado, 
       ru_nombre, 
	   as_nombre, 
	   cp_descripcion, 
	   cp_ponderacion 
  FROM sw_criterio_personalizado c, 
       sw_rubrica_docente r, 
	   sw_rubrica_evaluacion ru, 
	   sw_asignatura a 
 WHERE r.id_rubrica_evaluacion = ru.id_rubrica_evaluacion 
   AND r.id_criterio_personalizado = c.id_criterio_personalizado 
   AND r.id_asignatura = a.id_asignatura 
   AND c.id_rubrica_personalizada = 1 
   AND r.id_asignatura = 14 
   AND r.id_usuario = 5 
   AND r.id_paralelo = 10
 ORDER BY id_criterio_personalizado ASC