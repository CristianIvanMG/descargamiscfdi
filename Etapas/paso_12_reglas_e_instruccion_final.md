# PASO 12 — REGLAS DE CÓDIGO E INSTRUCCIÓN FINAL
## ContadorMx · xml.contadormx.net

---

## REGLAS DE CÓDIGO PARA TODA RESPUESTA

1. **Código completo y funcional** — sin `// ... resto del código`. Siempre el archivo entero.
2. **PHP 8.2+** — usar match, enums, fibers, readonly, named arguments donde aplique.
3. **Laravel 11** — usar las convenciones actuales (no Laravel 8 patterns).
4. **Sin comentarios obvios** — solo comentar lo que no es evidente.
5. **Bootstrap 5** — sin CSS inline excepto casos muy específicos.
6. **Alpine.js para reactividad** — no jQuery, no React, no Vue.
7. **Seguridad primero** — validar siempre con Form Requests, sanitizar outputs con Blade `{{ }}`.
8. **Accesibilidad** — etiquetas aria en formularios de e.firma (datos sensibles).
9. **Manejo de errores** — try/catch con mensajes útiles para el contador, logs internos detallados.
10. **Multilenguaje listo** — usar `__('clave')` y archivos de lang desde el inicio.

---

## INSTRUCCIÓN FINAL

Cuando te pida implementar cualquier componente de ContadorPro, debes:

1. Recordar todo el contexto de este documento como tu fuente de verdad
2. Respetar el stack sin proponer alternativas salvo que se pidan explícitamente
3. Generar código completo, listo para copiar y pegar en producción
4. Señalar si algo requiere configuración adicional en Hostinger hPanel
5. Indicar el comando exacto a correr por SSH cuando sea necesario
6. Pensar siempre en los 3 planos simultáneamente: lo que funciona hoy, lo que escala mañana, lo que gana dinero

**El producto es real. El mercado existe. El stack está definido. Ahora construye.**
