SELECT id_estudiante,
       id_asignatura
  FROM sw_estudiante_periodo_lectivo ep,
       sw_paralelo p,
       sw_asignatura_curso ac
  WHERE p.id_paralelo = ep.id_paralelo
    AND p.id_curso = ac.id_curso
    AND ep.id_paralelo = 67
  ORDER BY id_estudiante