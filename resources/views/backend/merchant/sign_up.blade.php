@include('backend.partials.header')
<header>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
</header>
<!-- signup form  -->
<form class="auth-v2-wrap" method="POST" action="{{ route('merchant.sign-up-store') }}">
    @csrf
    <div class="auth-v2-card">
        <div class="auth-v2-form">
            <div class="auth-v2-form-inner">
                <div class="text-center mb-4">
                    <h3 class="auth-v2-title">Inscription marchand</h3>
                    <p class="auth-v2-subtitle mb-0">Créez votre compte pour envoyer et suivre vos colis partout en Côte d'Ivoire.</p>
                </div>
                <div class="form-group auth-v2-field">
                    <label for="business_name" class="auth-v2-label">Nom de l'entreprise</label>
                    <div class="auth-v2-input-wrap">
                        <i class="fas fa-store"></i>
                        <input id="business_name" type="text" class="form-control form-control-lg @error('business_name') is-invalid @enderror" name="business_name" value="{{ old('business_name') }}"  autocomplete="business_name" autofocus placeholder="Nom de votre entreprise *">
                    </div>
                    @error('business_name')
                        <span class="invalid-feedback d-block" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="form-group auth-v2-field">
                    <label for="full_name" class="auth-v2-label">Nom complet du responsable</label>
                    <div class="auth-v2-input-wrap">
                        <i class="fas fa-user"></i>
                        <input id="full_name" type="text" class="form-control form-control-lg @error('full_name') is-invalid @enderror" name="full_name" value="{{ old('first_name') }}"  autocomplete="name" autofocus placeholder="Votre nom complet *">
                    </div>
                    @error('full_name')
                        <span class="invalid-feedback d-block" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="form-group auth-v2-field">
                    <label for="hub_id" class="auth-v2-label">Agence de rattachement</label>
                    <div class="auth-v2-input-wrap">
                        <i class="fas fa-location-dot"></i>
                        <select class="form-control select2 auth-v2-select" name="hub_id" id="hub_id" >
                            <option selected disabled>Choisissez votre agence</option>
                            @foreach ($hubs as $hub)
                                <option value="{{ $hub->id }}">{{ $hub->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @error('hub_id')
                    <small class="text-danger mt-2 d-block">{{ $message }}</small>
                    @enderror
                </div>

                <div class="form-group auth-v2-field">
                    <label for="mobile" class="auth-v2-label">Téléphone</label>
                    <div class="auth-v2-input-wrap auth-v2-phone-wrap">
                        <span class="auth-v2-phone-prefix">+225</span>
                        <input id="mobile" type="tel" class="form-control form-control-lg auth-v2-phone-input @error('mobile') is-invalid @enderror" name="mobile" value="{{ old('mobile',$request
                            ->phone ? $request->phone : "") }}"  autocomplete="tel" inputmode="numeric" maxlength="14" placeholder="07 01 02 03 04"  >
                    </div>
                    @error('mobile')
                        <span class="invalid-feedback d-block" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="form-group auth-v2-field">
                    <label for="password" class="auth-v2-label">Mot de passe</label>
                    <div class="auth-v2-input-wrap">
                        <i class="fas fa-lock"></i>
                        <input id="password" type="password" class="form-control form-control-lg @error('password') is-invalid @enderror" name="password"  autocomplete="new-password" placeholder="Choisissez un mot de passe *">
                    </div>
                    @error('password')
                        <span class="invalid-feedback d-block" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="form-group auth-v2-field">
                    <label for="address" class="auth-v2-label">Adresse</label>
                    <div class="auth-v2-input-wrap">
                        <i class="fas fa-map-location-dot"></i>
                        <textarea name="address" id="address" class="form-control auth-v2-textarea @error('address') is-invalid @enderror" placeholder="Votre adresse complète *" rows="3" autocomplete="street-address">{{ old('address')  }}</textarea>
                    </div>
                    @error('address')
                        <span class="invalid-feedback d-block" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-check auth-v2-terms">
                        <input id="merchant_registration_checkbox" name="policy" class="form-check-input" type="checkbox"><span class="auth-v2-terms-text">J'accepte la <a href="#" class="text-primary">{{ settings()->name }}</a> Politique de confidentialité et les conditions.</span>
                    </label>
                </div>
                <div class="form-group pt-2">
                    <button id="merchant_registration_submit" class="auth-v2-btn w-100" type="submit">Créer mon compte marchand <i class="fas fa-arrow-right ms-2"></i></button>
                </div>

                <div class="auth-v2-footer-links">
                    <span class="auth-v2-subtitle mb-0">Déjà membre ?</span>
                    <a href="{{ route('login') }}" class="footer-link">Se connecter</a>
                </div>
            </div>
        </div>
        <div class="auth-v2-side">
            <div class="auth-v2-side-inner">
                <a href="{{url('/')}}" class="navbar-brand d-inline-block">
                    <img src="{{ static_asset('frontend/images/logo-green.png') }}" class="auth-v2-logo" alt="logo">
                </a>
                <h4 class="auth-v2-tagline">Votre livraison express en Côte d'Ivoire</h4>
                <ul class="auth-v2-features list-unstyled">
                    <li><i class="fas fa-location-dot"></i><span>Suivi de colis en temps réel</span></li>
                    <li><i class="fas fa-motorcycle"></i><span>Livraison rapide à Abidjan et partout ailleurs</span></li>
                    <li><i class="fas fa-mobile-screen"></i><span>Application mobile marchand & livreur</span></li>
                </ul>
                <p class="auth-v2-side-foot mb-0">Plus de 7 500 agences à travers le pays.</p>
            </div>
        </div>
    </div>
