<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Sent Emails') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 bg-white p-6 rounded shadow">
            <form method="GET" action="{{ route('sent.email.list') }}" class="mb-4">
                <input type="text" name="search" placeholder="Search by subject or recipient"
                    value="{{ request('search') }}"
                    class="border border-gray-300 rounded px-4 py-2 w-full sm:w-1/3 focus:outline-none focus:ring-2 focus:ring-blue-400">
            </form>

            <!-- Success Message -->
            @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">
                {{ session('success') }}
            </div>
            @endif         

            <!-- Bulk Delete Form -->
            <form method="POST" action="{{ route('sent.email.bulkDelete') }}" onsubmit="return confirm('Are you sure you want to delete selected emails?');">
            @csrf
            @method('DELETE')

            <div class="overflow-x-auto">
                <table class="min-w-full table-auto border-collapse">
                    <thead>
                        <tr class="bg-gray-200 text-left text-sm text-gray-700">
                            <th class="py-2 px-4 border-b text-center">
                                <input type="checkbox" id="select-all">
                            </th>
                            <th class="py-2 px-4 border-b">#</th>
                            <th class="py-2 px-4 border-b">Recipients</th>
                            <th class="py-2 px-4 border-b">Subject</th>
                            <th class="py-2 px-4 border-b">Description</th>
                            <th class="py-2 px-4 border-b">Sent At</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($emails as $email)
                        <tr class="hover:bg-gray-50 text-sm">
                            <td class="py-2 px-4 border-b text-center">
                                <input type="checkbox" name="ids[]" value="{{ $email->id }}" class="select-item">
                            </td>
                            <td class="py-2 px-4 border-b">
                                {{ $loop->iteration + ($emails->currentPage() - 1) * $emails->perPage() }}
                            </td>
                            <td class="py-2 px-4 border-b">{{ $email->to }}</td>
                            <td class="py-2 px-4 border-b">{{ $email->subject }}</td>
                            <td class="py-2 px-4 border-b">{{ \Illuminate\Support\Str::limit(strip_tags($email->description), 50) }}</td>
                            <td class="py-2 px-4 border-b">{{ $email->created_at->format('d M, Y h:i A') }}</td>                          
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-gray-500">No emails found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Bulk Delete Button -->
            @if($emails->count())
            <div class="mt-4"  id="bulk-delete-btn" style="display: none;">
                <button type="submit"  class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
                    Delete Selected
                </button>
            </div>
            @endif
            </form>

            <div class="mt-4">
                {{ $emails->withQueryString()->links() }}
            </div>
            
            <!-- Select All Script -->
            <script>
                const selectAll = document.getElementById('select-all');
                const checkboxes = document.querySelectorAll('.select-item');
                const bulkDeleteBtn = document.getElementById('bulk-delete-btn');

                function toggleDeleteButton() {
                    const anyChecked = Array.from(checkboxes).some(checkbox => checkbox.checked);
                    bulkDeleteBtn.style.display = anyChecked ? 'block' : 'none';
                }

                selectAll.addEventListener('click', function () {
                    checkboxes.forEach(cb => cb.checked = this.checked);
                    toggleDeleteButton();
                });

                checkboxes.forEach(cb => cb.addEventListener('change', toggleDeleteButton));


                // Select all
                document.getElementById('select-all').addEventListener('click', function () {
                    let checkboxes = document.querySelectorAll('.select-item');
                    checkboxes.forEach(cb => cb.checked = this.checked);
                });
            </script>
        </div>
    </div>
</x-app-layout>
