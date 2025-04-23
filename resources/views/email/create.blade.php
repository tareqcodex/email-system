<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Send Email') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div id="message" class="mb-4"></div>
        
            <form id="emailForm" method="POST" enctype="multipart/form-data" class="bg-white shadow p-6 rounded">
                @csrf
        
                <div class="mb-4">
                    <label class="block text-sm font-medium">To (comma-separated)</label>
                    <input type="text" name="to" class="w-full border-gray-300 rounded mt-1">
                    <p class="text-red-500 text-sm error-to"></p>
                </div>
        
                <div class="mb-4">
                    <label class="block text-sm font-medium">Subject</label>
                    <input type="text" name="subject" class="w-full border-gray-300 rounded mt-1">
                    <p class="text-red-500 text-sm error-subject"></p>
                </div>
        
                <div class="mb-4">
                    <label class="block text-sm font-medium">Description</label>
                    <textarea name="description" id="description" rows="5" class="w-full border-gray-300 rounded mt-1"></textarea>
                    <p class="text-red-500 text-sm error-description"></p>
                </div>
        
                <div class="mb-4">
                    <label class="block text-sm font-medium">Attachments</label>
                    <input type="file" name="attachments[]" multiple class="mt-1">
                    <p class="text-red-500 text-sm error-attachments"></p>
                </div>
        
                <button type="submit" id="sendBtn"
                    class="bg-blue-500 text-white font-semibold py-2 px-4 rounded hover:bg-blue-600">
                    Send
                </button>
            </form>
        </div>


        <script src="{{ asset('assets/js/jquery.js') }}"></script>
        <!-- Select 2 CSS -->  
        {{-- <link href="{{asset('assets/css/select2.min.css') }}" rel="stylesheet" /> --}}
        <!-- Summernote CSS -->
        <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet" />
        
        
        <!-- Select 2 JS --> 
        {{-- <script src="{{ asset('assets/js/select2.min.js') }}"></script>             --}}
        <!-- Summernote JS -->        
        <script src="{{ asset('assets/js/summernote-lite.min.js') }}"></script> 
        
            <script>
                document.getElementById('emailForm').addEventListener('submit', function (e) {
                e.preventDefault();

                const form = e.target;

                // ✅ Summernote content manually push to textarea before FormData
                $('#description').val($('#description').summernote('code'));

                const formData = new FormData(form);
                
                // Reset previous messages
                document.querySelector('#message').innerHTML = '';
                form.querySelectorAll('p.text-red-500').forEach(el => el.textContent = '');

                const sendBtn = document.getElementById('sendBtn');
                const originalText = sendBtn.innerHTML;
                sendBtn.disabled = true;
                sendBtn.innerHTML = 'Sending email...';

                const files = form.querySelector('input[name="attachments[]"]').files;
                for (let i = 0; i < files.length; i++) {
                    if (files[i].size > 20 * 1024 * 1024) {
                        sendBtn.disabled = false;
                        sendBtn.innerHTML = originalText;
                        document.querySelector('#message').innerHTML = `<div class="bg-red-200 text-red-800 p-3 rounded">Attachment "${files[i].name}" exceeds 20MB limit.</div>`;
                        return;
                    }
                }

                fetch("{{ route('email.send') }}", {
                    method: "POST",
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                    },
                    body: formData
                })
                .then(async response => {
                    sendBtn.disabled = false;
                    sendBtn.innerHTML = originalText;

                    if (response.status === 422) {
                        const data = await response.json();
                        for (const field in data.errors) {
                            document.querySelector(`.error-${field.replace('.', '-')}`).textContent = data.errors[field][0];
                        }
                    } else if (response.ok) {
                        const data = await response.json();
                        form.reset();
                        $('select[name="to[]"]').val(null).trigger('change');
                        $('#description').summernote('code', ''); // Clear summernote editor

                            document.querySelector('#message').innerHTML = `<div class="bg-green-200 text-green-800 p-3 rounded">${data.message}</div>`;
                        } else {
                            const data = await response.json();
                            document.querySelector('#message').innerHTML = `<div class="bg-red-200 text-red-800 p-3 rounded">${data.message ?? 'Unexpected error occurred.'}</div>`;
                        }
                    })
                    .catch(error => {
                        sendBtn.disabled = false;
                        sendBtn.innerHTML = originalText;
                        console.error('Unexpected error:', error);
                        document.querySelector('#message').innerHTML = `<div class="bg-red-200 text-red-800 p-3 rounded">Unexpected error occurred.</div>`;
                    });
                });

            </script>           
           
            <script>
                 // Initialize Summernote editor
                $('#description').summernote({
                    height: 400, // Set height of the editor
                    minHeight: null, // Disable min height
                    maxHeight: null, // Disable max height
                    focus: true, // Focus on the editor
                    toolbar: [
                        ['style', ['bold', 'italic', 'underline', 'clear']],
                        ['font', ['strikethrough', 'superscript', 'subscript']],
                        ['fontsize', ['fontsize']],
                        ['color', ['color']],
                        ['para', ['ul', 'ol', 'paragraph']],
                        ['height', ['height']],
                        ['table', ['table']],
                        ['misc', ['codeview', 'undo', 'redo']]
                    ]
                });
                // document.addEventListener('DOMContentLoaded', function () {
                //     $('select[name="to[]"]').select2({
                //         placeholder: 'Select recipient(s)'
                //     });
                // });
            </script>
    </div>
</x-app-layout>
