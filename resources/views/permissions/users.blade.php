@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="card mt-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h1 class="card-title mb-0">Permission: <b>{{ $permissions->permission->name }}</b></h1>
                <div class="ms-auto">
                    <form id="permission_search_form" action="{{ route('permission.assignUserPermission') }}" method="POST" class="d-flex align-items-center gap-2">
                        @csrf
                        <input type="hidden" name="permission_id" value="{{ $permissions->permission->id }}">
                        <input type="hidden" name="user_id" id="permission_user_id">

                        <input type="text" id="permission_user_search" data-search-url="{{ route('permission.searchUser') }}" class="form-control" placeholder="Search user by email.." autocomplete="off">
                        <button type="submit" class="btn btn-primary">Assign</button>

                        <ul id="search_results" class="list-group position-absolute" style="z-index: 9999; top: 100%; width: 250px;"></ul>
                    </form>
                </div>
            </div>
            <div class="card-body">
                <div class="mb-0 table-responsive-sm">
                    <table id="usersTable" class="table table-bordered table-striped mb-0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(!empty($permissions->users->count()))
                                @foreach($permissions->users as $user)
                                    <tr>
                                        <td>{{ $user->id }}</td>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>
                                            <form action="{{ route('permission.removeUserPermission') }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <input type="hidden" name="user_id" value="{{ $user->id }}">
                                                <input type="hidden" name="permission_id" value="{{ $permissions->permission->id }}">
                                                <button type="button" class="btn btn-sm btn-delete" title="Remove user permission">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="4" class="text-center">No result.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
                @if($permissions->users->hasPages())
                    <div class="mt-3 d-flex justify-content-end">
                        {{ $permissions->users->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
