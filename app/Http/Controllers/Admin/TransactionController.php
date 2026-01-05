<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Organization;
use App\Models\Settlement;
use App\Exports\AdminTransactionsExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::with(['organization', 'member']);

        if ($request->filled('organization')) {
            $query->where('organization_id', $request->organization);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('search')) {
            $query->whereHas('member', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $transactions = $query->latest()->get();
        $organizations = Organization::orderBy('name')->get();

        return view('admin.transactions.index', compact('transactions', 'organizations'));
    }

    public function show(Transaction $transaction)
    {
        $transaction->load(['organization', 'member']);
        return view('admin.transactions.show', compact('transaction'));
    }

    public function syncToAccounting(Request $request)
    {
        $transactionIds = $request->input('transaction_ids', []);

        if (empty($transactionIds)) {
            return back()->with('error', 'No transactions selected for sync.');
        }

        $synced = Transaction::whereIn('id', $transactionIds)
            ->where('status', 'completed')
            ->update([
                'synced_to_accounting' => true,
                'synced_at' => now()
            ]);

        return back()->with('success', "Successfully synced {$synced} transaction(s) to accounting system.");
    }

    public function exportCsv()
    {
        return Excel::download(new AdminTransactionsExport, 'admin_transactions_' . date('Y-m-d_His') . '.csv', \Maatwebsite\Excel\Excel::CSV);
    }

    public function exportExcel()
    {
        return Excel::download(new AdminTransactionsExport, 'admin_transactions_' . date('Y-m-d_His') . '.xlsx');
    }

    public function exportPdf()
    {
        $transactions = Transaction::with(['organization', 'member'])->get();
        $pdf = Pdf::loadView('admin.transactions.pdf', compact('transactions'));
        return $pdf->download('admin_transactions_' . date('Y-m-d_His') . '.pdf');
    }

    public function getSettlementPreview(Request $request)
    {
        $transactionIds = $request->input('transaction_ids', []);

        if (empty($transactionIds)) {
            return response()->json(['error' => 'No transactions selected'], 400);
        }

        $transactions = Transaction::with(['organization', 'charge'])
            ->whereIn('id', $transactionIds)
            ->where('status', 'completed')
            ->whereNull('settlement_id')
            ->get();

        // Verify all transactions are from the same organization
        $organizationIds = $transactions->pluck('organization_id')->unique();
        if ($organizationIds->count() > 1) {
            return response()->json([
                'error' => 'All selected transactions must be from the same organization'
            ], 400);
        }

        if ($transactions->isEmpty()) {
            return response()->json(['error' => 'No valid transactions found'], 400);
        }

        $organization = $transactions->first()->organization;
        $totalAmount = $transactions->sum('amount');
        $totalPlatformFee = $transactions->sum('platform_fee');
        $netAmount = $totalAmount - $totalPlatformFee;

        return response()->json([
            'success' => true,
            'data' => [
                'organization' => [
                    'name' => $organization->name,
                    'bank_name' => $organization->bank_name,
                    'bank_account_number' => $organization->bank_account_number,
                    'bank_account_holder' => $organization->bank_account_holder,
                ],
                'transactions_count' => $transactions->count(),
                'total_amount' => number_format($totalAmount, 2),
                'total_platform_fee' => number_format($totalPlatformFee, 2),
                'net_amount' => number_format($netAmount, 2),
            ]
        ]);
    }

    public function markAsSettled(Request $request)
    {
        $transactionIds = $request->input('transaction_ids', []);

        if (empty($transactionIds)) {
            return back()->with('error', 'No transactions selected');
        }

        $transactions = Transaction::with('organization')
            ->whereIn('id', $transactionIds)
            ->where('status', 'completed')
            ->whereNull('settlement_id')
            ->get();

        // Verify all transactions are from the same organization
        $organizationIds = $transactions->pluck('organization_id')->unique();
        if ($organizationIds->count() > 1) {
            return back()->with('error', 'All selected transactions must be from the same organization');
        }

        if ($transactions->isEmpty()) {
            return back()->with('error', 'No valid transactions found');
        }

        $organization = $transactions->first()->organization;
        $totalAmount = $transactions->sum('amount');
        $totalPlatformFee = $transactions->sum('platform_fee');
        $netAmount = $totalAmount - $totalPlatformFee;

        // Create settlement
        $settlement = Settlement::create([
            'organization_id' => $organization->id,
            'settlement_number' => 'STL' . strtoupper(uniqid()),
            'amount' => $netAmount,
            'settlement_date' => now()->toDateString(),
            'scheduled_date' => now()->toDateString(),
            'status' => 'completed',
            'completed_at' => now(),
            'notes' => 'Settlement for ' . $transactions->count() . ' transaction(s)',
        ]);

        // Link transactions to settlement
        Transaction::whereIn('id', $transactionIds)->update([
            'settlement_id' => $settlement->id
        ]);

        return back()->with('success', "Successfully marked {$transactions->count()} transaction(s) as settled. Settlement #{$settlement->settlement_number} created.");
    }

    public function destroy(Transaction $transaction)
    {
        try {
            $transaction->delete();
            return redirect()->route('admin.transactions.index')
                ->with('success', 'Transaction deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('admin.transactions.index')
                ->with('error', 'Failed to delete transaction. ' . $e->getMessage());
        }
    }
}
