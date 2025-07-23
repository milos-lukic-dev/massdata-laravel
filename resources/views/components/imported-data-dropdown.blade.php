@if(!empty($importedDataLinks))
    <li class="nav-item has-treeview {{ $isImportedDataOpen ? 'menu-open' : '' }}">
        <a href="#" class="nav-link {{ $isImportedDataOpen ? 'active' : '' }}">
            <i class="nav-icon fas fa-database"></i>
            <p>
                Imported Data
                <i class="right fas fa-angle-left"></i>
            </p>
        </a>
        <ul class="nav nav-treeview">
            @foreach($importedDataLinks as $importedDataLinkType => $importedDataLink)
                @foreach($importedDataLink ?? [] as $importedDataLinkFileKey => $importedDataLinkIsActive)
                    <li class="nav-item">
                        <a href="{{ route('imported-data.show', [$importedDataLinkType, $importedDataLinkFileKey]) }}" class="nav-link {{ $importedDataLinkIsActive ? 'active' : '' }}">
                            <p>{{ ucfirst($importedDataLinkType) }} - {{ $importedDataLinkFileKey }}</p>
                        </a>
                    </li>
                @endforeach
            @endforeach
        </ul>
    </li>
@endif
