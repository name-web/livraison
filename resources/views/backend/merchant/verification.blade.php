@include('backend.partials.header')
<style>
    .otp-local-backdrop {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 1.5rem;
        background: linear-gradient(135deg, #0b3d20 0%, #15803d 55%, #16a34a 100%);
    }
    .otp-local-card {
        width: 100%;
        max-width: 460px;
        background: #ffffff;
        border-radius: 1.25rem;
        box-shadow: 0 25px 60px rgba(2, 44, 18, 0.35);
        padding: 2.25rem 2rem;
        animation: otpLocalIn .35s ease-out;
    }
    @keyframes otpLocalIn {
        from { opacity: 0; transform: translateY(14px) scale(.98); }
        to   { opacity: 1; transform: translateY(0) scale(1); }
    }
    .otp-local-icon {
        width: 62px;
        height: 62px;
        margin: 0 auto 1rem;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        background: #ecfdf5;
        color: #15803d;
    }
    .otp-local-icon svg { width: 30px; height: 30px; }
    .otp-local-title {
        font-size: 1.3rem;
        font-weight: 700;
        color: #0f172a;
        text-align: center;
        margin-bottom: .35rem;
    }
    .otp-local-sub {
        font-size: .88rem;
        color: #64748b;
        text-align: center;
        margin-bottom: 1.5rem;
        line-height: 1.5;
    }
    .otp-local-sub strong { color: #0f172a; }
    .otp-local-code {
        background: linear-gradient(135deg, #f0fdf4, #dcfce7);
        border: 1px dashed #86efac;
        border-radius: .85rem;
        padding: 1rem;
        text-align: center;
        margin-bottom: 1.5rem;
    }
    .otp-local-code .code {
        font-size: 2rem;
        font-weight: 800;
        letter-spacing: .45rem;
        color: #15803d;
        font-family: "Courier New", monospace;
    }
    .otp-local-code .note {
        display: block;
        margin-top: .4rem;
        font-size: .75rem;
        color: #16a34a;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .05rem;
    }
    .otp-local-field label {
        font-size: .8rem;
        font-weight: 600;
        color: #334155;
        margin-bottom: .4rem;
        display: block;
    }
    .otp-local-field input#otp {
        width: 100%;
        text-align: center;
        font-size: 1.4rem;
        font-weight: 700;
        letter-spacing: .6rem;
        color: #0f172a !important;
        padding: .65rem .5rem !important;
        border: 1.5px solid #e2e8f0;
        border-radius: .85rem;
        outline: none;
        transition: border-color .2s, box-shadow .2s;
    }
    .otp-local-field input#otp:focus {
        border-color: #15803d;
        box-shadow: 0 0 0 3px rgba(21, 128, 61, .15);
    }
    .otp-local-btn {
        width: 100%;
        border: none;
        border-radius: .85rem;
        padding: .75rem 1rem;
        font-size: .95rem;
        font-weight: 700;
        color: #fff;
        background: linear-gradient(135deg, #15803d, #16a34a);
        cursor: pointer;
        transition: transform .15s, box-shadow .15s;
        box-shadow: 0 8px 20px rgba(21, 128, 61, .3);
    }
    .otp-local-btn:hover { transform: translateY(-1px); box-shadow: 0 10px 24px rgba(21, 128, 61, .4); }
    .otp-local-btn:disabled { opacity: .6; cursor: not-allowed; transform: none; }
    .otp-local-resend {
        text-align: center;
        margin-top: 1.1rem;
        font-size: .85rem;
        color: #64748b;
    }
    .otp-local-resend button {
        background: none;
        border: none;
        color: #15803d;
        font-weight: 700;
        cursor: pointer;
        text-decoration: underline;
        padding: 0;
    }
    .otp-local-resend button:disabled {
        color: #94a3b8;
        cursor: not-allowed;
        text-decoration: none;
    }
    .otp-local-links {
        text-align: center;
        margin-top: 1.2rem;
        font-size: .85rem;
        color: #64748b;
    }
    .otp-local-links a { color: #15803d; font-weight: 600; }
    .otp-local-alert {
        border-radius: .75rem;
        padding: .65rem .9rem;
        font-size: .85rem;
        text-align: center;
        margin-bottom: 1.1rem;
    }
    .otp-local-alert.success { background: #ecfdf5; color: #15803d; border: 1px solid #bbf7d0; }
    .otp-local-alert.warning { background: #fffbeb; color: #b45309; border: 1px solid #fde68a; }
    .otp-local-alert.error   { background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; }
</style>

<div class="otp-local-backdrop">
    <div class="otp-local-card">
        <div class="otp-local-icon">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
            </svg>
        </div>
        <h1 class="otp-local-title">Vérification du téléphone</h1>
        <p class="otp-local-sub">
            Un code à 6 chiffres a été envoyé au numéro
            @php
                $maskedMobile = '';
                if (session('mobile')) {
                    $maskedDigits = preg_replace('/\D/', '', session('mobile'));
                    $maskedNational = substr($maskedDigits, 3);
                    $maskedMobile = '+225 ' . substr($maskedNational, 0, 2) . ' **** ** ' . substr($maskedNational, 8, 2);
                }
            @endphp
            <strong>{{ $maskedMobile }}</strong>.
            Il expire dans 10 minutes.
        </p>

        @if (\Session::has('success'))
            <div class="otp-local-alert success">{!! \Session::get('success') !!}</div>
        @elseif (\Session::has('warning'))
            <div class="otp-local-alert warning">{!! \Session::get('warning') !!}</div>
        @endif

        @if (app()->environment('local') && session('otp'))
            <div class="otp-local-code">
                <span class="code">{{ session('otp') }}</span>
                <span class="note">Code de démonstration — environnement local</span>
            </div>
        @endif

        <form method="POST" action="{{ route('merchant.otp-verification') }}">
            @csrf
            <input type="hidden" name="mobile" value="{{ session('mobile') }}">
            <div class="otp-local-field">
                <label for="otp">Code OTP</label>
                <input id="otp" type="number" class="@error('otp') is-invalid @enderror" name="otp"
                       value="{{ old('otp') }}" required autocomplete="otp" autofocus
                       minlength="6" maxlength="6" inputmode="numeric" placeholder="······">
                @error('otp')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>
            <button type="submit" class="otp-local-btn mt-3">Vérifier mon numéro</button>
        </form>

        <div class="otp-local-resend">
            Vous n'avez pas reçu le code ?
            <button type="button" id="otp-resend-btn">Renvoyer</button>
            <span id="otp-resend-timer" class="d-none"></span>
        </div>

        <form id="otp-resend-form" method="POST" action="{{ route('merchant.resend-otp') }}" class="d-none">
            @csrf
            <input type="hidden" name="mobile" value="{{ session('mobile') }}">
        </form>

        <div class="otp-local-links">
            Déjà membre ? <a href="{{ route('login') }}">Connectez-vous</a>
        </div>
    </div>
</div>

<script>
    (function () {
        var remaining = {{ (int) max(0, 60 - now()->diffInSeconds(\Carbon\Carbon::parse(session('otp_sent_at') ?? now()))) }};
        var btn = document.getElementById('otp-resend-btn');
        var timer = document.getElementById('otp-resend-timer');

        function tick() {
            if (remaining > 0) {
                btn.disabled = true;
                btn.textContent = 'Renvoyer dans ' + remaining + 's';
                remaining--;
                setTimeout(tick, 1000);
            } else {
                btn.disabled = false;
                btn.textContent = 'Renvoyer';
            }
        }
        tick();

        btn.addEventListener('click', function () {
            if (!btn.disabled) {
                document.getElementById('otp-resend-form').submit();
            }
        });
    })();
</script>

@include('backend.partials.footer')
