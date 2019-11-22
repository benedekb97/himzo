<nav class="navbar navbar-inverse navbar-static-top">
    <div class="container">
        <div class="navbar-header">
            <button class="navbar-toggle collapsed" type="button" data-toggle="collapse" data-target="#navbar">
                <span class="sr-only">Toggle Navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="{{ route('index') }}">Hímző</a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
            <ul class="nav navbar-nav">
                @if(Auth::check() && Auth::user()->role_id>1)
                    <li class="@yield('members.active')"><a class="@yield('members.active')" href="{{ route('members.index') }}">Irányítópult</a></li>
                @endif
                @if(Auth::check() && Auth::user()->activated==1)
                    <li class="dropdown @yield('orders.new.active')  @yield('user.orders.active') @yield('orders.unapproved.active') @yield('orders.active.active')">
                        <a class="dropdown-toggle" href="#" data-toggle="dropdown">Rendelések <span class="caret"></span></a>
                        <ul class="dropdown-menu">
                            <li class="@yield('orders.new.active')"><a class="@yield('orders.new.active') " href="{{ route('orders.new') }}">Új rendelés</a></li>
                            <li><a class="@yield('user.orders.active')" href="{{ route('user.orders') }}">Rendeléseim</a></li>
                            @if(Auth::user()->role_id>1)
                                <li role="separator" class="divider"></li>
                                <li class="@yield('orders.fake.active')"><a class="@yield('orders.fake.active')" href="{{ route('orders.fake') }}">Rendelés felvétele</a></li>
                            @endif
                        </ul>
                    </li>
                @endif
                @if(Auth::check() && Auth::user()->role_id>3)
                    <li class="@yield('designs.active')"><a href="{{ route('designs.index') }}">Tervek</a></li>
                @endif
                <li class="@yield('galleries.active')"><a class="@yield('galleries.active')" href="{{ route('gallery.index') }}">Képek</a></li>
            </ul>
            <ul class="nav navbar-nav navbar-right">
                @if(Auth::check())
                    @if(Auth::user()->role_id>3)
                        <li class="dropdown">
                            <a class="dropdown-toggle" href="#" data-toggle="dropdown">Pénzügyek <span class="caret"></span></a>
                            <ul class="dropdown-menu">
                                <li><a href="{{ route('transactions.teddy_bears') }}">Kasszák</a></li>
                            </ul>
                        </li>
                    @endif
                    @if(Auth::user()->role_id>4)
                        <li class="dropdown">
                            <a class="dropdonw-toggle" href="#" data-toggle="dropdown">Beállítások <span class="caret"></span></a>
                            <ul class="dropdown-menu">
                                <li><a href="{{ route('settings.index') }}">Főoldal</a></li>
                                <li><a href="{{ route('settings.gallery') }}">Galériák</a></li>
                                <li><a href="{{ route('settings.backgrounds') }}">Kordurák</a></li>
                            </ul>
                        </li>
                    @endif
                    <li class="dropdown">
                        <a class="dropdown-toggle" href="#" data-toggle="dropdown">{{ Auth::user()->name }} <span class="caret"></span></a>
                        <ul class="dropdown-menu">
                            <li><a href="{{ route('logout') }}">Kijelentkezés</a></li>
                        </ul>
                    </li>
                @else
                    <li><a href="#" data-toggle="modal" data-target="#register_modal">Regisztráció</a></li>
                    <li><a href="#" data-toggle="modal" data-target="#login_modal">Bejelentkezés</a></li>
                @endif
            </ul>
        </div>
    </div>
</nav>
