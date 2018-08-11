SELECT id_rubrica_evaluacion, 
       ap_tipo, 
       ac.ap_estado 
  FROM sw_rubrica_evaluacion r, 
       sw_aporte_evaluacion a, 
       sw_aporte_curso_cierre ac 
 WHERE r.id_aporte_evaluacion = a.id_aporte_evaluacion 
   AND r.id_aporte_evaluacion = ac.id_aporte_evaluacion 
   AND a.id_aporte_evaluacion = ac.id_aporte_evaluacion 
   AND r.id_aporte_evaluacion = 23 
   AND ac.id_curso = 27;

SELECT * FROM sw_aporte_curso_cierre WHERE id_aporte_evaluacion = 23;

SELECT e.id_estudiante, 
       es_apellidos, 
       es_nombres, 
       c.id_curso 
  FROM sw_estudiante e, 
       sw_estudiante_periodo_lectivo p, 
       sw_curso c, 
       sw_paralelo pa 
 WHERE c.id_curso = pa.id_curso 
   AND pa.id_paralelo = 21
   AND e.id_estudiante = p.id_estudiante 
   AND p.id_paralelo = 21
   AND es_retirado = 'N' 
ORDER BY es_apellidos, es_nombres ASC;

SELECT id_rubrica_evaluacion, 
       ac.ap_estado 
  FROM sw_rubrica_evaluacion r, 
       sw_aporte_evaluacion a, 
       sw_aporte_curso_cierre ac, 
       sw_periodo_evaluacion p 
 WHERE a.id_aporte_evaluacion = ac.id_aporte_evaluacion 
   AND ac.id_curso = 13 
   AND r.id_aporte_evaluacion = a.id_aporte_evaluacion 
   AND a.id_periodo_evaluacion = p.id_periodo_evaluacion 
   AND p.pe_principal = 4;

SELECT * FROM sw_paralelo;

SELECT es_promocionado(623,2,21);

select * from sw_estudiante_periodo_lectivo where id_estudiante = 332 or id_estudiante = 677;

SELECT as_abreviatura,
       a.id_asignatura 
  FROM sw_asignatura a, 
       sw_asignatura_curso ac, 
       sw_paralelo p 
 WHERE a.id_asignatura = ac.id_asignatura 
   AND p.id_curso = ac.id_curso 
   AND p.id_paralelo = 21 
 ORDER BY ac_orden;

-- FIS	9
-- QUIM	10
-- HIST	19
-- LIT	13
-- MAT	1
-- ING	7
-- DES.P.	21
-- EDU.F.	17
-- ED.ART	12
-- INFO	11
-- DIB	5
-- COMU	32
-- COMPRA	26
-- CONTA	152

SELECT calcular_promedio_final(2,623,21,11);

SELECT calcular_promedio_anual(2,623,21,11);

SELECT ep.id_estudiante 
  FROM sw_estudiante e, 
       sw_estudiante_periodo_lectivo ep 
 WHERE ep.id_estudiante = e.id_estudiante 
   AND ep.id_periodo_lectivo = 2 
   AND e.es_apellidos = 'CALDERON ESCOBAR' 
   AND e.es_nombres = 'ANDREA MISHELL';

SELECT * FROM sw_estudiante e WHERE e.es_apellidos = 'CALDERON ESCOBAR' AND e.es_nombres = 'ANDREA MISHELL';

select * from sw_estudiante_periodo_lectivo where id_estudiante=299;

delete from sw_estudiante where id_estudiante=340;

SELECT * FROM sw_estudiante e WHERE e.es_cedula = '1751321108';

SELECT * FROM sw_estudiante e WHERE e.es_nombre_completo = 'VASQUEZ CUZCO CARLOS ESTHALIN';

SELECT secuencial_menu_nivel_perfil(1, 6)


