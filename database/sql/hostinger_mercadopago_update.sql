CREATE TABLE IF NOT EXISTS pagos (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id BIGINT UNSIGNED NOT NULL,
  tipo_pago VARCHAR(30) NOT NULL,
  plan VARCHAR(40) NULL,
  monto DECIMAL(10,2) NOT NULL,
  estado VARCHAR(40) NOT NULL DEFAULT 'pendiente',
  proveedor_pago VARCHAR(40) NOT NULL DEFAULT 'mercadopago',
  provider_payment_id VARCHAR(255) NULL,
  provider_preference_id VARCHAR(255) NULL,
  external_reference VARCHAR(255) NULL,
  fecha_inicio TIMESTAMP NULL,
  fecha_fin TIMESTAMP NULL,
  payload JSON NULL,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  PRIMARY KEY (id),
  KEY pagos_external_reference_index (external_reference),
  KEY pagos_user_tipo_estado_index (user_id, tipo_pago, estado),
  CONSTRAINT pagos_user_id_foreign FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE suscripciones
  ADD COLUMN IF NOT EXISTS tipo_plan VARCHAR(40) NULL AFTER plan,
  ADD COLUMN IF NOT EXISTS activo TINYINT(1) NOT NULL DEFAULT 1 AFTER estatus,
  ADD COLUMN IF NOT EXISTS fecha_vencimiento TIMESTAMP NULL AFTER periodo_fin;
