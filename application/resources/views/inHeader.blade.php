<nav class="navbar box py-2" role="navigation" aria-label="main navigation">
  <div class="navbar-brand">
    <a class="navbar-item" href="{{ URL::to('admin') }}">
      <img src="{{ asset('talent_recruiters.jpg') }}" width="112" height="28">
    </a>

    <a role="button" class="navbar-burger" aria-label="menu" aria-expanded="false" data-target="navbarBasicExample">
      <span aria-hidden="true"></span>
      <span aria-hidden="true"></span>
      <span aria-hidden="true"></span>
    </a>
  </div>
    <div class="navbar-end">
      <div class="navbar-item">
        <div class="buttons">
          <a class="button" href="{{ URL::to('admin') }}">
            <span class="icon"><i class="fas fa-chart-line"></i></span>
            <strong>Panel</strong>
          </a>
          <a class="button" href="{{ URL::to('admin/users') }}">
            <span class="icon"><i class="fas fa-users"></i></span>
            <strong>Usuarios</strong>
          </a>
          <a class="button" href="{{ URL::to('admin/selectorasList') }}">
            <span class="icon"><i class="fas fa-address-book"></i></span>
            <strong>Selectoras</strong>
          </a>
          <a class="button is-warning" href="{{ URL::to('logout') }}">
            <span class="icon"><i class="fas fa-sign-out-alt"></i></span>
            <strong>Salir</strong>
          </a>
        </div>
      </div>
    </div>
  </div>
</nav>