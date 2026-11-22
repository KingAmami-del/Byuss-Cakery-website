<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>Masuk Admin · Byuss Cakery</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
</head>
<body class="adm">
<div class="split">

    {{-- Panel kiri: identitas --}}
    <aside class="split__brand" aria-hidden="true">
        <span class="split__logo">Byuss Cakery</span>
        <div class="split__copy">
            <p class="split__kicker">Ruang kerja pengelola</p>
            <p class="split__quote">Ada manis di<br>setiap cerita.</p>
        </div>
        <span class="split__foot">&copy; {{ date('Y') }} Byuss Cakery</span>
    </aside>

    {{-- Panel kanan: formulir --}}
    <main class="split__form">
        <div class="box">
            <a href="{{ url('/') }}" class="box__logo">Byuss Cakery</a>

            <h1 class="box__title">Selamat datang kembali</h1>
            <p class="box__sub">Masuk dengan akun admin untuk mengelola toko.</p>

            <form method="POST" action="{{ route('admin.login.submit') }}" data-login-form novalidate>
                @csrf

                <div class="fld">
                    <label for="email">Email</label>
                    <div class="fld__wrap">
                        <svg class="fld__icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2.5"/><path d="m4 7 8 6 8-6"/></svg>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" placeholder="nama@contoh.com"
                               autocomplete="username" autofocus required
                               @error('email') aria-invalid="true" aria-describedby="email-error" @enderror>
                    </div>
                    @error('email')
                        <p class="fld__error" id="email-error" role="alert">{{ $message }}</p>
                    @enderror
                </div>

                <div class="fld">
                    <label for="password">Kata sandi</label>
                    <div class="fld__wrap">
                        <svg class="fld__icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="4.5" y="10.5" width="15" height="10" rx="2.5"/><path d="M8 10.5V8a4 4 0 0 1 8 0v2.5"/></svg>
                        <input id="password" name="password" type="password" placeholder="Kata sandi"
                               autocomplete="current-password" required
                               @error('password') aria-invalid="true" aria-describedby="password-error" @enderror>
                        <button type="button" class="fld__eye" data-toggle-pass aria-label="Tampilkan kata sandi" aria-pressed="false">
                            <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/></svg>
                        </button>
                    </div>
                    @error('password')
                        <p class="fld__error" id="password-error" role="alert">{{ $message }}</p>
                    @enderror
                </div>

                <label class="chk">
                    <input type="checkbox" name="remember" value="1" @checked(old('remember'))>
                    <span>Ingat saya di perangkat ini</span>
                </label>

                <button type="submit" class="go" data-submit>Masuk</button>
            </form>

            <a href="{{ url('/') }}" class="box__back">&larr; Kembali ke situs</a>
        </div>
    </main>
</div>

<script>
    (function () {
        var eye = document.querySelector('[data-toggle-pass]');
        var pass = document.getElementById('password');
        if (eye && pass) {
            eye.addEventListener('click', function () {
                var show = pass.type === 'password';
                pass.type = show ? 'text' : 'password';
                eye.setAttribute('aria-pressed', show ? 'true' : 'false');
                eye.setAttribute('aria-label', show ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi');
            });
        }
        var form = document.querySelector('[data-login-form]');
        var btn = document.querySelector('[data-submit]');
        if (form && btn) {
            form.addEventListener('submit', function () {
                btn.disabled = true;
                btn.textContent = 'Memproses…';
            });
        }
    })();
</script>
</body>
</html>
