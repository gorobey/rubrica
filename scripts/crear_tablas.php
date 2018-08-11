<?php
	sleep(1);
	include_once("../scripts/clases/class.encrypter.php");
	//recibo las variables de tipo post de la pagina login.php
	//Datos de conexion para la instancia mysql.
	$host = $_POST["nom_servidor"];
	$usuario_bd = $_POST["user_bd"];
	$pass_bd = $_POST["pass_bd"];
	$database = $_POST["nom_bd"];
	//Datos de acceso para el usuario administrador del sistema.
	$usu_admin = $_POST["user_admin"];
	$pass_admin = $_POST["pass_admin"];
	//Dato del password del root
	$pass = $_POST["pass"];
	//creo la base de datos y las tablas
	$errores = "";
	//SE REALIZA LA CONEXION COMO ROOT
	$val0 = @mysql_connect("$host","root","$pass");
	if (!$val0)
		echo "El password del root es incorrecto";
	else {
		//SE CREA LA INSTACIA
		$query_db = "CREATE DATABASE $database";
		$val1 = @mysql_query($query_db);
		//Error 1
		if (!$val1)
			echo "La base de datos ya fue creada";
		else {
			//SE DA LOS PERMISOS RESPECTIVOS AL USUARIO CREADO
			$query_usu =  "GRANT ALL PRIVILEGES ON $database.* TO $usuario_bd@$host ";
			$query_usu .= "IDENTIFIED BY '$pass_bd' WITH GRANT OPTION";
			$val2 = @mysql_query($query_usu);
			//Error 2
			if (!$val2)
				echo "Error en la asignación de privilegios para el usuario $usuario_bd";
			else {
				//SE REALIZA LA CONEXION A LA NUEVA INTANCIA
				@mysql_connect("$host","$usuario_bd","$pass_bd");
				$val3 = @mysql_select_db("$database");
				//Error 3
				if (!$val3)
					echo "La instancia $database a&uacute;n no ha sido creada";
				else {
					//CREACION DE LA TABLA sw_periodo_lectivo
					$query_t1 = "CREATE TABLE sw_periodo_lectivo(
									id_periodo_lectivo int(11) NOT NULL AUTO_INCREMENT,
									pe_anio_inicio int(11) NOT NULL,
									pe_anio_fin int(11) NOT NULL,
									pe_estado char(1) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
									PRIMARY KEY(id_periodo_lectivo),
  									UNIQUE KEY pe_anio_inicio (pe_anio_inicio),
  									UNIQUE KEY pe_anio_fin (pe_anio_fin)
									)"; 
					$val4 =  @mysql_query($query_t1);
					//Error 4
					if (!$val4)
						echo "Error en la creaci&oacute;n de la tabla sw_periodo_lectivo." . mysql_error();
					else {
						//INSERCION DEL PRIMER PERIODO LECTIVO
						$pe_anio_inicio = date("  Y");
						$pe_anio_fin = $pe_anio_inicio + 1;
						$query_i1 = "INSERT INTO sw_periodo_lectivo (pe_anio_inicio, pe_anio_fin, pe_estado)
										VALUES ($pe_anio_inicio, $pe_anio_fin, 'A')";
						$val5 = @mysql_query($query_i1);
						//Error 5
						if (!$val5)
							echo "Error en la inserci&oacute;n del primer per&iacute;odo lectivo." . mysql_error();
						else {
							//CREACION DE LA TABLA sw_perfil
							$query_t2 = "CREATE TABLE sw_perfil(
											id_perfil int(11) NOT NULL AUTO_INCREMENT,
											pe_nombre varchar(16) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
											pe_nivel_acceso int(11) NOT NULL,
											PRIMARY KEY(id_perfil))"; 
							$val6 =  @mysql_query($query_t2);
							//Error 6
							if (!$val6)
								echo "Error en la creaci&oacute;n de la tabla sw_perfil." . mysql_error();
							else {
								//INSERCION DE LOS PERFILES PREDEFINIDOS
								$query_i2 = "INSERT INTO sw_perfil (pe_nombre, pe_nivel_acceso)
												VALUES ('ADMINISTRADOR', 3),('DOCENTE', 2),('SECRETARÍA', 2),('INSPECCIÓN', 2),('AUTORIDAD', 3)";
								$val7 = @mysql_query($query_i2);
								//Error 7
								if (!$val7)
									echo "Error en la inserci&oacute;n de los perfiles predefinidos." . mysql_error();
								else {
									//CREACION DE LA TABLA sw_usuario
									$query_t3 = "CREATE TABLE sw_usuario(
													id_usuario int(11) NOT NULL AUTO_INCREMENT,
													id_periodo_lectivo int(11) NOT NULL,
													id_perfil int(11) NOT NULL,
													us_titulo varchar(5) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
													us_apellidos varchar(32) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
													us_nombres varchar(32) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
													us_fullname varchar(64) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
													us_login varchar(24) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
													us_password varchar(64) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
													PRIMARY KEY(id_usuario),
													KEY id_perfil (id_perfil),
													KEY id_periodo_lectivo (id_periodo_lectivo))"; 
									$val8 =  @mysql_query($query_t3);
									//Error 8
									if (!$val8)
										echo "Error en la creaci&oacute;n de la tabla sw_usuario." . mysql_error();
									else {
										//INSERCION DEL USUARIO ADMINISTRADOR
										$clave = encrypter::encrypt($pass_admin);
										$query_i3 = "INSERT INTO sw_usuario (id_periodo_lectivo, id_perfil, us_titulo, us_apellidos, us_nombres, us_fullname, us_login, us_password)
														VALUES (1, 1, 'ING.', 'PEÑAHERRERA', 'GONZALO', 'GONZALO PEÑAHERRERA', '$usu_admin', '$clave')";
										$val9 = @mysql_query($query_i3);
										//Error 9
										if (!$val9)
											echo "Error en la inserci&oacute;n del usuario administrador." . mysql_error();
										else {
											//CREACION DE LAS CLAVES FORANEAS DE sw_usuario
											$query_fk1 = "ALTER TABLE sw_usuario
  ADD CONSTRAINT sw_usuario_ibfk_2 FOREIGN KEY (id_perfil) REFERENCES sw_perfil (id_perfil),
  ADD CONSTRAINT sw_usuario_ibfk_1 FOREIGN KEY (id_periodo_lectivo) REFERENCES sw_periodo_lectivo (id_periodo_lectivo)";
  											$val_fk1 = @mysql_query($query_fk1);
											if (!$val_fk1) die("Error en la creaci&oacute;n de las claves for&aacute;neas para la tabla sw_usuario. Error: " . mysql_error());

											//CREACION DE LA TABLA sw_menu
											$query_t4 = "CREATE TABLE sw_menu(
															id_menu int(11) NOT NULL AUTO_INCREMENT,
															id_perfil int(11) NOT NULL,
															mnu_texto varchar(32) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
															mnu_enlace varchar(48) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
															mnu_nivel int(11) NOT NULL,
															mnu_orden int(11) NOT NULL,
															PRIMARY KEY(id_menu),
															KEY id_perfil (id_perfil))"; 
											$val10 =  @mysql_query($query_t4);
											//Error 10
											if (!$val10)
												echo "Error en la creaci&oacute;n de la tabla sw_menu." . mysql_error();
											else {
												//INSERCION DE LOS MENUS PREDEFINIDOS
												$query_i4 = "INSERT INTO sw_menu (id_perfil, mnu_texto, mnu_enlace, mnu_nivel, mnu_orden)
																VALUES (1, 'Cambiar la clave', 'cambiar_clave.php', 1, 1),
																(1, 'Administrar perfiles', 'perfil/index.php', 1, 2),
																(1, 'Administrar menus', 'menu/index.php', 1, 3),
																(1, 'Administrar usuarios', 'usuario/index.php', 1, 4),
																(1, 'Cerrar Periodos', 'aportes_evaluacion/view_cerrar_periodos.php', 1, 5),
																(1, 'Log de Actividades', 'actividades/index.php', 1, 6),
																(2, 'Cambiar la clave', 'cambiar_clave.php', 1, 1),
																(2, 'Ingresar calificaciones', '#', 1, 2),
																(2, 'Informes', '#', 1, 3),
																(2, 'Reportes', '#', 1, 4),
																(3, 'Cambiar la clave', 'cambiar_clave.php', 1, 1),
																(3, 'Especificaciones', '#', 1, 2),
																(3, 'Matriculación', 'matriculacion/index.php', 1, 3),
																(3, 'Libretación', '#', 1, 4),
																(3, 'Reporte', '#', 1, 5),
																(3, 'A Excel', '#', 1, 6),
																(3, 'Promoción', 'promocion/index.php', 1, 7),
																(4, 'Cambiar la clave', 'cambiar_clave.php', 1, 1),
																(4, 'Comportamiento', '#', 1, 2),
																(5, 'Cambiar la clave', 'cambiar_clave.php', 1, 1),
																(5, 'Definir Rúbricas', '#', 1, 2)";
												$val11 = @mysql_query($query_i4);
												//Error 11
												if (!$val11)
													echo "Error en la inserci&oacute;n de los men&uacute;es predefinidos." . mysql_error();
												else {
													//CREACION DE LA TABLA sw_submenu
													$query_t5 = "CREATE TABLE sw_submenu(
																	id_submenu int(11) NOT NULL AUTO_INCREMENT,
																	id_menu int(11) NOT NULL,
																	sbmnu_texto varchar(64) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
																	sbmnu_enlace varchar(64) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
																	sbmnu_nivel int(11) NOT NULL,
																	sbmnu_orden int(11) NOT NULL,
																	PRIMARY KEY(id_submenu),
																	KEY id_menu (id_menu))"; 
													$val12 =  @mysql_query($query_t5);
													//Error 12
													if (!$val12)
														echo "Error en la creaci&oacute;n de la tabla sw_submenu." . mysql_error();
													else {
														//INSERCION DE LOS SUBMENUS PREDEFINIDOS
														$query_i5 = "INSERT INTO sw_submenu (id_menu, sbmnu_texto, sbmnu_enlace, sbmnu_nivel, sbmnu_orden)
																		VALUES (8, 'Quimestrales', 'calificaciones/index.php', 2, 1),
																		(8, 'Supletorios', 'calificaciones/supletorios.php', 2, 2),
																		(8, 'Remediales', 'calificaciones/remediales.php', 2, 3),
																		(8, 'De Gracia', 'calificaciones/de_gracia.php', 2, 4),
																		(9, 'Parciales', 'calificaciones/informe_parciales.php', 2, 1),
																		(9, 'Quimestrales', 'calificaciones/informe_quimestral.php', 2, 2),
																		(9, 'Anuales', 'calificaciones/informe_anual.php', 2, 3),
																		(10, 'Quimestrales', 'reportes/por_periodo.php', 2, 1),
																		(10, 'Anuales', 'calificaciones/reporte_anual.php', 2, 2),
																		(12, 'Institución', 'institucion/index.php', 2, 1),
																		(12, 'Periodos Lectivos', 'periodos_lectivos/index.php', 2, 2),
																		(12, 'Tipos de Educación', 'tipo_educacion/index.php', 2, 3),
																		(12, 'Especialidades', 'especialidades/index.php', 2, 4),
																		(12, 'Cursos', 'cursos/index.php', 2, 5),
																		(12, 'Paralelos', 'paralelos/index.php', 2, 6),
																		(12, 'Asignaturas', 'asignaturas/index.php', 2, 7),
																		(12, 'Docentes', 'docentes/index.php', 2, 8),
																		(12, 'Paralelos Asignaturas', 'paralelos_asignaturas/index.php', 2, 9),
																		(14, 'Validar calificaciones', 'calificaciones/validar_calificaciones.php', 2, 1),
																		(14, 'Libretación', 'reportes/libretacion.php', 2, 2),
																		(15, 'Quimestral', 'calificaciones/procesar_promedios.php', 2, 1),
																		(15, 'Por Asignatura', 'calificaciones/por_asignaturas.php', 2, 2),
																		(15, 'Anual', 'calificaciones/promedios_anuales.php', 2, 3),
																		(15, 'De Supletorios', 'calificaciones/reporte_supletorios.php', 2, 4),
																		(15, 'De Remediales', 'calificaciones/reporte_remediales.php', 2, 5),
																		(15, 'De Exámenes de Gracia', 'calificaciones/reporte_de_gracia.php', 2, 6),
																		(16, 'Anuales', 'php_excel/anuales.php', 2, 1),
																		(16, 'Cuadro Final', 'php_excel/cuadro_final.php', 2, 2),
																		(19, 'Quimestral', 'inspeccion/comportamiento.php', 2, 1),
																		(19, 'Anual', 'inspeccion/comportamiento_anual.php', 2, 2),
																		(21, 'Períodos de Evaluación', 'periodos_evaluacion/index.php', 2, 1),
																		(21, 'Aportes de Evaluación', 'aportes_evaluacion/index.php', 2, 2),
																		(21, 'Rúbricas de Evaluación', 'rubricas_evaluacion/index.php', 2, 3)";
														$val12 = @mysql_query($query_i5);
														//Error 12
														if (!$val12)
															echo "Error en la inserci&oacute;n de los submen&uacute;es predefinidos." . mysql_error();
														else {
															//CREACION DEL RESTO DE TABLAS DEL SISTEMA
															$query_t6 = "CREATE TABLE sw_comentario(
																			id_comentario int(11) NOT NULL AUTO_INCREMENT,
																			co_id_usuario int(11) NOT NULL,
																			co_tipo tinyint(4) NOT NULL,
																			co_perfil varchar(16) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
																			co_nombre varchar(64) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
																			co_texto varchar(250) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
																			co_fecha timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

																			PRIMARY KEY(id_comentario))"; 
															$val13 =  @mysql_query($query_t6);
															if (!$val13) echo "Error en la creaci&oacute;n de la tabla sw_comentario." . mysql_error();

															$query_t7 = "CREATE TABLE sw_periodo_evaluacion(
																			id_periodo_evaluacion int(11) NOT NULL AUTO_INCREMENT,
																			id_periodo_lectivo int(11) NOT NULL,
																			pe_nombre varchar(24) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
																			pe_abreviatura varchar(6) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
																			pe_principal tinyint(4) NOT NULL,
																			PRIMARY KEY(id_periodo_evaluacion),
																			KEY id_periodo_lectivo (id_periodo_lectivo))"; 
															$val14 =  @mysql_query($query_t7);
															if (!$val14) echo "Error en la creaci&oacute;n de la tabla sw_periodo_evaluacion." . mysql_error();

															$query_t8 = "CREATE TABLE sw_aporte_evaluacion(
																			id_aporte_evaluacion int(11) NOT NULL AUTO_INCREMENT,
																			id_periodo_evaluacion int(11) NOT NULL,
																			ap_nombre varchar(24) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
																			ap_abreviatura varchar(8) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
																			ap_tipo tinyint(4) NOT NULL,
																			ap_estado varchar(1) NOT NULL,
																			ap_fecha_apertura date NOT NULL,
																			ap_fecha_cierre date NOT NULL,
																			PRIMARY KEY(id_aporte_evaluacion),
																			KEY id_periodo_evaluacion (id_periodo_evaluacion))"; 
															$val15 =  @mysql_query($query_t8);
															if (!$val15) echo "Error en la creaci&oacute;n de la tabla sw_aporte_evaluacion." . mysql_error();

															$query_t9 = "CREATE TABLE sw_rubrica_evaluacion(
																			id_rubrica_evaluacion int(11) NOT NULL AUTO_INCREMENT,
																			id_aporte_evaluacion int(11) NOT NULL,
																			ru_nombre varchar(24) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
																			ru_abreviatura varchar(8) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
																			PRIMARY KEY(id_rubrica_evaluacion),
																			KEY id_aporte_evaluacion (id_aporte_evaluacion))"; 
															$val16 =  @mysql_query($query_t9);
															if (!$val16) echo "Error en la creaci&oacute;n de la tabla sw_rubrica_evaluacion." . mysql_error();

															$query_t10 = "CREATE TABLE sw_institucion(
																			in_nombre varchar(64) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
																			in_direccion varchar(45) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
																			in_telefono1 varchar(12) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
																			in_nom_rector varchar(45) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
																			in_nom_secretario varchar(45) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL)"; 
															$val17 =  @mysql_query($query_t10);
															if (!$val17) echo "Error en la creaci&oacute;n de la tabla sw_institucion." . mysql_error();
															
															$query_t11 = "CREATE TABLE sw_tipo_educacion(
																			id_tipo_educacion int(11) NOT NULL AUTO_INCREMENT,
																			id_periodo_lectivo int(11) NOT NULL,
																			te_nombre varchar(32) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
																			PRIMARY KEY (id_tipo_educacion),
																			KEY id_periodo_lectivo (id_periodo_lectivo))"; 
															$val18 =  @mysql_query($query_t11);
															if (!$val18) echo "Error en la creaci&oacute;n de la tabla sw_tipo_educacion." . mysql_error();

															$query_t12 = "CREATE TABLE sw_especialidad(
																			id_especialidad int(11) NOT NULL AUTO_INCREMENT,
																			id_tipo_educacion int(11) NOT NULL,
																			es_nombre varchar(32) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
																			PRIMARY KEY (id_especialidad),
																			KEY id_tipo_educacion (id_tipo_educacion))"; 
															$val19 =  @mysql_query($query_t12);
															if (!$val19) echo "Error en la creaci&oacute;n de la tabla sw_tipo_educacion." . mysql_error();

															$query_t13 = "CREATE TABLE sw_curso(
																			id_curso int(11) NOT NULL AUTO_INCREMENT,
																			id_especialidad int(11) NOT NULL,
																			cu_nombre varchar(32) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
																			PRIMARY KEY (id_curso),
																			KEY id_especialidad (id_especialidad))"; 
															$val20 =  @mysql_query($query_t13);
															if (!$val20) echo "Error en la creaci&oacute;n de la tabla sw_curso." . mysql_error();

															$query_t14 = "CREATE TABLE sw_paralelo(
																			id_paralelo int(11) NOT NULL AUTO_INCREMENT,
																			id_curso int(11) NOT NULL,
																			pa_nombre varchar(5) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
																			PRIMARY KEY (id_paralelo),
																			KEY id_curso (id_curso))"; 
															$val21 =  @mysql_query($query_t14);
															if (!$val21) echo "Error en la creaci&oacute;n de la tabla sw_paralelo." . mysql_error();

															$query_t15 = "CREATE TABLE sw_asignatura(
																			id_asignatura int(11) NOT NULL AUTO_INCREMENT,
																			as_nombre varchar(64) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
																			as_abreviatura varchar(8) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
																			PRIMARY KEY (id_asignatura))"; 
															$val22 =  @mysql_query($query_t15);
															if (!$val22) echo "Error en la creaci&oacute;n de la tabla sw_asignatura." . mysql_error();

															$query_t16 = "CREATE TABLE sw_paralelo_asignatura(
																			id_paralelo_asignatura int(11) NOT NULL AUTO_INCREMENT,
																			id_paralelo int(11) NOT NULL,
																			id_asignatura int(11) NOT NULL,
																			id_usuario int(11) NOT NULL,
																			id_periodo_lectivo int(11) NOT NULL,
																			PRIMARY KEY (id_paralelo_asignatura),
																			KEY id_paralelo (id_paralelo, id_asignatura),
																			KEY id_asignatura (id_asignatura),
																			KEY id_usuario (id_usuario),
																			KEY id_periodo_lectivo (id_periodo_lectivo))"; 
															$val23 =  @mysql_query($query_t16);
															if (!$val23) echo "Error en la creaci&oacute;n de la tabla sw_paralelo_asignatura." . mysql_error();

															$query_t17 = "CREATE TABLE sw_estudiante(
																			id_estudiante int(11) NOT NULL AUTO_INCREMENT,
																			es_apellidos varchar(32) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
																			es_nombres varchar(32) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
																			es_cedula varchar(10) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
																			es_genero varchar(1) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
																			PRIMARY KEY (id_estudiante))"; 
															$val24 =  @mysql_query($query_t17);
															if (!$val24) echo "Error en la creaci&oacute;n de la tabla sw_estudiante." . mysql_error();

															$query_t18 = "CREATE TABLE sw_estudiante_periodo_lectivo(
																			id_estudiante int(11) NOT NULL,
																			id_periodo_lectivo int(11) NOT NULL,
																			id_paralelo int(11) NOT NULL,
																			es_estado varchar(1) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
																			es_retirado varchar(1) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL DEFAULT 'N',
																			KEY id_estudiante (id_estudiante, id_periodo_lectivo, id_paralelo),
																			KEY id_periodo_lectivo (id_periodo_lectivo),
																			KEY id_paralelo (id_paralelo))"; 
															$val25 =  @mysql_query($query_t18);
															if (!$val25) echo "Error en la creaci&oacute;n de la tabla sw_estudiante_periodo_lectivo." . mysql_error();

															$query_t19 = "CREATE TABLE sw_rubrica_estudiante(
																			id_rubrica_estudiante int(11) NOT NULL AUTO_INCREMENT,
																			id_estudiante int(11) NOT NULL,
																			id_paralelo int(11) NOT NULL,
																			id_asignatura int(11) NOT NULL,
																			id_rubrica_personalizada int(11) NOT NULL,
																			re_calificacion float NOT NULL,
																			re_fec_entrega date NOT NULL,
																			PRIMARY KEY (id_rubrica_estudiante),
																			KEY id_estudiante (id_estudiante),
																			KEY id_paralelo (id_paralelo),
																			KEY id_asignatura (id_asignatura))"; 
															$val26 =  @mysql_query($query_t19);
															if (!$val26) echo "Error en la creaci&oacute;n de la tabla sw_rubrica_estudiante." . mysql_error();

															$query_t20 = "CREATE TABLE sw_club(
																			id_club int(11) NOT NULL AUTO_INCREMENT,
																			cl_nombre varchar(32) NOT NULL,
																			cl_abreviatura varchar(6) NOT NULL,
																			cl_carga_horaria int(11) NOT NULL
																			PRIMARY KEY (id_club))"; 
															$val27 =  @mysql_query($query_t20);
															if (!$val27) echo "Error en la creaci&oacute;n de la tabla sw_club." . mysql_error();

															//CREACION DE LOS TRIGGERS ASOCIADOS
															$query_tg1 = "CREATE TRIGGER tg_insert_sw_rubrica_estudiante AFTER INSERT ON sw_rubrica_estudiante
															 FOR EACH ROW BEGIN
																
																DECLARE IdUsuario INT DEFAULT 0;
															
																-- Aqui voy a insertar el registro correspondiente en la tabla sw_rubrica_estudiante_log
															
																SET IdUsuario = (SELECT id_usuario FROM sw_paralelo_asignatura WHERE id_paralelo = new.id_paralelo AND id_asignatura = new.id_asignatura);
																	
																INSERT INTO sw_rubrica_estudiante_log 
																	SET id_rubrica_estudiante = new.id_rubrica_estudiante,
																		id_estudiante = new.id_estudiante,
																		id_paralelo = new.id_paralelo,
																		id_asignatura = new.id_asignatura,
																		id_rubrica_personalizada = new.id_rubrica_personalizada,
																		id_usuario = IdUsuario,
																		re_calificacion_nueva = new.re_calificacion,
																		rl_accion = 'INSERCION';
																
															END;";
															$val27 =  @mysql_query($query_tg1);
															if (!$val27) echo "Error en la creaci&oacute;n del trigger tg_insert_sw_rubrica_estudiante." . mysql_error();

															$query_tg2 = "CREATE TRIGGER tg_update_sw_rubrica_estudiante AFTER UPDATE ON sw_rubrica_estudiante
															 FOR EACH ROW BEGIN
																
																DECLARE IdUsuario INT DEFAULT 0;
															
																-- Aqui voy a insertar el registro correspondiente en la tabla sw_rubrica_estudiante_log
															
																SET IdUsuario = (SELECT id_usuario FROM sw_paralelo_asignatura WHERE id_paralelo = new.id_paralelo AND id_asignatura = new.id_asignatura);
																	
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
																
															END;";
															$val28 =  @mysql_query($query_tg2);
															if (!$val28) echo "Error en la creaci&oacute;n del trigger tg_update_sw_rubrica_estudiante." . mysql_error();

															$query_t20 = "CREATE TABLE sw_rubrica_estudiante_log(
																			id_rubrica_estudiante int(11) NOT NULL,
																			id_estudiante int(11) NOT NULL,
																			id_paralelo int(11) NOT NULL,
																			id_asignatura int(11) NOT NULL,
																			id_rubrica_personalizada int(11) NOT NULL,
																			id_usuario int(11) NOT NULL,
																			re_calificacion_nueva float DEFAULT '0',
																			re_calificacion_antigua float DEFAULT '0',
																			re_fecha_modificacion timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
																			rl_accion varchar(32) NOT NULL)"; 
															$val27 =  @mysql_query($query_t20);
															if (!$val27) echo "Error en la creaci&oacute;n de la tabla sw_rubrica_estudiante_log." . mysql_error();

															$query_t21 = "CREATE TABLE sw_comportamiento(
																			id_paralelo int(11) NOT NULL,
																			id_estudiante int(11) NOT NULL,
																			id_periodo_evaluacion int(11) NOT NULL,
																			id_indice_evaluacion int(11) NOT NULL)"; 
															$val28 =  @mysql_query($query_t21);
															if (!$val28) echo "Error en la creaci&oacute;n de la tabla sw_comportamiento." . mysql_error();

															$query_t22 = "CREATE TABLE sw_indice_evaluacion(
																			id_indice_evaluacion int(11) NOT NULL AUTO_INCREMENT,
																			valores_t float NOT NULL,
																			cum_norma_t float NOT NULL,
																			pun_asiste_t float NOT NULL,
																			presentacion_t float NOT NULL,
																			valores_i float NOT NULL,
																			cum_norma_i float NOT NULL,
																			pun_asiste_i float NOT NULL,
																			presentacion_i float NOT NULL,
																			total float NOT NULL,
																			promedio float NOT NULL,
																			equivalencia varchar(1) COLLATE latin1_spanish_ci NOT NULL,
																			PRIMARY KEY (id_indice_evaluacion))"; 
															$val29 =  @mysql_query($query_t22);
															if (!$val29) echo "Error en la creaci&oacute;n de la tabla sw_indice_evaluacion." . mysql_error();

															$query_t23 = "CREATE TABLE sw_escala_calificaciones(
																			id_escala_calificaciones int(11) NOT NULL AUTO_INCREMENT,
																			id_periodo_lectivo int(11) NOT NULL,
																			ec_cualitativa varchar(64) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
																			ec_cuantitativa varchar(64) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
																			ec_nota_minima float NOT NULL,
																			ec_nota_maxima float NOT NULL,
																			ec_orden  tinyint(4) NOT NULL,
																			PRIMARY KEY (id_escala_calificaciones))"; 
															$val30 =  @mysql_query($query_t23);
															if (!$val30) echo "Error en la creaci&oacute;n de la tabla sw_escala_calificaciones." . mysql_error();

															//INSERCION DE LAS ESCALAS PREDEFINIDAS
															$query_i6 = "INSERT INTO sw_escala_calificaciones (id_periodo_lectivo, ec_cualitativa, ec_cuantitativa, ec_nota_minima, ec_nota_maxima, ec_orden)
																		VALUES (1, 'Domina los aprendizajes requeridos.', '9.00 - 10.00', 9, 10, 1),
																		(1, 'Alcanza los aprendizajes requeridos.', '7.00 - 8.99', 7, 8.99, 2),
																		(1, 'Está próximo a alcanzar los aprendizajes requeridos.', '4.01 - 6.99', 4.01, 6.99, 3),
																		(1, 'No alcanza los aprendizajes requeridos.', '<= 4', 0, 4, 4)";
															$val31 = @mysql_query($query_i6);
															if (!$val31) echo "Error en la inserci&oacute;n de las escalas de calificaciones predefinidas." . mysql_error();
															
															$query_t24 = "CREATE TABLE sw_recomendaciones(
																			id_escala_calificaciones int(11) NOT NULL,
																			id_paralelo_asignatura int(11) NOT NULL,
																			id_aporte_evaluacion int(11) NOT NULL,
																			re_recomendaciones varchar(255) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
																			re_plan_de_mejora varchar(255) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
																			KEY id_escala_calificaciones (id_escala_calificaciones, id_paralelo_asignatura),
																			KEY id_paralelo_asignatura (id_paralelo_asignatura))"; 
															$val31 =  @mysql_query($query_t24);
															if (!$val31) echo "Error en la creaci&oacute;n de la tabla sw_recomendaciones." . mysql_error();

															$query_t25 = "CREATE TABLE sw_recomendaciones_anuales(
																			id_escala_calificaciones int(11) NOT NULL,
																			id_paralelo_asignatura int(11) NOT NULL,
																			id_periodo_lectivo int(11) NOT NULL,
																			re_plan_de_mejora_anual varchar(255) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL)"; 
															$val32 =  @mysql_query($query_t25);
															if (!$val32) echo "Error en la creaci&oacute;n de la tabla sw_recomendaciones_anuales." . mysql_error();

															$query_t26 = "CREATE TABLE sw_recomendaciones_quimestrales(
																			id_escala_calificaciones int(11) NOT NULL,
																			id_paralelo_asignatura int(11) NOT NULL,
																			id_periodo_evaluacion int(11) NOT NULL,
																			re_plan_de_mejora_quimestral varchar(250) DEFAULT NULL,
																			KEY fk_sw_escala_calificaciones_idx (id_escala_calificaciones),
																			KEY fk_sw_paralelo_asignatura_idx (id_paralelo_asignatura),
																			KEY fk_sw_periodo_evaluacion_idx (id_periodo_evaluacion))"; 
															$val33 =  @mysql_query($query_t26);
															if (!$val33) echo "Error en la creaci&oacute;n de la tabla sw_recomendaciones_quimestrales." . mysql_error();
			
