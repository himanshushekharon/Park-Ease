<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class InvoiceController extends Controller
{
    /**
     * Generate PDF invoice/ticket for a booking
     *
     * @param Booking $booking
     * @return bool
     */
    public static function generateInvoice(Booking $booking)
    {
        try {
            // Ensure relationships are loaded
            $booking->load(['user', 'parkingLot', 'slot']);

            // Prepare data for the PDF view
            $data = [
                'booking' => $booking,
                'parkingLot' => $booking->parkingLot,
                'slot' => $booking->slot,
                'user' => $booking->user,
            ];

            // Generate PDF
            $pdf = Pdf::loadView('invoices.ticket', $data);

            // Configure PDF options for better rendering
            $pdf->setPaper('A4', 'portrait');
            $pdf->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);

            // Ensure directory exists
            if (!Storage::disk('public')->exists('invoices')) {
                Storage::disk('public')->makeDirectory('invoices');
            }

            // Create unique filename
            $invoiceNumber = 'INV-' . strtoupper(uniqid()) . '-' . $booking->booking_id;
            $fileName = 'invoice_' . $booking->_id . '.pdf';
            $filePath = 'invoices/' . $fileName;

            // Save PDF to storage/app/public/invoices
            Storage::disk('public')->put($filePath, $pdf->output());

            // Update booking record
            $booking->update([
                'invoice_path' => $filePath,
                'invoice_number' => $invoiceNumber,
                'generated_at' => now(),
            ]);

            return true;
        } catch (Exception $e) {
            Log::error('PDF Generation Failed for Booking ' . $booking->_id . ': ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Download the invoice PDF
     */
    public function download($bookingId)
    {
        try {
            $booking = Booking::findOrFail($bookingId);

            // Ensure user owns this booking or is an admin/owner
            if (Auth::id() !== $booking->user_id && Auth::id() !== $booking->parkingLot->owner_id) {
                return abort(403, 'Unauthorized action.');
            }

            if (!$booking->invoice_path || !Storage::disk('public')->exists($booking->invoice_path)) {
                // Try generating it if missing
                $success = self::generateInvoice($booking);
                if (!$success || !Storage::disk('public')->exists($booking->invoice_path)) {
                    return back()->with('error', 'Invoice not found and could not be generated.');
                }
            }

            return Storage::disk('public')->download($booking->invoice_path, 'ParkEase_Ticket_' . $booking->booking_id . '.pdf');
        } catch (Exception $e) {
            Log::error('Invoice Download Failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to download invoice.');
        }
    }

    /**
     * View the invoice PDF in browser
     */
    public function view($bookingId)
    {
        try {
            $booking = Booking::findOrFail($bookingId);

            // Ensure user owns this booking or is an admin/owner
            if (Auth::id() !== $booking->user_id && Auth::id() !== $booking->parkingLot->owner_id) {
                return abort(403, 'Unauthorized action.');
            }

            if (!$booking->invoice_path || !Storage::disk('public')->exists($booking->invoice_path)) {
                // Try generating it if missing
                $success = self::generateInvoice($booking);
                if (!$success || !Storage::disk('public')->exists($booking->invoice_path)) {
                    return back()->with('error', 'Invoice not found and could not be generated.');
                }
            }

            return Storage::disk('public')->response($booking->invoice_path);
        } catch (Exception $e) {
            Log::error('Invoice View Failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to view invoice.');
        }
    }
}
