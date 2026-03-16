<?php

namespace App\Observers;

use App\Models\AuditLog;

class AuditObserver
{
    protected function baseData($model)
    {
        return [
            'model_type' => class_basename($model),
            'model_id' => $model->id,
            'user_id' => auth('api')->id(),
            'ip_address' => request()->ip()
        ];
    }

    public function created($model): void
    {
        AuditLog::create(array_merge(
            $this->baseData($model),
            [
                'event' => 'created',
                'old_values' => null,
                'new_values' => $model->toArray()
            ]
        ));
    }

    public function updated($model): void
    {
        AuditLog::create(array_merge(
            $this->baseData($model),
            [
                'event' => 'updated',
                'old_values' => $model->getOriginal(),
                'new_values' => $model->getChanges()
            ]
        ));
    }

    public function deleted($model): void
    {
        AuditLog::create(array_merge(
            $this->baseData($model),
            [
                'event' => 'deleted',
                'old_values' => $model->getOriginal(),
                'new_values' => null
            ]
        ));
    }
}