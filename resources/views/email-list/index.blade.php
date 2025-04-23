<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800 leading-tight">Email List Upload</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 bg-white p-6 rounded shadow">
        <div class="flex justify-between items-center mb-4">
            <form action="{{ route('email.upload') }}" method="POST" enctype="multipart/form-data" class="flex gap-3">
                @csrf
                <input type="file" name="csv_file" class="border rounded px-2 py-1">
                <button type="submit" class="bg-blue-600 text-white px-3 py-1 rounded">Upload CSV</button>
            </form>

            <form method="GET" action="{{ route('email.list') }}">
                <input type="text" name="search" placeholder="Search email..." value="{{ $search }}"
                    class="border rounded px-3 py-1" />
            </form>
        </div>
          <!-- Success Message -->
          @if(session('success'))
          <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">
              {{ session('success') }}
          </div>
          @endif         

          @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                {{ session('error') }}
            </div>
          @endif
          <!-- Bulk Delete Form -->
          <form method="POST" action="{{ route('email.bulkDelete') }}" onsubmit="return confirm('Are you sure you want to delete selected emails?');">
          @csrf
          @method('DELETE')
        
          <div class="overflow-x-auto">
            <table class="min-w-full table-auto border-collapse">
                <thead>
                    <tr class="bg-gray-100 text-left">
                        <th class="py-2 px-4 border-b text-center">
                            <input type="checkbox" id="select-all">
                        </th>
                        <th class="px-4 py-2 border-b">#</th>
                        <th class="px-4 py-2 border-b">Email</th>
                        <th class="px-4 py-2 border-b">Uploaded At</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($emails as $email)
                        <tr class="hover:bg-gray-50">
                            <td class="py-2 px-4 border-b text-center">
                                <input type="checkbox" name="ids[]" value="{{ $email->id }}" class="select-item">
                            </td>
                            <td class="px-4 py-2 border-b">{{ $loop->iteration + ($emails->currentPage() - 1) * $emails->perPage() }}</td>
                            <td class="px-4 py-2 border-b">{{ $email->email }}</td>
                            <td class="px-4 py-2 border-b">{{ $email->created_at->format('d M, Y h:i A') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="text-center py-3 text-gray-500">No emails found.</td></tr>
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

        <div class="mt-4">{{ $emails->withQueryString()->links() }}</div>
        
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
