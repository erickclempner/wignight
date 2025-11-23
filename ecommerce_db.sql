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
  MODIFY ID_Producto int NOT NULL AUTO_INCREMENT;

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
