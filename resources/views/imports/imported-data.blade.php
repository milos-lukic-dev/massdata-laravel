@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="card mt-4">
            <div class="card-header">
                <h1 class="card-title mb-0">{{ ucfirst($importType) }} - {{ $fileKey }}</h1>
            </div>
            <div class="card-body">
                <form method="get" class="form-inline mb-3">
                    <input type="text" name="q" value="{{ request('q') }}" class="form-control mr-2" placeholder="Search (min 3 chars)...">
                    <button type="submit" class="btn btn-primary mr-2">Filter</button>
                    <a href="{{ route('imported-data.export', [$importType, $fileKey, 'q' => request('q')]) }}" class="btn btn-primary">Export</a>
                </form>

                <div class="mb-0 table-responsive-sm">
                    <table class="table table-bordered table-striped mb-0">
                        @if(!empty($headers))
                            <thead>
                                <tr>
                                    @foreach($headers as $field)
                                        <th>{{ $field['label'] }}</th>
                                    @endforeach
                                    <th>Actions</th>
                                </tr>
                            </thead>
                        @endif
                        <tbody>
                            @if(!empty($items->count()))
                                @foreach($items as $item)
                                    <tr>
                                        @if(!empty($headers))
                                            @foreach(array_keys($headers) as $field)
                                                <td>{{ $item->$field }}</td>
                                            @endforeach
                                        @endif
                                        <td>
                                            <form method="POST" action="{{ route('imported-data.audits', [$importType, $item->id]) }}" class="audit-form d-inline">
                                                @csrf
                                                <button type="button" class="btn btn-sm btn-audits" title="Show audit">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </form>
                                            @can("import-$importType")
                                                <form method="POST" action="{{ route('imported-data.destroy', [$importType, $fileKey, $item->id]) }}" style="display:inline;">
                                                    @csrf @method('DELETE')
                                                    <button type="button" class="btn btn-sm btn-delete" title="Delete row">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </form>
                                            @endcan
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="{{ !empty($headers) ? count($headers) + 1 : 1 }}" class="text-center">No result.</td>
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
    @include('components.modals.audit')
@endsection

