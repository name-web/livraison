@include('backend.partials.header')
@section('content')

        <div class="auth-v2-wrap">
            <div class="auth-v2-card">
                <div class="auth-v2-form">
                    <div class="auth-v2-form-inner">
                        <div class="text-center mb-4">
                            <h3 class="auth-v2-title">Bienvenue !</h3>
                            <p class="auth-v2-subtitle mb-0">Connectez-vous à votre espace pour continuer.</p>
                        </div>
                        <form method="POST" action="{{ route('login') }}">
                            @csrf
                            <div class="form-group auth-v2-field">
                                <label for="email" class="auth-v2-label">E-mail ou téléphone</label>
                                <div class="auth-v2-input-wrap">
                                    <i class="fas fa-user"></i>
                                    <input id="email" type="text" class="form-control form-control-lg @error('email') is-invalid @enderror" name="email" required autocomplete="email" autofocus  placeholder="E-mail ou téléphone"
                                    @if(Cookie::has('useremail')) ? value="{{Cookie::get('useremail')}}" : value="{{ old('email') }}" @endif>
                                </div>
                                @error('email')
                                    <span class="invalid-feedback d-block" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group auth-v2-field">
                                <label for="password" class="auth-v2-label">Mot de passe</label>
                                <div class="auth-v2-input-wrap">
                                    <i class="fas fa-lock"></i>
                                    <input id="password" type="password" class="form-control form-control-lg @error('password') is-invalid @enderror" name="password" required autocomplete="current-password" placeholder="Votre mot de passe"
                                    @if(Cookie::has('userpassword')) value="{{Cookie::get('userpassword')}}" @endif>
                                </div>
                                @error('password')
                                    <span class="invalid-feedback d-block" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group d-flex justify-content-between align-items-center my-3">
                                <label class="custom-control custom-checkbox mb-0">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}
                                    @if(Cookie::has('useremail')) checked @endif>
                                    <span class="form-check-label auth-v2-remember">Se souvenir de moi</span>
                                </label>
                            </div>
                            <button type="submit" class="auth-v2-btn w-100">Se connecter <i class="fas fa-arrow-right ms-2"></i></button>
                            <div class="text-center auth-v2-or">
                                <span>ou continuer avec</span>
                            </div>
                            @if(globalSettings('facebook_status') || globalSettings('google_status'))
                                <div class="row ">
                                    @if(globalSettings('facebook_status') == App\Enums\Status::ACTIVE)
                                    <div class="col-sm-6 m-auto  ">
                                        <a href="{{ route('social.login','facebook') }}" class="btn w-100 btn-social auth-v2-social mt-2" type="button"> <i class="fab fa-facebook"></i> Facebook</a>
                                    </div>
                                    @endif
                                    @if(globalSettings('google_status') == App\Enums\Status::ACTIVE)
                                    <div class="col-sm-6 m-auto  ">
                                        <a href="{{ route('social.login','google') }}" class="btn  w-100 btn-social auth-v2-social mt-2" type="button"><i class="fab fa-google"></i> Google</a>
                                    </div>
                                    @endif
                                </div>
                            @endif 
                            @if(env('DEMO') && env('DEMO') !=="")
                                <div class="text-center p-2">
                                    <span><b>Connexion démo</b></span>
                                </div>
                                <div class="row  ">
                                    <div class="col-sm-6">
                                        <button type="button" class="auth-v2-demo demo-login-btn w-100 mt-2" id="demo-admin" data-email="admin@wemaxdevs.com" data-password="12345678">Admin</button>
                                    </div>
                                    <div class="col-sm-6">
                                        <button type="button" class="auth-v2-demo demo-login-btn w-100 mt-2" id="demo-branch" data-email="branch@wemaxdevs.com" data-password="12345678">Agence</button>
                                    </div>
                                    <div class="col-sm-6">
                                        <button type="button" class="auth-v2-demo demo-login-btn w-100 mt-2" id="demo-merchant" data-email="merchant@wemaxdevs.com" data-password="12345678">Marchand</button>
                                    </div>
                                </div>
                            @endif

                        </form>
                        <div class="auth-v2-footer-links">
                            <a href="{{ route('merchant.sign-up') }}" class="footer-link">Créer un compte marchand</a>
                            <a href="{{ route('password.request') }}" class="footer-link">Mot de passe oublié ?</a>
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
        </div>
