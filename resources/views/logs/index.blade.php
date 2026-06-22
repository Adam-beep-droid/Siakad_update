@extends('layouts.app')
@section('title', 'Activity Log')
@section('page-title', 'Activity Log')
@section('page-subtitle', 'Riwayat seluruh aktivitas di sistem')

@section('content')
<div class="card">
    <div class="card-header py-3 d-flex align-items-center gap-2">
        <i class="bi bi-journal-text text-primary"></i>
        <strong>Build Log — Semua Aktivitas</strong>
        <span class="badge bg-secondary ms-auto" id="logCount">{{ $logs->total() }} total</span>
        <span class="badge bg-success" id="liveBadge">Live</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Waktu</th>
                    <th>Aksi</th>
                    <th>Deskripsi</th>
                    <th>Algoritma</th>
                    <th>Waktu Eksekusi</th>
                    <th>Komentar</th>
                    <th></th>
                </tr>
            </thead>
            <tbody id="logTableBody">
                @forelse($logs as $log)
                <tr data-log-id="{{ $log->id }}">
                    <td class="text-muted small">{{ $log->id }}</td>
                    <td class="small text-muted" style="white-space:nowrap">
                        {{ $log->created_at->format('d M Y') }}<br>
                        <strong>{{ $log->created_at->format('H:i:s') }}</strong>
                    </td>
                    <td>
                        <span class="badge bg-{{ $log->action_badge }}">
                            <i class="{{ $log->action_icon }} me-1"></i>{{ strtoupper($log->action) }}
                        </span>
                    </td>
                    <td class="small">
                        {{ $log->description }}
                        @if($log->target)
                            <br><code class="text-primary small">{{ $log->target }}</code>
                        @endif
                    </td>
                    <td class="small">
                        @if($log->algorithm)
                            <span class="badge" style="background:#ede9fe;color:#5b21b6;font-size:.7rem">
                                {{ $log->algorithm }}
                            </span>
                            @if($log->complexity)
                                <br><code class="text-muted" style="font-size:.7rem">{{ $log->complexity }}</code>
                            @endif
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td class="small">
                        @if($log->execution_time)
                            <span class="fw-600 text-primary">{{ round($log->execution_time * 1000, 4) }} ms</span>
                            <br><span class="text-muted" style="font-size:.7rem">{{ $log->execution_time }} s</span>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td class="small">
                        @if($log->user_comment)
                            <div class="p-1 rounded" style="background:#f1f5f9;font-size:.78rem;max-width:150px">
                                💬 {{ $log->user_comment }}
                            </div>
                        @else
                            <form method="POST" action="{{ route('logs.comment', $log) }}" class="d-flex gap-1">
                                @csrf
                                <input type="text" name="comment" class="form-control form-control-sm"
                                       placeholder="Tambah komentar..." style="min-width:120px;font-size:.75rem">
                                <button type="submit" class="btn btn-sm btn-outline-primary py-0" title="Simpan">
                                    <i class="bi bi-send" style="font-size:.7rem"></i>
                                </button>
                            </form>
                        @endif
                    </td>
                    <td class="small text-muted">
                        {{ $log->user?->name ?? 'System' }}
                        @if($log->data_count)
                            <br><span style="font-size:.7rem">n={{ $log->data_count }}</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr id="emptyLogRow">
                    <td colspan="8" class="text-center py-5 text-muted">
                        <i class="bi bi-journal-x display-6 d-block mb-2"></i>
                        Belum ada aktivitas tercatat.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($logs->hasPages())
    <div class="card-footer">
        {{ $logs->links() }}
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    function escapeHtml(value) {
        return String(value)
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#39;');
    }

    function formatExecution(time) {
        if (!time) return '<span class="text-muted">—</span>';
        return `<span class="fw-600 text-primary">${(time * 1000).toFixed(4)} ms</span><br><span class="text-muted" style="font-size:.7rem">${time} s</span>`;
    }

    function addLogRow(log) {
        const tbody = document.getElementById('logTableBody');
        if (!tbody) return;

        if (document.getElementById('emptyLogRow')) {
            document.getElementById('emptyLogRow').remove();
        }

        if (tbody.querySelector(`[data-log-id="${log.id}"]`)) return;

        const row = document.createElement('tr');
        row.dataset.logId = log.id;
        row.innerHTML = `
            <td class="text-muted small">${escapeHtml(log.id)}</td>
            <td class="small text-muted" style="white-space:nowrap">${escapeHtml(log.created_at)}</td>
            <td><span class="badge bg-${escapeHtml(log.action_badge)}"><i class="${escapeHtml(log.action_icon)} me-1"></i>${escapeHtml(log.action.toUpperCase())}</span></td>
            <td class="small">${escapeHtml(log.description)}${log.target ? `<br><code class="text-primary small">${escapeHtml(log.target)}</code>` : ''}</td>
            <td class="small">${log.algorithm ? `<span class="badge" style="background:#ede9fe;color:#5b21b6;font-size:.7rem">${escapeHtml(log.algorithm)}</span>${log.complexity ? `<br><code class="text-muted" style="font-size:.7rem">${escapeHtml(log.complexity)}</code>` : ''}` : '<span class="text-muted">—</span>'}</td>
            <td class="small">${formatExecution(log.execution_time)}</td>
            <td class="small">${log.user_comment ? `<div class="p-1 rounded" style="background:#f1f5f9;font-size:.78rem;max-width:150px">💬 ${escapeHtml(log.user_comment)}</div>` : '<span class="text-muted">—</span>'}</td>
            <td class="small text-muted">${escapeHtml(log.user_name)}${log.data_count ? `<br><span style="font-size:.7rem">n=${escapeHtml(log.data_count)}</span>` : ''}</td>
        `;
        tbody.prepend(row);

        const countEl = document.getElementById('logCount');
        if (countEl) {
            countEl.textContent = `${parseInt(countEl.textContent, 10) + 1} total`;
        }
    }

    let lastLogId = {{ $logs->first()?->id ?? 0 }};

    function pollLogs() {
        fetch('{{ route('logs.latest') }}')
            .then(res => res.json())
            .then(data => {
                if (!Array.isArray(data) || !data.length) return;
                data.forEach(log => {
                    if (log.id > lastLogId) {
                        addLogRow(log);
                        lastLogId = log.id;
                    }
                });
            })
            .catch(() => {});
    }

    setInterval(pollLogs, 4000);
</script>
@endpush
