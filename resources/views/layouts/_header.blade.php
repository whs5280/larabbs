<nav class="navbar navbar-expand-lg navbar-light bg-light navbar-static-top">
    <div class="container">
        <!-- Branding Image -->
        <a class="navbar-brand " href="{{ url('/') }}">
            LaraBBS
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <!-- Left Side Of Navbar -->
                <ul class="navbar-nav mr-auto">

                </ul>

                <!-- Right Side Of Navbar -->
                @if(\Illuminate\Support\Facades\Auth::check() === false)
                    <ul class="navbar-nav navbar-right">
                        <!-- Authentication Links -->
                        <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">登录</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('register') }}">注册</a></li>
                    </ul>
                @else
                    <ul class="navbar-nav navbar-right">
                        <!-- Authentication Links -->
                        <li class="nav-item"><a class="nav-link" href="{{ route('users.show', \Illuminate\Support\Facades\Auth::id()) }}">个人中心</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('users.edit', \Illuminate\Support\Facades\Auth::id()) }}">编辑资料</a></li>
                        <li class="nav-item"><a class="dropdown-item" id="logout" href="#">
                                <form action="{{ route('logout') }}" method="POST">
                                    {{ csrf_field() }}
                                    <button class="btn btn-block btn-danger" type="submit" name="button">退出</button>
                                </form>
                            </a>
                        </li>
                    </ul>
                   {{-- <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <a class="dropdown-item" href="{{ route('users.show', \Illuminate\Support\Facades\Auth::id()) }}">个人中心</a>
                        <a class="dropdown-item" href="{{ route('users.edit', \Illuminate\Support\Facades\Auth::id()) }}">编辑资料</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" id="logout" href="#">
                            <form action="{{ route('logout') }}" method="POST">
                                {{ csrf_field() }}
                                <button class="btn btn-block btn-danger" type="submit" name="button">退出</button>
                            </form>
                        </a>
                    </div>--}}
                @endif
            </div>
    </div>
</nav>