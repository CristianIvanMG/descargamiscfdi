window.contadorMxDescarga = (estadoUrl) => ({
    estadoUrl,
    estado: 'pendiente',
    totalCfdi: 0,
    mensajeError: '',

    async refresh() {
        if (!this.estadoUrl) {
            return;
        }

        const response = await fetch(this.estadoUrl, {
            headers: {
                Accept: 'application/json'
            }
        });

        if (!response.ok) {
            this.mensajeError = 'No fue posible consultar el estado.';
            return;
        }

        const data = await response.json();
        this.estado = data.estado ?? this.estado;
        this.totalCfdi = data.total_cfdi ?? 0;
        this.mensajeError = data.mensaje_error ?? '';
    },

    start() {
        this.refresh();
        window.setInterval(() => this.refresh(), 8000);
    }
});
