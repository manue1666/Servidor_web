CREATE TABLE `asociarproductos` (
  `id` int(255) NOT NULL,
  `idproductopadre` int(255) NOT NULL,
  `idproductopack` int(255) NOT NULL,
  `cantidadpack` int(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `asociartallas` (
  `id` int(255) NOT NULL,
  `idproductoprincipal` int(255) NOT NULL,
  `idproductotalla` int(255) NOT NULL,
  `talla` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `catalogos` (
  `id` int(255) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `path` varchar(255) NOT NULL,
  `estatus` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


CREATE TABLE `categorias` (
  `id` int(11) NOT NULL,
  `categoria` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `categoriasasociadas` (
  `id` int(255) NOT NULL,
  `idproducto` int(255) NOT NULL,
  `categoria` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


CREATE TABLE `categoriasasociadasventa` (
  `id` int(255) NOT NULL,
  `idproducto` int(255) NOT NULL,
  `categoria` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `configuraciones` (
  `id` int(255) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `detalle` varchar(255) NOT NULL,
  `valoruno` mediumtext NOT NULL,
  `valordos` mediumtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `cupones` (
  `id` int(255) NOT NULL,
  `cupon` varchar(255) NOT NULL,
  `codigo` varchar(20) NOT NULL,
  `porcentaje` int(255) NOT NULL,
  `minimo` int(255) NOT NULL,
  `maximo` int(255) NOT NULL,
  `canjes` int(11) NOT NULL,
  `estatus` int(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `cuponescanjeados` (
  `id` int(11) NOT NULL,
  `codigo` varchar(255) NOT NULL,
  `identificador` varchar(255) NOT NULL,
  `monto` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `industriaasociada` (
  `id` int(255) NOT NULL,
  `idproducto` int(255) NOT NULL,
  `industria` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `industriaasociadaventa` (
  `id` int(255) NOT NULL,
  `idproducto` int(255) NOT NULL,
  `industria` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `industrias` (
  `id` int(255) NOT NULL,
  `industria` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `medios` (
  `id` int(255) NOT NULL,
  `idproducto` int(255) NOT NULL,
  `medio` varchar(1000) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `mediosventa` (
  `id` int(255) NOT NULL,
  `idproducto` int(255) NOT NULL,
  `medio` varchar(1000) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `pedidos` (
  `id` int(255) NOT NULL,
  `identificador` varchar(255) DEFAULT NULL,
  `fecha` date NOT NULL DEFAULT current_timestamp(),
  `nombre` varchar(100) NOT NULL,
  `apellidop` varchar(100) NOT NULL,
  `apellidom` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `telefono` varchar(20) NOT NULL,
  `calle` varchar(150) NOT NULL,
  `exterior` varchar(150) NOT NULL,
  `interior` varchar(150) NOT NULL,
  `colonia` varchar(150) NOT NULL,
  `ciudad` varchar(150) NOT NULL,
  `estado` varchar(150) NOT NULL,
  `postal` int(30) NOT NULL,
  `pais` varchar(150) NOT NULL,
  `cupon` varchar(100) DEFAULT NULL,
  `cuponMonto` varchar(100) NOT NULL,
  `descuentoTotal` varchar(50) NOT NULL,
  `subtotal` varchar(10) DEFAULT NULL,
  `total` varchar(10) DEFAULT NULL,
  `productos` varchar(2000) NOT NULL,
  `envioMonto` varchar(50) NOT NULL,
  `estatus` int(2) NOT NULL,
  `openpay_id` varchar(500) DEFAULT NULL,
  `status_pago` varchar(500) DEFAULT NULL,
  `authorization` varchar(500) NOT NULL,
  `guia` varchar(255) DEFAULT NULL,
  `pdf_url` varchar(1000) DEFAULT NULL,
  `clabe` varchar(100) DEFAULT NULL,
  `vigencia` varchar(100) NOT NULL,
  `banco` varchar(100) NOT NULL,
  `convenio` varchar(100) NOT NULL,
  `referencia` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `productos` (
  `id` int(255) NOT NULL,
  `titulo` varchar(100) NOT NULL,
  `subtitulo` varchar(100) NOT NULL,
  `detalles` varchar(2000) NOT NULL,
  `estatus` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `productospedidos` (
  `id` int(255) NOT NULL,
  `idproducto` int(255) NOT NULL,
  `identificador` varchar(1000) NOT NULL,
  `cantidad` int(255) NOT NULL,
  `precio` double(10,2) NOT NULL,
  `surtido` int(255) NOT NULL,
  `estatus` int(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `productosventa` (
  `id` int(255) NOT NULL,
  `titulo` varchar(100) NOT NULL,
  `subtitulo` varchar(100) NOT NULL,
  `detalles` varchar(2000) NOT NULL,
  `estatus` int(1) NOT NULL,
  `stock` int(255) DEFAULT NULL,
  `stockminimo` int(255) NOT NULL,
  `sku` varchar(255) DEFAULT NULL,
  `preciounitario` decimal(10,2) NOT NULL,
  `preciomayoreo` decimal(10,2) NOT NULL,
  `cantidadmayoreo` int(255) NOT NULL,
  `descuento` decimal(10,2) NOT NULL,
  `talla` varchar(20) DEFAULT 'Unitalla'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `promociones` (
  `id` int(255) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `medio` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  `estatus` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `subcategorias` (
  `id` int(255) NOT NULL,
  `subcategoria` varchar(255) NOT NULL,
  `medio` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `subcategoriasasociadasventa` (
  `id` int(255) NOT NULL,
  `idproducto` int(255) NOT NULL,
  `subcategoria` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellidopaterno` varchar(100) NOT NULL,
  `apellidomaterno` varchar(100) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `rol` int(2) NOT NULL,
  `estatus` int(1) NOT NULL,
  `medio` longblob NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


CREATE TABLE `ventas` (
  `id` int(255) NOT NULL,
  `identificador` varchar(255) DEFAULT NULL,
  `titulo` varchar(100) NOT NULL,
  `subtitulo` varchar(100) NOT NULL,
  `detalles` varchar(2000) NOT NULL,
  `cantidad` int(255) DEFAULT NULL,
  `surtido` int(255) NOT NULL,
  `sku` varchar(255) DEFAULT NULL,
  `mayoreo` varchar(3) DEFAULT NULL,
  `precio` decimal(10,2) NOT NULL,
  `descuento` decimal(10,2) NOT NULL,
  `producto_id` int(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


CREATE TABLE `videos` (
  `id` int(255) NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `path` varchar(150) NOT NULL,
  `estatus` int(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


ALTER TABLE `asociarproductos`
  ADD PRIMARY KEY (`id`);


ALTER TABLE `asociartallas`
  ADD PRIMARY KEY (`id`);


ALTER TABLE `catalogos`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `categoriasasociadas`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `categoriasasociadasventa`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `configuraciones`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `cupones`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `cuponescanjeados`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `industriaasociada`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `industriaasociadaventa`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `industrias`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `medios`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `mediosventa`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `pedidos`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `productos`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `productospedidos`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `productosventa`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `promociones`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `subcategorias`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `subcategoriasasociadasventa`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

ALTER TABLE `ventas`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `videos`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `asociarproductos`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT;

ALTER TABLE `asociartallas`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT;

ALTER TABLE `catalogos`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT;

ALTER TABLE `categorias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `categoriasasociadas`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT;

ALTER TABLE `categoriasasociadasventa`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT;

ALTER TABLE `configuraciones`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT;

ALTER TABLE `cupones`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT;

ALTER TABLE `cuponescanjeados`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `industriaasociada`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT;

ALTER TABLE `industriaasociadaventa`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT;

ALTER TABLE `industrias`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT;

ALTER TABLE `medios`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT;

ALTER TABLE `mediosventa`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT;

ALTER TABLE `pedidos`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT;

ALTER TABLE `productos`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT;

ALTER TABLE `productospedidos`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT;

ALTER TABLE `productosventa`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT;

ALTER TABLE `promociones`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT;

ALTER TABLE `subcategorias`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT;

ALTER TABLE `subcategoriasasociadasventa`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT;

ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `ventas`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT;


ALTER TABLE `videos`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT;