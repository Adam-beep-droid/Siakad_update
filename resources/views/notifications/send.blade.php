@extends('layouts.app')
@section('title', 'Kirim Email')
@section('page-title', 'Kirim Email ke Mahasiswa')
@section('page-subtitle', 'Pengiriman notifikasi manual')

@section('content')
<div class="card">
    <div class="card-header py-3">
        <strong><i class="bi bi-envelope-paper me-2"></i>Form Kirim Email</strong>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('notifications.send') }}" class="needs-validation" id="sendEmailForm" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label class="form-label">Subjek</label>
                <input type="text" name="subject" class="form-control" placeholder="Contoh: Pengumuman Jadwal Kuliah" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Pesan</label>
                <textarea name="message" class="form-control" rows="6" placeholder="Tulis isi email di sini..." required></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Lampiran <span class="text-muted">(opsional — jpg, png, pdf, doc, docx, xlsx, zip, maks. 10MB)</span></label>
                <input type="file" name="attachment" class="form-control"
                    accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.zip,.rar,.txt">
                @error('attachment')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label class="form-label">Pilih Penerima</label>
                <div class="border rounded p-3" style="max-height: 300px; overflow-y: auto;">
                    @foreach($mahasiswas as $student)
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="recipients[]" value="{{ $student->email }}" id="student_{{ $student->id }}">
                            <label class="form-check-label" for="student_{{ $student->id }}">
                                {{ $student->nama }} ({{ $student->nim }}) — {{ $student->email }}
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>
            <button type="submit" class="btn btn-primary" id="submitEmailBtn">
                <span class="me-2"><i class="bi bi-send"></i></span>
                Kirim Email
            </button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('sendEmailForm')?.addEventListener('submit', function () {
        const btn = document.getElementById('submitEmailBtn');
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Mengirim...';
        }
        document.getElementById('global-loading')?.classList.remove('d-none');
    });
</script>
@endpush