drop table if exists sw_rubrica_estudiante_log;

create table sw_rubrica_estudiante_log (
	id_rubrica_estudiante int not null,
	id_estudiante int not null,
	id_paralelo int not null,
	id_asignatura int not null,
	id_rubrica_personalizada int not null,
	id_usuario int not null,
	re_calificacion_nueva float default 0,
	re_calificacion_antigua float default 0,
	re_fecha_modificacion timestamp default current_timestamp
);