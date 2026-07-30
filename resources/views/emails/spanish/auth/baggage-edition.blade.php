<h3>Autorización para {{ $booking->segments->first()?->airline_name ?? 'la aerolínea' }} Confirmación de Adición de
    Equipaje.</h3>
<p>Estimado/a {{ $booking->customer_name ?? 'Pasajero/a' }},</p>
<p>¡Saludos cordiales!</p>
<p>Según nuestra conversación y según lo acordado, hemos agregado las maletas para su reserva con
    {{ $booking->segments->first()?->airline_name ?? 'la aerolínea' }} bajo la
    Confirmación {{ $booking->airline_pnr ? $booking->airline_pnr : $booking->gk_pnr }}. Por favor, consulte los
    detalles a
    continuación.
</p>
<p>
    Costo total para todos los pasajeros: {{ $booking->currency ?? 'USD' }}
    {{ number_format($booking->amount_charged, 2) }} (todo incluido, impuestos y tarifas).
</p>

<p>
    Según nuestra conversación telefónica, yo, <b> {{ $booking->customer_name ?? '' }}</b>, autorizo a
    <b>
        {{ $booking->segments->first()?->airline_name ?? 'la aerolínea' }} /
        {{ $booking->agency_merchant_name ?? '' }}
    </b>
    a procesar los cargos antes mencionados a través de sus respectivos comercios para cargar a mi
    tarjeta ******{{ $booking->cards->first()?->card_last_four ?? '****' }} por la reserva del
    itinerario
    que se indica a continuación
    con {{ $booking->segments->first()?->airline_name ?? 'la aerolínea' }}.
</p>
<p>
    Esta autorización de pago es por el monto indicado anteriormente y es válida para un solo uso. Certifico que
    soy
    <b>{{ $booking->customer_name ?? '' }}</b>, un usuario autorizado de esta tarjeta y
    que no disputaré el pago con mi compañía de tarjeta de crédito/débito
    /banco.
</p>
<p>
    Por favor, confirme su aceptación de los términos y acuerdo a la declaración respondiendo a este correo electrónico
    con
    'Acepto' o 'Autorizo'.
</p>

<h4>Descripción de los Cargos:</h4>

@foreach ($booking->cards as $index => $card)
    @php
        $cardOrder = $card->card_order ?? ($card->cardorder ?? $index + 1);

        $amount =
            $card->charge_amount ??
            ($card->chargeamount ??
                ($cardOrder == 1
                    ? $booking->amount_paid_airline ?? 0
                    : (float) ($booking->amount_charged ?? 0) - (float) ($booking->amount_paid_airline ?? 0)));

        $merchantName =
            $card->merchant_name ??
            ($card->merchantname ??
                (optional($card->merchant)->merchant_name ??
                    (optional($card->merchant)->merchantname ??
                        (optional($card->merchant)->name ??
                            ($cardOrder == 1
                                ? $booking->airline_merchant_name ?? 'Comercio de la Aerolínea'
                                : $booking->agency_merchant_name ??
                                    ($booking->agencymerchantname ?? 'Comercio de la Agencia'))))));
    @endphp

    <p>
        {{ $index + 1 }}.
        {{ $booking->currency ?? 'USD' }} {{ number_format((float) $amount, 2) }}
        ({{ $merchantName }}, incl. impuestos y tarifas)
    </p>
@endforeach

<h4>Detalles de los Pasajeros:</h4>

<table style="width: 100%; border: 1px solid #000; border-collapse: collapse; margin: 16px 0;">
    <thead style="border: 1px solid #000;">
        <tr style="background-color: #f3f4f6; border-bottom: 1px solid #e5e7eb;">
            <th style="padding: 12px 16px; text-align: left; font-weight: 600;">N.º</th>
            <th style="padding: 12px 16px; text-align: left; font-weight: 600;">Tipo</th>
            <th style="padding: 12px 16px; text-align: left; font-weight: 600;">Nombre</th>
            <th style="padding: 12px 16px; text-align: left; font-weight: 600;">Segundo Nombre</th>
            <th style="padding: 12px 16px; text-align: left; font-weight: 600;">Apellido</th>
            <th style="padding: 12px 16px; text-align: left; font-weight: 600;">Género</th>
            <th style="padding: 12px 16px; text-align: left; font-weight: 600;">Fecha de Nac.</th>
            <th style="padding: 12px 16px; text-align: left; font-weight: 600;">Precio</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($booking->passengers as $index => $passenger)
            <tr style="border-bottom: 1px solid #e5e7eb;">
                <td style="padding: 12px 16px;">{{ $index + 1 }}</td>
                <td style="padding: 12px 16px;">{{ $passenger->type ?? 'ADT' }}</td>
                <td style="padding: 12px 16px;">{{ $passenger->first_name }}</td>
                <td style="padding: 12px 16px;">{{ $passenger->middle_name ?? '-' }}</td>
                <td style="padding: 12px 16px;">{{ $passenger->last_name }}</td>
                <td style="padding: 12px 16px;">{{ $passenger->gender ?? '-' }}</td>
                <td style="padding: 12px 16px;">
                    {{ $passenger->dob ? \Carbon\Carbon::parse($passenger->dob)->format('M-d-Y') : '-' }}
                </td>
                <td style="padding: 12px 16px;">USD
                    {{ number_format(($booking->amount_charged ?? 0) / max($booking->passengers->count(), 1), 2) }}
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

<h4>Resumen de la Compra:</h4>

<h6>Tipo de Pago - Autorización de Tarjeta de Crédito/Débito</h6>

<table style="width: 100%; border-collapse: collapse; margin: 16px 0; background-color: #f9fafb;">
    <tbody>
        <tr style="border-bottom: 1px solid #e5e7eb;">
            <td style="padding: 12px 16px; font-weight: 600; background-color: #f3f4f6; width: 40%;">Nombre del Titular
                de la Tarjeta:
            </td>
            <td style="padding: 12px 16px;">{{ $booking->cards->first()?->card_holder_name ?? 'N/A' }}</td>
        </tr>
        <tr style="border-bottom: 1px solid #e5e7eb;">
            <td style="padding: 12px 16px; font-weight: 600; background-color: #f3f4f6;">Tipo de Tarjeta:</td>
            <td style="padding: 12px 16px;">{{ $booking->cards->first()?->card_type ?? 'N/A' }}</td>
        </tr>
        <tr style="border-bottom: 1px solid #e5e7eb;">
            <td style="padding: 12px 16px; font-weight: 600; background-color: #f3f4f6;">Número de Tarjeta:</td>
            <td style="padding: 12px 16px;">{{ $booking->cards->first()?->card_number ?? 'N/A' }}</td>
        </tr>
        <tr style="border-bottom: 1px solid #e5e7eb;">
            <td style="padding: 12px 16px; font-weight: 600; background-color: #f3f4f6;">Fecha de Vencimiento:</td>
            <td style="padding: 12px 16px;">{{ $booking->cards->first()?->expiration ?? 'N/A' }}</td>
        </tr>
        <tr style="border-bottom: 1px solid #e5e7eb;">
            <td style="padding: 12px 16px; font-weight: 600; background-color: #f3f4f6;">Dirección de Facturación:</td>
            <td style="padding: 12px 16px;">{{ $booking->cards->first()?->billing_address ?? 'N/A' }}</td>
        </tr>
        <tr style="border-bottom: 1px solid #e5e7eb;">
            <td style="padding: 12px 16px; font-weight: 600; background-color: #f3f4f6;">Número de Teléfono:</td>
            <td style="padding: 12px 16px;">{{ $booking->cards->first()?->billing_phone ?? 'N/A' }}</td>
        </tr>
        <tr style="border-bottom: 1px solid #e5e7eb;">
            <td style="padding: 12px 16px; font-weight: 600; background-color: #f3f4f6;">Correo Electrónico:</td>
            <td style="padding: 12px 16px;">{{ $booking->customer_email }}</td>
        </tr>
        <tr style="border-bottom: 1px solid #e5e7eb;">
            <td style="padding: 12px 16px; font-weight: 600; background-color: #f3f4f6;">Monto Total:</td>
            <td style="padding: 12px 16px; font-weight: 600; color: #059669;">USD
                {{ number_format($booking->amount_charged, 2) }}</td>
        </tr>
        <tr style="border-bottom: 1px solid #e5e7eb;">
            <td style="padding: 12px 16px; font-weight: 600; background-color: #f3f4f6;">Fecha de Transacción:</td>
            <td style="padding: 12px 16px;">{{ \Carbon\Carbon::now()->format('M dS, Y') }}</td>
        </tr>
    </tbody>
</table>
