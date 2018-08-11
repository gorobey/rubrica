select ep.id_paralelo,
	   concat(cu_abreviatura,pa_nombre,' ',es_abreviatura) as paralelo,
       count(ep.id_estudiante) as numero
  from sw_paralelo p, sw_curso c, 
       sw_especialidad e, 
       sw_estudiante_periodo_lectivo ep 
 where c.id_curso = p.id_curso 
   and e.id_especialidad = c.id_especialidad 
   and p.id_paralelo = ep.id_paralelo
   and ep.id_periodo_lectivo = 1
 group by ep.id_paralelo;

