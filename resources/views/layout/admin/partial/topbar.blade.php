<!--**********************************
            Nav header start
        ***********************************-->
<?php 
$configuration = CheckHelp::configuration();
$myProfile = CheckHelp::getProfile();

?>
<div class="nav-header">
    <a href="index.html" class="brand-logo">
        <table>
            <tr>
                <td>
                    <img src="{{ asset('image/konfigurasi/'.$configuration->picture_configuration) }}" alt=""
                        width="80%;" style="border-radius: 8px;">
                </td>
                <td>
                    <strong style="display: block; color: rgb(29, 28, 28);">{{ $configuration->name_configuration
                        }}</strong>
                </td>
            </tr>
        </table>


    </a>

    <div class="nav-control">
        <div class="hamburger">
            <span class="line"></span><span class="line"></span><span class="line"></span>
        </div>
    </div>
</div>
<!--**********************************
            Nav header end
        ***********************************-->

<!--**********************************



            Header start
        ***********************************-->
<div class="header">
    <div class="header-content">
        <nav class="navbar navbar-expand">
            <div class="collapse navbar-collapse justify-content-between">
                <div class="header-left">
                    <div class="dashboard_bar">
                        App Stock V.1
                    </div>
                </div>
                <ul class="navbar-nav header-right">
                    <li class="nav-item dropdown header-profile">
                        <?php
                        $gambarProfile = 'default.png';
                        if($myProfile->picture_profile != null){
                            $gambarProfile = $myProfile->picture_profile;
                        }    
                        ?>
                        <a class="nav-link" href="javascript:void(0)" role="button" data-toggle="dropdown">
                            <img src="{{ asset('image/users/'.$gambarProfile) }}" width="20" alt="" />
                            <div class="header-info">
                                <span class="text-black"><strong>{{ $myProfile->name_profile }}</strong></span>
                                <p class="fs-12 mb-0">{{ $myProfile->role }}</p>
                            </div>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right">
                            <a href="{{ route('admin.profile.index') }}" class="dropdown-item ai-icon">
                                <i class="flaticon-381-user"></i>
                                <span class="ml-2">My Profile </span>
                            </a>
                            <form action="{{ route('logout') }}" method="post">
                                @csrf
                                <button type="submit" class="dropdown-item ai-icon">
                                    <svg id="icon-logout" xmlns="http://www.w3.org/2000/svg" class="text-danger"
                                        width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                                        <polyline points="16 17 21 12 16 7"></polyline>
                                        <line x1="21" y1="12" x2="9" y2="12"></line>
                                    </svg>
                                    <span class="ml-2">Logout </span>
                                </button>
                            </form>
                        </div>
                    </li>
                </ul>
            </div>
        </nav>
    </div>
</div>
<!--**********************************
            Header end ti-comment-alt
        ***********************************-->