<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Member;
use App\Exports\TransactionsExport;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class TransactionController extends Controller
{
    use AuthorizesRequests;
    public function index(Request $request)
    {
        $orgId = auth()->user()->organization_id;

        $query = Transaction::with('member')
            ->where('organization_id', $orgId);

        // Search filter
        if ($request->filled('search')) {
            $query->whereHas('member', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        // Type filter
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Date range filter
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $transactions = $query->latest()->paginate(15)->withQueryString();

        // Calculate stats
        $stats = [
            'total_revenue' => Transaction::where('organization_id', $orgId)
                ->where('status', 'completed')
                ->where('type', 'payment')
                ->sum('amount'),
            'pending_amount' => Transaction::where('organization_id', $orgId)
                ->where('status', 'pending')
                ->sum('amount'),
            'total_transactions' => Transaction::where('organization_id', $orgId)->count(),
        ];

        return view('organization.transactions.index', compact('transactions', 'stats'));
    }

    public function show(Transaction $transaction)
    {
        if ($transaction->organization_id !== auth()->user()->organization_id) {
            abort(403);
        }

        return view('organization.transactions.show', compact('transaction'));
    }

    public function exportCsv()
    {
        $transactions = Transaction::where('organization_id', auth()->user()->organization_id)
            ->with('member')
            ->latest()
            ->get();

        $filename = 'transactions_' . date('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($transactions) {
            $file = fopen('php://output', 'w');

            fputcsv($file, ['Transaction ID', 'Member Name', 'Member Email', 'Type', 'Amount (RM)', 'Status', 'Payment Method', 'Date']);

            foreach ($transactions as $transaction) {
                fputcsv($file, [
                    $transaction->id,
                    $transaction->member->name ?? '-',
                    $transaction->member->email ?? '-',
                    ucfirst($transaction->type),
                    number_format($transaction->amount, 2),
                    ucfirst($transaction->status),
                    $transaction->payment_method ?? '-',
                    $transaction->created_at->format('Y-m-d H:i:s')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportExcel()
    {
        $filename = 'transactions_' . date('Y-m-d_His') . '.xlsx';
        return Excel::download(new TransactionsExport(auth()->user()->organization_id), $filename);
    }

    public function exportPdf()
    {
        $transactions = Transaction::where('organization_id', auth()->user()->organization_id)
            ->with('member')
            ->latest()
            ->get();

        $pdf = Pdf::loadView('organization.transactions.pdf', compact('transactions'));
        return $pdf->download('transactions_' . date('Y-m-d_His') . '.pdf');
    }
}
