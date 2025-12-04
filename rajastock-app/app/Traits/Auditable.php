<?php

namespace App\Traits;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

trait Auditable
{
    public static function bootAuditable()
    {
        static::created(function ($model) {
            $model->saveAuditLog('created');
        });

        static::updated(function ($model) {
            $model->saveAuditLog('updated');
        });

        static::deleting(function ($model) {
            $model->saveAuditLog('deleted');
        });
    }

    public function saveAuditLog($event)
    {
        $oldValues = null;
        $newValues = null;
        if ($event === 'created') {
            $newValues = $this->getAttributes();
        }

        if ($event === 'updated') {
            $oldValues = $this->getOriginal();
            $newValues = $this->getChanges();
        }

        if ($event === 'deleted') {           
            $oldValues = $this->getAttributes();
            $newValues = ['status' => 'deleted'];
        }

        AuditLog::create([
            'user_id'    => Auth::id(),
            'event'      => $event,
            'model'      => get_class($this),
            'model_id'   => $this->getKey(),
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip'         => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
