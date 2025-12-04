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
(12, 'Aceite de CBD para Dormir 30ml', 'Aceite de cannabidiol de espectro completo 1000mg con melatonina. Ayuda a conciliar el sueño de forma natural sin crear dependencia. Sabor natural de menta. Certificado por laboratorio. Incluye gotero de precisión. Sin THC.', 1799.99, 18, 'NaturalCalm', 'Canadá'),
(13, 'Pijama de Seda Natural Unisex', 'Conjunto de pijama 100% seda de morera grado 6A. Termorregulador natural que mantiene frescura en verano y calidez en invierno. Hipoalergénico y suave al tacto. Disponible en tallas S-XL. Color azul noche con detalles dorados.', 2199.99, 22, 'SilkDreams', 'China'),
(14, 'Colchón Topper de Gel Refrescante', 'Sobrecolchón de 8cm con espuma de memoria infusionada con gel refrescante. Reduce la temperatura corporal hasta 3°C. Funda removible con tecnología antibacteriana. Tamaño Queen. Ideal para noches calurosas.', 1599.99, 30, 'CoolSleep', 'Estados Unidos'),
(15, 'Auriculares para Dormir Inalámbricos', 'Banda suave con auriculares planos integrados. Perfectos para dormir de lado. Bluetooth 5.0, batería de 10 horas. Material transpirable y lavable. Incluye 50 sonidos de naturaleza pregrabados.', 849.99, 55, 'SleepBuds', 'Japón'),
(16, 'Spray de Almohada Lavanda Premium', 'Spray aromaterapéutico con aceites esenciales puros de lavanda francesa, manzanilla romana y vetiver. Fórmula sin alcohol que no mancha. Botella de 100ml con atomizador fino. Promueve relajación profunda.', 349.99, 80, 'AromaRest', 'Francia'),
(17, 'Antifaz de Seda con Perlas de Gel', 'Máscara de ojos de seda pura con bolsas de perlas de gel removibles. Puede usarse frío para desinflamar o tibio para relajar. Bloquea 100% de la luz. Correa ajustable. Incluye bolsa de viaje.', 549.99, 45, 'SilkRest', 'Corea del Sur'),
(18, 'Lámpara de Sal del Himalaya USB', 'Lámpara tallada en cristal de sal rosa genuina del Himalaya. Emite luz cálida y suave ideal para crear ambiente nocturno. Conexión USB. Peso aproximado 1.5kg. Cada pieza es única. Purifica el aire naturalmente.', 449.99, 65, 'HimalayaGlow', 'Pakistán'),
(19, 'Suplemento de Magnesio para el Sueño', 'Fórmula avanzada de glicinato de magnesio 400mg con L-teanina y extracto de pasiflora. 60 cápsulas vegetarianas. Sin gluten ni lactosa. Promueve relajación muscular y mental para un sueño reparador.', 399.99, 90, 'NightVitamins', 'Estados Unidos'),
(20, 'Pantuflas Térmicas con Lavanda', 'Pantuflas calentables en microondas con relleno de semillas de lino y flores de lavanda orgánica. Mantienen el calor hasta 30 minutos. Interior de felpa suave. Talla única (36-42). Alivian pies fríos y tensión.', 499.99, 40, 'WarmFeet', 'España'),
(21, 'Máquina de Sonidos Blancos Premium', 'Dispositivo con 30 sonidos de alta fidelidad: lluvia, océano, bosque, ventilador y más. Temporizador programable. Volumen ajustable. Tamaño compacto ideal para viajes. Incluye adaptador y cable USB-C.', 699.99, 50, 'SoundSleep', 'Estados Unidos'),
(22, 'Edredón de Plumas de Ganso', 'Edredón relleno de plumas de ganso blanco premium con poder de relleno 700+. Tela exterior de algodón egipcio 300 hilos. Costuras en cuadros para distribución uniforme. Tamaño King. Incluye bolsa de almacenamiento.', 3499.99, 15, 'LuxuryRest', 'Hungría'),
(23, 'Vela Aromática de Soja Nocturna', 'Vela artesanal de cera de soja 100% natural con mecha de algodón. Aroma de vainilla, sándalo y notas de ámbar. Tiempo de combustión 45 horas. Envase de vidrio reutilizable. No tóxica ni produce hollín.', 279.99, 70, 'CandleNight', 'México'),
(24, 'Almohada Cervical Ortopédica', 'Almohada contorneada con doble altura para diferentes posiciones de sueño. Espuma de memoria de alta densidad con canal de ventilación. Funda de tencel antibacteriana removible. Alivia dolor de cuello y hombros.', 999.99, 35, 'OrthoSleep', 'Alemania'),
(25, 'Kit de Meditación Nocturna', 'Set completo con cojín de meditación zafu, antifaz de seda, vela de soja, incienso de sándalo y guía de meditaciones para dormir en español. Todo en elegante caja de regalo. Ideal para principiantes.', 1299.99, 25, 'ZenNight', 'India'),
(26, 'Protector de Colchón Impermeable', 'Cubrecolchón con membrana impermeable silenciosa y transpirable. Superficie de algodón terry suave. Protege contra ácaros, bacterias y líquidos. Elástico en las esquinas. Tamaño Queen. Lavable a máquina.', 649.99, 55, 'SleepGuard', 'Portugal'),
(27, 'Reloj Despertador Proyector', 'Despertador digital que proyecta la hora en el techo. Pantalla LED regulable. Radio FM, puerto USB de carga, alarma dual con sonidos naturales. Función de repetición inteligente. Respaldo de batería.', 549.99, 45, 'TimeSleep', 'China'),
(28, 'Gummies de Melatonina Sabor Mora', 'Gomitas masticables con 5mg de melatonina, vitamina B6 y extracto de pasiflora. Sabor natural de mora azul. 60 piezas por frasco. Sin azúcar añadida, veganas y libres de gluten. Ayudan a regular el ciclo del sueño.', 349.99, 85, 'SleepyBites', 'Estados Unidos'),
(29, 'Bata de Baño de Microfibra Premium', 'Bata ultra suave de microfibra de doble capa. Secado rápido y altamente absorbente. Bolsillos amplios y cinturón. Disponible en tallas S-XXL. Color gris lunar. Perfecta para relajarse antes de dormir.', 799.99, 30, 'CozyWrap', 'Turquía'),
(30, 'Set de Aceites Esenciales para Dormir', 'Colección de 6 aceites esenciales puros: lavanda, manzanilla, bergamota, cedro, ylang-ylang y mezcla especial WigNight. Frascos de 10ml cada uno con gotero. Incluye guía de uso y recetas para difusor.', 699.99, 60, 'EssentialNight', 'Francia'),
(31, 'Funda de Almohada de Cobre Antimicrobiana', 'Set de 2 fundas de almohada con hilos de cobre incorporados. Naturalmente antibacteriano y antifúngico. Reduce arrugas y beneficia la piel. Satén de alta calidad. Tamaño estándar. Cierre de sobre.', 599.99, 40, 'CopperSleep', 'Israel'),
(32, 'Termómetro Ambiental Inteligente', 'Monitor de temperatura y humedad con pantalla e-ink. Conectividad WiFi para historial en app. Alertas personalizables cuando las condiciones no son óptimas para dormir. Batería recargable USB-C. Diseño minimalista.', 449.99, 50, 'ClimaSleep', 'Suiza');

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
  MODIFY ID_Producto int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

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
