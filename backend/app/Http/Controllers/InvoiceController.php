<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invoice;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Enums\InvoiceStatus;
use App\Enums\PaymentTerms;
use Mews\Purifier\Facades\Purifier;
use App\Http\Requests\InvoiceStoreRequest;
use App\Http\Requests\InvoiceUpdateRequest;

class InvoiceController extends Controller
{
    /**
     * Fetch all invoices sorted by latest created, and return as JSON response.
     */
    public function index()
    {
        return response()->json(Invoice::orderBy('created_at', 'desc')->get());
    }

    /**
     * Display the specified invoice.
     *
     * @param string $id  The ID of the invoice to retrieve.
     * @return \Illuminate\Http\JsonResponse  JSON response with invoice data or error message.
     */
    public function show(string $id)
    {
        $invoice = Invoice::find($id);
        if (!$invoice) {
            return response()->json([
                'error' => 'Invoice not found.',
                'invoice_id' => $id
            ], 404);
        }
        return response()->json($invoice);
    }

    /**
     * Store a newly created invoice in storage.
     *
     * - Validates and sanitizes the incoming request data.
     * - Calculates `paymentDue` and `total`.
     * - Encodes item list as JSON.
     *
     * @param InvoiceStoreRequest $request  The validated invoice request.
     * @return \Illuminate\Http\JsonResponse  The created invoice resource or error response.
     */
    public function store(InvoiceStoreRequest $request)
    {
        $validated = $request->validated();
        $sanitized = $this->sanitizeInvoiceInput($validated);
        // Set status
        if (($sanitized['status'] ?? null) === InvoiceStatus::Draft->value) {
            $sanitized['status'] = InvoiceStatus::Draft->value;
        } elseif (($sanitized['status'] ?? null) === InvoiceStatus::Pending->value) {
            $sanitized['status'] = InvoiceStatus::Pending->value;
        } else {
            return response()->json(['error' => 'Invalid status. Must be either "draft" or "pending".'], 422);
        }
        // Calculate paymentDue
        $sanitized['paymentDue'] = Carbon::parse($sanitized['createdAt'])->addDays((int)$sanitized['paymentTerms'])->toDateString();
        if (isset($sanitized['items']) && is_array($sanitized['items'])) {
            $sanitized['total'] = array_reduce($sanitized['items'], function($sum, $item) {
                return $sum + ((isset($item['quantity']) ? $item['quantity'] : 0) * (isset($item['price']) ? $item['price'] : 0));
            }, 0);
            $sanitized['items'] = json_encode($sanitized['items']);
        } else {
            $sanitized['total'] = 0;
            $sanitized['items'] = json_encode([]);
        }
        $invoice = Invoice::create($sanitized);
        return response()->json($invoice, 201);
    }

    /**
     * Update an existing invoice.
     *
     * - Finds invoice by ID and ensures it's editable (not paid).
     * - Validates and sanitizes input.
     * - Recalculates `paymentDue` and `total`.
     * - Updates the invoice with new data.
     *
     * @param InvoiceUpdateRequest $request  The validated invoice update request.
     * @param string $id  The ID of the invoice to update.
     * @return \Illuminate\Http\JsonResponse  The updated invoice or error response.
     */
    public function update(InvoiceUpdateRequest $request, string $id)
    {
        $invoice = Invoice::find($id);
        if (!$invoice) {
            return response()->json([
                'error' => 'Invoice not found.',
                'invoice_id' => $id
            ], 404);
        }
        // Prevent updating paid invoices
        if ($invoice->status === InvoiceStatus::Paid) {
            return response()->json([
                'error' => 'This invoice has been marked as paid and cannot be edited.'
            ], 403);
        }
        $validated = $request->validated();
        $sanitized = $this->sanitizeInvoiceInput($validated);
        if (($sanitized['status'] ?? null) === InvoiceStatus::Draft->value) {
            $sanitized['status'] = InvoiceStatus::Draft->value;
        } elseif (($sanitized['status'] ?? null) === InvoiceStatus::Pending->value) {
            $sanitized['status'] = InvoiceStatus::Pending->value;
        } else {
            return response()->json(['error' => 'Invalid status. Must be either "draft" or "pending".'], 422);
        }
        // Calculate paymentDue 
        $sanitized['paymentDue'] = Carbon::parse($sanitized['createdAt'])->addDays((int)$sanitized['paymentTerms'])->toDateString();
        if (isset($sanitized['items']) && is_array($sanitized['items'])) {
            $sanitized['total'] = array_reduce($sanitized['items'], function($sum, $item) {
                return $sum + ((isset($item['quantity']) ? $item['quantity'] : 0) * (isset($item['price']) ? $item['price'] : 0));
            }, 0);
            $sanitized['items'] = json_encode($sanitized['items']);
        }
        $invoice->update($sanitized);
        return response()->json($invoice);
    }

    /**
     * Delete an invoice by ID.
     *
     * - Finds the invoice and returns 404 if not found.
     * - Deletes the invoice from the database.
     *
     * @param string $id  The ID of the invoice to delete.
     * @return \Illuminate\Http\JsonResponse  Empty response with 204 on success or 404 if not found.
     */
    public function destroy(string $id)
    {
        $invoice = Invoice::find($id);
        if (!$invoice) {
            return response()->json([
                'error' => 'Invoice not found.',
                'invoice_id' => $id
            ], 404);
        }
        $invoice->delete();
        return response()->json(null, 204);
    }

    /**
     * Mark a pending invoice as paid.
     *
     * - Finds the invoice by ID and returns 404 if not found.
     * - Only allows invoices with "pending" status to be marked as "paid".
     * - Updates the status and returns the updated invoice.
     *
     * @param string $id  The ID of the invoice to mark as paid.
     * @return \Illuminate\Http\JsonResponse  Updated invoice or error response.
     */
    public function markAsPaid($id)
    {
        $invoice = Invoice::find($id);
        if (!$invoice) {
            return response()->json([
                'error' => 'Invoice not found.',
                'invoice_id' => $id
            ], 404);
        }
        if ($invoice->status !== InvoiceStatus::Pending) {
            return response()->json([
                'error' => 'Only pending invoices can be marked as paid.'
            ], 400);
        }
        $invoice->status =  InvoiceStatus::Paid;
        $invoice->save();
        return response()->json($invoice);
    }

    /**
     * Recursively sanitize all input fields using HTMLPurifier.
     * @param array $data  Raw input data to be sanitized.
     * @return array       Sanitized input data.
     */
    private function sanitizeInvoiceInput(array $data): array
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = $this->sanitizeInvoiceInput($value);
            } elseif (is_string($value)) {
                $data[$key] = Purifier::clean($value, 'plain');
            } else {
                // Leave integers, floats, booleans, etc. untouched
                $data[$key] = $value;
            }
        }
        return $data;
    }
}
