# PASO 5 — SEGURIDAD DE LA E.FIRMA · ARQUITECTURA CRÍTICA
## ContadorMx · xml.contadormx.net

---

## SEGURIDAD DE LA E.FIRMA — ARQUITECTURA CRÍTICA

### Principio absoluto
La clave privada (.key) y su contraseña **NUNCA tocan el servidor PHP**.
El firmado ocurre 100% en el navegador usando WebCrypto API.

### Flujo completo

```
NAVEGADOR                              SERVIDOR PHP
─────────────────────────────────────────────────────
1. Usuario sube archivo .key          
   (File API — solo en memoria JS)    
                                      
2. WebCrypto importa la clave         
   PKCS#8 sin salir del browser       
                                      
3. JS firma el XML de autenticación   
   del SAT con RSA-SHA256             
                                      
4. JS envía SOLO:                     
   - token_firmado (base64)     ───►  5. PHP valida formato del token
   - rfc (string)               ───►  6. PHP llama al SAT SOAP con token
   - csrf_token                 ───►  7. SAT devuelve token de sesión
                                      8. PHP guarda token SAT en sesión
                                         encriptada (encrypt(), TTL 4h)
                                      9. PHP responde: {session_ok: true}
                                      
10. JS borra la clave de memoria      
    (variable = null, GC)             
```

### Código JavaScript crítico — efirma.js

```javascript
// public/js/efirma.js
// NUNCA modifiques este archivo sin revisión de seguridad

const EFirma = {
  
  // Lee el archivo .key y lo importa como CryptoKey (nunca sale del browser)
  async importarClave(archivoKey, password) {
    const buffer = await archivoKey.arrayBuffer();
    
    // El .key del SAT es PKCS#8 cifrado con 3DES — debemos descifrar primero
    // usando node-forge o una librería compatible con WebCrypto
    const forge = window.forge; // cdn.jsdelivr.net/npm/node-forge
    const pem = this._bufferToPem(buffer);
    const privateKey = forge.pki.decryptRsaPrivateKey(pem, password);
    
    if (!privateKey) throw new Error('Contraseña incorrecta o archivo .key inválido');
    
    // Convertir a formato WebCrypto
    const pkcs8Der = forge.asn1.toDer(forge.pki.privateKeyToAsn1(privateKey)).getBytes();
    const pkcs8Buffer = this._stringToArrayBuffer(pkcs8Der);
    
    return await crypto.subtle.importKey(
      'pkcs8',
      pkcs8Buffer,
      { name: 'RSASSA-PKCS1-v1_5', hash: 'SHA-256' },
      false,       // no exportable — clave nunca sale de WebCrypto
      ['sign']
    );
  },

  // Firma el XML de autenticación del SAT
  async firmarAutenticacion(cryptoKey, rfc) {
    const xmlAutenticacion = this._generarXmlAutenticacion(rfc);
    const encoder = new TextEncoder();
    const datos = encoder.encode(xmlAutenticacion);
    
    const firma = await crypto.subtle.sign(
      'RSASSA-PKCS1-v1_5',
      cryptoKey,
      datos
    );
    
    return {
      xml_original: xmlAutenticacion,
      firma_b64: btoa(String.fromCharCode(...new Uint8Array(firma))),
    };
  },

  // Envía SOLO el token firmado al servidor — NUNCA la clave privada
  async autenticarConServidor(rfc, tokenFirmado) {
    const response = await fetch('/api/sat/autenticar', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
      },
      body: JSON.stringify({
        rfc: rfc,
        token_firmado: tokenFirmado.firma_b64,
        xml_autenticacion: tokenFirmado.xml_original,
      }),
    });
    return await response.json();
  },

  // Limpia toda referencia a la clave de memoria
  limpiarClave(cryptoKey) {
    cryptoKey = null;
    // El GC de JS se encargará del resto
    // Avisamos al usuario que la sesión duró lo que duró
  },

  _generarXmlAutenticacion(rfc) {
    const uuid = crypto.randomUUID();
    const ahora = new Date().toISOString();
    const expira = new Date(Date.now() + 5 * 60 * 1000).toISOString();
    return `<?xml version="1.0" encoding="UTF-8"?>
<s:Envelope xmlns:s="http://schemas.xmlsoap.org/soap/envelope/">
  <s:Header>
    <o:Security xmlns:o="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd">
      <u:Timestamp xmlns:u="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd">
        <u:Created>${ahora}</u:Created>
        <u:Expires>${expira}</u:Expires>
      </u:Timestamp>
    </o:Security>
  </s:Header>
  <s:Body>
    <Autentica xmlns="http://DescargaMasivaTerceros.gob.mx"/>
  </s:Body>
</s:Envelope>`;
  },

  _bufferToPem(buffer) {
    const bytes = new Uint8Array(buffer);
    const binary = bytes.reduce((acc, b) => acc + String.fromCharCode(b), '');
    const b64 = btoa(binary);
    return `-----BEGIN ENCRYPTED PRIVATE KEY-----\n${b64.match(/.{1,64}/g).join('\n')}\n-----END ENCRYPTED PRIVATE KEY-----`;
  },

  _stringToArrayBuffer(str) {
    const buf = new ArrayBuffer(str.length);
    const view = new Uint8Array(buf);
    for (let i = 0; i < str.length; i++) view[i] = str.charCodeAt(i) & 0xff;
    return buf;
  },
};
```
