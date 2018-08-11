select * from sw_aporte_evaluacion where id_periodo_evaluacion > 5;

select * from sw_aporte_curso_cierre;

select * from sw_curso;

insert into sw_aporte_curso_cierre
select id_aporte_evaluacion, 19, ap_fecha_apertura, ap_fecha_cierre, ap_estado
from sw_aporte_evaluacion where id_aporte_evaluacion >= 16 and id_aporte_evaluacion <= 22;

delete from sw_aporte_curso_cierre
where id_curso = 12
and id_aporte_evaluacion >= 16 and id_aporte_evaluacion <= 22;

select * from sw_aporte_curso_cierre;

call sp_abrir_periodos();

call sp_cerrar_periodos();

