<div class="container py-3">
    <h1 class="h5 mb-3">Audit Logs</h1>

    <form class="row g-2 mb-3" method="get">
        <div class="col-auto">
            <input name="q" class="form-control form-control-sm" placeholder="Search…" value="{{ request('q') }}">
        </div>
        <div class="col-auto">
            <select name="action" class="form-select form-select-sm">
                <option value="">All actions</option>
                @foreach (['create', 'update', 'delete', 'hard_delete'] as $a)
                    <option value="{{ $a }}" @selected(request('action') === $a)>
                        {{ ucfirst(str_replace('_', ' ', $a)) }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-auto">
            <input name="adminId" class="form-control form-control-sm" placeholder="Admin ID"
                value="{{ request('adminId') }}">
        </div>
        <div class="col-auto">
            <input name="entityId" class="form-control form-control-sm" placeholder="Entity ID"
                value="{{ request('entityId') }}">
        </div>
        <div class="col-auto">
            <input type="date" name="from" class="form-control form-control-sm" value="{{ request('from') }}">
        </div>
        <div class="col-auto">
            <input type="date" name="to" class="form-control form-control-sm" value="{{ request('to') }}">
        </div>
        <div class="col-auto">
            <select name="perPage" class="form-select form-select-sm">
                @foreach ([10, 20, 50, 100] as $pp)
                    <option value="{{ $pp }}" @selected(request('perPage', 20) == $pp)>{{ $pp }}/page</option>
                @endforeach
            </select>
        </div>
        <div class="col-auto">
            <button class="btn btn-sm btn-primary">Filter</button>
            <a href="{{ route('admin.audit.index') }}" class="btn btn-sm btn-light">Reset</a>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-sm align-middle">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Admin</th>
                    <th>Date</th>
                    <th>Action</th>
                    <th>Model</th>
                    <th>Entity</th>
                    <th>Summary (before → after)</th>
                    <th>IP</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($logs as $row)
                    @php
                        $badge = match ($row->action) {
                            'create' => 'success',
                            'update' => 'warning',
                            'delete' => 'secondary',
                            'hard_delete' => 'danger',
                            default => 'light',
                        };
                        $max = 8;
                    @endphp
                    <tr>
                        <td class="text-muted">#{{ $row->id }}</td>
                        <td class="text-nowrap">#{{ $row->adminId }}</td>
                        <td class="text-nowrap">
                            {{ \Illuminate\Support\Carbon::parse($row->createdAt)->format('d M Y, H:i') }}</td>
                        <td><span
                                class="badge bg-{{ $badge }}">{{ ucfirst(str_replace('_', ' ', $row->action)) }}</span>
                        </td>
                        <td>{{ $row->modelChanged }}</td>
                        <td>#{{ $row->entityId }}</td>
                        <td class="small">
                            @if (empty($row->pairs))
                                <em>No field-level changes.</em>
                            @else
                                <ul class="mb-1">
                                    @foreach (collect($row->pairs)->take($max) as $field => $vals)
                                        @php
                                            [$old, $new] = $vals;
                                            $oldS =
                                                is_scalar($old) || is_null($old)
                                                    ? (string) ($old ?? '')
                                                    : json_encode($old);
                                            $newS =
                                                is_scalar($new) || is_null($new)
                                                    ? (string) ($new ?? '')
                                                    : json_encode($new);
                                        @endphp
                                        <li>
                                            <strong>{{ $field }}</strong>:
                                            <span
                                                class="text-muted">{{ $oldS === '' ? '∅' : \Illuminate\Support\Str::limit($oldS, 80) }}</span>
                                            →
                                            <span>{{ $newS === '' ? '∅' : \Illuminate\Support\Str::limit($newS, 80) }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                                @if (count($row->pairs) > $max)
                                    <span class="text-muted">+ {{ count($row->pairs) - $max }} more…</span>
                                @endif

                                <details class="mt-1">
                                    <summary>View raw</summary>
                                    <pre class="bg-light p-2 rounded small">{{ json_encode(json_decode($row->changes, true), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                </details>
                            @endif
                        </td>
                        <td class="text-muted">{{ $row->ip }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted">No audit logs yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-end">
        {{ $logs->links() }}
    </div>
</div>
