window.contadorMxDashboard = (metricasUrl) => ({
    metricasUrl,
    metricas: {
        cfdi_emitidos: 0,
        cfdi_recibidos: 0,
        iva_trasladado: 0,
        iva_acreditable: 0
    },

    async load() {
        if (!this.metricasUrl) {
            return;
        }

        const response = await fetch(this.metricasUrl, {
            headers: {
                Accept: 'application/json'
            }
        });

        if (response.ok) {
            this.metricas = await response.json();
        }
    }
});
