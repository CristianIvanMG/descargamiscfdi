CREATE TABLE IF NOT EXISTS rfcs (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id BIGINT UNSIGNED NOT NULL,
  rfc VARCHAR(13) NOT NULL,
  razon_social VARCHAR(255) NOT NULL,
  regimen_fiscal VARCHAR(255) NULL,
  activo TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  PRIMARY KEY (id),
  UNIQUE KEY rfcs_user_rfc_unique (user_id, rfc),
  CONSTRAINT rfcs_user_id_foreign FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS descarga_jobs (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id BIGINT UNSIGNED NOT NULL,
  rfc_id BIGINT UNSIGNED NULL,
  estado VARCHAR(40) NOT NULL DEFAULT 'pendiente',
  tipo VARCHAR(20) NOT NULL,
  fecha_inicio DATE NOT NULL,
  fecha_fin DATE NOT NULL,
  solicitud_id VARCHAR(255) NULL,
  paquete_id VARCHAR(255) NULL,
  total_cfdi INT UNSIGNED NOT NULL DEFAULT 0,
  mensaje_error TEXT NULL,
  iniciado_en TIMESTAMP NULL,
  terminado_en TIMESTAMP NULL,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  PRIMARY KEY (id),
  KEY descarga_jobs_solicitud_id_index (solicitud_id),
  KEY descarga_jobs_user_rfc_tipo_fechas_index (user_id, rfc_id, tipo, fecha_inicio, fecha_fin),
  CONSTRAINT descarga_jobs_user_id_foreign FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT descarga_jobs_rfc_id_foreign FOREIGN KEY (rfc_id) REFERENCES rfcs(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS cfdis (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id BIGINT UNSIGNED NOT NULL,
  rfc_id BIGINT UNSIGNED NULL,
  uuid VARCHAR(36) NOT NULL,
  tipo VARCHAR(20) NOT NULL,
  serie VARCHAR(255) NULL,
  folio VARCHAR(255) NULL,
  rfc_emisor VARCHAR(13) NOT NULL,
  nombre_emisor VARCHAR(255) NULL,
  rfc_receptor VARCHAR(13) NOT NULL,
  nombre_receptor VARCHAR(255) NULL,
  fecha_emision DATETIME NOT NULL,
  subtotal DECIMAL(14,2) NOT NULL DEFAULT 0,
  descuento DECIMAL(14,2) NOT NULL DEFAULT 0,
  iva DECIMAL(14,2) NOT NULL DEFAULT 0,
  total DECIMAL(14,2) NOT NULL DEFAULT 0,
  moneda VARCHAR(10) NOT NULL DEFAULT 'MXN',
  estatus VARCHAR(30) NOT NULL DEFAULT 'vigente',
  xml_path VARCHAR(255) NULL,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  PRIMARY KEY (id),
  UNIQUE KEY cfdis_user_uuid_unique (user_id, uuid),
  KEY cfdis_uuid_index (uuid),
  KEY cfdis_tipo_index (tipo),
  KEY cfdis_rfc_emisor_index (rfc_emisor),
  KEY cfdis_rfc_receptor_index (rfc_receptor),
  KEY cfdis_fecha_emision_index (fecha_emision),
  KEY cfdis_user_tipo_fecha_index (user_id, tipo, fecha_emision),
  CONSTRAINT cfdis_user_id_foreign FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT cfdis_rfc_id_foreign FOREIGN KEY (rfc_id) REFERENCES rfcs(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS cfdi_conceptos (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  cfdi_id BIGINT UNSIGNED NOT NULL,
  clave_prod_serv VARCHAR(255) NULL,
  descripcion TEXT NULL,
  cantidad DECIMAL(14,6) NOT NULL DEFAULT 0,
  valor_unitario DECIMAL(14,6) NOT NULL DEFAULT 0,
  importe DECIMAL(14,2) NOT NULL DEFAULT 0,
  descuento DECIMAL(14,2) NOT NULL DEFAULT 0,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  PRIMARY KEY (id),
  CONSTRAINT cfdi_conceptos_cfdi_id_foreign FOREIGN KEY (cfdi_id) REFERENCES cfdis(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS suscripciones (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id BIGINT UNSIGNED NOT NULL,
  plan VARCHAR(40) NOT NULL,
  proveedor_pago VARCHAR(40) NULL,
  proveedor_id VARCHAR(255) NULL,
  estatus VARCHAR(40) NOT NULL DEFAULT 'activa',
  periodo_inicio TIMESTAMP NULL,
  periodo_fin TIMESTAMP NULL,
  renovacion_automatica TINYINT(1) NOT NULL DEFAULT 0,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  PRIMARY KEY (id),
  KEY suscripciones_user_estatus_plan_index (user_id, estatus, plan),
  CONSTRAINT suscripciones_user_id_foreign FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS donations (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id BIGINT UNSIGNED NOT NULL,
  proveedor_pago VARCHAR(40) NOT NULL DEFAULT 'mercadopago',
  proveedor_id VARCHAR(255) NULL,
  estado VARCHAR(40) NOT NULL DEFAULT 'pendiente',
  monto DECIMAL(10,2) NULL,
  donated_at TIMESTAMP NULL,
  active_until TIMESTAMP NULL,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  PRIMARY KEY (id),
  KEY donations_user_estado_active_index (user_id, estado, active_until),
  CONSTRAINT donations_user_id_foreign FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
