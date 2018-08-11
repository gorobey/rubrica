SELECT COUNT(*) AS num_registros
  FROM sw_rubrica_estudiante r, 
       sw_asignatura a, 
	   sw_estudiante e, 
	   sw_paralelo_asignatura pa, 
	   sw_usuario u, 
	   sw_periodo_evaluacion pe, 
	   sw_aporte_evaluacion ap, 
	   sw_rubrica_evaluacion ru 
 WHERE r.id_paralelo = pa.id_paralelo 
   and r.id_asignatura = pa.id_asignatura 
   and r.id_asignatura = a.id_asignatura 
   and pa.id_usuario = u.id_usuario 
   and r.id_rubrica_personalizada = ru.id_rubrica_evaluacion 
   and ap.id_aporte_evaluacion = ru.id_aporte_evaluacion 
   and pe.id_periodo_evaluacion = ap.id_periodo_evaluacion 
   and r.id_estudiante = e.id_estudiante 
   and pa.id_usuario = 2 
   and re_calificacion > 10 

