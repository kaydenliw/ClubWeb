@extends('layouts.dashboard')

@section('page-title', 'Add Transaction')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Add Transaction</h1>
        <p class="text-sm text-gray-500 mt-1">Add a {{ ucfirst($type) }} for {{ $member->name }}</p>
    </div>

    <form action="{{ route('organization.members.transactions.store', $member) }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="type" value="{{ $type }}">

        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6 space-y-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Transaction Type</label>
                <p class="text-lg font-semibold text-gray-900">
                    @if($type === 'payment')
                        <span class="text-green-600">Payment</span>
                    @else
                        <span class="text-red-600">Refund</span>
                    @endif
                </p>
            </div>

            <div>
                <label for="charge_id" class="block text-sm font-medium text-gray-700 mb-2">
                    Charge/Plan <span class="text-red-500">*</span>
                </label>
                <select name="charge_id" id="charge_id" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Select Charge/Plan</option>
                    @foreach($charges as $charge)
                        <option value="{{ $charge->id }}" data-amount="{{ $charge->amount }}">
                            {{ $charge->title }} - RM {{ number_format($charge->amount, 2) }}
                        </option>
                    @endforeach
                </select>
                @error('charge_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">
                    Amount (RM) <span class="text-red-500">*</span>
                </label>
                <input type="number" name="amount" id="amount" step="0.01" min="0.01" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                       placeholder="0.00">
                @error('amount')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="payment_method" class="block text-sm font-medium text-gray-700 mb-2">
                    Payment Method <span class="text-red-500">*</span>
                </label>
                <select name="payment_method" id="payment_method" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Select Payment Method</option>
                    <option value="bank_transfer">Bank Transfer</option>
                    <option value="cash">Cash</option>
                    <option value="cheque">Cheque</option>
                    <option value="online_banking">Online Banking</option>
                    <option value="credit_card">Credit Card</option>
                </select>
                @error('payment_method')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="receipt" class="block text-sm font-medium text-gray-700 mb-2">
                    Upload Receipt/Bank Slip
                </label>
                <div class="relative">
                    <input type="file" name="receipt" id="receipt" accept="image/*,.pdf"
                           class="hidden"
                           onchange="updateReceiptFileName(this)">
                    <label for="receipt" class="flex items-center justify-center w-full px-4 py-3 border-2 border-dashed border-gray-300 rounded-lg cursor-pointer hover:border-blue-500 hover:bg-blue-50 transition @error('receipt') border-red-500 @enderror">
                        <div class="text-center">
                            <svg class="mx-auto h-8 w-8 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <p class="mt-1 text-sm text-gray-600">
                                <span class="font-medium text-blue-600">Click to upload</span> or drag and drop
                            </p>
                            <p class="text-xs text-gray-500" id="receipt-file-name">JPG, PNG, PDF up to 2MB</p>
                        </div>
                    </label>
                </div>
                @error('receipt')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                <textarea name="notes" id="notes" rows="3"
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                          placeholder="Add any additional notes..."></textarea>
                @error('notes')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
                <a href="{{ route('organization.members.show', $member) }}"
                   class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 rounded-lg transition">
                    Cancel
                </a>
                <button type="submit"
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition">
                    Add Transaction
                </button>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
// Auto-fill amount when charge is selected
document.getElementById('charge_id').addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    const amount = selectedOption.getAttribute('data-amount');
    if (amount) {
        document.getElementById('amount').value = parseFloat(amount).toFixed(2);
    }
});

// Update receipt filename display
function updateReceiptFileName(input) {
    const fileNameDisplay = document.getElementById('receipt-file-name');
    if (input.files && input.files[0]) {
        const fileName = input.files[0].name;
        const fileSize = (input.files[0].size / 1024 / 1024).toFixed(2);
        fileNameDisplay.textContent = `${fileName} (${fileSize} MB)`;
        fileNameDisplay.classList.add('font-medium', 'text-green-600');
    } else {
        fileNameDisplay.textContent = 'JPG, PNG, PDF up to 2MB';
        fileNameDisplay.classList.remove('font-medium', 'text-green-600');
    }
}
</script>
@endpush
@endsection
