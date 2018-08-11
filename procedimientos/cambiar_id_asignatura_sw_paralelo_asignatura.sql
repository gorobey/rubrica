select * from sw_rubrica_estudiante where id_paralelo = 23 and id_asignatura = 24;

update sw_rubrica_estudiante set id_asignatura = 31 where id_paralelo = 23 and id_asignatura = 24;

select * from sw_rubrica_estudiante where id_paralelo = 23 and id_asignatura = 31;

DROP TRIGGER `colegion_1`.`tg_update_sw_rubrica_estudiante`;

select * from sw_paralelo_asignatura where id_paralelo = 23 and id_asignatura = 24;

update sw_paralelo_asignatura set id_asignatura = 31 where id_paralelo = 23 and id_asignatura = 24;

select * from sw_paralelo_asignatura where id_paralelo = 23 and id_asignatura = 31;

select * from sw_asignatura_curso where id_curso = 15 and id_asignatura = 24;

update sw_asignatura_curso set id_asignatura = 31 where id_curso = 15 and id_asignatura = 24;

select * from sw_asignatura_curso where id_curso = 15 and id_asignatura = 31;

DELIMITER $$
CREATE
DEFINER=`root`@`localhost`
TRIGGER `colegion_1`.`tg_update_sw_rubrica_estudiante`
AFTER UPDATE ON `colegion_1`.`sw_rubrica_estudiante`
FOR EACH ROW
BEGIN
	
	DECLARE IdUsuario INT DEFAULT 0;

	SET IdUsuario = (SELECT id_usuario FROM sw_paralelo_asignatura WHERE id_paralelo = new.id_paralelo AND id_asignatura = old.id_asignatura);
		
	INSERT INTO sw_rubrica_estudiante_log 
       SET id_rubrica_estudiante = new.id_rubrica_estudiante,
		   id_estudiante = new.id_estudiante,
		   id_paralelo = new.id_paralelo,
		   id_asignatura = new.id_asignatura,
		   id_rubrica_personalizada = new.id_rubrica_personalizada,
		   id_usuario = IdUsuario,
		   re_calificacion_nueva = new.re_calificacion,
		   re_calificacion_antigua = old.re_calificacion,
           rl_accion = 'ACTUALIZACION';

END