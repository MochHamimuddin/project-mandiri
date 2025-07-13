<aside id="sidebar" class="sidebar">
    <ul class="sidebar-nav" id="sidebar-nav">
        {{-- Menu for all users --}}
        <li class="nav-item">
            <a class="nav-link collapsed" href="/daftar-laporan">
                <i class="bi bi-file-earmark-text"></i>
                <span>Daftar Laporan</span>
            </a>
        </li>

        {{-- Menu for admin users (role '001') --}}
        @if(auth()->user()->code_role === '001')

            <li class="nav-item">
                <a class="nav-link collapsed" href="{{ route('mitra.index') }}">
                    <i class="bi bi-building"></i>
                    <span>Kelola Mitra</span>
                </a>
            </li>

        @endif
    </ul>
</aside>
