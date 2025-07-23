@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="card mt-4">
            <div class="card-header">
                <h1 class="card-title mb-0">Import logs</h1>
            </div>
            <div class="card-body">
                <div class="mb-0 table-responsive-sm">
                    <table class="table table-bordered table-striped mb-0">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Import Type</th>
                                <th>File Key</th>
                                <th>File Name</th>
                                <th>Status</th>
                                <th>Created At</th>
                                <th>Logs</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(!empty($items->count()))
                                @foreach($items as $item)
                                    <tr>
                                        <td>{{ $item->user_id }} - {{ $item->user->name ?? '-/-' }}</td>
                                        <td>{{ $item->import_type }}</td>
                                        <td>{{ $item->file_key }}</td>
                                        <td>{{ $item->file_name }}</td>
                                        <td>{{ $item->status }}</td>
                                        <td>{{ $item->created_at }}</td>
                                        <td>
                                            <a href="{{ route('import-log.show', $item->id) }}" class="btn btn-sm" title="Show errors">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="6" class="text-center">No result.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
                @if($items->hasPages())
                    <div class="mt-3 d-flex justify-content-end">
                        {{ $items->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
