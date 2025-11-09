<!-- Sidebar -->
<div class="sidebar modern-sidebar" data-background-color="dark">
    <div class="sidebar-logo">
        <div class="logo-header">
            <a href="{{ route('dashboard') }}" class="logo d-flex align-items-center">
                <img src="{{ asset('assets/img/kaiadmin/logo_light.svg') }}" alt="Pharmacy" height="26" />
                <span class="ms-2 fw-semibold text-white">Pharmacy POS</span>
            </a>
            <button class="btn btn-sm btn-outline-light toggle-sidebar ms-auto">
                <i class="fas fa-bars"></i>
            </button>
        </div>
    </div>

    <div class="sidebar-wrapper scrollbar scrollbar-inner">
        <div class="sidebar-content">
            <ul class="nav flex-column nav-secondary">

                <!-- Dashboard -->
                <li class="nav-item active">
                    <a href="{{ route('dashboard') }}" class="nav-link">
                        <i class="fas fa-home"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                <!-- Inventory Management Section -->
                <li class="nav-section">
                    <h6 class="text-uppercase text-muted mt-3 mb-2">Inventory Management</h6>
                </li>

                @can('item_view')
                <li class="nav-item">
                    <a href="{{ route('items.index') }}" class="nav-link">
                        <i class="fas fa-pills"></i>
                        <span>Items / Medicines</span>
                    </a>
                </li>
                @endcan

                @can('category_view')
                <li class="nav-item">
                    <a href="{{ route('categories.index') }}" class="nav-link">
                        <i class="fas fa-tags"></i>
                        <span>Categories</span>
                    </a>
                </li>
                @endcan

                @can('unit_view')
                <li class="nav-item">
                    <a href="{{ route('units.index') }}" class="nav-link">
                        <i class="fas fa-ruler-combined"></i>
                        <span>Units</span>
                    </a>
                </li>
                @endcan

                @can('supplier_view')
                <li class="nav-item">
                    <a href="{{ route('suppliers.index') }}" class="nav-link">
                        <i class="fas fa-truck"></i>
                        <span>Suppliers</span>
                    </a>
                </li>
                @endcan

                @can('stock_movement_view')
                <li class="nav-item">
                    <a href="{{ route('stock_movements.index') }}" class="nav-link">
                        <i class="fas fa-exchange-alt"></i>
                        <span>Stock Movements</span>
                    </a>
                </li>
                @endcan


                <!-- Purchasing -->
                <li class="nav-section">
                    <h6 class="text-uppercase text-muted mt-3 mb-2">Purchasing</h6>
                </li>

                @can('purchase_view')
                <li class="nav-item">
                    <a href="{{ route('purchases.index') }}" class="nav-link">
                        <i class="fas fa-file-invoice-dollar"></i>
                        <span>Purchases</span>
                    </a>
                </li>
                @endcan

                @can('purchase_item_view')
                <li class="nav-item">
                    <a href="{{ route('purchase-items.index') }}" class="nav-link">
                        <i class="fas fa-list-ol"></i>
                        <span>Purchase Items</span>
                    </a>
                </li>
                @endcan

                @can('purchase_report_view')
                <li class="nav-item">
                    <a href="{{ route('purchases.items-report') }}" class="nav-link">
                        <i class="fas fa-chart-bar"></i>
                        <span>Purchase Reports</span>
                    </a>
                </li>
                @endcan


                <!-- Sales -->
                <li class="nav-section">
                    <h6 class="text-uppercase text-muted mt-3 mb-2">Sales</h6>
                </li>

                <li class="nav-item">
                    <a href="{{ route('customers.index') }}" class="nav-link">
                        <i class="fas fa-user-friends"></i>
                        <span>Customers</span>
                    </a>
                </li>

                @can('sale_view')
                <li class="nav-item">
                    <a href="{{ route('sales.create') }}" class="nav-link">
                        <i class="fas fa-receipt"></i>
                        <span>Sales Transactions</span>
                    </a>
                </li>
                @endcan

                @can('sale_item_view')
                <li class="nav-item">
                    <a href="{{ route('sale-items.index') }}" class="nav-link">
                        <i class="fas fa-list-ol"></i>
                        <span>Sale Items</span>
                    </a>
                </li>
                @endcan


                <!-- Reports -->
                <li class="nav-section">
                    <h6 class="text-uppercase text-muted mt-3 mb-2">Reports & Analytics</h6>
                </li>

                <li class="nav-item">
                    <a href="{{ route('reports.index') }}" class="nav-link">
                        <i class="fas fa-chart-pie"></i>
                        <span>Reports Dashboard</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('reports.sales') }}" class="nav-link">
                        <i class="fas fa-chart-line"></i>
                        <span>Sales Reports</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('reports.inventory') }}" class="nav-link">
                        <i class="fas fa-box-open"></i>
                        <span>Inventory Reports</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('reports.profit-loss') }}" class="nav-link">
                        <i class="fas fa-dollar-sign"></i>
                        <span>Profit & Loss</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('reports.expiry') }}" class="nav-link">
                        <i class="fas fa-exclamation-triangle"></i>
                        <span>Expiry Reports</span>
                    </a>
                </li>

                @can('purchase_report_view')
                <li class="nav-item">
                    <a href="{{ route('purchases.statistics') }}" class="nav-link">
                        <i class="fas fa-shopping-basket"></i>
                        <span>Purchase Analytics</span>
                    </a>
                </li>
                @endcan


                <!-- User Management -->
                <li class="nav-section">
                    <h6 class="text-uppercase text-muted mt-3 mb-2">User Management</h6>
                </li>

                @can('user_view')
                <li class="nav-item">
                    <a href="{{ route('users.index') }}" class="nav-link">
                        <i class="fas fa-users"></i>
                        <span>Users</span>
                    </a>
                </li>
                @endcan

                @can('role_management')
                <li class="nav-item">
                    <a href="{{ route('roles.index') }}" class="nav-link">
                        <i class="fas fa-user-shield"></i>
                        <span>Roles & Permissions</span>
                    </a>
                </li>
                @endcan


                <!-- Alerts -->
                <li class="nav-section">
                    <h6 class="text-uppercase text-muted mt-3 mb-2">Alerts & Notifications</h6>
                </li>

                @can('purchase_batch_manage')
                <li class="nav-item">
                    <a href="{{ route('purchases.batch-report') }}?expiring_soon=1" class="nav-link">
                        <i class="fas fa-exclamation-triangle text-warning"></i>
                        <span>Expiry Alerts</span>
                    </a>
                </li>
                @endcan

                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="fas fa-sync-alt text-info"></i>
                        <span>Reorder Notifications</span>
                    </a>
                </li>

                @can('purchase_payment_manage')
                <li class="nav-item">
                    <a href="{{ route('purchases.index') }}?payment_status=pending" class="nav-link">
                        <i class="fas fa-clock text-danger"></i>
                        <span>Payment Due Alerts</span>
                    </a>
                </li>
                @endcan
            </ul>
        </div>
    </div>
