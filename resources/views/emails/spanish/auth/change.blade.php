<h3>Autorización para {{ $booking->segments->first()?->airline_name ?? 'la aerolínea' }} Confirmación de Cambio.
    </h3>

    <p>Estimado/a {{ $booking->customer_name ?? 'Pasajero/a' }},</p>
    <p>¡Saludos cordiales!</p>
    <p>Según nuestra conversación y según lo acordado, hemos realizado su cambio con
        {{ $booking->segments->first()?->airline_name ?? 'la aerolínea' }} bajo la
        Confirmación {{ $booking->airline_pnr ? $booking->airline_pnr : $booking->gk_pnr }}. Por favor, consulte los detalles a
        continuación.
    </p>
    
    <p>
        Costo total para todos los pasajeros: {{ $booking->currency ?? 'USD' }}
        {{ number_format($booking->amount_charged, 2) }} (todo incluido: impuestos y tasas).
    </p>

    <p>
        Según nuestra conversación telefónica, yo, <b> {{ $booking->customer_name ?? '' }}</b>, autorizo a
        <b>
            {{ $booking->segments->first()?->airline_name ?? 'la aerolínea' }} /
            {{ $booking->agency_merchant_name ?? '' }}
        </b>
        a procesar los cargos mencionados anteriormente bajo sus respectivos comerciantes, cargando mi
        tarjeta terminada en ******{{ $booking->cards->first()?->card_last_four ?? '****' }} para la reserva y el
        itinerario que se detallan a continuación con {{ $booking->segments->first()?->airline_name ?? 'la aerolínea' }}.
    </p>
    <p>
        Esta autorización de pago corresponde al monto indicado arriba y es válida solo para uso único. Certifico que
        soy
        <b>{{ $booking->customer_name ?? '' }}</b>, usuario/a autorizado/a de esta tarjeta y que no disputaré este pago con la compañía emisora de la tarjeta o con mi banco.
    </p>
    <p>
        Por favor confirme su aceptación de los términos y la declaración respondiendo a este correo con
        'Acepto' o 'Autorizo'.
    </p>

    <h4>Descripción de los cargos:</h4>

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
                                    ? $booking->airline_merchant_name ?? 'Airline Merchant'
                                    : $booking->agency_merchant_name ??
                                        ($booking->agencymerchantname ?? 'Agency Merchant'))))));
        @endphp

        <p>
            {{ $index + 1 }}.
            {{ $booking->currency ?? 'USD' }} {{ number_format((float) $amount, 2) }}
            ({{ $merchantName }}, incl. impuestos y tasas)
        </p>
    @endforeach

    <h4>Detalles de los pasajeros:</h4>

    <table style="width: 100%; border: 1px solid #000; border-collapse: collapse; margin: 16px 0;">
        <thead style="border: 1px solid #000;">
            <tr style="background-color: #f3f4f6; border-bottom: 1px solid #e5e7eb;">
                <th style="padding: 12px 16px; text-align: left; font-weight: 600;">N.º</th>
                <th style="padding: 12px 16px; text-align: left; font-weight: 600;">Tipo</th>
                <th style="padding: 12px 16px; text-align: left; font-weight: 600;">Nombre</th>
                <th style="padding: 12px 16px; text-align: left; font-weight: 600;">Segundo Nombre</th>
                <th style="padding: 12px 16px; text-align: left; font-weight: 600;">Apellido</th>
                <th style="padding: 12px 16px; text-align: left; font-weight: 600;">Género</th>
                <th style="padding: 12px 16px; text-align: left; font-weight: 600;">Fecha de Nacimiento</th>
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

    <h4>Resumen de la compra:</h4>

    <h6>Tipo de pago - Autorización con tarjeta de crédito/débito</h6>

    <table style="width: 100%; border-collapse: collapse; margin: 16px 0; background-color: #f9fafb;">
        <tbody>
            <tr style="border-bottom: 1px solid #e5e7eb;">
                <td style="padding: 12px 16px; font-weight: 600; background-color: #f3f4f6; width: 40%;">Nombre del titular
                    de la tarjeta:
                </td>
                <td style="padding: 12px 16px;">{{ $booking->cards->first()?->card_holder_name ?? 'N/A' }}</td>
            </tr>
            <tr style="border-bottom: 1px solid #e5e7eb;">
                <td style="padding: 12px 16px; font-weight: 600; background-color: #f3f4f6;">Tipo de tarjeta:</td>
                <td style="padding: 12px 16px;">{{ $booking->cards->first()?->card_type ?? 'N/A' }}</td>
            </tr>
            <tr style="border-bottom: 1px solid #e5e7eb;">
                <td style="padding: 12px 16px; font-weight: 600; background-color: #f3f4f6;">Número de tarjeta:</td>
                <td style="padding: 12px 16px;">{{ $booking->cards->first()?->card_number ?? 'N/A' }}</td>
            </tr>
            <tr style="border-bottom: 1px solid #e5e7eb;">
                <td style="padding: 12px 16px; font-weight: 600; background-color: #f3f4f6;">Vencimiento:</td>
                <td style="padding: 12px 16px;">{{ $booking->cards->first()?->expiration ?? 'N/A' }}</td>
            </tr>
            <tr style="border-bottom: 1px solid #e5e7eb;">
                <td style="padding: 12px 16px; font-weight: 600; background-color: #f3f4f6;">Dirección de facturación:</td>
                <td style="padding: 12px 16px;">{{ $booking->cards->first()?->billing_address ?? 'N/A' }}</td>
            </tr>
            <tr style="border-bottom: 1px solid #e5e7eb;">
                <td style="padding: 12px 16px; font-weight: 600; background-color: #f3f4f6;">Número de teléfono:</td>
                <td style="padding: 12px 16px;">{{ $booking->cards->first()?->billing_phone ?? 'N/A' }}</td>
            </tr>
            <tr style="border-bottom: 1px solid #e5e7eb;">
                <td style="padding: 12px 16px; font-weight: 600; background-color: #f3f4f6;">Correo electrónico:</td>
                <td style="padding: 12px 16px;">{{ $booking->customer_email }}</td>
            </tr>
            <tr style="border-bottom: 1px solid #e5e7eb;">
                <td style="padding: 12px 16px; font-weight: 600; background-color: #f3f4f6;">Monto total:</td>
                <td style="padding: 12px 16px; font-weight: 600; color: #059669;">USD
                    {{ number_format($booking->amount_charged, 2) }}</td>
            </tr>
            <tr style="border-bottom: 1px solid #e5e7eb;">
                <td style="padding: 12px 16px; font-weight: 600; background-color: #f3f4f6;">Fecha de la transacción:</td>
                <td style="padding: 12px 16px;">{{ \Carbon\Carbon::now()->format('M dS, Y') }}</td>
            </tr>
        </tbody>
    </table>
