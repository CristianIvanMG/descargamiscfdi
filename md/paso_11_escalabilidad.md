# PASO 11 — PLAN DE ESCALABILIDAD A FUTURO
## ContadorMx · xml.contadormx.net

---

## PLAN DE ESCALABILIDAD A FUTURO

### Fase actual — Hostinger Business (0–5K usuarios)
- Stack actual completo como se describió
- 1 CPU, 2GB RAM, MySQL local, archivos locales
- Queue con driver database, caché con archivos
- Monitoreo: Laravel Telescope en local, logs en producción

### Fase 2 — Hostinger Cloud VPS (5K–20K usuarios)
```
Cambios mínimos de código (solo .env):
- CACHE_STORE=redis          ← Upstash Redis gratis 10K req/día
- QUEUE_CONNECTION=redis     ← Más rápido y confiable que database
- FILESYSTEM_DISK=s3         ← Backblaze B2 (S3 compatible, sin egress fee)
  + AWS_ACCESS_KEY_ID, AWS_SECRET_ACCESS_KEY, AWS_DEFAULT_REGION, AWS_BUCKET

Infraestructura:
- Hostinger Cloud VPS con 4 cores, 8GB RAM
- supervisor para queue workers persistentes (no cron)
- MySQL en servidor separado o PlanetScale
```

### Fase 3 — Arquitectura distribuida (20K+ usuarios)
```
- CDN: Cloudflare free tier para assets estáticos
- Read replicas MySQL para queries pesadas de reportes
- Queues separadas por prioridad: descarga_sat, alertas, emails
- Rate limiting por RFC en Redis (evitar bloqueos del SAT)
- API pública con Laravel Sanctum para plan Despacho
- Microservicio de generación de PDF (separar carga del web server)
```