@show
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
    padding: 3rem 2.6rem;
    display: flex;
    align-items: center;
}
.auth-v2-form-inner{
    width: 100%;
    max-width: 400px;
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
    margin-bottom: 1.15rem;
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
.auth-v2-input-wrap > i{
    position: absolute;
    left: 1rem;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
    font-size: .95rem;
    z-index: 3;
}
.auth-v2-input-wrap .form-control{
    padding: 0.25rem 1rem 0.25rem 2.7rem !important;
    border-radius: .85rem !important;
    border: 1.5px solid #e2e8f0;
    background: #f8fafc;
    font-size: .95rem !important;
    min-height: 48px;
    color: #0f172a !important;
}
.auth-v2-input-wrap .form-control::placeholder{
    color: #94a3b8;
}
.auth-v2-input-wrap .form-control:focus{
    border-color: var(--primary-color);
    box-shadow: 0 0 0 4px color-mix(in srgb, var(--primary-color) 15%, transparent);
    background: #fff;
}
.auth-v2-remember{
    color: #475569;
    font-size: .9rem;
    padding-left: .3rem;
}
.auth-v2-btn{
    background: var(--primary-color);
    color: #fff;
    border: none;
    border-radius: .85rem;
    padding: .85rem 1rem;
    font-size: 1rem;
    font-weight: 700;
    transition: transform .25s ease, box-shadow .25s ease;
}
.auth-v2-btn:hover{
    color: #fff;
    transform: translateY(-2px);
    box-shadow: 0 12px 26px color-mix(in srgb, var(--primary-color) 35%, transparent);
}
.auth-v2-or{
    margin: 1.3rem 0 .6rem;
    position: relative;
}
.auth-v2-or span{
    background: #fff;
    padding: 0 .8rem;
    color: #94a3b8;
    font-size: .85rem;
    position: relative;
    z-index: 2;
}
.auth-v2-or::before{
    content: "";
    position: absolute;
    left: 0;
    right: 0;
    top: 50%;
    height: 1px;
    background: #e2e8f0;
    z-index: 1;
}
.auth-v2-social{
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: .55rem;
    background: #fff !important;
    color: #334155 !important;
    border: 1.5px solid #e2e8f0 !important;
    border-radius: .85rem !important;
    font-weight: 600;
    font-size: .92rem;
    transition: border-color .25s ease, box-shadow .25s ease;
}
.auth-v2-social:hover{
    border-color: var(--primary-color) !important;
    box-shadow: 0 8px 20px color-mix(in srgb, var(--primary-color) 14%, transparent);
}
.auth-v2-demo{
    background: #f1f5f9;
    border: 1.5px solid #e2e8f0;
    color: #334155;
    border-radius: .85rem;
    padding: .65rem 1rem;
    font-weight: 600;
    font-size: .9rem;
    transition: all .25s ease;
}
.auth-v2-demo:hover{
    background: var(--primary-color);
    border-color: var(--primary-color);
    color: #fff;
}
.auth-v2-footer-links{
    display: flex;
    justify-content: space-between;
    gap: 1rem;
    margin-top: 1.8rem;
    border-top: 1px dashed #e2e8f0;
    padding-top: 1.3rem;
}
.auth-v2-footer-links .footer-link{
    color: var(--primary-color);
    font-weight: 600;
    font-size: .9rem;
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
        max-width: 460px;
    }
}
</style>
@include('backend.partials.footer')