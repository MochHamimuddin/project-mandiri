<aside id="sidebar" class="sidebar">
    <ul class="sidebar-nav" id="sidebar-nav">
        {{-- Menu for admin users (role '001') --}}
        @if(auth()->user()->code_role === '001')
            <li class="nav-item">
                <a class="nav-link collapsed" href="{{ route('daftar-laporan') }}">
                    <i class="bi bi-file-earmark-text"></i>
                    <span>Daftar Laporan</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link collapsed" href="{{ route('mitra.index') }}">
                    <i class="bi bi-building"></i>
                    <span>Kelola Mitra</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link collapsed" href="{{ route('data-sib.index') }}">
                    <i class="bi bi-building"></i>
                    <span>Kelola SIB</span>
                </a>
            </li>
        @endif

        @if(auth()->user()->code_role === '002')
            @php
                // List of all report routes
                $reportRoutes = [
                    'fatigue-preventive.dashboard',
                    'inspeksi.dashboard',
                    'keselamatan.dashboard',
                    'fire-preventive.dashboard',
                    'development-manpower.dashboard',
                    'program-kesehatan.dashboard',
                    'program-lingkungan.dashboard',
                    'daftar-laporan'
                ];

                // List of all SIB routes
                $sibRoutes = [
                    'data-sib.create',
                    'data-sib.edit',
                    'data-sib.show'
                ];

                // Check current page type
                $isReportPage = in_array(\Route::currentRouteName(), $reportRoutes);
                $isSibPage = in_array(\Route::currentRouteName(), $sibRoutes);
            @endphp

            {{-- Show Laporan menu only when not on SIB page --}}
            @unless($isSibPage)
                <li class="nav-item">
                    <a class="nav-link @if($isReportPage) active @else collapsed @endif" href="{{ route('daftar-laporan') }}">
                        <i class="bi bi-file-earmark-text"></i>
                        <span>Daftar Laporan</span>
                    </a>
                </li>
            @endunless

            {{-- Show SIB menu only when not on report page --}}
            @unless($isReportPage)
                <li class="nav-item">
                    <a class="nav-link @if($isSibPage) active @else collapsed @endif" href="{{ route('data-sib.create') }}">
                        <i class="bi bi-plus-circle"></i>
                        <span>Buat SIB Baru</span>
                    </a>
                </li>
            @endunless
        @endif

        @if(auth()->user()->username === 'superadmin_kppmining')
            <li class="nav-item">
                <a class="nav-link collapsed" href="{{ route('users.index') }}">
                    <i class="bi bi-building"></i>
                    <span>Kelola Admin</span>
                </a>
            </li>
        @endif
    </ul>
</aside>
