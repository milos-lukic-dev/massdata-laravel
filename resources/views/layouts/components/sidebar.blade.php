<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="{{ url('/') }}" class="brand-link text-decoration-none">
        <span class="brand-text font-weight-bold">Priority Tire</span>
    </a>

    <div class="sidebar">
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
                @can('user-management')
                    <li class="nav-item {{ request()->is('user-management*') || request()->is('permission*') ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link {{ request()->is('user-management*') || request()->is('permission*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-people-arrows"></i>
                            <p>
                                User Management
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ route('user.index') }}" class="nav-link {{ request()->is('user-management*') ? 'active' : '' }}">
                                    <p>Users</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('permission.index') }}" class="nav-link {{ request()->is('permission*') ? 'active' : '' }}">
                                    <p>Permissions</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endcan
                <x-import-data-dropdown></x-import-data-dropdown>
                <x-imported-data-dropdown></x-imported-data-dropdown>
                <li class="nav-item">
                    <a href="{{ route('import-log.index') }}" class="nav-link {{ request()->is('import-log*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-boxes"></i>
                        <p>
                            Imports
                        </p>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</aside>
