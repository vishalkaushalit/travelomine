<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class AgentETicketController extends Controller
{
    /**
     * Display the e-ticket editor page.
     */
    public function index()
    {
        $defaultHtml = $this->getDefaultHtml();
        return view('agent.e-ticket.editor', compact('defaultHtml'));
    }

    /**
     * Compile template and generate PDF download.
     */
    public function generatePdf(Request $request)
    {
        $content = $request->input('content', $this->getDefaultHtml());

        // We clean contenteditable attributes and styling from the output HTML before rendering
        $cleanedContent = preg_replace('/contenteditable="true"/i', '', $content);
        
        $pdf = Pdf::loadView('agent.e-ticket.pdf', [
            'content' => $cleanedContent
        ])->setPaper('A4', 'portrait');
            
        return $pdf->download("e-ticket-" . date('Ymd-His') . ".pdf");
    }

    /**
     * Get default HTML structure for the editor.
     */
    private function getDefaultHtml()
    {
        return <<<'HTML'
<div class="ticket-container" style="width: 100%; max-width: 800px; margin: 0 auto; border: 1px solid #dcdcdc; border-radius: 6px; overflow: hidden; background-color: #ffffff; color: #333333; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 11px; line-height: 1.4;">
    <!-- Header -->
    <table style="width: 100%; border-collapse: collapse; background-color: #051430; color: #ffffff;">
        <tr>
            <td style="padding: 25px 20px; width: 55%; vertical-align: middle;">
                <div style="font-size: 26px; font-weight: bold; color: #ffffff; margin: 0; letter-spacing: 0.5px;">
                    <span style="color: #c5a46e;">B</span>usiness<span style="color: #c5a46e;">C</span>lass<span style="color: #c5a46e;">T</span>ravel.us
                </div>
                <div style="font-size: 10px; color: #c5a46e; text-transform: uppercase; letter-spacing: 2px; margin-top: 5px; margin-bottom: 0;">
                    Elevate Your Journey
                </div>
            </td>
            <td style="width: 45%; background-color: #0f2249; text-align: right; vertical-align: middle; padding: 0;">
                <div style="font-size: 14px; font-weight: bold; color: rgba(255,255,255,0.15); text-transform: uppercase; padding-right: 20px;">
                    Luxury Flight Itinerary
                </div>
            </td>
        </tr>
    </table>

    <!-- Title & Status Area -->
    <table style="width: 100%; border-collapse: collapse; margin-top: 15px; margin-bottom: 15px; padding: 0 15px;">
        <tr>
            <td style="text-align: left; vertical-align: middle; padding: 0 15px;">
                <div style="font-size: 24px; font-weight: bold; color: #051430; text-transform: uppercase; margin: 0;">
                    Electronic Ticket <span style="color: #c5a46e; font-size: 20px;">✈</span>
                </div>
                <div style="font-size: 12px; color: #666666; text-transform: uppercase; letter-spacing: 1px; margin-top: 2px;">
                    Itinerary & Receipt
                </div>
            </td>
            <td style="text-align: right; width: 220px; vertical-align: middle; padding: 0 15px;">
                <div style="border: 2px solid #c5a46e; border-radius: 6px; padding: 10px 15px; text-align: center; background-color: #fdfbf7;">
                    <div style="font-size: 9px; color: #666666; text-transform: uppercase; font-weight: bold; letter-spacing: 0.5px;">Booking Status</div>
                    <div style="font-size: 16px; font-weight: bold; color: #051430; margin-top: 2px;">
                        <span style="color: #c5a46e;">✔</span> CONFIRMED
                    </div>
                </div>
            </td>
        </tr>
    </table>

    <!-- Grid Info Boxes -->
    <table style="width: 100%; border-collapse: collapse; margin-bottom: 15px;">
        <tr>
            <td style="width: 25%; padding: 0 8px;">
                <div style="border: 1px solid #e2e8f0; border-radius: 5px; background-color: #f8fafc; padding: 10px; text-align: center; min-height: 55px;">
                    <div style="font-size: 14px; color: #c5a46e; margin-bottom: 3px;">🗊</div>
                    <div style="font-size: 8px; color: #64748b; text-transform: uppercase; font-weight: bold; letter-spacing: 0.5px; margin-bottom: 4px;">Booking Reference (PNR)</div>
                    <div style="font-size: 13px; color: #c5a46e; font-weight: bold;">8FO8UC</div>
                </div>
            </td>
            <td style="width: 25%; padding: 0 8px;">
                <div style="border: 1px solid #e2e8f0; border-radius: 5px; background-color: #f8fafc; padding: 10px; text-align: center; min-height: 55px;">
                    <div style="font-size: 14px; color: #c5a46e; margin-bottom: 3px;">🎫</div>
                    <div style="font-size: 8px; color: #64748b; text-transform: uppercase; font-weight: bold; letter-spacing: 0.5px; margin-bottom: 4px;">E-Ticket Numbers</div>
                    <div style="font-size: 9px; line-height: 1.2;">157-2138797203<br>157-2138797204</div>
                </div>
            </td>
            <td style="width: 25%; padding: 0 8px;">
                <div style="border: 1px solid #e2e8f0; border-radius: 5px; background-color: #f8fafc; padding: 10px; text-align: center; min-height: 55px;">
                    <div style="font-size: 14px; color: #c5a46e; margin-bottom: 3px;">📅</div>
                    <div style="font-size: 8px; color: #64748b; text-transform: uppercase; font-weight: bold; letter-spacing: 0.5px; margin-bottom: 4px;">Issue Date</div>
                    <div style="font-size: 11px; font-weight: bold; color: #0f172a;">20 JULY 2026</div>
                </div>
            </td>
            <td style="width: 25%; padding: 0 8px;">
                <div style="border: 1px solid #e2e8f0; border-radius: 5px; background-color: #f8fafc; padding: 10px; text-align: center; min-height: 55px;">
                    <div style="font-size: 14px; color: #c5a46e; margin-bottom: 3px;">📅</div>
                    <div style="font-size: 8px; color: #64748b; text-transform: uppercase; font-weight: bold; letter-spacing: 0.5px; margin-bottom: 4px;">Date of Booking</div>
                    <div style="font-size: 11px; font-weight: bold; color: #0f172a;">20 JULY 2026</div>
                </div>
            </td>
        </tr>
    </table>

    <!-- Official Notice -->
    <div style="background-color: #f1f5f9; border-radius: 4px; padding: 8px 12px; margin: 0 8px 15px 8px; text-align: center; font-size: 9.5px; color: #475569; border-left: 3px solid #c5a46e;">
        <strong>Notice:</strong> This is your official itinerary/receipt. You must bring a valid photo ID for airport check-in and security.
    </div>

    <!-- Passenger Details Section -->
    <div style="background-color: #051430; color: #ffffff; padding: 6px 12px; font-size: 11px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; border-radius: 4px; margin: 15px 8px 8px 8px;">
        <span style="color: #c5a46e; margin-right: 6px;">👤</span> Passenger Details
    </div>
    <table style="width: 100%; border-collapse: collapse; margin: 0 8px 15px 8px;">
        <thead>
            <tr>
                <th style="background-color: #f8fafc; border-bottom: 2px solid #e2e8f0; color: #475569; font-size: 9px; font-weight: bold; text-transform: uppercase; padding: 8px; text-align: left; width: 30px;">#</th>
                <th style="background-color: #f8fafc; border-bottom: 2px solid #e2e8f0; color: #475569; font-size: 9px; font-weight: bold; text-transform: uppercase; padding: 8px; text-align: left;">Passenger Name</th>
                <th style="background-color: #f8fafc; border-bottom: 2px solid #e2e8f0; color: #475569; font-size: 9px; font-weight: bold; text-transform: uppercase; padding: 8px; text-align: left;">Date of Birth</th>
                <th style="background-color: #f8fafc; border-bottom: 2px solid #e2e8f0; color: #475569; font-size: 9px; font-weight: bold; text-transform: uppercase; padding: 8px; text-align: left;">Nationality</th>
                <th style="background-color: #f8fafc; border-bottom: 2px solid #e2e8f0; color: #475569; font-size: 9px; font-weight: bold; text-transform: uppercase; padding: 8px; text-align: left;">E-Ticket Number</th>
                <th style="background-color: #f8fafc; border-bottom: 2px solid #e2e8f0; color: #475569; font-size: 9px; font-weight: bold; text-transform: uppercase; padding: 8px; text-align: left;">Frequent Flyer Tier</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="border-bottom: 1px solid #e2e8f0; padding: 10px 8px; vertical-align: middle; color: #334155; text-align: center;">
                    <span style="display: inline-block; width: 18px; height: 18px; line-height: 18px; background-color: #c5a46e; color: #ffffff; border-radius: 50%; font-weight: bold; font-size: 10px; text-align: center;">1</span>
                </td>
                <td style="border-bottom: 1px solid #e2e8f0; padding: 10px 8px; vertical-align: middle; color: #0f172a; font-weight: bold;">Mr Shamaprasad Krishnamurthy Bangalore</td>
                <td style="border-bottom: 1px solid #e2e8f0; padding: 10px 8px; vertical-align: middle; color: #334155;">22 NOVEMBER 1963</td>
                <td style="border-bottom: 1px solid #e2e8f0; padding: 10px 8px; vertical-align: middle; color: #334155;">
                    <span style="font-size: 9px; background-color: #f1f5f9; padding: 2px 6px; border-radius: 3px; border: 1px solid #e2e8f0; color: #475569;">UNITED STATES</span>
                </td>
                <td style="border-bottom: 1px solid #e2e8f0; padding: 10px 8px; vertical-align: middle; color: #334155;">157-2138797203</td>
                <td style="border-bottom: 1px solid #e2e8f0; padding: 10px 8px; vertical-align: middle; color: #c5a46e; font-weight: bold;">⭐ BC ELITE GOLD</td>
            </tr>
            <tr>
                <td style="border-bottom: 1px solid #e2e8f0; padding: 10px 8px; vertical-align: middle; color: #334155; text-align: center;">
                    <span style="display: inline-block; width: 18px; height: 18px; line-height: 18px; background-color: #c5a46e; color: #ffffff; border-radius: 50%; font-weight: bold; font-size: 10px; text-align: center;">2</span>
                </td>
                <td style="border-bottom: 1px solid #e2e8f0; padding: 10px 8px; vertical-align: middle; color: #0f172a; font-weight: bold;">Mrs Mala Shamaprasad</td>
                <td style="border-bottom: 1px solid #e2e8f0; padding: 10px 8px; vertical-align: middle; color: #334155;">27 MAY 1967</td>
                <td style="border-bottom: 1px solid #e2e8f0; padding: 10px 8px; vertical-align: middle; color: #334155;">
                    <span style="font-size: 9px; background-color: #f1f5f9; padding: 2px 6px; border-radius: 3px; border: 1px solid #e2e8f0; color: #475569;">UNITED STATES</span>
                </td>
                <td style="border-bottom: 1px solid #e2e8f0; padding: 10px 8px; vertical-align: middle; color: #334155;">157-2138797204</td>
                <td style="border-bottom: 1px solid #e2e8f0; padding: 10px 8px; vertical-align: middle; color: #c5a46e; font-weight: bold;">⭐ BC ELITE GOLD</td>
            </tr>
        </tbody>
    </table>

    <!-- Outbound Journey -->
    <table style="width: 100%; border-collapse: collapse; background-color: #c5a46e; color: #ffffff; margin: 15px 8px 0 8px;">
        <tr>
            <td style="padding: 8px 12px; font-size: 11px; font-weight: bold; text-transform: uppercase; vertical-align: middle;">
                <span style="background-color: #051430; color: #ffffff; padding: 2px 6px; border-radius: 2px; margin-right: 6px;">01</span> Outbound Journey &nbsp;✈&nbsp; Seattle ➔ Bengaluru &nbsp;|&nbsp; SAT, 17 OCTOBER 2026
            </td>
            <td style="text-align: right; padding: 8px 12px; vertical-align: middle;">
                <span style="background-color: #630505; color: #ffffff; font-size: 8px; font-weight: bold; text-transform: uppercase; padding: 3px 8px; border-radius: 3px; letter-spacing: 0.5px;">Trip Status: CONFIRMED</span>
            </td>
        </tr>
    </table>
    <table style="width: 100%; border-collapse: collapse; margin: 0 8px 20px 8px; border: 1px solid #c5a46e; background-color: #ffffff;">
        <tr>
            <!-- Airline details -->
            <td style="width: 25%; padding: 15px; border-right: 1px solid #f1f5f9; vertical-align: top;">
                <div style="font-size: 14px; font-weight: bold; color: #051430;">✈ Qatar Airways</div>
                <table style="width: 100%; border-collapse: collapse; margin-top: 8px;">
                    <tr>
                        <td style="padding: 0; width: 50%;">
                            <div style="font-size: 8px; color: #64748b; text-transform: uppercase;">Flight</div>
                            <div style="font-size: 11px; font-weight: bold; color: #0f172a;">QR 720</div>
                        </td>
                        <td style="padding: 0; width: 50%;">
                            <div style="font-size: 8px; color: #64748b; text-transform: uppercase;">Flight</div>
                            <div style="font-size: 11px; font-weight: bold; color: #0f172a;">QR 572</div>
                        </td>
                    </tr>
                </table>
            </td>
            <!-- Route timeline -->
            <td style="width: 50%; padding: 15px 10px; border-right: 1px solid #f1f5f9; vertical-align: top;">
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="width: 38%; vertical-align: top; text-align: left;">
                            <div style="font-size: 8px; color: #64748b; text-transform: uppercase;">Departure</div>
                            <div style="font-size: 18px; font-weight: bold; color: #051430; margin: 0;">16:25</div>
                            <div style="font-size: 11px; font-weight: bold; color: #0f172a; margin-top: 3px;">SEATTLE</div>
                            <div style="font-size: 8px; color: #64748b; margin-top: 2px; line-height: 1.3;">Seattle-Tacoma International Airport<br>United States of America</div>
                        </td>
                        <td style="width: 24%; vertical-align: middle; text-align: center;">
                            <div style="font-size: 8px; color: #64748b; margin-bottom: 2px; font-weight: bold;">21h 35m</div>
                            <div style="height: 2px; background-color: #cbd5e1; position: relative; margin: 4px 0;">
                                <span style="font-size: 10px; color: #c5a46e; position: absolute; left: 40%; top: -6px;">✈</span>
                            </div>
                            <div style="font-size: 8px; color: #630505; margin-top: 2px; font-weight: bold;">1 Stop(s)</div>
                        </td>
                        <td style="width: 38%; vertical-align: top; text-align: left; padding-left: 10px;">
                            <div style="font-size: 8px; color: #64748b; text-transform: uppercase;">Arrival</div>
                            <div style="font-size: 18px; font-weight: bold; color: #051430; margin: 0;">02:30 +2</div>
                            <div style="font-size: 11px; font-weight: bold; color: #0f172a; margin-top: 3px;">BENGALURU</div>
                            <div style="font-size: 8px; color: #64748b; margin-top: 2px; line-height: 1.3;">Kempegowda International Airport<br>India | Terminal 2</div>
                        </td>
                    </tr>
                </table>
            </td>
            <!-- Cabin / baggage services -->
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

    <!-- Return Journey -->
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
    <table style="width: 100%; border-collapse: collapse; margin: 0 8px 20px 8px; border: 1px solid #c5a46e; background-color: #ffffff;">
        <tr>
            <!-- Airline details -->
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
            <!-- Route timeline -->
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
            <!-- Cabin / baggage services -->
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

    <!-- Bottom Row -->
    <table style="width: 100%; border-collapse: collapse; margin-top: 10px;">
        <tr>
            <!-- Fare Details -->
            <td style="width: 33.33%; padding: 0 8px; vertical-align: top;">
                <div style="border: 1px solid #e2e8f0; border-radius: 5px; background-color: #f8fafc; padding: 12px; min-height: 160px;">
                    <div style="font-size: 10px; font-weight: bold; color: #051430; border-bottom: 2px solid #c5a46e; padding-bottom: 5px; margin-bottom: 10px; text-transform: uppercase; letter-spacing: 0.5px;">
                        <span style="color: #c5a46e;">$</span> Fare Details
                    </div>
                    <table style="width: 100%; border-collapse: collapse; margin-bottom: 5px;">
                        <tr>
                            <td style="font-size: 9px; color: #475569; text-align: left;">Base Fare</td>
                            <td style="font-size: 9px; font-weight: bold; color: #0f172a; text-align: right;">USD 6,254.00</td>
                        </tr>
                        <tr>
                            <td style="font-size: 9px; color: #475569; text-align: left;">Taxes & Fee</td>
                            <td style="font-size: 9px; font-weight: bold; color: #0f172a; text-align: right;">USD 3,332.18</td>
                        </tr>
                        <tr>
                            <td style="font-size: 9px; color: #475569; text-align: left;">Service Fee</td>
                            <td style="font-size: 9px; font-weight: bold; color: #0f172a; text-align: right;">USD 149.82</td>
                        </tr>
                    </table>
                    <div style="background-color: #fefcbf; border: 1px solid #fef08a; border-radius: 3px; padding: 6px; margin-top: 8px;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <tr>
                                <td style="font-size: 10px; font-weight: bold; color: #854d0e; text-transform: uppercase;">Total Paid</td>
                                <td style="font-size: 13px; font-weight: bold; color: #b45309; text-align: right;">USD 9,736.00</td>
                            </tr>
                        </table>
                    </div>
                    <div style="font-size: 8px; color: #64748b; margin-top: 6px; text-transform: uppercase; font-weight: bold;">Payment Method: CARD</div>
                </div>
            </td>
            
            <!-- Important Info -->
            <td style="width: 33.33%; padding: 0 8px; vertical-align: top;">
                <div style="border: 1px solid #e2e8f0; border-radius: 5px; background-color: #f8fafc; padding: 12px; min-height: 160px;">
                    <div style="font-size: 10px; font-weight: bold; color: #051430; border-bottom: 2px solid #c5a46e; padding-bottom: 5px; margin-bottom: 10px; text-transform: uppercase; letter-spacing: 0.5px;">
                        <span style="color: #c5a46e;">🛈</span> Important Info
                    </div>
                    <ul style="margin: 0; padding: 0; list-style: none;">
                        <li style="position: relative; padding-left: 12px; font-size: 8.5px; color: #475569; margin-bottom: 6px; line-height: 1.3;">
                            <span style="position: absolute; left: 0; top: 0; color: #c5a46e; font-size: 8px;">✔</span> Please arrive at least 3 hours before departure.
                        </li>
                        <li style="position: relative; padding-left: 12px; font-size: 8.5px; color: #475569; margin-bottom: 6px; line-height: 1.3;">
                            <span style="position: absolute; left: 0; top: 0; color: #c5a46e; font-size: 8px;">✔</span> Carry a valid passport and visa (if required).
                        </li>
                        <li style="position: relative; padding-left: 12px; font-size: 8.5px; color: #475569; margin-bottom: 6px; line-height: 1.3;">
                            <span style="position: absolute; left: 0; top: 0; color: #c5a46e; font-size: 8px;">✔</span> Online check-in opens 48 hours before departure.
                        </li>
                        <li style="position: relative; padding-left: 12px; font-size: 8.5px; color: #475569; margin-bottom: 6px; line-height: 1.3;">
                            <span style="position: absolute; left: 0; top: 0; color: #c5a46e; font-size: 8px;">✔</span> Gates close 30 minutes before departure.
                        </li>
                    </ul>
                </div>
            </td>
            
            <!-- Booking Summary & Barcode -->
            <td style="width: 33.33%; padding: 0 8px; vertical-align: top;">
                <div style="border: 1px solid #e2e8f0; border-radius: 5px; background-color: #f8fafc; padding: 12px; min-height: 160px;">
                    <div style="font-size: 10px; font-weight: bold; color: #051430; border-bottom: 2px solid #c5a46e; padding-bottom: 5px; margin-bottom: 10px; text-transform: uppercase; letter-spacing: 0.5px;">
                        <span style="color: #c5a46e;">🗎</span> Booking Summary
                    </div>
                    <table style="width: 100%; border-collapse: collapse; margin-bottom: 5px;">
                        <tr>
                            <td style="font-size: 8.5px; color: #64748b; text-align: left; padding-bottom: 4px;">Booking Reference (PNR)</td>
                            <td style="font-size: 8.5px; font-weight: bold; color: #0f172a; text-align: right; padding-bottom: 4px;">8FO8UC</td>
                        </tr>
                        <tr>
                            <td style="font-size: 8.5px; color: #64748b; text-align: left; padding-bottom: 4px;">Booking Status</td>
                            <td style="font-size: 8.5px; font-weight: bold; color: #0f172a; text-align: right; padding-bottom: 4px;">CONFIRMED</td>
                        </tr>
                        <tr>
                            <td style="font-size: 8.5px; color: #64748b; text-align: left; padding-bottom: 4px;">Issue Date</td>
                            <td style="font-size: 8.5px; font-weight: bold; color: #0f172a; text-align: right; padding-bottom: 4px;">20 JULY 2026</td>
                        </tr>
                        <tr>
                            <td style="font-size: 8.5px; color: #64748b; text-align: left; padding-bottom: 4px;">Payment Method</td>
                            <td style="font-size: 8.5px; font-weight: bold; color: #0f172a; text-align: right; padding-bottom: 4px;">CARD</td>
                        </tr>
                    </table>
                    <div style="border-top: 1px dashed #cbd5e1; margin-top: 10px; padding-top: 8px; text-align: center;">
                        <div style="font-family: 'Courier New', Courier, monospace; font-weight: bold; letter-spacing: 6px; font-size: 14px; color: #334155; background-color: #e2e8f0; padding: 4px; border-radius: 3px; display: inline-block;">||||| | ||||| | ||</div>
                        <div style="font-size: 8px; color: #64748b; margin-top: 2px; letter-spacing: 1px;">8FO8UC</div>
                    </div>
                </div>
            </td>
        </tr>
    </table>

    <!-- Footer Banner -->
    <table style="width: 100%; border-collapse: collapse; background-color: #051430; color: #ffffff; margin-top: 20px;">
        <tr>
            <td style="padding: 15px; font-size: 9px; color: #e2e8f0; width: 70%; vertical-align: middle;">
                📞 24/7 Customer Support: <span style="color: #c5a46e; font-weight: bold;">+1 888 306 4617</span> &nbsp;|&nbsp;
                ✉ Email: <span style="color: #c5a46e; font-weight: bold;">support@businessclasstravel.us</span> &nbsp;|&nbsp;
                🌐 Website: <span style="color: #c5a46e; font-weight: bold;">www.businessclasstravel.us</span>
            </td>
            <td style="width: 30%; text-align: right; padding: 15px; vertical-align: middle; font-size: 12px; font-weight: bold; color: #c5a46e; font-style: italic;">
                Elevate Your Journey &nbsp;✈
            </td>
        </tr>
    </table>
</div>
HTML;
    }
}
