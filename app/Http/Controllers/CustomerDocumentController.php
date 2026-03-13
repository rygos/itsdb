<?php

namespace App\Http\Controllers;

use App\Helpers\LogHelper;
use App\Models\Customer;
use App\Models\CustomerDocument;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CustomerDocumentController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'customer_id' => ['required', 'integer', 'exists:customers,id'],
            'document' => ['required', 'file', 'max:20480'],
            'description' => ['nullable', 'string', 'max:255'],
        ]);

        $customer = Customer::findOrFail($validated['customer_id']);
        $file = $request->file('document');
        $storedName = Str::uuid()->toString().'.'.$file->getClientOriginalExtension();
        $path = $file->storeAs('customer-documents/'.$customer->id, $storedName, 'local');

        $document = CustomerDocument::create([
            'customer_id' => $customer->id,
            'user_id' => auth()->id(),
            'original_name' => $file->getClientOriginalName(),
            'stored_name' => $storedName,
            'disk' => 'local',
            'path' => $path,
            'description' => $validated['description'] ?? null,
            'file_size' => $file->getSize(),
            'mime_type' => $file->getClientMimeType(),
        ]);

        LogHelper::log('customer', $customer->id, 'Document', 'Upload Dokument: '.$document->original_name);

        return redirect()->route('customers.view', $customer->id);
    }

    public function download(int $id): StreamedResponse
    {
        $document = CustomerDocument::findOrFail($id);
        abort_unless(Storage::disk($document->disk)->exists($document->path), 404);

        return Storage::disk($document->disk)->download($document->path, $document->original_name);
    }

    public function delete(int $id): RedirectResponse
    {
        $document = CustomerDocument::findOrFail($id);
        $customerId = $document->customer_id;

        Storage::disk($document->disk)->delete($document->path);
        LogHelper::log('customer', $customerId, 'Document', 'Delete Dokument: '.$document->original_name);
        $document->delete();

        return redirect()->route('customers.view', $customerId);
    }
}
