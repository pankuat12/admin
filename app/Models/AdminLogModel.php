<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class AdminLogModel extends Model
{
    public function getLogs($p)
    {
        $q = DB::table('audit_logs');

        // Filters
        if (!empty($p['action'])) {
            $q->where('action', $p['action']);
        }
        if (!empty($p['model'])) {
            $q->where('modelChanged', $p['model']);
        }
        if (!empty($p['adminId'])) {
            $q->where('adminId', (int)$p['adminId']);
        }
        if (!empty($p['entityId'])) {
            $q->where('entityId', (int)$p['entityId']);
        }
        if (!empty($p['from'])) {
            $q->where('createdAt', '>=', $p['from'] . ' 00:00:00');
        }
        if (!empty($p['to'])) {
            $q->where('createdAt', '<=', $p['to'] . ' 23:59:59');
        }

        // Free-text search
        if (!empty($p['q'])) {
            $term = '%' . $p['q'] . '%';
            $q->where(function ($w) use ($term) {
                $w->where('modelChanged', 'like', $term)
                    ->orWhere('action', 'like', $term)
                    ->orWhere('ip', 'like', $term)
                    ->orWhere('adminId', 'like', $term)
                    ->orWhere('entityId', 'like', $term);
            });
        }

        $q->orderByDesc('id');

        $perPage = !empty($p['perPage']) ? (int)$p['perPage'] : 20;
        return $q->paginate($perPage);
    }

    public function changedPairs($row): array
    {
        $changes = $row->changes;
        if (is_string($changes)) {
            $changes = json_decode($changes, true);
        } elseif (is_object($changes)) {
            $changes = json_decode(json_encode($changes), true);
        }
        $changes = is_array($changes) ? $changes : [];

        $before = $changes['before'] ?? [];
        $after  = $changes['after']  ?? [];
        $diff   = $changes['diff']   ?? [];

        $pairs = [];

        // For "update" entries you showed: diff is {"field":{"old":x,"new":y}}
        foreach ($diff as $field => $val) {
            if (is_array($val) && array_key_exists('old', $val)) {
                $pairs[$field] = [$val['old'] ?? null, $val['new'] ?? null];
            }
        }
        // For "create" entries you showed: diff is {"field": value}
        if ($row->action === 'create' || $row->action === 'created') {
            foreach ($diff as $field => $val) {
                if (!isset($pairs[$field])) {
                    $pairs[$field] = [null, $val];
                }
            }
            // fallback if diff empty → compare before/after
            if (empty($diff)) {
                foreach (array_keys($after) as $f) {
                    $pairs[$f] = [$before[$f] ?? null, $after[$f]];
                }
            }
        }
        // For "hard_delete": your diff uses {"field": value} meaning removed
        if ($row->action === 'hard_delete' || $row->action === 'deleted_hard') {
            foreach ($diff as $field => $val) {
                if (!isset($pairs[$field])) {
                    $pairs[$field] = [$val, null];
                }
            }
            if (empty($diff)) {
                foreach (array_keys($before) as $f) {
                    $pairs[$f] = [$before[$f], null];
                }
            }
        }
        // For "delete" (soft delete): your diff is {"isTrashed":{"old":0,"new":1}}
        if ($row->action === 'delete' || $row->action === 'deleted') {
            if (empty($pairs)) {
                foreach (array_keys($before + $after) as $f) {
                    $old = $before[$f] ?? null;
                    $new = $after[$f] ?? null;
                    if ($old !== $new) {
                        $pairs[$f] = [$old, $new];
                    }
                }
            }
        }
        return $pairs;
    }
}
