@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="card mt-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h1 class="card-title mb-0">Users</h1>
                <a href="{{ route('user.create') }}" class="btn btn-primary ms-auto">
                    <i class="fas fa-plus me-1"></i> Create New User
                </a>
            </div>
            <div class="card-body">
                <div class="mb-0 table-responsive-sm">
                    <table class="table table-bordered table-striped mb-0">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Permissions</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if(!empty($users->count()))
                            @foreach($users as $user)
                                <tr>
                                    <td>{{ $user->id }}</td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->getPermissionNames()->join(', ') }}</td>
                                    <td>
                                        @can('user-management')
                                            <a href="{{ route('user.edit', $user->id) }}" class="btn btn-sm" title="Edit user">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('user.destroy') }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <input type="hidden" name="id" value="{{ $user->id }}">
                                                <button type="button" class="btn btn-sm btn-delete" title="Delete user">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        @endcan
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="5" class="text-center">No result.</td>
                            </tr>
                        @endif
                        </tbody>
                    </table>
                </div>
                @if($users->hasPages())
                    <div class="mt-3 d-flex justify-content-end">
                        {{ $users->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

