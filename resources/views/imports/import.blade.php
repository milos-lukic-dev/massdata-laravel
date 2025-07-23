@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="card mt-4">
            <div class="card-header">
                <h1 class="card-title mb-0">Data Import</h1>
            </div>
            <div class="card-body">
                <div id="import-data" data-import-types='@json($availableImportTypes)'>

                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0 list-unstyled">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('import.upload') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label for="import_type" class="form-label">Import Type</label>
                            <select name="import_type" id="import_type" class="form-control" required>
                                <option value="">Select</option>
                                @if(!empty($availableImportTypes))
                                    @foreach($availableImportTypes as $key => $type)
                                        <option value="{{ $key }}">{{ $type['label'] }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        <div id="file-inputs-container"></div>

                        <div class="text-end mt-3">
                            <button type="submit" class="btn btn-primary">Import</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

