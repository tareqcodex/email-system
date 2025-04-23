<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit User Role and Permissions') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div id="message" class="mb-4"></div>
            <form action="{{route('set-role-permission',$get_user['id'])}}" method="POST" class="bg-white shadow p-6 rounded">           
                @csrf
        
                <div class="mb-4">
                    <label class="block text-sm font-medium">Email</label>
                    <input type="email" name="subject" class="w-full border-gray-300 rounded mt-1"  readonly value="{{$get_user['email']}}">                    
                </div>
        
                <div class="mb-4">
                    <label class="block text-sm font-medium">User Roles</label>
                    <select name="user_role" class="w-full border-gray-300 rounded mt-1">
                        <option value="">Select a role</option>
                        @foreach ($all_roles as $role)                           
                           @php
                               $all_roles_array = explode(' ', $role->name);                               
                           @endphp
                        <option value="{{$role->id}}" @if (array_intersect($get_current_role, $all_roles_array)){{'selected'}}
                            @endif>{{$role->name}}</option>

                        @endforeach
                    </select>
                </div>
        
                <div class="mb-4">
                    <label class="block text-sm font-medium">User Permissions</label>
                    @foreach ($all_permissions as $permission) 
                    @php
                        $all_permissions_array = explode(' ', $permission->name); 
                                                
                    @endphp 
                      <input type="checkbox" name="user_permissions[]" value="{{ $permission->name}}"  @if (array_intersect($get_current_permissions, $all_permissions_array)){{'checked'}}
                      @endif> 
                      <label class="mr-2">{{ $permission->name}}</label> 
                  @endforeach
                </div>   
        
                <button type="submit" id="sendBtn"
                    class="bg-blue-500 text-white font-semibold py-2 px-4 rounded hover:bg-blue-600">
                    Update User
                </button>
            </form>
        </div>
    </div>
</x-app-layout>
