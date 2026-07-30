@extends('layouts.agent')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            @php
                                $isSpanish =
                                    $booking->language && strpos(strtolower($booking->language), 'spanish') !== false;
                            @endphp
                            @if ($isSpanish)
                                Editar Correo de Autorización
                            @else
                                Edit Authorization Email
                            @endif
                        </h3>
                        <div class="card-tools">
                            <span class="badge badge-info">
                                @if ($isSpanish)
                                    🇪🇸 Español
                                @else
                                    🇬🇧 English
                                @endif
                            </span>
                            <span class="badge badge-secondary">
                                @if ($isSpanish)
                                    Plantilla: {{ $booking->service_type ?? 'New Booking' }} (ES)
                                @else
                                    Template: {{ $booking->service_type ?? 'New Booking' }} (EN)
                                @endif
                            </span>
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('agent.authorize.preview', $booking->id) }}" method="POST" id="emailForm">
                            @csrf

                            <div class="form-group">
                                <label for="main_content">
                                    @if ($isSpanish)
                                        Contenido Principal <span class="text-danger">*</span>
                                    @else
                                        Main Content <span class="text-danger">*</span>
                                    @endif
                                </label>
                                <textarea name="main_content" id="main_content" class="form-control summernote" rows="15">{{ old('main_content', $mainContent ?? '') }}</textarea>
                                @error('main_content')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="purchase_summary">
                                    @if ($isSpanish)
                                        Resumen de Compra
                                    @else
                                        Purchase Summary
                                    @endif
                                </label>
                                <textarea name="purchase_summary" id="purchase_summary" class="form-control summernote" rows="10">{{ old('purchase_summary', $purchaseSummary ?? '') }}</textarea>
                            </div>

                            <div class="form-group">
                                <div class="alert alert-info">
                                    @if ($isSpanish)
                                        <i class="fas fa-info-circle"></i>
                                        <strong>Idioma detectado:</strong> Español - Usando plantilla en español
                                    @else
                                        <i class="fas fa-info-circle"></i>
                                        <strong>Detected Language:</strong> English - Using English template
                                    @endif
                                </div>
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">
                                    @if ($isSpanish)
                                        <i class="fas fa-eye"></i> Vista Previa
                                    @else
                                        <i class="fas fa-eye"></i> Preview
                                    @endif
                                </button>
                                <a href="{{ route('agent.bookings.index') }}" class="btn btn-secondary">
                                    @if ($isSpanish)
                                        <i class="fas fa-times"></i> Cancelar
                                    @else
                                        <i class="fas fa-times"></i> Cancel
                                    @endif
                                </a>
                                @if ($booking->auth_email_sent_at)
                                    <button type="button" class="btn btn-warning" onclick="confirmResend(this)">
                                        @if ($isSpanish)
                                            <i class="fas fa-redo"></i> Reenviar
                                        @else
                                            <i class="fas fa-redo"></i> Resend
                                        @endif
                                    </button>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

    @push('scripts')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.18/summernote-bs4.min.js"></script>
        <link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.18/summernote-bs4.min.css" rel="stylesheet">

        <script>
            $(document).ready(function() {
                $('.summernote').summernote({
                    height: 300,
                    toolbar: [
                        ['style', ['bold', 'italic', 'underline', 'clear']],
                        ['font', ['strikethrough', 'superscript', 'subscript']],
                        ['fontsize', ['fontsize']],
                        ['color', ['color']],
                        ['para', ['ul', 'ol', 'paragraph']],
                        ['table', ['table']],
                        ['insert', ['link', 'picture', 'video']],
                        ['view', ['fullscreen', 'codeview', 'help']]
                    ]
                });
            });

            function confirmResend(btn) {
                @if ($isSpanish)
                    if (confirm('¿Está seguro de que desea reenviar este correo de autorización?')) {
                        if (btn) btn.disabled = true;
                        document.getElementById('emailForm').action = "{{ route('agent.authorize.resend', $booking->id) }}";
                        document.getElementById('emailForm').submit();
                    }
                @else
                    if (confirm('Are you sure you want to resend this authorization email?')) {
                        if (btn) btn.disabled = true;
                        document.getElementById('emailForm').action = "{{ route('agent.authorize.resend', $booking->id) }}";
                        document.getElementById('emailForm').submit();
                    }
                @endif
            }
        </script>
    @endpush

    @push('styles')
        <style>
            .note-editor {
                border-radius: 4px;
            }

            .note-editor .note-toolbar {
                background: #f8f9fa;
                border-bottom: 1px solid #dee2e6;
            }
        </style>
    @endpush