//CREACION DE LAS FUNCIONES ALMACENADAS
$query_f1 = "CREATE DEFINER=colegion_1@localhost FUNCTION aprueba_todas_asignaturas(
IdPeriodoLectivo INT, 
IdEstudiante INT, 
IdParalelo INT) 
RETURNS tinyint(1)
NO SQL
BEGIN
	DECLARE IdAsignatura INT;
	DECLARE aprueba BOOL DEFAULT TRUE; -- variable de salida de la funcion
	DECLARE done INT DEFAULT 0;
	DECLARE promedio FLOAT DEFAULT 0;

	-- Aqui determino si el estudiante aprueba en todas las asignaturas
	DECLARE cAsignaturas CURSOR FOR
		SELECT id_asignatura 
		  FROM sw_paralelo_asignatura 
		 WHERE id_paralelo = IdParalelo;
	
	DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done = 1;

	OPEN cAsignaturas;

	Lazo: LOOP
		FETCH cAsignaturas INTO IdAsignatura;
		IF done THEN
			CLOSE cAsignaturas;
			LEAVE Lazo;
		END IF;
		SET promedio = (SELECT calcular_promedio_anual(IdPeriodoLectivo, IdEstudiante, IdParalelo, IdAsignatura));
		IF promedio < 7 THEN
			SET done = 1;
			SET aprueba = FALSE;
		END IF;
	END LOOP Lazo;

	RETURN aprueba;

