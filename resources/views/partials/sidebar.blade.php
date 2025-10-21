<div class="sidebar" data-background-color="dark">
    <div class="sidebar-logo">
        <!-- Logo Header -->
        <div class="logo-header" data-background-color="dark">
            <a href="{{ route('dashboard') }}" class="logo">
                <img src="{{ asset('assets/img/kaiadmin/logo_light.svg') }}" alt="Pharmacy" class="navbar-brand" height="20" />
                {{-- <span class="logo-text  text-white">Pharmacy POS</span> --}}
            </a>
            <div class="nav-toggle">
                <button class="btn btn-toggle toggle-sidebar"><i class="fas fa-bars"></i></button>
            </div>
        </div>
        <!-- End Logo Header -->
    </div>

    <div class="sidebar-wrapper scrollbar scrollbar-inner">
        <div class="sidebar-content">
            <ul class="nav nav-secondary">
                <!-- Dashboard -->
                <li class="nav-item active">
                    <a href="{{ route('dashboard') }}">
                        <i class="fas fa-home"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                <!-- Inventory Management Section -->
                <li class="nav-section">
                    <span class="sidebar-mini-icon"><i class="fas fa-warehouse"></i></span>
                    <h4 class="text-section">Inventory Management</h4>
                </li>

                <!-- Items -->
                @can('item_view')
                <li class="nav-item">
                    <a href="{{ route('items.index') }}">
                        <i class="fas fa-pills"></i>
                        <p>Items / Medicines</p>
                    </a>
                </li>
                @endcan

                <!-- Categories -->
                @can('category_view')
                <li class="nav-item">
                    <a href="{{ route('categories.index') }}">
                        <i class="fas fa-tags"></i>
                        <p>Categories</p>
                    </a>
                </li>
                @endcan

                <!-- Units -->
                @can('unit_view')
                <li class="nav-item">
                    <a href="{{ route('units.index') }}">
                        <i class="fas fa-ruler-combined"></i>
                        <p>Units</p>
                    </a>
                </li>
                @endcan

                <!-- Suppliers -->
                @can('supplier_view')
                <li class="nav-item">
                    <a href="{{ route('suppliers.index') }}">
                        <i class="fas fa-truck"></i>
                        <p>Suppliers</p>
                    </a>
                </li>
                @endcan

                <!-- Stock Management -->
                @can('stock_movement_view')
                <li class="nav-item">
                    <a href="{{ route('stock_movements.index') }}">
                        <i class="fas fa-exchange-alt"></i>
                        <p>Stock Movements</p>
                    </a>
                </li>
                @endcan

                <!-- Purchasing Section -->
                <li class="nav-section">
                    <span class="sidebar-mini-icon"><i class="fas fa-shopping-cart"></i></span>
                    <h4 class="text-section">Purchasing</h4>
                </li>

                <!-- Purchases -->
                @can('purchase_view')
                <li class="nav-item">
                    <a href="{{ route('purchases.index') }}">
                        <i class="fas fa-file-invoice-dollar"></i>
                        <p>Purchases</p>
                    </a>
                </li>
                @endcan

                <!-- Purchase Items -->
                @can('purchase_item_view')
                <li class="nav-item">
                    <a href="{{ route('purchase-items.index') }}">
                        <i class="fas fa-list-ol"></i>
                        <p>Purchase Items</p>
                    </a>
                </li>
                @endcan

                <!-- Purchase Reports -->
                @can('purchase_report_view')
                <li class="nav-item">
                    <a href="{{ route('purchases.items-report') }}">
                        <i class="fas fa-chart-bar"></i>
                        <p>Purchase Reports</p>
                    </a>
                </li>
                @endcan

                <!-- Batch Management -->
                {{-- @can('purchase_batch_manage')
                <li class="nav-item">
                    <a href="{{ route('purchases.batch-report') }}">
                        <i class="fas fa-boxes"></i>
                        <p>Batch Management</p>
                    </a>
                </li>
                @endcan --}}



                <!-- Export Purchases -->
                {{-- @can('purchase_export')
                <li class="nav-item">
                    <a href="{{ route('purchases.export') }}">
                        <i class="fas fa-file-export"></i>
                        <p>Export Purchases</p>
                    </a>
                </li>
                @endcan --}}

                <!-- Sales Section -->
                <li class="nav-section">
                    <span class="sidebar-mini-icon"><i class="fas fa-cash-register"></i></span>
                    <h4 class="text-section">Sales</h4>
                </li>

                <!-- Sales -->
                @can('sale_view')
                <li class="nav-item">
                    <a href="{{ route('sales.create') }}">
                        <i class="fas fa-receipt"></i>
                        <p>Sales Transactions</p>
                    </a>
                </li>
                @endcan

                <!-- Sale Items -->
                @can('sale_item_view')
                <li class="nav-item">
                    <a href="{{ route('sale-items.index') }}">
                        <i class="fas fa-list-ol"></i>
                        <p>Sale Items</p>
                    </a>
                </li>
                @endcan


                <!-- Reports Section -->
                <li class="nav-section">
                    <span class="sidebar-mini-icon"><i class="fas fa-chart-bar"></i></span>
                    <h4 class="text-section">Reports & Analytics</h4>
                </li>

                <!-- Sales Reports -->
                <li class="nav-item">
                    <a href="#">
                        <i class="fas fa-chart-line"></i>
                        <p>Sales Reports</p>
                    </a>
                </li>

                <!-- Inventory Reports -->
                <li class="nav-item">
                    <a href="#">
                        <i class="fas fa-box-open"></i>
                        <p>Inventory Reports</p>
                    </a>
                </li>

                <!-- Purchase Analytics -->
                @can('purchase_report_view')
                <li class="nav-item">
                    <a href="{{ route('purchases.statistics') }}">
                        <i class="fas fa-shopping-basket"></i>
                        <p>Purchase Analytics</p>
                    </a>
                </li>
                @endcan

                <!-- User Management Section -->
                <li class="nav-section">
                    <span class="sidebar-mini-icon"><i class="fas fa-users-cog"></i></span>
                    <h4 class="text-section">User Management</h4>
                </li>

                <!-- Users -->
                @can('user_view')
                <li class="nav-item">
                    <a href="{{ route('users.index') }}">
                        <i class="fas fa-users"></i>
                        <p>Users</p>
                    </a>
                </li>
                @endcan

                <!-- Roles & Permissions -->
                @can('role_management')
                <li class="nav-item">
                    <a href="{{ route('roles.index') }}">
                        <i class="fas fa-user-shield"></i>
                        <p>Roles & Permissions</p>
                    </a>
                </li>
                @endcan

                <!-- Alerts Section -->
                <li class="nav-section">
                    <span class="sidebar-mini-icon"><i class="fas fa-bell"></i></span>
                    <h4 class="text-section">Alerts & Notifications</h4>
                </li>

                <!-- Expiry Alerts -->
                @can('purchase_batch_manage')
                <li class="nav-item">
                    <a href="{{ route('purchases.batch-report') }}?expiring_soon=1">
                        <i class="fas fa-exclamation-triangle text-warning"></i>
                        <p>Expiry Alerts</p>
                    </a>
                </li>
                @endcan

                <!-- Reorder Notifications -->
                <li class="nav-item">
                    <a href="#">
                        <i class="fas fa-sync-alt text-info"></i>
                        <p>Reorder Notifications</p>
                    </a>
                </li>

                <!-- Payment Due Alerts -->
                @can('purchase_payment_manage')
                <li class="nav-item">
                    <a href="{{ route('purchases.index') }}?payment_status=pending">
                        <i class="fas fa-clock text-danger"></i>
                        <p>Payment Due Alerts</p>
                    </a>
                </li>
                @endcan
            </ul>
        </div>
    </div>
</div>