</form>
<!-- end signup form  -->
<script src="{{static_asset('backend')}}/plugins/jquery/jquery-3.3.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        // $( "#hub_id" ).select2();
        (function () {
            var input = document.getElementById('mobile');
            if (!input) return;

            function formatMobile(value) {
                var d = (value || '').replace(/\D/g, '');
                if (d.length > 10) d = d.slice(0, 10);
                var out = '';
                if (d.length > 0) out = d.slice(0, 2);
                if (d.length > 2) out += ' ' + d.slice(2, 4);
                if (d.length > 4) out += ' ' + d.slice(4, 6);
                if (d.length > 6) out += ' ' + d.slice(6, 8);
                if (d.length > 8) out += ' ' + d.slice(8, 10);
                return out;
            }

            input.value = formatMobile(input.value);

            input.addEventListener('input', function () {
                input.value = formatMobile(input.value);
            });

            var form = input.closest('form');
            if (form) {
                form.addEventListener('submit', function () {
                    input.value = (input.value || '').replace(/\D/g, '');
                });
            }
        })();
    </script>
<style  >
.login-dashboard-main-wrapper{
    padding-top: 0!important;
}
.auth-v2-wrap{
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2rem 1rem;
    background: linear-gradient(135deg, #f1f7f3 0%, #ffffff 55%, #e7f3eb 100%);
}
.auth-v2-card{
    width: 100%;
    max-width: 1020px;
    display: flex;
    border-radius: 1.5rem;
    overflow: hidden;
    background: #fff;
    box-shadow: 0 30px 70px rgba(21, 128, 61, 0.16);
}
.auth-v2-side{
    width: 42%;
    background:
        linear-gradient(180deg, rgba(255,255,255,0.93) 0%, rgba(245,251,247,0.90) 100%),
        url("{{ static_asset('frontend/images/hero-abidjan.jpg') }}") center/cover no-repeat;
    color: #334155;
    padding: 3rem 2.2rem;
    display: flex;
    align-items: center;
    position: relative;
    overflow: hidden;
}
.auth-v2-side::before{
    content: "";
    position: absolute;
    top: -90px;
    right: -80px;
    width: 260px;
    height: 260px;
    border-radius: 50%;
    background: radial-gradient(circle, color-mix(in srgb, var(--primary-color) 22%, transparent) 0%, transparent 70%);
    pointer-events: none;
}
.auth-v2-side::after{
    content: "";
    position: absolute;
    bottom: -70px;
    left: -60px;
    width: 200px;
    height: 200px;
    border-radius: 50%;
    background: radial-gradient(circle, color-mix(in srgb, var(--primary-color) 15%, transparent) 0%, transparent 70%);
    pointer-events: none;
}
.auth-v2-side-inner{
    position: relative;
    z-index: 2;
}
.auth-v2-logo{
    max-height: 48px;
    filter: drop-shadow(0 6px 16px rgba(21, 128, 61, 0.18));
}
.auth-v2-tagline{
    color: #0f172a;
    font-size: 1.25rem;
    font-weight: 700;
    margin: 1.8rem 0 1.4rem;
    line-height: 1.4;
}
.auth-v2-features li{
    display: flex;
    align-items: center;
    gap: .85rem;
    margin-bottom: 1.1rem;
    color: #334155;
    font-size: .95rem;
}
.auth-v2-features li i{
    width: 38px;
    height: 38px;
    flex-shrink: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: .75rem;
    background: color-mix(in srgb, var(--primary-color) 11%, transparent);
    border: 1px solid color-mix(in srgb, var(--primary-color) 20%, transparent);
    color: var(--primary-color);
    font-size: .95rem;
}
.auth-v2-side-foot{
    margin-top: 2.4rem;
    font-size: .85rem;
    color: #64748b;
    border-top: 1px solid #e2e8f0;
    padding-top: 1.2rem;
}
.auth-v2-form{
    width: 58%;
    padding: 2.6rem;
    display: flex;
    align-items: flex-start;
    max-height: 100vh;
    overflow-y: auto;
}
.auth-v2-form-inner{
    width: 100%;
    max-width: 420px;
    margin: 0 auto;
}
.auth-v2-title{
    font-weight: 800;
    color: #0f172a;
    margin-bottom: .3rem;
}
.auth-v2-subtitle{
    color: #64748b;
    font-size: .95rem;
}
.auth-v2-field{
    margin-bottom: 1rem;
}
.auth-v2-label{
    font-weight: 600;
    font-size: .88rem;
    color: #334155;
    margin-bottom: .4rem;
}
.auth-v2-input-wrap{
    position: relative;
}
.auth-v2-phone-wrap{
    display: flex;
    align-items: stretch;
}
.auth-v2-phone-prefix{
    flex: 0 0 auto;
    display: flex;
    align-items: center;
    justify-content: center;
    min-width: 3.7rem;
    padding: 0 .55rem;
    font-size: .9rem;
    font-weight: 700;
    color: #334155;
    background: #eef2f7;
    border: 1.5px solid #e2e8f0;
    border-right: 0;
    border-radius: .85rem 0 0 .85rem;
    z-index: 2;
    pointer-events: none;
    letter-spacing: .02rem;
}
.auth-v2-phone-wrap:focus-within .auth-v2-phone-prefix{
    border-color: var(--primary-color);
    border-right: 0;
    background: #f0fdf4;
}
.auth-v2-input-wrap > i{
    position: absolute;
    left: 1rem;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
    font-size: .95rem;
    z-index: 3;
    pointer-events: none;
}
.auth-v2-input-wrap .form-control{
    padding: 0.25rem 1rem 0.25rem 2.7rem !important;
    border-radius: .85rem !important;
    border: 1.5px solid #e2e8f0;
    background: #f8fafc;
    font-size: .95rem !important;
    min-height: 46px;
    color: #0f172a !important;
}
.auth-v2-input-wrap .auth-v2-phone-input{
    flex: 1 1 auto;
    min-width: 0;
    border-radius: 0 .85rem .85rem 0 !important;
    border-left: 0;
    padding: 0.25rem 1rem !important;
    letter-spacing: .05rem;
}
.auth-v2-input-wrap .form-control::placeholder{
    color: #94a3b8;
}
.auth-v2-input-wrap .form-control:focus{
    border-color: var(--primary-color);
    box-shadow: 0 0 0 4px color-mix(in srgb, var(--primary-color) 15%, transparent);
    background: #fff;
}
.auth-v2-input-wrap .select2-container .select2-selection{
    border: 1.5px solid #e2e8f0;
    border-radius: .85rem !important;
    background: #f8fafc;
    min-height: 46px;
}
.auth-v2-input-wrap .select2-container .select2-selection__rendered{
    padding-left: 2.7rem !important;
    color: #0f172a !important;
    line-height: 44px;
    font-size: .95rem;
}
.auth-v2-input-wrap .select2-container .select2-selection__placeholder{
    color: #94a3b8 !important;
}
.auth-v2-input-wrap .select2-container .select2-selection__arrow{
    height: 44px;
}
.auth-v2-input-wrap .auth-v2-textarea{
    padding-top: .8rem;
    padding-left: 2.7rem;
    border-radius: .85rem !important;
    color: #0f172a !important;
}
.auth-v2-input-wrap .auth-v2-textarea + i{
    top: 1.1rem;
    transform: none;
}
.auth-v2-terms{
    font-size: .85rem;
    color: #64748b;
    align-items: flex-start;
    gap: .5rem;
}
.auth-v2-terms .form-check-input{
    margin-top: .3rem;
}
.auth-v2-terms-text a{
    color: var(--primary-color);
}
.auth-v2-btn{
    background: var(--primary-color);
    color: #fff;
    border: none;
    border-radius: .85rem;
    padding: .85rem 1rem;
    font-size: 1rem;
    font-weight: 700;
    transition: transform .25s ease, box-shadow .25s ease, opacity .25s ease;
}
.auth-v2-btn:hover{
    color: #fff;
    transform: translateY(-2px);
    box-shadow: 0 12px 26px color-mix(in srgb, var(--primary-color) 35%, transparent);
}
.auth-v2-btn:disabled{
    opacity: .55;
    transform: none;
    box-shadow: none;
}
.auth-v2-footer-links{
    display: flex;
    justify-content: center;
    align-items: center;
    gap: .5rem;
    border-top: 1px dashed #e2e8f0;
    padding-top: 1.2rem;
}
.auth-v2-footer-links .footer-link{
    color: var(--primary-color);
    font-weight: 600;
    font-size: .95rem;
    text-decoration: none;
}
.auth-v2-footer-links .footer-link:hover{
    text-decoration: underline;
}
@media (max-width: 767.98px){
    .auth-v2-side{
        display: none;
    }
    .auth-v2-form{
        width: 100%;
        padding: 2.4rem 1.6rem;
    }
    .auth-v2-card{
        max-width: 480px;
    }
}
</style>
@include('backend.partials.footer')