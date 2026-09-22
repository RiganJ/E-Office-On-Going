<!doctype html>
<html lang="id">

    <head>
        <meta charset="utf-8">
        <meta name="viewport"
              content="width=device-width,initial-scale=1">
        <title>E-Office Universitas</title>
        <link rel="stylesheet"
              href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
              referrerpolicy="no-referrer">
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>@vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>

    <body class="app-shell">
        <div class="app-frame">
            <aside class="app-sidebar"><a href="{{ route('dashboard') }}"
                   class="app-logo"><span
                          class="logo-mark">E</span><span>E-Office<small>UNIVERSITAS</small></span></a>
                <nav>
                    <p class="app-menu-label">UTAMA</p><a
                       class="nav @if (request()->routeIs('dashboard')) is-active @endif"
                       href="{{ route('dashboard') }}"><span><i
                               class="fa-solid fa-house"></i></span>Beranda</a>
                    @if (auth()->user()->hasPermission('view documents'))
                        <p class="app-menu-label">INBOX</p><a
                           class="nav @if (request()->routeIs('documents.*')) is-active @endif"
                           href="{{ route('documents.index') }}"><span><i
                                   class="fa-solid fa-folder-open"></i></span>Dokumen</a><a
                           class="nav @if (request()->routeIs('incoming-letters.*')) is-active @endif"
                           href="{{ route('incoming-letters.index') }}"><span><i
                                   class="fa-solid fa-envelope"></i></span>Surat Masuk</a><a
                           class="nav @if (request()->routeIs('dispositions.*')) is-active @endif"
                           href="{{ route('dispositions.index') }}"><span><i
                                   class="fa-solid fa-share"></i></span>Disposisi</a><a
                           class="nav @if (request()->routeIs('internal-documents.*') && request()->route('type') === 'MEMO') is-active @endif"
                           href="{{ route('internal-documents.create', 'MEMO') }}"><span><i
                                   class="fa-solid fa-envelope-open-text"></i></span>Nota Dinas</a>
                    @endif
                    @if (auth()->user()->hasPermission('create documents'))
                        <p class="app-menu-label">PENGAJUAN</p><a
                           class="nav @if (request()->routeIs('outgoing-letters.*')) is-active @endif"
                           href="{{ route('outgoing-letters.index') }}"><span><i
                                   class="fa-solid fa-paper-plane"></i></span>Surat Keluar</a><a
                           class="nav @if (request()->routeIs('internal-documents.*') && request()->route('type') === 'ASSIGNMENT') is-active @endif"
                           href="{{ route('internal-documents.create', 'ASSIGNMENT') }}"><span><i
                                   class="fa-solid fa-briefcase"></i></span>Surat Tugas</a><a
                           class="nav"
                           href="{{ route('documents.index', ['status' => 'DRAFT']) }}"><span><i
                                   class="fa-regular fa-file-lines"></i></span>Draft</a>
                    @endif
                    @if (auth()->user()->hasPermission('approve documents'))
                        <p class="app-menu-label">PERSETUJUAN</p><a
                           class="nav @if (request()->routeIs('approvals.*')) is-active @endif"
                           href="{{ route('approvals.index') }}"><span><i
                                   class="fa-solid fa-check-double"></i></span>Menunggu Saya</a>
                    @endif
                    @if (auth()->user()->hasPermission('view documents'))
                        <p class="app-menu-label">DOKUMEN</p><a
                           class="nav @if (request()->routeIs('archives.*')) is-active @endif"
                           href="{{ route('archives.index') }}"><span><i
                                   class="fa-solid fa-box-archive"></i></span>Arsip
                            Digital</a>
                    @endif
                    @if (auth()->user()->hasPermission('manage workflows') ||
                            auth()->user()->hasPermission('manage master data'))
                        <p class="app-menu-label">ADMINISTRASI</p>
                        @if (auth()->user()->hasPermission('manage workflows'))
                            <a class="nav @if (request()->routeIs('workflows.*')) is-active @endif"
                               href="{{ route('workflows.index') }}"><span><i
                                       class="fa-solid fa-diagram-project"></i></span>Alur
                                Persetujuan</a>
                            @endif @if (auth()->user()->hasPermission('manage master data'))
                                <a class="nav @if (request()->routeIs('numbering-rules.*')) is-active @endif"
                                   href="{{ route('numbering-rules.index') }}"><span><i
                                           class="fa-solid fa-hashtag"></i></span>Master Nomor
                                    Surat</a>
                            @endif
                        @endif
                </nav>
                <div class="sidebar-bottom"><span class="status-dot"></span>Sistem aktif</div>
            </aside>
            <main class="app-main">
                <header class="app-topbar">
                    <div><b>Administrasi Digital</b><span>Kelola dokumen kampus secara
                            terarah</span></div>
                    <div class="top-user"><span
                              class="user-dot">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span><span>{{ auth()->user()->name }}</span>
                        <form method="post"
                              action="{{ route('logout') }}"
                              data-confirm="Keluar dari sistem?">@csrf<button
                                    type="submit">Keluar</button></form>
                    </div>
                </header>
                <div class="app-content">
                    @if (session('success'))
                        <div data-swal-success="{{ session('success') }}"></div>
                        @endif @if ($errors->any())
                            <div
                                 data-swal-error="Periksa kembali isian yang ditandai sebelum melanjutkan.">
                            </div>
                        @endif
                        <x-page-guide />@yield('content')
                </div>
            </main>
        </div>
    </body>

</html>
