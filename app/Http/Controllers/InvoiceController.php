<?php

namespace App\Http\Controllers;

use App\Mail\InvoiceMail;
use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class InvoiceController extends Controller
{
    public function generatePDF(Request $request)
    {

        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'company_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'invoice_number' => 'required|unique:invoices|string|max:50',
            'invoice_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:invoice_date',
            'po_number' => 'required|string|max:50',
            'bill_to_name' => 'required|string|max:255',
            'bill_to_address' => 'required|string',
            'ship_to_name' => 'required|string|max:255',
            'ship_to_address' => 'required|string',
            'items' => 'required|array|min:1',
            'items.*.name' => 'required|string|max:255',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:0',
            'items.*.rate' => 'required|numeric|min:0',
            'items.*.amount' => 'required|numeric|min:0',
            'subtotal' => 'required|numeric|min:0',
            'discount_rate' => 'nullable|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'tax_rate' => 'nullable|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'total' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        // Handle company logo upload
        $logoPath = null;
        if ($request->hasFile('company_logo')) {
            // Store in public disk and get the path without 'public/'
            $logoPath = $request->file('company_logo')->store('company-logos', 'public');
        }

        // Create invoice record with items as JSON
        $invoice = Invoice::create([
            // 'user_id' => Auth::user()->id,
            'company_name' => $validated['company_name'],
            'company_logo' => $logoPath, // Store just the path, not the URL
            'invoice_number' => $validated['invoice_number'],
            'invoice_date' => $validated['invoice_date'],
            'due_date' => $validated['due_date'],
            'po_number' => $validated['po_number'],
            'bill_to_name' => $validated['bill_to_name'],
            'bill_to_address' => $validated['bill_to_address'],
            'ship_to_name' => $validated['ship_to_name'],
            'ship_to_address' => $validated['ship_to_address'],
            'items' => json_encode($validated['items']),
            'subtotal' => $validated['subtotal'],
            'discount_rate' => $validated['discount_rate'],
            'discount_amount' => $validated['discount_amount'],
            'tax_rate' => $validated['tax_rate'],
            'tax_amount' => $validated['tax_amount'],
            'total' => $validated['total'],
            'notes' => $validated['notes'],
        ]);

        // Prepare data for PDF
        $pdfData = [
            'invoice' => $invoice,
            'items' => json_decode($invoice->items, true),
        ];

        // Convert image to base64 for PDF (most reliable method for DomPDF)
        if ($logoPath) {
            // Get full storage path to the image
            $fullPath = storage_path('app/public/' . $logoPath);

            if (file_exists($fullPath)) {
                $type = pathinfo($fullPath, PATHINFO_EXTENSION);
                $data = file_get_contents($fullPath);
                $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
                $pdfData['logo_base64'] = $base64;
            }
        }

        // dd($pdfData['invoice']);

        // Generate PDF
        $pdf = Pdf::loadView('invoice_pdf', $pdfData);

        return $this->emailInvoice();
        // Return the PDF for download
        return $pdf->download('invoice-' . $invoice->invoice_number . '.pdf');
    }

    private function emailInvoice(string $pdfContent)
    {
        Mail::to($email)->send(new InvoiceMail( $pdfContent));
    }
}
