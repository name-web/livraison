<?php

namespace App\Http\Controllers\Backend\MerchantPanel;

use App\Http\Controllers\Controller;
use App\Models\Backend\Merchantpanel\InvoiceParcel;
use App\Repositories\Invoice\InvoiceInterface;

class InvoiceController extends Controller
{
    protected $repo;

    public function __construct(InvoiceInterface $repo)
    {
        $this->repo = $repo;
    }

    public function index()
    {
        $invoices = $this->repo->get();
        $stats = $this->repo->stats();

        return view('backend.merchant_panel.invoice.index', compact('invoices', 'stats'));
    }

    public function InvoiceDetails($invoiceId)
    {
        $invoice = $this->repo->InvoiceDetails($invoiceId);
        $invoiceParcels = InvoiceParcel::where('invoice_id', $invoice->id)->paginate(10);
        $summary = InvoiceParcel::where('invoice_id', $invoice->id)
            ->selectRaw('COUNT(*) as total_parcels, SUM(collected_amount) as collection, SUM(total_charge_amount) as charges, SUM(current_payable) as payable')
            ->first();

        return view('backend.merchant_panel.invoice.invoice_details', compact('invoice', 'invoiceParcels', 'summary'));
    }
}