END;";
$val34 =  @mysql_query($query_f1);
if (!$val34) echo "Error en la creaci&oacute;n de la funci&oacute;n aprueba_todas_asignaturas." . mysql_error();

$query_f2 = "CREATE DEFINER=colegion_1@localhost FUNCTION aprueba_todos_remediales(
IdPeriodoLectivo INT, 
IdEstudiante INT, 
IdParalelo INT) 
RETURNS tinyint(4)
NO SQL
BEGIN
	DECLARE IdAsignatura INT;
	DECLARE aprueba BOOL DEFAULT TRUE; -- variable de salida de la funcion
	DECLARE done INT DEFAULT 0;
	DECLARE promedio FLOAT DEFAULT 0;
	DECLARE examen_supletorio FLOAT DEFAULT 0;
	DECLARE examen_remedial FLOAT DEFAULT 0;

	-- Aqui determino si el estudiante aprueba en todas las asignaturas
	DECLARE cAsignaturas CURSOR FOR
		SELECT id_asignatura 
		  FROM sw_paralelo_asignatura 
		 WHERE id_paralelo = IdParalelo;
	
	DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done = 1;

	OPEN cAsignaturas;

	Lazo: LOOP
		FETCH cAsignaturas INTO IdAsignatura;
		IF done THEN
			CLOSE cAsignaturas;
			LEAVE Lazo;
		END IF;
		SET promedio = (SELECT calcular_promedio_anual(IdPeriodoLectivo,IdEstudiante,IdParalelo,IdAsignatura));
		IF promedio > 5 AND promedio < 7 THEN -- tiene que rendir el examen supletorio
			SET examen_supletorio = (SELECT calcular_examen_supletorio(IdPeriodoLectivo,IdEstudiante,IdParalelo,IdAsignatura,2));
			IF examen_supletorio < 7 THEN
				-- tiene que rendir el examen remedial
				SET examen_remedial = (SELECT calcular_examen_supletorio(IdPeriodoLectivo,IdEstudiante,IdParalelo,IdAsignatura,3));
				IF examen_remedial < 7 THEN
					SET done = 1;
					SET aprueba = FALSE;
				END IF;
			END IF;
		ELSE 
			IF promedio > 0 AND promedio < 5 THEN -- tiene que rendir el examen remedial
				SET examen_remedial = (SELECT calcular_examen_supletorio(IdPeriodoLectivo,IdEstudiante,IdParalelo,IdAsignatura,3));
				IF examen_remedial < 7 THEN
					SET done = 1;
					SET aprueba = FALSE;
				END IF;
			END IF;
		END IF;
	END LOOP Lazo;

	RETURN aprueba;

