<div class="sidebar" data-color="orange" data-background-color="white" data-image="{{ asset('material') }}/img/sidebar-1.jpg">
  <div class="logo">
    <a class="simple-text logo-normal">
      {{ __('Transformando Valores') }}
    </a>
  </div>
  <div class="sidebar-wrapper">
    <ul class="nav">
      <li class="nav-item{{ $activePage == 'dashboard' ? ' active' : '' }}">
        <a class="nav-link" href="{{ route('home') }}">
          <i class="material-icons">dashboard</i>
            <p>{{ __('Cotação') }}</p>
        </a>
      </li>
      <li class="nav-item{{ $activePage == 'historicoCotacao' ? ' active' : '' }}">
        <a class="nav-link" href="{{ route('historicoCotacao') }}">
          <i class="material-icons">content_paste</i>
            <p>{{ __('Histórico') }}</p>
        </a>
      </li>
    </ul>
  </div>
</div>
