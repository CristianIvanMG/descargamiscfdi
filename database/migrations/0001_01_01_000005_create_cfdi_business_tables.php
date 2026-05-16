<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rfcs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('rfc', 13);
            $table->string('razon_social');
            $table->string('regimen_fiscal')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
            $table->unique(['user_id', 'rfc']);
        });

        Schema::create('descarga_jobs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('rfc_id')->nullable()->constrained('rfcs')->nullOnDelete();
            $table->string('estado', 40)->default('pendiente');
            $table->string('tipo', 20);
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->string('solicitud_id')->nullable()->index();
            $table->string('paquete_id')->nullable();
            $table->unsignedInteger('total_cfdi')->default(0);
            $table->text('mensaje_error')->nullable();
            $table->timestamp('iniciado_en')->nullable();
            $table->timestamp('terminado_en')->nullable();
            $table->timestamps();
            $table->index(['user_id', 'rfc_id', 'tipo', 'fecha_inicio', 'fecha_fin']);
        });

        Schema::create('cfdis', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('rfc_id')->nullable()->constrained('rfcs')->nullOnDelete();
            $table->string('uuid', 36)->index();
            $table->string('tipo', 20)->index();
            $table->string('serie')->nullable();
            $table->string('folio')->nullable();
            $table->string('rfc_emisor', 13)->index();
            $table->string('nombre_emisor')->nullable();
            $table->string('rfc_receptor', 13)->index();
            $table->string('nombre_receptor')->nullable();
            $table->dateTime('fecha_emision')->index();
            $table->decimal('subtotal', 14, 2)->default(0);
            $table->decimal('descuento', 14, 2)->default(0);
            $table->decimal('iva', 14, 2)->default(0);
            $table->decimal('total', 14, 2)->default(0);
            $table->string('moneda', 10)->default('MXN');
            $table->string('estatus', 30)->default('vigente');
            $table->string('xml_path')->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'uuid']);
            $table->index(['user_id', 'tipo', 'fecha_emision']);
        });

        Schema::create('cfdi_conceptos', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('cfdi_id')->constrained('cfdis')->cascadeOnDelete();
            $table->string('clave_prod_serv')->nullable();
            $table->text('descripcion')->nullable();
            $table->decimal('cantidad', 14, 6)->default(0);
            $table->decimal('valor_unitario', 14, 6)->default(0);
            $table->decimal('importe', 14, 2)->default(0);
            $table->decimal('descuento', 14, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('suscripciones', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('plan', 40);
            $table->string('proveedor_pago', 40)->nullable();
            $table->string('proveedor_id')->nullable();
            $table->string('estatus', 40)->default('activa');
            $table->timestamp('periodo_inicio')->nullable();
            $table->timestamp('periodo_fin')->nullable();
            $table->boolean('renovacion_automatica')->default(false);
            $table->timestamps();
            $table->index(['user_id', 'estatus', 'plan']);
        });

        Schema::create('donations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('proveedor_pago', 40)->default('mercadopago');
            $table->string('proveedor_id')->nullable();
            $table->string('estado', 40)->default('pendiente');
            $table->decimal('monto', 10, 2)->nullable();
            $table->timestamp('donated_at')->nullable();
            $table->timestamp('active_until')->nullable();
            $table->timestamps();
            $table->index(['user_id', 'estado', 'active_until']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('donations');
        Schema::dropIfExists('suscripciones');
        Schema::dropIfExists('cfdi_conceptos');
        Schema::dropIfExists('cfdis');
        Schema::dropIfExists('descarga_jobs');
        Schema::dropIfExists('rfcs');
    }
};
