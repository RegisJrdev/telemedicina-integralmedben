<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sms_templates', function (Blueprint $table) {
            // null  = template geral (enviado a todos os pacientes)
            // valor = enviado apenas quando o paciente selecionou esse plano
            $table->string('plan_id')->nullable()->after('event');
        });
    }

    public function down(): void
    {
        Schema::table('sms_templates', function (Blueprint $table) {
            $table->dropColumn('plan_id');
        });
    }
};
