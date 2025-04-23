<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Sent Emails') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 bg-white p-6 rounded shadow">
            {{-- <form method="GET" action="{{ route('search-users') }}" class="mb-4">
                <input type="text" name="search" placeholder="Search by email"
                    value="{{ request('search') }}"
                    class="border border-gray-300 rounded px-4 py-2 w-full sm:w-1/3 focus:outline-none focus:ring-2 focus:ring-blue-400">
            </form> --}}

            <!-- Success Message -->
            @if(session('status'))
            <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">
                {{ session('status') }}
            </div>
            @endif   
                  

            <!-- Bulk Delete Form -->
            <form method="POST" action="" onsubmit="return confirm('Are you sure you want to delete selected emails?');">
            @csrf
            @method('DELETE')

            <div class="overflow-x-auto">
                <table class="min-w-full table-auto border-collapse">
                    <thead>
                        <tr class="bg-gray-200 text-left text-sm text-gray-700">                           
                            <th class="py-2 px-4 border-b">#</th>
                            <th class="py-2 px-4 border-b">User Name</th>
                            <th class="py-2 px-4 border-b">Email</th>
                            <th class="py-2 px-4 border-b">User Role</th>
                            <th class="py-2 px-4 border-b">User Permissiom</th>
                            <th class="py-2 px-4 border-b text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>                       
                  @forelse ($all_users_role_permission as $users_role_permissions)
                  <tr class="hover:bg-gray-50 text-sm">
                      <td class="py-2 px-4 border-b text-center">
                        {{$users_role_permissions->id}}
                      </td>                   
                      <td class="py-2 px-4 border-b">{{$users_role_permissions->name}}</td>
                      <td class="py-2 px-4 border-b">{{$users_role_permissions->email}}</td>
                      <td class="py-2 px-4 border-b"> 
                          @foreach ($users_role_permissions['roles'] as $current_role)
                              {{$current_role->name}}
                          @endforeach
                      </td>
                      <td class="py-2 px-4 border-b">
                          @foreach ($users_role_permissions['permissions'] as $permissions)
                              {{$permissions->name.','}}
                          @endforeach
                      </td>                          
                                               
                      <td class="py-2 px-4 border-b">
                        <a href="{{route('edit-user',$users_role_permissions->id)}}"><button type="button" class="bg-transparent hover:bg-blue-500 text-blue-700 font-semibold hover:text-white py-2 px-4 border border-blue-500 hover:border-transparent rounded"> Edit </button>  </a>
                        <a href="{{route('remove-user-role-permissions',$users_role_permissions->id)}}" onclick="return confirm('Are you sure want to remove the user role and permissions to the user?')"><button type="button" class="bg-transparent hover:bg-yellow-500 text-blue-700 font-semibold hover:text-white py-2 px-4 border border-blue-500 hover:border-transparent rounded"> Remove Role & Permissions </button>  </a>
                        <a href="{{route('delete-user',$users_role_permissions->id)}}" onclick="return confirm('Are you sure want to delete the user?')"><button type="button" class="bg-transparent hover:bg-red-500 text-blue-700 font-semibold hover:text-white py-2 px-4 border border-blue-500 hover:border-transparent rounded"> Delete User </button>  </a>
                      </td>                          
                  </tr>
                  @empty
                  <tr>
                      <td colspan="7" class="text-center py-4 text-gray-500">No emails found.</td>
                  </tr>
                  @endforelse
                    </tbody>
                </table>
            </div>           
            </form>

            <div class="mt-4">
                {{-- {{ $emails->withQueryString()->links() }} --}}
            </div>
            
        </div>
    </div>
</x-app-layout>