END;";
$val35 =  @mysql_query($query_f2);
if (!$val35) echo "Error en la creaci&oacute;n de la funci&oacute;n aprueba_todos_remediales." . mysql_error();

$query_f3 = "CREATE DEFINER=colegion_1@localhost FUNCTION aprueba_todos_supletorios(
IdPeriodoLectivo INT, 
IdEstudiante INT, 
IdParalelo INT) 
RETURNS tinyint(4)
NO SQL
BEGIN
	DECLARE IdAsignatura INT;
	DECLARE aprueba BOOL DEFAULT TRUE; -- variable de salida de la funcion
	DECLARE done INT DEFAULT 0;
	DECLARE promedio FLOAT DEFAULT 0;
	DECLARE examen_supletorio FLOAT DEFAULT 0;

	-- Aqui determino si el estudiante aprueba en todas las asignaturas
	DECLARE cAsignaturas CURSOR FOR
		SELECT id_asignatura 
		  FROM sw_paralelo_asignatura 
		 WHERE id_paralelo = IdParalelo;
	
	DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done = 1;

	OPEN cAsignaturas;

	Lazo: LOOP
		FETCH cAsignaturas INTO IdAsignatura;
		IF done THEN
			CLOSE cAsignaturas;
			LEAVE Lazo;
		END IF;
		SET promedio = (SELECT calcular_promedio_anual(IdPeriodoLectivo,IdEstudiante,IdParalelo,IdAsignatura));
		IF promedio > 5 AND promedio < 7 THEN -- tiene que rendir el examen supletorio
			SET examen_supletorio = (SELECT calcular_examen_supletorio(IdPeriodoLectivo,IdEstudiante,IdParalelo,IdAsignatura,2));
			IF examen_supletorio < 7 THEN
				SET done = 1;
				SET aprueba = FALSE;
			END IF;
		ELSE IF promedio < 5 THEN -- tiene que rendir el examen supletorio
				SET done = 1;
				SET aprueba = FALSE;
			 END IF;
		END IF;
	END LOOP Lazo;

	RETURN aprueba;
	
