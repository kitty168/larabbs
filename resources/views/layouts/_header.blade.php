<nav class="navbar navbar-expand-lg navbar-light bg-light navbar-static-top">
  <div class="container">
    {{--品牌Logo--}}
    <a class="navbar-brand" href="{{ url('/') }}">
        LaraBBS
    </a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      {{--左侧的导航--}}
      <ul class="navbar-nav mr-auto">
        <li class="nav-item active">

        </li>

      </ul>

      {{--右侧的导航--}}
      <url class="navbar-nav navbar-right">
        <li class="nav-item"><a href="{{ route('login') }}" class="nav-link">登录</a></li>
        <li class="nav-item"><a href="{{ route('register') }}" class="nav-link">注册</a></li>
      </url>

    </div>
  </div>

</nav>