</div>

<!-- Modern Sidebar CSS -->
<style>
.modern-sidebar {
    background: #111827;
    width: 250px;
    min-height: 100vh;
    color: #e5e7eb;
    transition: all 0.3s ease;
    box-shadow: 2px 0 10px rgba(0, 0, 0, 0.3);
}

.modern-sidebar .logo-header {
    padding: 1rem 1.2rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    display: flex;
    align-items: center;
}

.modern-sidebar .toggle-sidebar {
    background: transparent;
    border: none;
    color: #9ca3af;
    transition: color 0.3s ease;
}

.modern-sidebar .toggle-sidebar:hover {
    color: #fff;
}

.modern-sidebar .nav-link {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 0.75rem 1.2rem;
    color: #9ca3af;
    font-weight: 500;
    border-radius: 10px;
    transition: all 0.3s ease;
}

.modern-sidebar .nav-link i {
    font-size: 1.1rem;
    width: 22px;
    text-align: center;
    color: #9ca3af;
    transition: color 0.3s ease;
}

.modern-sidebar .nav-link:hover {
    background: #1f2937;
    color: #fff;
}

.modern-sidebar .nav-link:hover i {
    color: #10b981;
}

.modern-sidebar .nav-item.active > .nav-link {
    background: linear-gradient(90deg, #10b981, #059669);
    color: #fff;
}

.modern-sidebar .nav-item.active i {
    color: #fff;
}

.modern-sidebar .nav-section h6 {
    font-size: 0.75rem;
    letter-spacing: 1px;
    color: #6b7280;
    padding: 0.5rem 1.2rem;
    margin-top: 1rem;
    text-transform: uppercase;
    border-top: 1px solid rgba(255, 255, 255, 0.08);
}

.scrollbar {
    overflow-y: auto;
}

.scrollbar::-webkit-scrollbar {
    width: 6px;
}

.scrollbar::-webkit-scrollbar-thumb {
    background: #374151;
    border-radius: 10px;
}
</style>
