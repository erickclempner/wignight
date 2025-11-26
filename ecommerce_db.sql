CREATE TABLE Carrito_Compras (
  ID_Carrito int NOT NULL,
  ID_Usuario_FK int NOT NULL,
  ID_Producto_FK int NOT NULL,
  Cantidad int NOT NULL DEFAULT '1'
);

-- --------------------------------------------------------

--
-- Table structure for table Ordenes
--

CREATE TABLE Ordenes (
  ID_Orden int NOT NULL,
  ID_Usuario_FK int NOT NULL,
  Fecha_Orden timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  Total_Orden decimal(10,2) NOT NULL,
  Estado_Orden varchar(50) NOT NULL DEFAULT 'Procesando',
  Direccion_Envio_Snapshot text NOT NULL
);

-- --------------------------------------------------------

--
-- Table structure for table Ordenes_Detalles
--

CREATE TABLE Ordenes_Detalles (
  ID_Detalle int NOT NULL,
  ID_Orden_FK int NOT NULL,
  ID_Producto_FK int NOT NULL,
  Cantidad int NOT NULL,
  Precio_Unitario_Snapshot decimal(10,2) NOT NULL,
  Subtotal_Linea decimal(10,2) NOT NULL
);

-- --------------------------------------------------------

--
-- Table structure for table Productos
--

CREATE TABLE Productos (
  ID_Producto int NOT NULL,
  Nombre varchar(255) NOT NULL,
  Descripcion text,
  Fotos longblob,
  Precio decimal(10,2) NOT NULL,
  Cantidad_Almacen int NOT NULL DEFAULT '0',
  Fabricante varchar(150) DEFAULT NULL,
  Origen varchar(100) DEFAULT NULL
);

INSERT INTO Productos (ID_Producto, Nombre, Descripcion, Precio, Cantidad_Almacen, Fabricante, Origen) VALUES
(1, 'Almohada de Memoria Viscoelástica Premium', 'Almohada ergonómica con espuma de memoria que se adapta perfectamente a la forma de tu cabeza y cuello. Diseñada para reducir puntos de presión y mantener una alineación óptima de la columna durante el sueño. Incluye funda lavable de bambú transpirable.', 899.99, 45, 'DreamComfort', 'México'),
(2, 'Máscara de Dormir con Sonido Blanco', 'Máscara de ojos premium con auriculares bluetooth integrados. Reproduce sonidos relajantes, meditaciones guiadas y música para dormir. Batería de 12 horas. Material ultrasuave que bloquea 100% de la luz.', 1299.99, 30, 'SleepTech', 'Estados Unidos'),
(3, 'Difusor de Aromas Nocturno Inteligente', 'Difusor ultrasónico con control por app y 7 colores de luz LED ajustables. Programable para activarse automáticamente antes de dormir. Incluye aceites esenciales de lavanda, eucalipto y manzanilla. Capacidad de 300ml.', 749.99, 60, 'AromaLife', 'China'),
(4, 'Cobija Ponderada Terapéutica 7kg', 'Manta con peso distribuido uniformemente que proporciona una sensación de abrazo relajante. Relleno de microperlas de vidrio hipoalergénicas. Funda exterior de algodón orgánico removible y lavable. Ideal para reducir ansiedad y mejorar calidad del sueño.', 1899.99, 25, 'RestWell', 'Canadá'),
(5, 'Humidificador Ultra Silencioso', 'Humidificador de niebla fría con tecnología ultrasónica silenciosa (menos de 25dB). Tanque de 4 litros con autonomía de 24 horas. Apagado automático y luz nocturna ajustable. Perfecto para mantener humedad óptima durante la noche.', 599.99, 50, 'PureAir', 'Corea del Sur'),
(6, 'Set de Sábanas de Bambú King Size', 'Juego completo de sábanas 100% fibra de bambú orgánico. Extremadamente suaves, termoreguladoras e hipoalergénicas. Incluye sábana ajustable, sábana plana y 2 fundas de almohada. Tejido de 400 hilos que mejora con cada lavado.', 1499.99, 35, 'EcoSleep', 'India'),
(7, 'Luz Nocturna con Simulador de Amanecer', 'Despertador con luz progresiva que simula el amanecer natural. 20 niveles de intensidad y 7 sonidos naturales. Función de radio FM. Ayuda a despertar de forma natural y mejora el estado de ánimo matutino.', 899.99, 40, 'SunRise', 'Alemania'),
(8, 'Té Nocturno Relajante Orgánico (30 sobres)', 'Mezcla premium de hierbas orgánicas: valeriana, pasiflora, tila y melisa. Sin cafeína. Certificado orgánico y comercio justo. Cada sobre está individualmente envuelto para preservar frescura y aroma. Promueve relajación natural.', 299.99, 100, 'HerbalNight', 'México'),
(9, 'Cortinas Blackout Térmicas', 'Cortinas opacas que bloquean 99% de la luz y reducen ruido exterior. Aislamiento térmico que mantiene temperatura ideal. Fácil instalación con argollas incluidas. Disponible en color gris oscuro, tamaño 2.4m x 2.1m por panel (set de 2).', 1199.99, 28, 'WindowPro', 'España'),
(10, 'Monitor de Sueño Inteligente', 'Dispositivo no invasivo que se coloca bajo el colchón y monitorea ciclos de sueño, frecuencia cardíaca y respiratoria. App móvil con análisis detallado y consejos personalizados. Compatible con iOS y Android. No requiere uso de wearables.', 2499.99, 15, 'SleepSense', 'Estados Unidos'),
(11, 'Almohada Corporal para Embarazadas', 'Almohada en forma de U de 1.70m diseñada para brindar soporte completo durante el embarazo y lactancia. Relleno ajustable de fibra hipoalergénica. Funda de algodón orgánico lavable. También ideal para personas con problemas de espalda.', 1099.99, 20, 'MamaCare', 'Brasil'),
(12, 'Aceite de CBD para Dormir 30ml', 'Aceite de cannabidiol de espectro completo 1000mg con melatonina. Ayuda a conciliar el sueño de forma natural sin crear dependencia. Sabor natural de menta. Certificado por laboratorio. Incluye gotero de precisión. Sin THC.', 1799.99, 18, 'NaturalCalm', 'Canadá');

