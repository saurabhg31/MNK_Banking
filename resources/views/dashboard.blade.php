<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg" style="height: 900px; width: 1300px">
                <div class="p-6 text-gray-900">
                    @if (session()->has('msg'))
                        <div class="alert alert-success">{{ session()->get('msg') }}</div>
                    @endif
                    @if (session()->has('error'))
                        <div class="alert alert-danger">{{ session()->get('error') }}</div>
                    @endif
                    @if (isset($users))
                        {{ __('Add Accounts') }}
                        <form method="POST" action="{{ route('admin.add_accounts') }}" id="addAccountsForm">
                            @csrf
                            <button type="button" class="btn btn-success" onclick="addMoreAccountFields()">Add
                                More</button>
                            <br><br>
                            <div class="accountDiv">
                                <select name="user[]" required>
                                    <option value="">Select User</option>
                                    @foreach ($users as $user)
                                        <option value={{ $user->id }}>{{ $user->name }}</option>}}
                                    @endforeach
                                </select>
                                <input type="text" name="firstname[]" placeholder="First name" value=""
                                    required>
                                <input type="text" name="lastname[]" placeholder="Last name" value="" required>
                                <input type="date" name="dob[]" placeholder="Date of Birth" value=""
                                    required>
                                <input type="text" name="address[]" placeholder="Address" value="" required
                                    style="width: 30%;">
                            </div>
                            <br>
                            <button type="submit" class="btn btn-primary">Add Accounts</button>
                        </form>
                    @endif
                    <br>
                    @if (isset($accounts))
                        <div class="accountsDiv">
                            {{ __('Accounts') }}
                            <div class="mb-3">
                                <input type="text" id="searchInput" class="form-control"
                                    placeholder="Search in table..." onkeyup="searchTable()">
                            </div>
                            <table class="table table-bordered table-striped table-hover" id="accountTable">
                                <thead class="table-dark">
                                    <tr>
                                        <th scope="col">User</th>
                                        <th scope="col">Name</th>
                                        <th scope="col">DOB</th>
                                        <th scope="col">Account No.</th>
                                        <th scope="col">Balance</th>
                                        <th scope="col"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($accounts as $account)
                                        <tr>
                                            <td>{{ $account->username }}</td>
                                            <td>{{ $account->first_name . ' ' . $account->last_name }}</td>
                                            <td>{{ $account->dob }}</td>
                                            <td>{{ $account->account_no }}</td>
                                            <td>{{ $account->balance . ' ' . $account->currency }}</td>
                                            <td>
                                                @if ($account->hasTransaction)
                                                    <a href="/transaction/detail/{{ $account->id }}"
                                                        class="btn btn-primary" target="_blank">Show Transactions</a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                    @if (isset($user) && $user === true)
                        @if ($account)
                            <h3 class="font-semibold text-xl text-gray-800 leading-tight">
                                {{ __('Transfer Funds') }}
                            </h3>
                            <form action="{{ route('funds.transfer') }}" method="POST">
                                @csrf
                                <input type="number" name="recipient_account_no" value=""
                                    placeholder="Account No." required>
                                <input type="number" name="amount" step="0.01" value="" placeholder="Amount"
                                    required>
                                <select name="currency" required>
                                    <option value="USD">USD</option>
                                    <option value="GBP">GBP</option>
                                    <option value="EUR">EUR</option>
                                </select>
                                <input type="text" name="description" value="" style="width: 40%;"
                                    placeholder="Description" required>
                                <button type="submit" class="btn btn-primary">Initiate Transfer</button>
                            </form>
                        @else
                            <div class="alert alert-warning">No accounts created yet</div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
<script>
    function addMoreAccountFields() {
        const accountDiv = document.querySelector('.accountDiv');
        const clonedDiv = accountDiv.cloneNode(true);
        clonedDiv.querySelectorAll('input').forEach(input => input.value = '');

        const removeButton = document.createElement('button');
        removeButton.type = 'button';
        removeButton.className = 'btn btn-danger';
        removeButton.textContent = 'Remove';
        removeButton.style.marginLeft = '10px';

        removeButton.addEventListener('click', () => {
            clonedDiv.nextElementSibling?.remove();
            clonedDiv.remove();
        });

        clonedDiv.appendChild(removeButton);

        const lineBreak = document.createElement('br');

        const submitButton = document.querySelector('#addAccountsForm button[type="submit"]');
        submitButton.parentNode.insertBefore(clonedDiv, submitButton);
        submitButton.parentNode.insertBefore(lineBreak, submitButton);
    }

    function searchTable() {
        const input = document.getElementById('searchInput').value.toLowerCase();
        const table = document.getElementById('accountTable');
        const rows = table.getElementsByTagName('tr');

        for (let i = 1; i < rows.length; i++) {
            const row = rows[i];
            const cells = row.getElementsByTagName('td');
            let matchFound = false;

            for (let j = 0; j < cells.length; j++) {
                const cellText = cells[j].innerText.toLowerCase();
                if (cellText.includes(input)) {
                    matchFound = true;
                    break;
                }
            }

            row.style.display = matchFound ? '' : 'none';
        }
    }
</script>
