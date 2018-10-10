SELECT id_rubrica_evaluacion, 
        ap_tipo, 
        ac.ap_estado 
    FROM sw_rubrica_evaluacion r, 
        sw_aporte_evaluacion a, 
        sw_aporte_curso_cierre ac,
        sw_asignatura asignatura
    WHERE r.id_aporte_evaluacion = a.id_aporte_evaluacion 
    AND r.id_aporte_evaluacion = ac.id_aporte_evaluacion 
    AND a.id_aporte_evaluacion = ac.id_aporte_evaluacion 
    AND r.id_tipo_asignatura = asignatura.id_tipo_asignatura
    AND asignatura.id_asignatura = 172
    AND r.id_aporte_evaluacion = 56
    AND ac.id_curso = 61
    --AND r.id_tipo_asignatura = $id_tipo_asignatura