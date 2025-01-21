<nav class="navbar">
    <div class="container-fluid">
        <div @if($is_rtl) dir="rtl"  @endif class="rtl-supported-navbar">
            <div class="navbar-header">
                <a href="javascript:void(0);" class="bars"></a>
                <a class="navbar-brand" href="{{ route('dashboard') }}"><?php echo Config::get('settings.company_name'); ?></a>
            </div>
            <div>
                <a href="javascript:void(0);" class="dropdown-toggle text-white" data-toggle="dropdown" role="button"
                    aria-haspopup="true" aria-expanded="true">
                    <i class="material-icons">more_vert</i>
                </a>
                <ul class="dropdown-menu @if(!$is_rtl) pull-right @endif">
                    <li><a href="{{ route('profile') }}"><i class="material-icons">person</i>Profile</a></li>
                    <li role="separator" class="divider"></li>
                    <li><a href="javascript:void(0); document.getElementById('logout-form').submit();"><i
                                class=" fas fa-sign-out-alt"></i> Sign Out</a></li>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="dis-none">
                        @csrf
                    </form>
                </ul>
            </div>
        </div>

        {{-- <div class="collapse navbar-collapse" id="navbar-collapse">
            <ul class="nav navbar-nav navbar-right">
                <!-- #END# Tasks -->
                <div class=" height-28 btn-group user-helper-dropdown m-t-18 text-white bor-1 pointer-cursor p-both-0">

                    <i class="material-icons" data-toggle="dropdown" aria-haspopup="true"
                        aria-expanded="true">keyboard_arrow_down</i>
                    <ul class="dropdown-menu">
                        <li><a href="{{ route('profile') }}"><i class="material-icons">person</i>Profile</a></li>
                        <li role="separator" class="divider"></li>
                        <li><a href="javascript:void(0); document.getElementById('logout-form').submit();"><i
                                    class=" fas fa-sign-out-alt"></i> Sign Out</a></li>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="dis-none">
                            @csrf
                        </form>
                    </ul>
                </div>
            </ul>
        </div> --}}

    </div>
</nav>
