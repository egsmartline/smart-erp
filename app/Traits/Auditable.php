<?php

namespace App\Traits;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

trait Auditable
{
    protected static function bootAuditable(): void
    {
        static::created(function ($model) {
            $model->audit('create', [], $model->getAuditableAttributes());
        });

        static::updated(function ($model) {
            $old = array_intersect_key(
                $model->getOriginal(),
                array_flip($model->getAuditableFields())
            );
            $new = $model->getAuditableAttributes();
            if ($old !== $new) {
                $model->audit('update', $old, $new);
            }
        });

        static::deleted(function ($model) {
            if (!in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses_recursive(static::class)) || !$model->isForceDeleting()) {
                $model->audit('delete', $model->getAuditableAttributes(), []);
            }
        });

        if (in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses_recursive(static::class))) {
            static::restored(function ($model) {
                $model->audit('restore', [], $model->getAuditableAttributes());
            });
        }
    }

    protected function getAuditableFields(): array
    {
        return array_diff($this->getFillable(), ['id', 'tenant_id', 'created_at', 'updated_at', 'deleted_at']);
    }

    protected function getAuditableAttributes(): array
    {
        $fields = $this->getAuditableFields();
        $data = [];
        foreach ($fields as $field) {
            if (array_key_exists($field, $this->getAttributes())) {
                $data[$field] = $this->{$field};
            }
        }
        return $data;
    }

    protected function audit(string $action, array $oldValues, array $newValues): void
    {
        if (!Auth::check()) {
            return;
        }

        AuditLog::create([
            'tenant_id' => session('current_tenant_id') ?? Auth::user()->tenant_id,
            'user_id' => Auth::id(),
            'action' => $action,
            'model' => static::class,
            'model_id' => $this->getKey(),
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'description' => $this->getAuditDescription($action),
        ]);
    }

    protected function getAuditDescription(string $action): string
    {
        $modelName = class_basename(static::class);
        $label = method_exists($this, 'auditLabel') ? $this->auditLabel() : "{$modelName} #{$this->getKey()}";
        return match ($action) {
            'create' => "تم إنشاء {$label}",
            'update' => "تم تحديث {$label}",
            'delete' => "تم حذف {$label}",
            'restore' => "تم استعادة {$label}",
            default => "{$action} على {$label}",
        };
    }
}
