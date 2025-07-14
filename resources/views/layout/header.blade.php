<header id="header" class="header fixed-top d-flex align-items-center">
    <!-- Logo Section -->
    <div class="d-flex align-items-center justify-content-between">
        <a href="{{ route('daftar-laporan') }}" class="logo d-flex align-items-center">
            <img src="https://seen.asia/file/logocorporate/Logo_CR-U-AA-000446530861be54ae.png" alt="Logo">
            <span class="d-none d-lg-block">SUBCONT & SIB</span>
        </a>
        <i class="bi bi-list toggle-sidebar-btn"></i>
    </div><!-- End Logo -->
<!-- End Search Bar -->

    <!-- Navigation -->
    <nav class="header-nav ms-auto">
        <ul class="d-flex align-items-center">
            <!-- Mobile Search Icon -->
            <li class="nav-item d-block d-lg-none">
                <a class="nav-link nav-icon search-bar-toggle" href="#">
                    <i class="bi bi-search"></i>
                </a>
            </li><!-- End Search Icon-->

            <!-- Profile Dropdown -->
            <li class="nav-item dropdown pe-3">
                <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
                    <img src="{{ asset(auth()->user()->photo ?? 'https://static.vecteezy.com/system/resources/previews/005/544/718/non_2x/profile-icon-design-free-vector.jpg') }}" alt="Profile" class="rounded-circle">
                    <span class="d-none d-md-block dropdown-toggle ps-2">{{ auth()->user()->nama_lengkap }}</span>
                </a>

                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
                    <li class="dropdown-header">
                        <h6>{{ auth()->user()->nama_lengkap }}</h6>
                        <span>
                            @if(auth()->user()->code_role === '001')
                                Admin
                            @elseif(auth()->user()->code_role === '002')
                                Pengguna
                            @endif
                        </span>
                    </li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>

                    @if(in_array(auth()->user()->code_role, ['001', '002']))
                    @endif

                    <li>
                        <hr class="dropdown-divider">
                    </li>

                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <a class="dropdown-item d-flex align-items-center" href="{{ route('logout') }}"
                               onclick="event.preventDefault(); this.closest('form').submit();">
                                <i class="bi bi-box-arrow-right"></i>
                                <span>Keluar</span>
                            </a>
                        </form>
                    </li>
                </ul>
            </li><!-- End Profile Nav -->
        </ul>
    </nav><!-- End Icons Navigation -->
</header><!-- End Header -->
