@extends('layouts.agent')

@section('title', 'E-Ticket Editor')

@push('styles')
<style>
    .workspace-bg {
        background-color: #f1f5f9;
        min-height: 100vh;
        padding: 30px 15px;
    }
    .editor-toolbar {
        background: #051430;
        color: #fff;
        padding: 12px 20px;
        border-radius: 8px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        margin-bottom: 25px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 15px;
    }
    .toolbar-group {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .toolbar-btn {
        background: rgba(255,255,255,0.1);
        border: 1px solid rgba(255,255,255,0.15);
        color: #fff;
        padding: 6px 12px;
        border-radius: 4px;
        cursor: pointer;
        font-size: 13px;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .toolbar-btn:hover {
        background: #c5a46e;
        color: #051430;
        border-color: #c5a46e;
    }
    .toolbar-btn.btn-pdf {
        background: #c5a46e;
        color: #051430;
        border-color: #c5a46e;
        font-weight: 700;
    }
    .toolbar-btn.btn-pdf:hover {
        background: #b08e56;
        border-color: #b08e56;
        color: #fff;
    }
    .toolbar-btn.btn-reset {
        background: #630505;
        border-color: #630505;
    }
    .toolbar-btn.btn-reset:hover {
        background: #800a0a;
        color: #fff;
    }
    .paper-sheet {
        background: #fff;
        max-width: 800px;
        margin: 0 auto;
        padding: 0;
        box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        border-radius: 8px;
        min-height: 1000px;
        position: relative;
    }
    /* Visual feedback for editable elements */
    [contenteditable="true"] {
        outline: none;
        transition: background-color 0.2s ease;
    }
    [contenteditable="true"]:hover {
        background-color: rgba(197, 164, 110, 0.05) !important;
        cursor: text;
    }
    [contenteditable="true"]:focus {
        background-color: rgba(197, 164, 110, 0.1) !important;
        box-shadow: 0 0 0 2px rgba(197, 164, 110, 0.3);
    }
    .editor-label-badge {
        background: #c5a46e;
        color: #051430;
        font-size: 10px;
        font-weight: 700;
        padding: 2px 6px;
        border-radius: 3px;
        text-transform: uppercase;
    }
</style>
@endpush

@section('content')
<div class="workspace-bg">
    <div class="container-fluid">
        <!-- Header Info -->
        <div class="row mb-4">
            <div class="col-12 text-center text-md-left d-md-flex align-items-center justify-content-between">
                <div>
                    <h2 class="text-navy font-weight-bold mb-1">Visual E-Ticket Editor</h2>
                    <p class="text-muted mb-0">Directly click on any text, dates, names, or prices in the e-ticket below to edit them, then export as PDF.</p>
                </div>
                <div class="mt-3 mt-md-0">
                    <span class="editor-label-badge"><i class="fas fa-edit mr-1"></i> Visual Editing Enabled</span>
                </div>
            </div>
        </div>

        <!-- WordPress-like Editor Toolbar -->
        <div class="editor-toolbar">
            <div class="toolbar-group">
                <!-- Rich Text Actions -->
                <button type="button" class="toolbar-btn" onclick="formatDoc('bold')" title="Bold"><i class="fas fa-bold"></i></button>
                <button type="button" class="toolbar-btn" onclick="formatDoc('italic')" title="Italic"><i class="fas fa-italic"></i></button>
                <button type="button" class="toolbar-btn" onclick="formatDoc('underline')" title="Underline"><i class="fas fa-underline"></i></button>
                <button type="button" class="toolbar-btn" onclick="formatDoc('justifyLeft')" title="Align Left"><i class="fas fa-align-left"></i></button>
                <button type="button" class="toolbar-btn" onclick="formatDoc('justifyCenter')" title="Align Center"><i class="fas fa-align-center"></i></button>
                <button type="button" class="toolbar-btn" onclick="formatDoc('justifyRight')" title="Align Right"><i class="fas fa-align-right"></i></button>
                
                <!-- Color Selector -->
                <div class="dropdown d-inline-block">
                    <button type="button" class="toolbar-btn dropdown-toggle" data-toggle="dropdown">
                        <i class="fas fa-tint mr-1"></i> Color
                    </button>
                    <div class="dropdown-menu p-2" style="min-width: 120px;">
                        <a href="javascript:void(0)" onclick="setTextColor('#051430')" class="dropdown-item d-flex align-items-center"><span style="width:12px;height:12px;background:#051430;display:inline-block;margin-right:8px;border-radius:2px;"></span> Navy</a>
                        <a href="javascript:void(0)" onclick="setTextColor('#c5a46e')" class="dropdown-item d-flex align-items-center"><span style="width:12px;height:12px;background:#c5a46e;display:inline-block;margin-right:8px;border-radius:2px;"></span> Gold</a>
                        <a href="javascript:void(0)" onclick="setTextColor('#630505')" class="dropdown-item d-flex align-items-center"><span style="width:12px;height:12px;background:#630505;display:inline-block;margin-right:8px;border-radius:2px;"></span> Red</a>
                        <a href="javascript:void(0)" onclick="setTextColor('#333333')" class="dropdown-item d-flex align-items-center"><span style="width:12px;height:12px;background:#333333;display:inline-block;margin-right:8px;border-radius:2px;"></span> Black</a>
                    </div>
                </div>
            </div>

            <div class="toolbar-group">
                <!-- Structural Actions -->
                <button type="button" class="toolbar-btn" onclick="addPassengerRow()"><i class="fas fa-user-plus"></i> Add Passenger</button>
                <button type="button" class="toolbar-btn" onclick="toggleReturnJourney()"><i class="fas fa-exchange-alt"></i> Toggle Return</button>
            </div>

            <div class="toolbar-group">
                <!-- PDF & Reset Actions -->
                <button type="button" class="toolbar-btn btn-reset" onclick="resetTemplate()"><i class="fas fa-undo"></i> Reset</button>
                <button type="button" class="toolbar-btn btn-pdf" onclick="generatePdf()"><i class="fas fa-file-pdf"></i> Convert into PDF</button>
            </div>
        </div>

        <!-- Editable Paper Sheet Workspace -->
        <div class="paper-sheet">
            <div id="editable-ticket-container" contenteditable="true">
                {!! $defaultHtml !!}
            </div>
        </div>
    </div>
</div>

<!-- Hidden form to post compiled HTML to PDF Generator -->
<form id="pdf-form" action="{{ route('agent.e-ticket.pdf') }}" method="POST" style="display:none;" target="_blank">
    @csrf
    <textarea name="content" id="pdf-content-input"></textarea>
</form>
@endsection

@push('scripts')
<script>
    // Rich-text formatting helper
    function formatDoc(cmd, value = null) {
        document.execCommand(cmd, false, value);
    }

    function setTextColor(color) {
        document.execCommand('styleWithCSS', false, true);
        document.execCommand('foreColor', false, color);
    }

    // Dynamic passenger insertion
    function addPassengerRow() {
        const tableBody = $('#editable-ticket-container').find('.passenger-table tbody');
        if (tableBody.length === 0) return;

        const rowCount = tableBody.find('tr').length + 1;
        const newRowHtml = `
            <tr>
                <td style="border-bottom: 1px solid #e2e8f0; padding: 10px 8px; vertical-align: middle; color: #334155; text-align: center;">
                    <span style="display: inline-block; width: 18px; height: 18px; line-height: 18px; background-color: #c5a46e; color: #ffffff; border-radius: 50%; font-weight: bold; font-size: 10px; text-align: center;">${rowCount}</span>
                </td>
                <td style="border-bottom: 1px solid #e2e8f0; padding: 10px 8px; vertical-align: middle; color: #0f172a; font-weight: bold;">New Passenger Name</td>
                <td style="border-bottom: 1px solid #e2e8f0; padding: 10px 8px; vertical-align: middle; color: #334155;">DD MONTH YYYY</td>
                <td style="border-bottom: 1px solid #e2e8f0; padding: 10px 8px; vertical-align: middle; color: #334155;">
                    <span style="font-size: 9px; background-color: #f1f5f9; padding: 2px 6px; border-radius: 3px; border: 1px solid #e2e8f0; color: #475569;">UNITED STATES</span>
                </td>
                <td style="border-bottom: 1px solid #e2e8f0; padding: 10px 8px; vertical-align: middle; color: #334155;">157-0000000000</td>
                <td style="border-bottom: 1px solid #e2e8f0; padding: 10px 8px; vertical-align: middle; color: #c5a46e; font-weight: bold;">⭐ BC ELITE GOLD</td>
            </tr>
        `;
        tableBody.append(newRowHtml);
    }

    // Toggle return journey
    let returnJourneyBackup = null;
    function toggleReturnJourney() {
        // Find outbound and return journey components
        const container = $('#editable-ticket-container');
        const returnBanner = container.find('table').filter(function() {
            return $(this).text().includes('02') && $(this).text().includes('Return Journey');
        });
        
        if (returnBanner.length > 0) {
            // It currently exists, so we back it up and remove it
            const returnCard = returnBanner.next('table');
            returnJourneyBackup = {
                banner: returnBanner.detach(),
                card: returnCard.detach()
            };
        } else if (returnJourneyBackup) {
            // Find outbound journey card and insert return journey right after it
            const outboundBanner = container.find('table').filter(function() {
                return $(this).text().includes('01') && $(this).text().includes('Outbound Journey');
            });
            const outboundCard = outboundBanner.next('table');
            
            if (outboundCard.length > 0) {
                outboundCard.after(returnJourneyBackup.card);
                outboundCard.after(returnJourneyBackup.banner);
                returnJourneyBackup = null;
            }
        } else {
            // Create a brand new default return journey if no backup exists
            const outboundBanner = container.find('table').filter(function() {
                return $(this).text().includes('01') && $(this).text().includes('Outbound Journey');
            });
            const outboundCard = outboundBanner.next('table');

            if (outboundCard.length > 0) {
                const returnBannerHtml = `
                    <table style="width: 100%; border-collapse: collapse; background-color: #c5a46e; color: #ffffff; margin: 15px 8px 0 8px;">
                        <tr>
                            <td style="padding: 8px 12px; font-size: 11px; font-weight: bold; text-transform: uppercase; vertical-align: middle;">
                                <span style="background-color: #051430; color: #ffffff; padding: 2px 6px; border-radius: 2px; margin-right: 6px;">02</span> Return Journey &nbsp;✈&nbsp; Bengaluru ➔ Seattle &nbsp;|&nbsp; MON, 09 NOVEMBER 2026
                            </td>
                            <td style="text-align: right; padding: 8px 12px; vertical-align: middle;">
                                <span style="background-color: #630505; color: #ffffff; font-size: 8px; font-weight: bold; text-transform: uppercase; padding: 3px 8px; border-radius: 3px; letter-spacing: 0.5px;">Trip Status: CONFIRMED</span>
                            </td>
                        </tr>
                    </table>
                `;
                const returnCardHtml = `
                    <table style="width: 100%; border-collapse: collapse; margin: 0 8px 20px 8px; border: 1px solid #c5a46e; background-color: #ffffff;">
                        <tr>
                            <td style="width: 25%; padding: 15px; border-right: 1px solid #f1f5f9; vertical-align: top;">
                                <div style="font-size: 14px; font-weight: bold; color: #051430;">✈ Qatar Airways</div>
                                <table style="width: 100%; border-collapse: collapse; margin-top: 8px;">
                                    <tr>
                                        <td style="padding: 0; width: 50%;">
                                            <div style="font-size: 8px; color: #64748b; text-transform: uppercase;">Flight</div>
                                            <div style="font-size: 11px; font-weight: bold; color: #0f172a;">QR 573</div>
                                        </td>
                                        <td style="padding: 0; width: 50%;">
                                            <div style="font-size: 8px; color: #64748b; text-transform: uppercase;">Flight</div>
                                            <div style="font-size: 11px; font-weight: bold; color: #0f172a;">QR 719</div>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                            <td style="width: 50%; padding: 15px 10px; border-right: 1px solid #f1f5f9; vertical-align: top;">
                                <table style="width: 100%; border-collapse: collapse;">
                                    <tr>
                                        <td style="width: 38%; vertical-align: top; text-align: left;">
                                            <div style="font-size: 8px; color: #64748b; text-transform: uppercase;">Departure</div>
                                            <div style="font-size: 18px; font-weight: bold; color: #051430; margin: 0;">03:50</div>
                                            <div style="font-size: 11px; font-weight: bold; color: #0f172a; margin-top: 3px;">BENGALURU</div>
                                            <div style="font-size: 8px; color: #64748b; margin-top: 2px; line-height: 1.3;">Kempegowda International Airport<br>India | Terminal 2</div>
                                        </td>
                                        <td style="width: 24%; vertical-align: middle; text-align: center;">
                                            <div style="font-size: 8px; color: #64748b; margin-bottom: 2px; font-weight: bold;">21h 20m</div>
                                            <div style="height: 2px; background-color: #cbd5e1; position: relative; margin: 4px 0;">
                                                <span style="font-size: 10px; color: #c5a46e; position: absolute; left: 40%; top: -6px;">✈</span>
                                            </div>
                                            <div style="font-size: 8px; color: #630505; margin-top: 2px; font-weight: bold;">1 Stop(s)</div>
                                        </td>
                                        <td style="width: 38%; vertical-align: top; text-align: left; padding-left: 10px;">
                                            <div style="font-size: 8px; color: #64748b; text-transform: uppercase;">Arrival</div>
                                            <div style="font-size: 18px; font-weight: bold; color: #051430; margin: 0;">11:40</div>
                                            <div style="font-size: 11px; font-weight: bold; color: #0f172a; margin-top: 3px;">SEATTLE</div>
                                            <div style="font-size: 8px; color: #64748b; margin-top: 2px; line-height: 1.3;">Seattle-Tacoma International Airport<br>United States of America</div>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                            <td style="width: 25%; padding: 15px; vertical-align: top;">
                                <table style="width: 100%; border-collapse: collapse; margin-bottom: 8px;">
                                    <tr>
                                        <td style="width: 18px; vertical-align: top; color: #c5a46e; font-size: 11px;">💺</td>
                                        <td style="vertical-align: top; padding-left: 4px;">
                                            <div style="font-size: 7.5px; color: #64748b; text-transform: uppercase; font-weight: bold;">Class</div>
                                            <div style="font-size: 9.5px; font-weight: bold; color: #0f172a;">BUSINESS (P)</div>
                                        </td>
                                    </tr>
                                </table>
                                <table style="width: 100%; border-collapse: collapse; margin-bottom: 8px;">
                                    <tr>
                                        <td style="width: 18px; vertical-align: top; color: #c5a46e; font-size: 11px;">💼</td>
                                        <td style="vertical-align: top; padding-left: 4px;">
                                            <div style="font-size: 7.5px; color: #64748b; text-transform: uppercase; font-weight: bold;">Baggage Allowance</div>
                                            <div style="font-size: 9.5px; font-weight: bold; color: #0f172a;">2 x 32 kg</div>
                                        </td>
                                    </tr>
                                </table>
                                <table style="width: 100%; border-collapse: collapse;">
                                    <tr>
                                        <td style="width: 18px; vertical-align: top; color: #c5a46e; font-size: 11px;">🍽</td>
                                        <td style="vertical-align: top; padding-left: 4px;">
                                            <div style="font-size: 7.5px; color: #64748b; text-transform: uppercase; font-weight: bold;">Meal</div>
                                            <div style="font-size: 9.5px; font-weight: bold; color: #0f172a;">FULL MEAL SERVICE</div>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                `;
                outboundCard.after(returnCardHtml);
                outboundCard.after(returnBannerHtml);
            }
        }
    }

    // Reset back to original HTML template
    function resetTemplate() {
        if (confirm('Are you sure you want to discard all changes and reset the e-ticket?')) {
            location.reload();
        }
    }

    // Post edited HTML content to PDF generator
    function generatePdf() {
        const ticketContent = document.getElementById('editable-ticket-container').innerHTML;
        document.getElementById('pdf-content-input').value = ticketContent;
        document.getElementById('pdf-form').submit();
    }
</script>
@endpush
