create database AsistenciaQRDemo001;
use AsistenciaQRDemo001;

CREATE TABLE IF NOT EXISTS estudiante(
    idEstudiante INT PRIMARY KEY AUTO_INCREMENT,
    Nombre VARCHAR(50) NOT NULL,
    Apellidos VARCHAR(50) NOT NULL,
    DNI VARCHAR(8) NOT NULL UNIQUE,
    Grado INT NOT NULL,
    Seccion VARCHAR(5),
    qr_code VARCHAR(255) UNIQUE
);

CREATE TABLE IF NOT EXISTS personal(
    idPersonal INT PRIMARY KEY AUTO_INCREMENT,
    Nombre VARCHAR(50) NOT NULL,
    Apellido VARCHAR(50) NOT NULL,
    usuario VARCHAR(20) NOT NULL UNIQUE,
    contraseña VARCHAR(255) NOT NULL,
    rol ENUM('Admin','Encargado') DEFAULT 'Admin'
);

CREATE TABLE IF NOT EXISTS asistencia(
    idAsistencia INT PRIMARY KEY AUTO_INCREMENT,
    idEstudiante INT,
    idPersonal INT,
    fechaHora DATETIME DEFAULT CURRENT_TIMESTAMP,
    tipoAsistencia ENUM('Asistio','Falto','Tardanza','Falta justificada','Tardanza justificada') DEFAULT 'Asistio',
    FOREIGN KEY (idEstudiante) REFERENCES estudiante(idEstudiante),
    FOREIGN KEY (idPersonal) REFERENCES personal(idPersonal)
);

DELIMITER $$
CREATE PROCEDURE insertar_estudiante(
    IN p_nombre VARCHAR(50),
    IN p_apellido VARCHAR(50),
    IN p_dni VARCHAR(8),
    IN p_grado INT,
    IN p_seccion VARCHAR(5),
    IN p_qr_code VARCHAR(255)
)
BEGIN
    INSERT INTO estudiante (Nombre, Apellidos, DNI, Grado, Seccion, qr_code)
    VALUES (p_nombre, p_apellido, p_dni, p_grado, p_seccion, p_qr_code);
END $$
DELIMITER ;
select * from personal;
select * from estudiante;

INSERT INTO personal (Nombre, Apellido, usuario, contraseña, rol)
VALUES ('Deyvi','Paz','admin',SHA2('admin123123', 256),'Admin');
INSERT INTO personal (Nombre, Apellido, usuario, contraseña, rol)
VALUES ('Deyvi','Paz','user001',SHA2('user001', 256),'Encargado');



CALL insertar_estudiante('David','Paz','60951351',5,'A','QR60951351');
CALL insertar_estudiante('Ana','Lopez','60951352',5,'A','QR60951352');
CALL insertar_estudiante('Maria','Perez','60325195',5,'A','QR60325195');
select * from estudiante;

