create database AsistenciaQRDemo001;
use AsistenciaQRDemo001;


CREATE TABLE estudiante(
    idEstudiante INT PRIMARY KEY AUTO_INCREMENT,
    Nombre VARCHAR(50) NOT NULL,
    Apellidos VARCHAR(50) NOT NULL,
    DNI VARCHAR(8) NOT NULL UNIQUE,
    Grado INT NOT NULL,
    Seccion VARCHAR(5),
    qr_code VARCHAR(255) UNIQUE
);

CREATE TABLE personal(
    idPersonal INT PRIMARY KEY AUTO_INCREMENT,
    Nombre VARCHAR(50) NOT NULL,
    Apellido VARCHAR(50) NOT NULL,
    usuario VARCHAR(20) NOT NULL UNIQUE,
    contrasena VARCHAR(255) NOT NULL,
    rol ENUM('Admin','Encargado') DEFAULT 'Admin'
);
		
CREATE TABLE asistencia (
    idAsistencia INT PRIMARY KEY AUTO_INCREMENT,
    idEstudiante INT,
    idPersonal INT,
    fechaEntrada DATETIME DEFAULT CURRENT_TIMESTAMP,
    fechaSalida DATETIME NULL,
    tipoAsistencia ENUM('Asistio','Falto','Tardanza','Falta justificada','Tardanza justificada') DEFAULT 'Asistio',
    FOREIGN KEY (idEstudiante) REFERENCES estudiante(idEstudiante),
    FOREIGN KEY (idPersonal) REFERENCES personal(idPersonal)
);


DELIMITER $$
CREATE PROCEDURE insertar_estudiante (
    IN pNombre VARCHAR(100),
    IN pApellidos VARCHAR(100),
    IN pDNI CHAR(8),
    IN pGrado INT,
    IN pSeccion VARCHAR(5),
    IN pqr_code VARCHAR(50)
)
BEGIN
    IF EXISTS (SELECT 1 FROM estudiante WHERE DNI = pDNI) THEN
        SIGNAL SQLSTATE '45000' 
        SET MESSAGE_TEXT = 'DNI duplicado';
    ELSE
        INSERT INTO estudiante (Nombre, Apellidos, DNI, Grado, Seccion, qr_code)
        VALUES (pNombre, pApellidos, pDNI, pGrado, pSeccion, pqr_code);
    END IF;
END$$
DELIMITER ;

select * from personal;		
select * from asistencia;

CALL insertar_estudiante('David','Paz','60951351',3,'A','QR60951351');
CALL insertar_estudiante('Carlos','Lopez','87654321',3,'B','QR87654321');
	
INSERT INTO personal (Nombre, Apellido, usuario, contrasena, rol)
VALUES ('Deyvi','Paz','admin',SHA2('admin123123', 256),'Admin');
INSERT INTO personal (Nombre, Apellido, usuario, contrasena, rol)
VALUES ('Deyvi','Paz','user001',SHA2('user001', 256),'Encargado');

			
	

-- Eliminar datos de tabla 
set SQL_SAFE_UPDATES = 0;
delete from asistencia;
alter table asistencia auto_increment = 1;