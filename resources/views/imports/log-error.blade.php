@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="card mt-4">
            <div class="card-header">
                <h1 class="card-title mb-0">{{ $import->import_type }} - {{ $import->file_name }}</h1>
            </div>
            <div class="card-body">
                <div class="mb-0 table-responsive-sm">
                    <table class="table table-bordered table-striped mb-0">
                        <thead>
                            <tr>
                                <th>Value</th>
                                <th>Row</th>
                                <th>Column</th>
                                <th>Message</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(!empty($importErrors->count()))
                                @foreach($importErrors as $item)
                                    <tr>
                                        <td>{{ $item->value }}</td>
                                        <td>{{ $item->file_row }}</td>
                                        <td>{{ $item->file_column }}</td>
                                        <td>{{ $item->message }}</td>
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
                @if($importErrors->hasPages())
                    <div class="mt-3 d-flex justify-content-end">
                        {{ $importErrors->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
