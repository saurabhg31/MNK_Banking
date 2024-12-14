<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Transactions list for account ' . $account->account_no) }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg" style="height: 900px; width: 1300px">
                <div class="p-6 text-gray-900">
                    <div>
                        <table class="table table-bordered table-striped table-hover" id="transactionsTable">
                            <thead class="table-dark">
                                <tr>
                                    <th scope="col">From Account</th>
                                    <th scope="col">To Account</th>
                                    <th scope="col">Transfer Amt</th>
                                    <th scope="col">From Currency</th>
                                    <th scope="col">Transfer Amt after conversion</th>
                                    <th scope="col">To Currency</th>
                                    <th scope="col">Type</th>
                                    <th scope="col">Balance before transfer</th>
                                    <th scope="col">Balance after transfer</th>
                                    <th scope="col">Description</th>
                                    <th scope="col">Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($transactions as $transaction)
                                    <tr>
                                        <td>{{ $transaction->from_account }}</td>
                                        <td>{{ $transaction->to_account }}</td>
                                        <td>{{ $transaction->amount }}</td>
                                        <td>{{ $transaction->amount_currency }}</td>
                                        <td>{{ $transaction->payment_amt }}</td>
                                        <td>{{ $transaction->payment_currency }}</td>
                                        @if ($account->account_no == $transaction->to_account)
                                            <td>Credit</td>
                                            <td>{{ $transaction->to_account_bal_before_transfer }}</td>
                                            <td>{{ $transaction->to_account_bal_after_transfer }}</td>
                                        @else
                                            <td>Debit</td>
                                            <td>{{ $transaction->from_account_bal_before_transfer }}</td>
                                            <td>{{ $transaction->from_account_bal_after_transfer }}</td>
                                        @endif
                                        <td>{{ $transaction->description }}</td>
                                        <td>{{ $transaction->created_at }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