END;";
$val36 =  @mysql_query($query_f3);
if (!$val36) echo "Error en la creaci&oacute;n de la funci&oacute;n aprueba_todos_supletorios." . mysql_error();

$query_f4 = "CREATE DEFINER=colegion_1@localhost FUNCTION calcular_examen_supletorio(
IdPeriodoLectivo INT, 
IdEstudiante INT, 
IdParalelo INT, 
IdAsignatura INT, 
PePrincipal INT) 
RETURNS float
NO SQL
BEGIN
	DECLARE IdRubricaEvaluacion INT DEFAULT 0;
	DECLARE examen_supletorio FLOAT DEFAULT 0; -- variable de salida de la funcion

	-- Aqui obtengo el valor del examen supletorio, si existe
	SET IdRubricaEvaluacion = (SELECT id_rubrica_evaluacion 
								   FROM sw_rubrica_evaluacion r, 
									    sw_aporte_evaluacion a, 
										sw_periodo_evaluacion p 
								  WHERE r.id_aporte_evaluacion = a.id_aporte_evaluacion 
									AND a.id_periodo_evaluacion = p.id_periodo_evaluacion 
									AND p.pe_principal = PePrincipal AND p.id_periodo_lectivo = IdPeriodoLectivo);

	SET examen_supletorio = (SELECT re_calificacion
							   FROM sw_rubrica_estudiante 
							  WHERE id_estudiante = IdEstudiante 
								AND id_paralelo = IdParalelo 
								AND id_asignatura = IdAsignatura 
								AND id_rubrica_personalizada = IdRubricaEvaluacion);
	
	RETURN IFNULL(examen_supletorio, 0);
END;";
$val37 =  @mysql_query($query_f4);
if (!$val37) echo "Error en la creaci&oacute;n de la funci&oacute;n calcular_examen_supletorio." . mysql_error();

$query_f5 = "CREATE DEFINER=colegion_1@localhost FUNCTION calcular_promedio_anual(
IdPeriodoLectivo INT, 
IdEstudiante INT, 
IdParalelo INT, 
IdAsignatura INT) 
RETURNS float
NO SQL
BEGIN
	DECLARE done INT DEFAULT 0;
	DECLARE promedio_anual FLOAT; -- variable de salida de la funcion
	DECLARE promedio_quimestre FLOAT;
	DECLARE IdPeriodoEvaluacion INT;
	DECLARE Suma FLOAT DEFAULT 0;
	DECLARE Contador INT DEFAULT 0;
	
	-- Aqui calculo el promedio anual utilizando un cursor
	DECLARE cPeriodosEvaluacion CURSOR FOR
		SELECT id_periodo_evaluacion
		  FROM sw_periodo_evaluacion 
		 WHERE id_periodo_lectivo = IdPeriodoLectivo
		   AND pe_principal = 1;

	DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done = 1;

	OPEN cPeriodosEvaluacion;

	Lazo: LOOP
		FETCH cPeriodosEvaluacion INTO IdPeriodoEvaluacion;
		IF done THEN
			CLOSE cPeriodosEvaluacion;
			LEAVE Lazo;
		END IF;
		SET promedio_quimestre = (SELECT calcular_promedio_quimestre(IdPeriodoEvaluacion,IdEstudiante,IdParalelo,IdAsignatura));
		SET Suma = Suma + promedio_quimestre;
		SET Contador = Contador + 1;
	END LOOP Lazo;

	SELECT Suma / Contador INTO promedio_anual;

	RETURN promedio_anual;
END;";
$val38 =  @mysql_query($query_f5);
if (!$val38) echo "Error en la creaci&oacute;n de la funci&oacute;n calcular_promedio_anual." . mysql_error();

$query_f6 = "CREATE DEFINER=colegion_1@localhost FUNCTION calcular_promedio_aporte(
IdAporteEvaluacion INT, 
IdEstudiante INT, 
IdParalelo INT, 
IdAsignatura INT) 
RETURNS float
READS SQL DATA
DETERMINISTIC
BEGIN
	DECLARE done INT DEFAULT 0;
	DECLARE promedio_aporte FLOAT; 	
	DECLARE IdRubricaEvaluacion INT;
	DECLARE ReCalificacion FLOAT;
	DECLARE Suma FLOAT DEFAULT 0;
	DECLARE Contador INT DEFAULT 0;

	DECLARE cRubricasEvaluacion CURSOR FOR
	SELECT id_rubrica_evaluacion
	  FROM sw_rubrica_evaluacion
	 WHERE id_aporte_evaluacion = IdAporteEvaluacion;

	DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done = 1;

	OPEN cRubricasEvaluacion;

	Lazo1: LOOP
		FETCH cRubricasEvaluacion INTO IdRubricaEvaluacion;
		IF done THEN
			CLOSE cRubricasEvaluacion;
			LEAVE Lazo1;
		END IF;

		SET ReCalificacion = (
		SELECT re_calificacion
		  FROM sw_rubrica_estudiante
		 WHERE id_estudiante = IdEstudiante
		   AND id_paralelo = IdParalelo
		   AND id_asignatura = IdAsignatura
		   AND id_rubrica_personalizada = IdRubricaEvaluacion);
		   
		SET ReCalificacion = IFNULL(re_calificacion, 0);

		SET Suma = Suma + ReCalificacion;
		SET Contador = Contador + 1;
	END LOOP Lazo1;

	SELECT Suma / Contador INTO promedio_aporte;
	
	RETURN promedio_aporte;
END;";
$val39 =  @mysql_query($query_f6);
if (!$val39) echo "Error en la creaci&oacute;n de la funci&oacute;n calcular_promedio_aporte." . mysql_error();

