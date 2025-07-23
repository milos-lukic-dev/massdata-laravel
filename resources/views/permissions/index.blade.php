@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="card mt-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h1 class="card-title mb-0">Permissions</h1>
                <a href="{{ route('permission.create') }}" class="btn btn-primary ms-auto">
                    <i class="fas fa-plus me-1"></i> Create New Permission
                </a>
            </div>
            <div class="card-body">
                @if($permissions->count())
                    <div class="row">
                        @foreach($permissions as $permission)
                            <div class="col-md-4 col-sm-6 mb-4">
                                <div class="card h-100 shadow-sm">
                                    <div class="card-body d-flex align-items-center">
                                        <div>
                                            <h5 class="card-title mb-0">
                                                {{ $permission->name }}
                                            </h5>
                                        </div>
                                        <div class="ms-auto d-flex align-items-center gap-2">
                                            <a href="{{ route('permission.users', $permission->id) }}" class="btn btn-sm"
                                               title="View Users with this Permission">
                                                <i class="fas fa-users"></i>
                                            </a>

                                            <a href="{{ route('permission.edit', $permission->id) }}" class="btn btn-sm"
                                               title="Edit Permission">
                                                <i class="fas fa-edit"></i>
                                            </a>

                                            <form action="{{ route('permission.destroy') }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <input type="hidden" name="id" value="{{ $permission->id }}">
                                                <button type="button" class="btn btn-sm btn-delete" title="Delete Permission">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @if($permissions->hasPages())
                        <div class="d-flex justify-content-end">
                            {{ $permissions->links() }}
                        </div>
                    @endif
                @else
                    <p class="mb-0 text-center">No result.</p>
                @endif
            </div>
        </div>
    </div>
@endsection
