# PASO 3 — ESQUEMA DE BASE DE DATOS · MySQL 8
## ContadorMx · xml.contadormx.net

---

## ESQUEMA DE BASE DE DATOS — MySQL 8

### Tabla: users
```sql
id               BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
name             VARCHAR(255) NOT NULL
email            VARCHAR(255) UNIQUE NOT NULL
password         VARCHAR(255) NOT NULL
email_verified_at TIMESTAMP NULL
plan             ENUM('gratis','pro','despacho') DEFAULT 'gratis'
plan_expires_at  TIMESTAMP NULL
remember_token   VARCHAR(100)
created_at       TIMESTAMP
updated_at       TIMESTAMP

INDEX idx_email (email)
INDEX idx_plan (plan)
```

### Tabla: rfcs
```sql
id               BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
user_id          BIGINT UNSIGNED NOT NULL FK→users.id CASCADE DELETE
rfc              VARCHAR(13) NOT NULL
razon_social     VARCHAR(255)
regimen_fiscal   VARCHAR(10)
es_propio        TINYINT(1) DEFAULT 1   -- 1=propio, 0=cliente del despacho
activo           TINYINT(1) DEFAULT 1
ultima_descarga  TIMESTAMP NULL
created_at       TIMESTAMP
updated_at       TIMESTAMP

UNIQUE KEY unique_user_rfc (user_id, rfc)
INDEX idx_rfc (rfc)
```

### Tabla: cfdis
```sql
id               BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
user_id          BIGINT UNSIGNED NOT NULL FK→users.id
rfc_id           BIGINT UNSIGNED NOT NULL FK→rfcs.id
uuid             VARCHAR(36) UNIQUE NOT NULL
tipo             ENUM('I','E','T','N','P') NOT NULL  -- Ingreso/Egreso/Traslado/Nomina/Pago
direccion        ENUM('emitido','recibido') NOT NULL
version          VARCHAR(5) DEFAULT '4.0'
serie            VARCHAR(25)
folio            VARCHAR(40)
fecha            DATETIME NOT NULL
fecha_timbrado   DATETIME
rfc_emisor       VARCHAR(13) NOT NULL
nombre_emisor    VARCHAR(255)
rfc_receptor     VARCHAR(13) NOT NULL
nombre_receptor  VARCHAR(255)
uso_cfdi         VARCHAR(10)
subtotal         DECIMAL(20,6) NOT NULL DEFAULT 0
descuento        DECIMAL(20,6) DEFAULT 0
total            DECIMAL(20,6) NOT NULL DEFAULT 0
moneda           VARCHAR(10) DEFAULT 'MXN'
tipo_cambio      DECIMAL(20,6) DEFAULT 1
metodo_pago      VARCHAR(5)
forma_pago       VARCHAR(5)
condiciones_pago VARCHAR(255)
iva_trasladado   DECIMAL(20,6) DEFAULT 0
iva_retenido     DECIMAL(20,6) DEFAULT 0
isr_retenido     DECIMAL(20,6) DEFAULT 0
ieps             DECIMAL(20,6) DEFAULT 0
estado           ENUM('vigente','cancelado','por_verificar') DEFAULT 'vigente'
cancelado_en     TIMESTAMP NULL
ruta_xml         VARCHAR(500)          -- path cifrado en storage/app/private/
ruta_pdf         VARCHAR(500)
pac_nodo_certificacion VARCHAR(50)
created_at       TIMESTAMP
updated_at       TIMESTAMP

INDEX idx_rfc_id_fecha (rfc_id, fecha)
INDEX idx_uuid (uuid)
INDEX idx_tipo_direccion (tipo, direccion)
INDEX idx_estado (estado)
INDEX idx_fecha (fecha)
INDEX idx_rfc_emisor (rfc_emisor)
INDEX idx_rfc_receptor (rfc_receptor)
```

### Tabla: cfdi_conceptos
```sql
id               BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
cfdi_id          BIGINT UNSIGNED NOT NULL FK→cfdis.id CASCADE DELETE
clave_prod_serv  VARCHAR(10)
clave_unidad     VARCHAR(10)
descripcion      VARCHAR(1000)
cantidad         DECIMAL(20,6)
valor_unitario   DECIMAL(20,6)
importe          DECIMAL(20,6)
descuento        DECIMAL(20,6) DEFAULT 0
iva_tasa         DECIMAL(10,6)
created_at       TIMESTAMP

INDEX idx_cfdi_id (cfdi_id)
```

### Tabla: descarga_jobs
```sql
id               BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
user_id          BIGINT UNSIGNED NOT NULL FK→users.id
rfc_id           BIGINT UNSIGNED NOT NULL FK→rfcs.id
tipo_descarga    ENUM('cfdi','metadata') DEFAULT 'cfdi'
direccion        ENUM('emitido','recibido','ambos') DEFAULT 'ambos'
fecha_inicio     DATE NOT NULL
fecha_fin        DATE NOT NULL
estado           ENUM('pendiente','procesando','completado','error') DEFAULT 'pendiente'
total_solicitado INT DEFAULT 0
total_descargado INT DEFAULT 0
total_nuevos     INT DEFAULT 0
mensaje_error    TEXT
job_id_laravel   VARCHAR(36)       -- UUID del job en la cola Laravel
iniciado_en      TIMESTAMP NULL
completado_en    TIMESTAMP NULL
created_at       TIMESTAMP
updated_at       TIMESTAMP

INDEX idx_user_id (user_id)
INDEX idx_estado (estado)
```

### Tabla: suscripciones
```sql
id               BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
user_id          BIGINT UNSIGNED NOT NULL FK→users.id UNIQUE
plan             ENUM('pro','despacho') NOT NULL
proveedor_pago   ENUM('conekta','stripe') NOT NULL
suscripcion_id   VARCHAR(255)      -- ID en Conekta o Stripe
cliente_id       VARCHAR(255)      -- Customer ID en proveedor
estado           ENUM('activa','cancelada','vencida','trial') DEFAULT 'trial'
trial_ends_at    TIMESTAMP NULL
current_period_start TIMESTAMP NULL
current_period_end   TIMESTAMP NULL
cancelled_at     TIMESTAMP NULL
created_at       TIMESTAMP
updated_at       TIMESTAMP

INDEX idx_user_id (user_id)
INDEX idx_suscripcion_id (suscripcion_id)
```

### Tabla: audit_log
```sql
id               BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
user_id          BIGINT UNSIGNED NOT NULL FK→users.id
rfc_id           BIGINT UNSIGNED FK→rfcs.id
accion           VARCHAR(100) NOT NULL  -- 'descarga_xml','ver_cfdi','export_excel'
ip_address       VARCHAR(45)
user_agent       VARCHAR(500)
detalles         JSON
created_at       TIMESTAMP

INDEX idx_user_id_created (user_id, created_at)
```
