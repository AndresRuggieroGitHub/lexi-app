<footer class="footer">
  <div class="footer-inner">
    <div class="footer-brand">
      <a href="{{ route('app') }}" class="footer-logo" aria-label="{{ __('lexi.nav.home') }}">Lexi</a>
      <p class="footer-tagline">{{ __('lexi.footer.tagline') }}</p>
    </div>
    <nav class="footer-nav" aria-label="{{ __('lexi.footer.platform') }}">
      <h3 class="footer-heading">{{ __('lexi.footer.platform') }}</h3>
      <ul>
        <li><a href="{{ route('app') }}">{{ __('lexi.nav.home') }}</a></li>
        <li><a href="{{ route('biblioteca') }}">{{ __('lexi.nav.library') }}</a></li>
        <li><a href="{{ route('ejercicios') }}">{{ __('lexi.nav.exercises') }}</a></li>
        <li><a href="{{ route('progreso') }}">{{ __('lexi.nav.progress') }}</a></li>
      </ul>
    </nav>
    <nav class="footer-nav" aria-label="{{ __('lexi.footer.account') }}">
      <h3 class="footer-heading">{{ __('lexi.footer.account') }}</h3>
      <ul>
        <li><a href="{{ route('profile.show') }}">{{ __('lexi.nav.profile') }}</a></li>
        <li><a href="{{ route('producto') }}">{{ __('lexi.nav.premium') }}</a></li>
        <li><a href="{{ route('carrito') }}">{{ __('lexi.nav.cart') }}</a></li>
      </ul>
    </nav>
    <nav class="footer-nav" aria-label="{{ __('lexi.footer.support') }}">
      <h3 class="footer-heading">{{ __('lexi.footer.support') }}</h3>
      <ul>
        <li><a href="{{ route('contacto') }}">{{ __('lexi.nav.contact') }}</a></li>
        <li><a href="{{ route('info') }}">{{ __('lexi.nav.info') }}</a></li>
        <li><a href="{{ route('terminos') }}">{{ __('lexi.nav.terms') }}</a></li>
        <li><a href="{{ route('privacidad') }}">{{ __('lexi.nav.privacy') }}</a></li>
      </ul>
    </nav>
    <div class="footer-stats" aria-label="{{ __('lexi.footer.prototype_summary') }}">
      <div class="footer-stat">
        <span class="footer-stat-num">30</span>
        <span class="footer-stat-label">{!! __('lexi.footer.available_languages') !!}</span>
      </div>
      <div class="footer-stat">
        <span class="footer-stat-num">2</span>
        <span class="footer-stat-label">{!! __('lexi.footer.functional_languages') !!}</span>
      </div>
    </div>
  </div>
  <div class="footer-bottom">
    <p>{{ __('lexi.footer.rights') }}</p>
  </div>
</footer>