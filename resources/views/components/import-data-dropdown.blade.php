@if(!empty($availableImportTypes))
    <li class="nav-item">
        <a href="{{ url('data-import') }}" class="nav-link {{ request()->is('data-import') ? 'active' : '' }}">
            <i class="nav-icon fas fa-boxes"></i>
            <p>Data Import</p>
        </a>
    </li>
@endif
