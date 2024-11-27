use master
go

--drop database Proyecto_Integrador1
--go

create database Proyecto_Integrador1
go

use Proyecto_Integrador1
go


create table Orden_datos(
	No_orden_interna int primary key,
	No_registros_asignados int,
	Fecha_elaboracion date,
	Magnitud nvarchar(50),
	Fecha_recepcion date,
	Fecha_termino_servicio date,
	Vendedor nvarchar(100),
	Elaboro_oi nvarchar(100)
)


create table Cliente(
	Id_cliente int primary key,
	Nombre nvarchar(150),
	Direccion nvarchar(200),
	Atencion nvarchar(100),
	No_orden_interna int --fk 2
)

create table Observaciones(
	No_orden_interna int, --fk 1
	Observaciones_generales nvarchar(250)
)

create table Datos_factura(
	Id_factura int primary key,
	Id_cliente int, -- fk 3
	Actividad_realizada nvarchar(250),
	Lugar_calibracion nvarchar(250)
)

create table Datos_empresa(
	Id_cliente int, -- fk 4
	Telefono nvarchar(15),
	Contacto nvarchar(100),
	Correo nvarchar(50),
	Servicio nvarchar(50),
	Fecha_entrega date
)


create table Datos_equipo(
	No_orden_interna int, -- fk 5
	Numero_ingreso int,
	Certificado_informe nvarchar(50),
	Tipo_servicio nvarchar(100),
	Equipo nvarchar(100),
	Marca_modelo nvarchar(100),
	Codigo_fabricante nvarchar(30),
	Serie nvarchar(100),
	Identificacion nvarchar(100),
	Intervalo nvarchar(100),
	Resolucion nvarchar(100),
	Grado_clase_escala nvarchar(100),
	Accesorios nvarchar(150),
	Observaciones nvarchar(200),
	Material nvarchar(100),
	Numero_partes int,
	Numero_plano int,
	Numero_cotas int,
	Numero_piezas int
)

ALTER TABLE Observaciones --1
ADD CONSTRAINT Observaciones_orden_interna FOREIGN KEY (No_orden_interna)
REFERENCES Orden_datos (No_orden_interna);

ALTER TABLE Cliente --2
ADD CONSTRAINT Cliente_orden_interna FOREIGN KEY (No_orden_interna)
REFERENCES Orden_datos (No_orden_interna);

ALTER TABLE Datos_factura --3
ADD CONSTRAINT Factura_id_cliente FOREIGN KEY (Id_cliente)
REFERENCES Cliente (Id_cliente);

ALTER TABLE Datos_empresa --4
ADD CONSTRAINT Empresa_id_cliente FOREIGN KEY (Id_cliente)
REFERENCES Cliente (Id_cliente);

ALTER TABLE Datos_equipo --5
ADD CONSTRAINT Orden_equipo FOREIGN KEY (No_orden_interna)
REFERENCES Orden_datos (No_orden_interna);