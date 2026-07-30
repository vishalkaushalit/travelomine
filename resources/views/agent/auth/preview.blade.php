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
                                Vista Previa del Correo de Autorización
                            @else
                                Authorization Email Preview
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
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Email Preview -->
                        <div class="email-preview"
                            style="border:1px solid #ddd; padding:20px; background:#fff; border-radius:4px;">
                            <div
                                style="background:#1e3a8a; color:#fff; padding:20px; font-size:24px; font-weight:bold; margin:-20px -20px 20px -20px; border-radius:4px 4px 0 0;">
                                @if ($isSpanish)
                                    Autorización de Pago
                                @else
                                    Payment Authorization
                                @endif
                            </div>

                            <div style="padding:0 20px;">
                                {!! $mainContent !!}

                                @include('components.flight-itinerary-email')
                                {!! $purchaseSummary !!}
                                @include('components.flight-terms')
                            </div>
                        </div>

                        <div class="mt-4">
                            <form action="{{ route('agent.authorize.send', $booking->id) }}" method="POST" onsubmit="if(this.dataset.submitted) return false; this.dataset.submitted = true; this.querySelector('button[type=submit]').disabled = true;">
                                @csrf
                                <input type="hidden" name="main_content" value="{{ $mainContent }}">
                                <input type="hidden" name="purchase_summary" value="{{ $purchaseSummary }}">

                                <div class="form-group">
                                    <div class="alert alert-warning">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        @if ($isSpanish)
                                            <strong>Confirmación:</strong> Por favor, revise el contenido del correo antes
                                            de enviarlo.
                                            Una vez enviado, no se puede deshacer.
                                        @else
                                            <strong>Confirmation:</strong> Please review the email content before sending.
                                            Once sent, it cannot be undone.
                                        @endif
                                    </div>
                                </div>

                                <div class="form-group">
                                    <button type="submit" class="btn btn-success"
                                        onclick="return confirm('@if ($isSpanish) ¿Está seguro de que desea enviar este correo? @else Are you sure you want to send this email? @endif')">
                                        @if ($isSpanish)
                                            <i class="fas fa-paper-plane"></i> Enviar Correo
                                        @else
                                            <i class="fas fa-paper-plane"></i> Send Email
                                        @endif
                                    </button>
                                    <a href="{{ route('agent.authorize.edit', $booking->id) }}" class="btn btn-secondary">
                                        @if ($isSpanish)
                                            <i class="fas fa-edit"></i> Editar
                                        @else
                                            <i class="fas fa-edit"></i> Edit
                                        @endif
                                    </a>
                                    <a href="{{ route('agent.bookings.index') }}" class="btn btn-danger">
                                        @if ($isSpanish)
                                            <i class="fas fa-times"></i> Cancelar
                                        @else
                                            <i class="fas fa-times"></i> Cancel
                                        @endif
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

    @push('styles')
        <style>
            .email-preview {
                background: #ffffff;
                max-width: 850px;
                margin: 0 auto;
            }

            .email-preview table {
                width: 100% !important;
            }

            .email-preview img {
                max-width: 100%;
                height: auto;
            }
        </style>
    @endpush
