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
        {{--登录验证显示--}}
        @guest
          <li class="nav-item"><a href="{{ route('login') }}" class="nav-link">登录</a></li>
          <li class="nav-item"><a href="{{ route('register') }}" class="nav-link">注册</a></li>
        @else
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <img src="https://cdn.learnku.com/uploads/images/201709/20/1/PtDKbASVcz.png?imageView2/1/w/60/h/60" class="img-responsive img-circle" width="30px" height="30px">
              {{ Auth::user()->name }}
            </a>
            <div class="dropdown-menu" aria-labelledby="navbarDropdown">
              <a class="dropdown-item" href="#">个人中心</a>
              <a class="dropdown-item" href="#">编辑资料</a>
              <div class="dropdown-divider"></div>
              <a class="dropdown-item" href="#">
                <form action="{{ route('logout') }}" method="POST">
                  {{ csrf_field() }}
                  <button class="btn btn-block btn-danger" type="submit" name="button">退出</button>
                </form>
              </a>
            </div>
          </li>
        @endguest

      </url>

    </div>
  </div>

</nav>