-- --------------------------------------------------------

--
-- Table structure for table Roles
--

CREATE TABLE Roles (
  id_rol int NOT NULL,
  nombre_rol varchar(50) NOT NULL
);

--
-- Dumping data for table Roles
--

INSERT INTO Roles (id_rol, nombre_rol) VALUES
(1, 'Cliente'),
(2, 'Administrador');

-- --------------------------------------------------------

--
-- Table structure for table Usuarios
--

CREATE TABLE Usuarios (
  ID_Usuario int NOT NULL,
  Nombre_Usuario varchar(100) NOT NULL,
  Correo_Electronico varchar(255) NOT NULL,
  Contrasena varchar(255) NOT NULL,
  Fecha_Nacimiento date DEFAULT NULL,
  Numero_Tarjeta_Bancaria varchar(255) DEFAULT NULL,
  Direccion_Postal text,
  id_rol int NOT NULL DEFAULT '1'
);

--
-- Dumping data for table Usuarios
--

INSERT INTO Usuarios (ID_Usuario, Nombre_Usuario, Correo_Electronico, Contrasena, Fecha_Nacimiento, Numero_Tarjeta_Bancaria, Direccion_Postal, id_rol) VALUES
(1, 'Erick Clempner', 'erickclempner@gmail.com', '$2y$10$TsiP1wLqemDYAlSYDBuYUOf/PlT21RJjK5cgpD2IjJocIOFtZMnCi', '2004-08-24', NULL, 'Anahuac', 1),
(3, 'Admin', 'erick@wiger.ai', '$2y$10$MySD8qNSMcv7.4gdIqPa.eh7eFvk9x5RgRYbfCB/POPgDWWQW0L5y', '1990-01-01', NULL, 'Oficina', 2);

--
-- Indexes for dumped tables
--

--
-- Indexes for table Carrito_Compras
--
ALTER TABLE Carrito_Compras
  ADD PRIMARY KEY (ID_Carrito),
  ADD UNIQUE KEY ID_Usuario_FK (ID_Usuario_FK,ID_Producto_FK),
  ADD KEY ID_Producto_FK (ID_Producto_FK);

--
-- Indexes for table Ordenes
--
ALTER TABLE Ordenes
  ADD PRIMARY KEY (ID_Orden),
  ADD KEY ID_Usuario_FK (ID_Usuario_FK);

--
-- Indexes for table Ordenes_Detalles
--
ALTER TABLE Ordenes_Detalles
  ADD PRIMARY KEY (ID_Detalle),
  ADD KEY ID_Orden_FK (ID_Orden_FK),
  ADD KEY ID_Producto_FK (ID_Producto_FK);

--
-- Indexes for table Productos
--
ALTER TABLE Productos
  ADD PRIMARY KEY (ID_Producto);

--
-- Indexes for table Roles
--
ALTER TABLE Roles
  ADD PRIMARY KEY (id_rol);

--
-- Indexes for table Usuarios
--
ALTER TABLE Usuarios
  ADD PRIMARY KEY (ID_Usuario),
  ADD UNIQUE KEY Correo_Electronico (Correo_Electronico),
  ADD KEY fk_id_rol (id_rol);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table Carrito_Compras
--
ALTER TABLE Carrito_Compras
  MODIFY ID_Carrito int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table Ordenes
--
ALTER TABLE Ordenes
  MODIFY ID_Orden int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table Ordenes_Detalles
--
ALTER TABLE Ordenes_Detalles
  MODIFY ID_Detalle int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table Productos
--
ALTER TABLE Productos
  MODIFY ID_Producto int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table Roles
--
ALTER TABLE Roles
  MODIFY id_rol int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table Usuarios
--
ALTER TABLE Usuarios
  MODIFY ID_Usuario int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table Carrito_Compras
--
ALTER TABLE Carrito_Compras
  ADD CONSTRAINT Carrito_Compras_ibfk_1 FOREIGN KEY (ID_Usuario_FK) REFERENCES Usuarios (ID_Usuario),
  ADD CONSTRAINT Carrito_Compras_ibfk_2 FOREIGN KEY (ID_Producto_FK) REFERENCES Productos (ID_Producto);

--
-- Constraints for table Ordenes
--
ALTER TABLE Ordenes
  ADD CONSTRAINT Ordenes_ibfk_1 FOREIGN KEY (ID_Usuario_FK) REFERENCES Usuarios (ID_Usuario);

--
-- Constraints for table Ordenes_Detalles
--
ALTER TABLE Ordenes_Detalles
  ADD CONSTRAINT Ordenes_Detalles_ibfk_1 FOREIGN KEY (ID_Orden_FK) REFERENCES Ordenes (ID_Orden),
  ADD CONSTRAINT Ordenes_Detalles_ibfk_2 FOREIGN KEY (ID_Producto_FK) REFERENCES Productos (ID_Producto);

--
-- Constraints for table Usuarios
--
ALTER TABLE Usuarios
  ADD CONSTRAINT fk_id_rol FOREIGN KEY (id_rol) REFERENCES Roles (id_rol);