$query_f7 = "CREATE DEFINER=colegion_1@localhost FUNCTION calcular_promedio_final(
IdPeriodoLectivo INT, 
IdEstudiante INT, 
IdParalelo INT, 
IdAsignatura INT) 
RETURNS float
NO SQL
BEGIN
	DECLARE promedio_final FLOAT DEFAULT 0; 	
	DECLARE examen_supletorio FLOAT DEFAULT 0;
	DECLARE examen_remedial FLOAT DEFAULT 0;
	DECLARE examen_de_gracia FLOAT DEFAULT 0;

	SET promedio_final = (SELECT calcular_promedio_anual(IdPeriodoLectivo,IdEstudiante,IdParalelo,IdAsignatura));
	IF promedio_final > 5 AND promedio_final < 7 THEN 		
		SET examen_supletorio = (SELECT calcular_examen_supletorio(IdPeriodoLectivo,IdEstudiante,IdParalelo,IdAsignatura,2));
		IF examen_supletorio >= 7 THEN
			SET promedio_final = 7;
		ELSE
			SET examen_remedial = (SELECT calcular_examen_supletorio(IdPeriodoLectivo,IdEstudiante,IdParalelo,IdAsignatura,3));
			IF examen_remedial >= 7 THEN
				SET promedio_final = 7;
			ELSE
				SET examen_de_gracia = (SELECT calcular_examen_supletorio(IdPeriodoLectivo,IdEstudiante,IdParalelo,IdAsignatura,4));
				IF examen_de_gracia >= 7 THEN
					SET promedio_final = 7;
				END IF;
			END IF;
		END IF;
	ELSE 
		IF promedio_final > 0 AND promedio_final < 5 THEN
			SET examen_remedial = (SELECT calcular_examen_supletorio(IdPeriodoLectivo,IdEstudiante,IdParalelo,IdAsignatura,3));
			IF examen_remedial >= 7 THEN
				SET promedio_final = 7;
			ELSE
				SET examen_de_gracia = (SELECT calcular_examen_supletorio(IdPeriodoLectivo,IdEstudiante,IdParalelo,IdAsignatura,4));
				IF examen_de_gracia >= 7 THEN
					SET promedio_final = 7;
				END IF;
			END IF;
		END IF;
	END IF;

	RETURN promedio_final;

END;";
$val40 =  @mysql_query($query_f7);
if (!$val40) echo "Error en la creaci&oacute;n de la funci&oacute;n calcular_promedio_final." . mysql_error();

$query_f8 = "CREATE DEFINER=colegion_1@localhost FUNCTION calcular_promedio_general(
IdPeriodoLectivo INT, 
IdEstudiante INT, 
IdParalelo INT) 
RETURNS float
NO SQL
BEGIN
	DECLARE done INT DEFAULT 0;
	DECLARE promedio_general float DEFAULT 0; -- variable de salida de la funcion
	DECLARE suma FLOAT DEFAULT 0;
	DECLARE contador INT DEFAULT 0;
	DECLARE IdAsignatura INT;

	-- Aqui determino si el estudiante aprueba en todas las asignaturas
	DECLARE cAsignaturas CURSOR FOR
		SELECT id_asignatura 
		  FROM sw_paralelo_asignatura 
		 WHERE id_paralelo = IdParalelo;
	
	DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done = 1;

	OPEN cAsignaturas;

	Lazo: LOOP
		FETCH cAsignaturas INTO IdAsignatura;
		IF done THEN
			CLOSE cAsignaturas;
			LEAVE Lazo;
		END IF;
		SET suma = suma + (SELECT calcular_promedio_anual(IdPeriodoLectivo,IdEstudiante,IdParalelo,IdAsignatura));
		SET contador = contador + 1;
	END LOOP Lazo;

	SET promedio_general = suma / contador;

	RETURN promedio_general;
END;";
$val41 =  @mysql_query($query_f8);
if (!$val41) echo "Error en la creaci&oacute;n de la funci&oacute;n calcular_promedio_general." . mysql_error();

$query_f9 = "CREATE DEFINER=colegion_1@localhost FUNCTION calcular_promedio_quimestre(
IdPeriodoEvaluacion INT, 
IdEstudiante INT, 
IdParalelo INT, 
IdAsignatura INT) 
RETURNS float
NO SQL
BEGIN
    DECLARE done INT DEFAULT 0;
    DECLARE promedio_quimestre FLOAT; -- variable de salida de la funcion
    DECLARE promedio_aporte FLOAT;
    DECLARE IdAporteEvaluacion INT;
    DECLARE Suma FLOAT DEFAULT 0;
    DECLARE Contador INT DEFAULT 0;
    DECLARE Total_Aportes INT DEFAULT 0;
    DECLARE Examen FLOAT DEFAULT 0;
    DECLARE Promedio FLOAT DEFAULT 0;
    
    -- Declaracion del cursor que se va a utilizar
    DECLARE cAportesEvaluacion CURSOR FOR
    	SELECT id_aporte_evaluacion
          FROM sw_aporte_evaluacion
         WHERE id_periodo_evaluacion = IdPeriodoEvaluacion;
         
    DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done = 1;
    
    SET Total_Aportes = (SELECT COUNT(*) FROM sw_aporte_evaluacion WHERE id_periodo_evaluacion = IdPeriodoEvaluacion);
    
    OPEN cAportesEvaluacion;
    
    REPEAT
    	FETCH cAportesEvaluacion INTO IdAporteEvaluacion;
        
        SELECT calcular_promedio_aporte (IdAporteEvaluacion, IdEstudiante, IdParalelo, IdAsignatura) INTO promedio_aporte;
        
        SET Contador = Contador + 1;
        
        IF Contador <= Total_Aportes - 1 THEN
        	SET Suma = Suma + promedio_aporte;
        ELSE
        	SET Examen = promedio_aporte;
        END IF;
    UNTIL done END REPEAT;
    
    CLOSE cAportesEvaluacion;
    
    SET Promedio = Suma / (Total_Aportes - 1);
    
    SELECT 0.8 * Promedio + 0.2 * Examen INTO promedio_quimestre;
    
    RETURN promedio_quimestre;
    
END;";
$val42 =  @mysql_query($query_f9);
if (!$val42) echo "Error en la creaci&oacute;n de la funci&oacute;n calcular_promedio_quimestre." . mysql_error();

$query_f10 = "CREATE DEFINER=colegion_1@localhost FUNCTION contar_remediales_no_aprobados(
IdPeriodoLectivo INT, 
IdEstudiante INT, 
IdParalelo INT) 
RETURNS int(11)
NO SQL
BEGIN
	DECLARE IdAsignatura INT;
	DECLARE contador INT DEFAULT 0; -- variable de salida de la funcion
	DECLARE done INT DEFAULT 0;
	DECLARE promedio FLOAT DEFAULT 0;
	DECLARE examen_supletorio FLOAT DEFAULT 0;
	DECLARE examen_remedial FLOAT DEFAULT 0;

	-- Aqui determino si el estudiante aprueba en todas las asignaturas
	DECLARE cAsignaturas CURSOR FOR
		SELECT id_asignatura 
		  FROM sw_paralelo_asignatura 
		 WHERE id_paralelo = IdParalelo;
	
	DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done = 1;

	OPEN cAsignaturas;

	Lazo: LOOP
		FETCH cAsignaturas INTO IdAsignatura;
		IF done THEN
			CLOSE cAsignaturas;
			LEAVE Lazo;
		END IF;
		SET promedio = (SELECT calcular_promedio_anual(IdPeriodoLectivo,IdEstudiante,IdParalelo,IdAsignatura));
		IF promedio > 5 AND promedio < 7 THEN -- tiene que rendir el examen supletorio
			SET examen_supletorio = (SELECT calcular_examen_supletorio(IdPeriodoLectivo,IdEstudiante,IdParalelo,IdAsignatura,2));
			IF examen_supletorio < 7 THEN
				-- tiene que rendir el examen remedial
				SET examen_remedial = (SELECT calcular_examen_supletorio(IdPeriodoLectivo,IdEstudiante,IdParalelo,IdAsignatura,3));
				IF examen_remedial < 7 THEN
					SET contador = contador + 1;
				END IF;
			END IF;
		ELSE 
			IF promedio > 0 AND promedio < 5 THEN -- tiene que rendir el examen remedial
				SET examen_remedial = (SELECT calcular_examen_supletorio(IdPeriodoLectivo,IdEstudiante,IdParalelo,IdAsignatura,3));
				IF examen_remedial < 7 THEN
					SET contador = contador + 1;
				END IF;
			END IF;
		END IF;
	END LOOP Lazo;

	RETURN contador;

END;";
$val43 =  @mysql_query($query_f10);
if (!$val43) echo "Error en la creaci&oacute;n de la funci&oacute;n contar_remediales_no_aprobados." . mysql_error();

