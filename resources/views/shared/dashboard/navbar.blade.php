<header class="navbar p-0 d-flex align-items-center justify-content-between">
    <div class="navbar-left d-flex align-items-center">
        <button id="sidebar-toggle" class="btn text-green me-2">
             <i class="fa-solid fa-bars"></i>

        </button>
            <img src="{{asset('assets/images/userLogo.png')}}" alt="Logo" class="logo">

    </div>
    <div class="navbar-right d-flex align-items-center">
        <div class="search-container me-3">
            <input type="text" placeholder="Search" class="search-input">
            <button class="search-btn">
                <i class="fa-solid fa-magnifying-glass"></i>
            </button>
        </div>
        <div class="navbar-icons d-flex align-items-center me-3">
            <button class="nav-icon-btn"><i class="fa-regular fa-bell"></i></button>
            <button class="nav-icon-btn"><i class="fa-solid fa-language"></i></button>
            <button class="nav-icon-btn"><i class="fa-regular fa-sun"></i></button>
        </div>
        <div class="user-profile">
            <img src="{{asset('assets/images/user.png')}}" alt="User" class="user-avatar">
        </div>
    </div>
</header>