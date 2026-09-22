<!doctype html>
<html lang="id">

    <head>
        <meta charset="utf-8">
        <meta name="viewport"
              content="width=device-width,initial-scale=1">
        <title>Masuk · E-Office Universitas</title>@vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>

    <body class="login-page">
        <main class="login-shell">
            <section class="login-brand">
                <div class="brand-top">Selamat datang di</div>
                <div class="brand-content">
                    <div class="brand-mark">E</div>
                    <h1>E-Office</h1>
                    <p>Universitas</p><span></span>
                    <p class="brand-copy">Kelola persuratan, disposisi, dan proses administrasi
                        kampus dengan lebih cepat,
                        aman, dan terintegrasi.</p>
                </div>
                <div class="brand-footer">ADMINISTRASI DIGITAL · UNIVERSITAS</div>
            </section>
            <section class="login-form-panel">
                <div class="login-mobile-brand">
                    <div class="mobile-mark">E</div><span>E-Office Universitas</span>
                </div>
                <div class="login-form-wrap">
                    <p class="eyebrow">PORTAL PEGAWAI</p>
                    <h2>Masuk ke akun Anda</h2>
                    <p class="login-subtitle">Gunakan akun universitas untuk melanjutkan.</p>
                    <form method="post"
                          action="{{ route('login') }}"
                          class="login-form">@csrf<label for="email">Email</label><input
                               id="email"
                               name="email"
                               type="email"
                               value="{{ old('email') }}"
                               placeholder="nama@universitas.ac.id"
                               required
                               autofocus
                               autocomplete="email">
                        @error('email')
                            <p class="login-error">{{ $message }}</p>
                        @enderror
                        <div class="login-password-label">
                            <label for="password">Kata sandi</label>
                        </div><input id="password"
                               name="password"
                               type="password"
                               placeholder="Masukkan kata sandi"
                               required
                               autocomplete="current-password">
                        <div class="captcha-row"><label for="captcha">Verifikasi:
                                <b>{{ session('login_captcha_question') }}</b></label><button
                                    class="captcha-refresh"
                                    type="submit"
                                    formaction="{{ route('login.captcha') }}"
                                    formmethod="post"
                                    title="Ganti soal CAPTCHA">↻</button></div><input id="captcha"
                               name="captcha"
                               type="number"
                               inputmode="numeric"
                               placeholder="Jawaban CAPTCHA"
                               required
                               autocomplete="off">
                        @error('captcha')
                            <p class="login-error">{{ $message }}</p>
                        @enderror
                        <label class="remember"><input type="checkbox"
                                   name="remember"> <span>Ingat saya di perangkat
                                ini</span></label><button class="login-submit"
                                type="submit">Masuk <span>→</span></button>
                    </form>
                    <p class="login-help">Butuh bantuan? Hubungi administrator E-Office.</p>
                </div>
            </section>
        </main>
    </body>

</html>
