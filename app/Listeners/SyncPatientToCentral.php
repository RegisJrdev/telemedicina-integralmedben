<?php

namespace App\Listeners;

use App\Events\PatientCreated;
use App\Models\CentralPatient;
use App\Models\CentralPatientAnswer;
use App\Models\Patient;
use App\Models\Question;
use Illuminate\Contracts\Queue\ShouldQueue;

class SyncPatientToCentral implements ShouldQueue
{
    public function handle(PatientCreated $event): void
    {
        $cpfQuestionId = Question::whereIn('id', array_keys($event->answers))
            ->where('is_unique', true)
            ->value('id');

        $cpf = $cpfQuestionId
            ? preg_replace('/\D/', '', $event->answers[(string) $cpfQuestionId] ?? '')
            : null;

        if (!$cpf) {
            return;
        }

        $centralPatient = CentralPatient::firstOrCreate(
            ['cpf' => $cpf],
            ['tenant_id' => $event->tenantId]
        );

        foreach ($event->answers as $questionId => $answer) {
            CentralPatientAnswer::updateOrCreate(
                [
                    'central_patient_id' => $centralPatient->id,
                    'question_id' => $questionId,
                ],
                ['answer' => $answer]
            );
        }

        // Atualiza o tenant patient com a referência ao central
        Patient::on('tenant')
            ->where('id', $event->tenantPatientId)
            ->update(['central_patient_id' => $centralPatient->id]);
    }
}