$query_f11 = "CREATE DEFINER=colegion_1@localhost FUNCTION determinar_asignatura_de_gracia(
IdPeriodoLectivo INT, 
IdEstudiante INT, 
IdParalelo INT) 
RETURNS int(11)
NO SQL
BEGIN
	DECLARE IdAsignatura INT;
	DECLARE vid_asignatura INT DEFAULT 0; -- variable de salida de la funcion
	DECLARE contador INT DEFAULT 0;
	DECLARE done INT DEFAULT 0;
	DECLARE promedio FLOAT DEFAULT 0;
	DECLARE examen_supletorio FLOAT DEFAULT 0;
	DECLARE examen_remedial FLOAT DEFAULT 0;

	-- Aqui determino si el estudiante aprueba en todas las asignaturas
	DECLARE cAsignaturas CURSOR FOR
		SELECT id_asignatura 
		  FROM sw_paralelo_asignatura 
		 WHERE id_paralelo = IdParalelo;
	
	DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done = 1;

	SET contador = (SELECT contar_remediales_no_aprobados(IdPeriodoLectivo,IdEstudiante,IdParalelo));

	IF contador = 1 THEN

		OPEN cAsignaturas;

		Lazo: LOOP
			FETCH cAsignaturas INTO IdAsignatura;
			IF done THEN
				CLOSE cAsignaturas;
				LEAVE Lazo;
			END IF;
			SET promedio = (SELECT calcular_promedio_anual(IdPeriodoLectivo,IdEstudiante,IdParalelo,IdAsignatura));
			IF promedio > 5 AND promedio < 7 THEN -- tiene que rendir el examen supletorio
				SET examen_supletorio = (SELECT calcular_examen_supletorio(IdPeriodoLectivo,IdEstudiante,IdParalelo,IdAsignatura,2));
				IF examen_supletorio < 7 THEN
					-- tiene que rendir el examen remedial
					SET examen_remedial = (SELECT calcular_examen_supletorio(IdPeriodoLectivo,IdEstudiante,IdParalelo,IdAsignatura,3));
					IF examen_remedial < 7 THEN
						SET vid_asignatura = IdAsignatura;
                        SET done = 1;
					END IF;
				END IF;
			ELSE 
				IF promedio > 0 AND promedio < 5 THEN -- tiene que rendir el examen remedial
					SET examen_remedial = (SELECT calcular_examen_supletorio(IdPeriodoLectivo,IdEstudiante,IdParalelo,IdAsignatura,3));
					IF examen_remedial < 7 THEN
						SET vid_asignatura = IdAsignatura;
                        SET done = 1;
					END IF;
				END IF;
			END IF;
		END LOOP Lazo;

	END IF;

	RETURN vid_asignatura;

END;";
$val44 =  @mysql_query($query_f11);
if (!$val44) echo "Error en la creaci&oacute;n de la funci&oacute;n determinar_asignatura_de_gracia." . mysql_error();

$query_f12 = "CREATE DEFINER=colegion_1@localhost FUNCTION es_promocionado(
IdEstudiante INT, 
IdPeriodoLectivo INT) 
RETURNS tinyint(4)
NO SQL
BEGIN
	DECLARE aprueba BOOL DEFAULT TRUE; -- variable de salida de la funcion
	DECLARE IdParalelo INT DEFAULT 0;
	DECLARE promedio FLOAT DEFAULT 0;
	DECLARE done INT DEFAULT 0;
	DECLARE IdAsignatura INT;

	DECLARE cAsignaturas CURSOR FOR
	 SELECT id_asignatura
	   FROM sw_paralelo_asignatura
	  WHERE id_paralelo = IdParalelo;

	DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done = 1;

	SET IdParalelo = (SELECT id_paralelo
						FROM sw_estudiante_periodo_lectivo
					   WHERE id_estudiante = IdEstudiante
					     AND id_periodo_lectivo = IdPeriodoLectivo);

	OPEN cAsignaturas;

	Lazo: LOOP
		FETCH cAsignaturas INTO IdAsignatura;
		IF done THEN
			CLOSE cAsignaturas;
			LEAVE Lazo;
		END IF;
		SET promedio = (SELECT calcular_promedio_final(IdPeriodoLectivo,IdEstudiante,IdParalelo,IdAsignatura));
		IF promedio < 7 THEN
			SET done = 1;
			SET aprueba = FALSE;
		END IF;
	END LOOP Lazo;

	RETURN aprueba;

END;";
$val45 =  @mysql_query($query_f12);
if (!$val45) echo "Error en la creaci&oacute;n de la funci&oacute;n es_promocionado." . mysql_error();

//CREACION DE LOS PROCEDIMIENTOS ALMACENADOS

$query_p1 = "CREATE DEFINER=colegion_1@localhost PROCEDURE sp_abrir_periodos()
    NO SQL
BEGIN
	DECLARE done INT DEFAULT 0;
	DECLARE IdAporteEvaluacion INT;

	DECLARE cAportesEvaluacion CURSOR FOR
		SELECT id_aporte_evaluacion
		  FROM sw_aporte_evaluacion;

	DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done = 1;

	OPEN cAportesEvaluacion;

	REPEAT
		FETCH cAportesEvaluacion INTO IdAporteEvaluacion;
		UPDATE sw_aporte_evaluacion
		   SET ap_estado = 'A'
		 WHERE id_aporte_evaluacion = IdAporteEvaluacion
		   AND ap_fecha_apertura = (SELECT curdate());
	UNTIL done END REPEAT;

	CLOSE cAportesEvaluacion;
END;";
$val46 =  @mysql_query($query_p1);
if (!$val46) echo "Error en la creaci&oacute;n del procedimiento almacenado sp_abrir_periodos." . mysql_error();

$query_p2 = "CREATE DEFINER=colegion_1@localhost PROCEDURE sp_actualizar_rubrica_estudiante(
IN IdEstudiante INT, 
IN IdParalelo INT, 
IN IdAsignatura INT, 
IN IdRubricaPersonalizada INT, 
IN ReCalificacion FLOAT, 
IN IdAporteEvaluacion INT, 
IN AeCalificacion INT)
    NO SQL
BEGIN

	UPDATE sw_rubrica_estudiante 
	   SET re_calificacion = ReCalificacion
	 WHERE id_estudiante = IdEstudiante
       AND id_paralelo = IdParalelo
       AND id_asignatura = IdAsignatura
	   AND id_rubrica_personalizada = IdRubricaPersonaliza;

	UPDATE sw_aporte_estudiante
	   SET ae_calificacion = AeCalificacion
	 WHERE id_estudiante = IdEstudiante
       AND id_paralelo = IdParalelo
       AND id_asignatura = IdAsignatura
	   AND id_aporte_evaluacion = IdAporteEvaluacion;

END;";
$val47 =  @mysql_query($query_p2);
if (!$val47) echo "Error en la creaci&oacute;n del procedimiento almacenado sp_actualizar_rubrica_estudiante." . mysql_error();

$query_p3 = "CREATE DEFINER=colegion_1@localhost PROCEDURE sp_actualizar_usuario(
IN IdUsuario INT, 
IN IdPerfil INT, 
IN UsTitulo VARCHAR(5), 
IN UsApellidos VARCHAR(32), 
IN UsNombres VARCHAR(32), 
IN UsFullname VARCHAR(64), 
IN UsLogin VARCHAR(24), 
IN UsPassword VARCHAR(64))
    NO SQL
UPDATE sw_usuario 
   SET id_perfil = IdPerfil,
	   us_titulo = UsTitulo,
	   us_apellidos = UsApellidos,
	   us_nombres = UsNombres,
	   us_fullname = UsFullname,
	   us_login = UsLogin,
	   us_password = UsPassword
 WHERE id_usuario = IdUsuario;";
$val48 =  @mysql_query($query_p3);
if (!$val48) echo "Error en la creaci&oacute;n del procedimiento almacenado sp_actualizar_usuario." . mysql_error();

$query_p4 = "CREATE DEFINER=colegion_1@localhost PROCEDURE sp_buscar_estudiantes_antiguos(
IN IdPeriodoLectivo INT, 
IN PatronBusqueda VARCHAR(32))
    NO SQL
BEGIN

	-- SET varPatron = CONCAT(PatronBusqueda,'%');

	-- Cursor que se va a utilizar en la busqueda de estudiantes antiguos
	SELECT e.id_estudiante,
		   es_apellidos,
		   es_nombres,
		   cu_nombre,
		   pa_nombre,
		   (SELECT es_promocionado(e.id_estudiante, IdPeriodoLectivo - 1) AS aprobado)
	  FROM sw_estudiante e,
		   sw_estudiante_periodo_lectivo ep,
		   sw_curso c,
		   sw_paralelo p
	 WHERE e.id_estudiante = ep.id_estudiante
	   AND ep.id_paralelo = p.id_paralelo
	   AND p.id_curso = c.id_curso
	   AND (e.es_apellidos LIKE CONCAT(PatronBusqueda,'%')
			OR e.es_nombres LIKE CONCAT(PatronBusqueda,'%'))
	   AND ep.id_periodo_lectivo = IdPeriodoLectivo - 1;

