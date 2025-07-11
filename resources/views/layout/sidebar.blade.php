<aside id="sidebar" class="sidebar">
    <ul class="sidebar-nav" id="sidebar-nav">
        <li class="nav-item">
            <a class="nav-link collapsed" href="/dashboard">
                <i class="bi bi-grid"></i>
                <span>Dashboard</span>
            </a>
        </li>
        @can('access-admin-menu')
            <li class="nav-item">
                <a class="nav-link collapsed" href="#">
                    <i class="bi bi-folder2"></i>
                    <span>Daftar Laporan</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link collapsed" href="#">
                    <i class="bi bi-folder2"></i>
                    <span>Daftar Admin</span>
                </a>
            </li>
        @endcan
        <li class="nav-item">
            <a class="nav-link collapsed" href="#">
                <i class="bi bi-folder2"></i>
                <span>Daftar Pengguna</span>
            </a>
        </li>
    </ul>
</aside>
