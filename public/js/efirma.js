window.contadorMxEfirma = () => ({
    cerFile: null,
    keyFile: null,
    password: '',
    signedToken: '',
    error: '',

    setCer(event) {
        this.cerFile = event.target.files[0] ?? null;
    },

    setKey(event) {
        this.keyFile = event.target.files[0] ?? null;
    },

    async signChallenge() {
        this.error = '';

        if (!window.crypto?.subtle) {
            this.error = 'Tu navegador no soporta WebCrypto.';
            return;
        }

        if (!this.cerFile || !this.keyFile || this.password.length === 0) {
            this.error = 'Selecciona certificado, llave privada y contraseña.';
            return;
        }

        const challenge = `${Date.now()}:${this.cerFile.name}:${this.keyFile.name}`;
        const encoded = new TextEncoder().encode(challenge);
        const digest = await crypto.subtle.digest('SHA-256', encoded);
        const bytes = Array.from(new Uint8Array(digest));

        this.signedToken = btoa(String.fromCharCode(...bytes));
    }
});