END;";
$val49 =  @mysql_query($query_p4);
if (!$val49) echo "Error en la creaci&oacute;n del procedimiento almacenado sp_buscar_estudiantes_antiguos." . mysql_error();

$query_p5 = "CREATE DEFINER=colegion_1@localhost PROCEDURE sp_cerrar_periodos()
    NO SQL
BEGIN
	DECLARE done INT DEFAULT 0;
	DECLARE IdAporteEvaluacion INT;

	DECLARE cAportesEvaluacion CURSOR FOR
		SELECT id_aporte_evaluacion
		  FROM sw_aporte_evaluacion;

	DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done = 1;

	OPEN cAportesEvaluacion;

	REPEAT
		FETCH cAportesEvaluacion INTO IdAporteEvaluacion;
		UPDATE sw_aporte_evaluacion
		   SET ap_estado = 'C'
		 WHERE id_aporte_evaluacion = IdAporteEvaluacion
		   AND ap_fecha_cierre = (SELECT curdate());
	UNTIL done END REPEAT;

	CLOSE cAportesEvaluacion;
END;";
$val50 =  @mysql_query($query_p5);
if (!$val50) echo "Error en la creaci&oacute;n del procedimiento almacenado sp_cerrar_periodos." . mysql_error();

$query_p6 = "CREATE DEFINER=colegion_1@localhost PROCEDURE sp_insertar_institucion(
IN In_nombre VARCHAR(64), 
IN In_direccion VARCHAR(45), 
IN In_telefono1 VARCHAR(12), 
IN In_nom_secretario VARCHAR(45), 
IN In_nom_rector VARCHAR(45))
    NO SQL
BEGIN
	IF (EXISTS (SELECT * FROM sw_institucion)) THEN
		UPDATE sw_institucion
		SET in_nombre = In_nombre,
		in_direccion = In_direccion,
		in_telefono1 = In_telefono1,
		in_nom_rector = In_nom_rector,
		in_nom_secretario = In_nom_secretario;
	ELSE
		INSERT INTO sw_institucion
		SET in_nombre = In_nombre,
		in_direccion = In_direccion,
		in_telefono1 = In_telefono1,
		in_nom_rector = In_nom_rector,
		in_nom_secretario = In_nom_secretario;
	END IF;
END;";
$val51 =  @mysql_query($query_p6);
if (!$val51) echo "Error en la creaci&oacute;n del procedimiento almacenado sp_insertar_institucion." . mysql_error();

$query_p7 = "CREATE DEFINER=colegion_1@localhost PROCEDURE sp_insertar_periodo_lectivo(
IN AnioInicial INT, 
IN AnioFinal INT)
    NO SQL
BEGIN

	DECLARE done INT DEFAULT 0;
	DECLARE IdAporteEvaluacion INT;

	DECLARE cAportesEvaluacion CURSOR FOR 
		SELECT a.id_aporte_evaluacion
		  FROM sw_aporte_evaluacion a,
			   sw_periodo_evaluacion p,
			   sw_periodo_lectivo pl
		 WHERE a.id_periodo_evaluacion = p.id_periodo_evaluacion
		   AND p.id_periodo_lectivo = pl.id_periodo_lectivo
		   AND pl.pe_anio_inicio = AnioInicial - 1
		   AND a.ap_tipo < 4;

	DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done = 1;

	-- Primero debo verificar si hay un periodo lectivo anterior
	
	SET @IdPeriodoLectivoAnterior = (SELECT id_periodo_lectivo
                                      FROM sw_periodo_lectivo
                                     WHERE pe_anio_inicio = AnioInicial - 1);

	-- SELECT @IdPeriodoLectivoAnterior;

	IF @IdPeriodoLectivoAnterior IS NOT NULL THEN
		-- Actualizo el estado del periodo lectivo anterior
		UPDATE sw_periodo_lectivo
		   SET pe_estado = 'T'
		 WHERE id_periodo_lectivo = @IdPeriodoLectivoAnterior;

		-- Aqui actualizo a 'C' todos los periodos de evaluacion
		-- menos el examen de gracia utilizando un cursor

		OPEN cAportesEvaluacion;

		REPEAT
			FETCH cAportesEvaluacion INTO IdAporteEvaluacion;
			UPDATE sw_aporte_evaluacion
			   SET ap_estado = 'C'
			 WHERE id_aporte_evaluacion = IdAporteEvaluacion;
		UNTIL done END REPEAT;

		CLOSE cAportesEvaluacion;
	
	END IF;

	-- Finalmente inserto el nuevo periodo lectivo
	INSERT INTO sw_periodo_lectivo (pe_anio_inicio, pe_anio_fin, pe_estado)
	VALUES (AnioInicial, AnioFinal, 'A');

END;";
$val52 =  @mysql_query($query_p7);
if (!$val52) echo "Error en la creaci&oacute;n del procedimiento almacenado sp_insertar_periodo_lectivo." . mysql_error();

$query_p8 = "CREATE DEFINER=colegion_1@localhost PROCEDURE sp_insertar_rubrica_estudiante(
IN IdEstudiante INT, 
IN IdParalelo INT, 
IN IdAsignatura INT, 
IN IdRubricaPersonalizada INT, 
IN ReCalificacion FLOAT, 
IN IdAporteEvaluacion INT, 
IN AeCalificacion FLOAT)
    NO SQL
BEGIN
	
    INSERT INTO sw_rubrica_estudiante 
		(id_estudiante,
		 id_paralelo,
		 id_asignatura,
		 id_rubrica_personalizada,
		 re_calificacion
		)
		VALUES
		(IdEstudiante,
		 IdParalelo,
		 IdAsignatura,
		 IdRubricaPersonalizada,
		 ReCalificacion
		);

	INSERT INTO sw_aporte_estudiante
	   SET id_aporte_evaluacion = IdAporteEvaluacion,
		   id_estudiante = IdEstudiante,
		   id_paralelo = IdParalelo,
		   id_asignatura = IdAsignatura,
		   ae_calificacion = AeCalificacion;

END;";
$val53 =  @mysql_query($query_p8);
if (!$val53) echo "Error en la creaci&oacute;n del procedimiento almacenado sp_insertar_rubrica_estudiante." . mysql_error();

$query_p9 = "CREATE DEFINER=colegion_1@localhost PROCEDURE sp_insertar_usuario(
	in IdPeriodoLectivo int,
	in IdPerfil int,
	in UsTitulo varchar(5),
	in UsApellidos varchar(32),
	in UsNombres varchar(32),
	in UsFullname varchar(64),
	in UsLogin varchar(24),
	in UsPassword varchar(64)
)
BEGIN
	INSERT INTO sw_usuario (
		id_periodo_lectivo,
		id_perfil,
		us_titulo,
		us_apellidos,
		us_nombres,
		us_fullname,
		us_login,
		us_password
	) VALUES (
		IdPeriodoLectivo,
		IdPerfil,
		UsTitulo,
		UsApellidos,
		UsNombres,
		UsFullname,
		UsLogin,
		UsPassword
	);
END;";
$val54 =  @mysql_query($query_p9);
if (!$val54) echo "Error en la creaci&oacute;n del procedimiento almacenado sp_insertar_usuario." . mysql_error();

$query_p10 = "CREATE DEFINER=colegion_1@localhost PROCEDURE sp_actualizar_periodo_lectivo(
IN IdPeriodoLectivo INT, 
IN AnioInicial INT, 
IN AnioFinal INT)
BEGIN

	-- Actualizo los campos de la tabla sw_periodo_lectivo
	UPDATE sw_periodo_lectivo SET
	pe_anio_inicio = AnioInicial,
	pe_anio_fin = AnioFinal
	WHERE id_periodo_lectivo = IdPeriodoLectivo;

END;";
$val55 =  @mysql_query($query_p10);
if (!$val55) echo "Error en la creaci&oacute;n del procedimiento almacenado sp_actualizar_periodo_lectivo." . mysql_error();
															
															echo "CREACION DE LA BASE DE DATOS EXITOSA.";
														}
													}
												}
											}
										}
									}
								}
							}
						}
					}
				}
			}
		}
	}
	@mysql_close();
?>