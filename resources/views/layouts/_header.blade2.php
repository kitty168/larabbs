<nav class="navbar navbar-default navbar-static-top">
    <div class="container">
        <div class="navbar-header">
            {{-- Collapsed Hamburger --}}
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse">
                <span class="sr-onle">Toggle Navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>

            {{-- Branding Image --}}
            <a href="{{ url('/') }}" class="navbar-brand">
                LaraBBS
            </a>
        </div>

        <div class="collapse navbar-collapse" id="app-navbar-collapse">
            {{-- Left Side Of Navbar --}}
            <url class="nav navbar-nav">

            </url>

            {{-- Right Side Of Navbar --}}
            <ul class="nav navbar-nav navbar-right">
                {{-- Authentication Links --}}
                @guest
                    {{-- 未登录用户显示的内容 --}}
                    <li><a href="{{ route('login') }}">登录</a></li>
                    <li><a href="{{ route('register') }}">注册</a></li>
                @else
                    {{-- 已登录用户显示的内容 --}}
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                            <span class="user-avatar pull-left" style="margin-right:8px; margin-top:-5px;">
                                <img src="https://timgsa.baidu.com/timg?image&quality=80&size=b9999_10000&sec=1546540963783&di=f407332ec334bda173686117ec6e3e02&imgtype=0&src=http%3A%2F%2Fwww.ld12.com%2Fupimg358%2Fallimg%2F20160629%2F050530501243696.jpg" class="img-responsive img-circle" width="30px" height="30px">
                            </span>
                            {{ Auth::user()->name }} <span class="caret"></span>
                        </a>

                        <ul class="dorpdown-menu" role="menu">
                            <li>
                                <a href="{{ route('logout') }}"
                                    onclick="event.preventDefault();
                                             document.getElementById('logout-form').submit();">
                                    退出登录
                                </a>

                                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                    {{ csrf_field() }}
                                </form>
                            </li>
                        </ul>
                    </li>
                @endguest
            </ul>
        </div>
    </div>
</nav>