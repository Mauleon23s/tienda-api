<?php

namespace App\Observers;

use App\Models\AuditLog;

class AuditObserver
{
    public function created($model)
    {
        AuditLog::create([
            'model_type' => class_basename($model),
            'model_id' => $model->id,
            'event' => 'created',
            'old_values' => null,
            'new_values' => $model->toArray()
        ]);
    }

    public function updated($model)
    {
        AuditLog::create([
            'model_type' => class_basename($model),
            'model_id' => $model->id,
            'event' => 'updated',
            'old_values' => $model->getOriginal(),
            'new_values' => $model->getChanges()
        ]);
    }

    public function deleted($model)
    {
        AuditLog::create([
            'model_type' => class_basename($model),
            'model_id' => $model->id,
            'event' => 'deleted',
            'old_values' => $model->getOriginal(),
            'new_values' => null
        ]);
    }
}