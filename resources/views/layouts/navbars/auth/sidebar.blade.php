@if(Auth::check())
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

<style>
    /* Sidebar enhancements & clean bounded scrolling */
    #sidenav-main {
        background: #fff;
        border-radius: 15px;
        display: flex !important;
        flex-direction: column !important;
        overflow: hidden !important; /* Prevent outer container scrolling */
        height: calc(100vh - 32px) !important;
        max-height: calc(100vh - 32px) !important;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        transition: all 0.2s ease-in-out;
        z-index: 1040;
    }

    #sidenav-main .sidenav-header {
        flex: 0 0 75px !important;
        height: 75px !important;
        min-height: 75px !important;
        max-height: 75px !important;
        display: flex;
        align-items: center;
        padding: 0 1rem;
        overflow: hidden;
    }

    #sidenav-main hr {
        flex: 0 0 1px !important;
        margin: 0 !important;
    }

    #sidenav-main #sidenav-collapse-main {
        display: block !important; /* MUST stay visible */
        flex: 1 1 0% !important;
        min-height: 0 !important;
        height: auto !important;
        max-height: 100% !important;
        overflow-y: auto !important;
        overflow-x: hidden !important;
        overscroll-behavior: contain !important;
        padding-bottom: 2rem !important;
        scrollbar-width: thin;
        scrollbar-color: rgba(0, 0, 0, 0.25) transparent;
    }

    #sidenav-main #sidenav-collapse-main::-webkit-scrollbar {
        width: 5px;
    }
    #sidenav-main #sidenav-collapse-main::-webkit-scrollbar-track {
        background: transparent;
    }
    #sidenav-main #sidenav-collapse-main::-webkit-scrollbar-thumb {
        background: rgba(0, 0, 0, 0.25);
        border-radius: 4px;
    }
    #sidenav-main #sidenav-collapse-main::-webkit-scrollbar-thumb:hover {
        background: rgba(0, 0, 0, 0.45);
    }

    #sidenav-main .nav-item {
        transition: all 0.2s ease-in-out;
    }

    #sidenav-main .nav-link {
        border-radius: 12px;
        margin: 4px 10px;
        padding: 10px 14px;
        display: flex;
        align-items: center;
        font-size: 14px;
        color: #444;
        transition: background 0.3s ease, color 0.3s ease, transform 0.2s ease;
    }

    #sidenav-main .nav-link:hover {
        background: var(--bs-primary);
        color: #fff;
        transform: translateX(3px);
    }

    #sidenav-main .nav-link.active {
        background: var(--bs-primary) !important;
        color: #fff !important;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
    }

    #sidenav-main .icon {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        margin-right: 10px;
        background-color: #f8f9fa;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease-in-out;
        color: var(--bs-primary);
    }

    #sidenav-main .nav-link:hover .icon {
        color: var(--bs-primary);
    }

    #sidenav-main .nav-link.active .icon {
        background-color: transparent;
        color: #fff !important;
    }

    #sidenav-main h6 {
        font-size: 11px;
        color: #888;
        margin-top: 10px;
        margin-bottom: 5px;
        transition: opacity 0.2s ease;
    }

    @media (min-width: 1200px) {
        body.g-sidenav-hidden #sidenav-main {
            max-width: 85px !important;
            width: 85px !important;
            overflow: hidden;
            transform: translateX(0) !important;
        }
        body.g-sidenav-hidden .main-content {
            margin-left: 105px !important;
        }
    }

    body.g-sidenav-hidden #sidenav-main .nav-link-text,
    body.g-sidenav-hidden #sidenav-main h6,
    body.g-sidenav-hidden #sidenav-main .navbar-brand span {
        display: none !important;
    }

    body.g-sidenav-hidden #sidenav-main .nav-link {
        justify-content: center !important;
        margin: 4px 5px;
        padding: 10px 5px;
    }

    body.g-sidenav-hidden #sidenav-main .icon {
        margin-right: 0 !important;
    }

    body.g-sidenav-hidden #sidenav-main .sidenav-header {
        justify-content: center !important;
        padding-left: 0 !important;
        padding-right: 0 !important;
    }

    body.g-sidenav-hidden #sidenav-main .navbar-brand {
        padding: 1rem 0 !important;
        margin: 0 !important;
        display: flex;
        justify-content: center;
    }

    body.g-sidenav-hidden #sidenav-main #sidebarToggleBtn {
        margin-right: 0 !important;
    }
</style>

<aside class="card sidenav navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-3"
    id="sidenav-main">
    <div class="sidenav-header d-flex align-items-center position-relative" style="min-height: {{ max(70, ($orgSetting->logo_height ?? 55) + 15) }}px; height: auto; padding: 8px 12px;">
        <a class="navbar-brand m-0 d-flex align-items-center justify-content-center mx-auto" href="{{ url('dashboard') }}" style="z-index: 1; margin-top: {{ $orgSetting->logo_margin_top ?? 0 }}px !important; margin-bottom: {{ $orgSetting->logo_margin_bottom ?? 0 }}px !important;">
            @if(!empty($orgSetting) && $orgSetting->logo)
                <img src="{{ asset($orgSetting->logo) }}" style="max-height: {{ $orgSetting->logo_height ?? 55 }}px !important; max-width: {{ $orgSetting->logo_width ?? 180 }}px !important; padding: {{ $orgSetting->logo_padding ?? 4 }}px !important; object-fit: contain !important; transition: all 0.2s ease;" alt="main_logo">
            @else
                <!-- <img src="{{ asset('assets/img/logo-ct.png') }}" style="max-height: 50px !important; width: auto;" alt="main_logo"> -->
                <span class="ms-1 font-weight-bold sidenav-text">{{ $orgSetting->name ?? 'Travel IMS' }}</span>
            @endif
        </a>
        <a href="javascript:;" class="text-secondary p-0 d-flex align-items-center justify-content-center position-absolute" id="sidebarToggleBtn" style="right: 10px; top: 50%; transform: translateY(-50%); width: 28px; height: 28px; border-radius: 6px; background: #f8f9fa; border: 1px solid #e9ecef; z-index: 1050; transition: all 0.2s ease; cursor: pointer;">
            <i class="fa fa-bars fs-6" id="sidebarToggleIcon"></i>
        </a>
    </div>
    
    <script>
        (function() {
            function initSidebar() {
                const body = document.body;
                const toggleIcon = document.getElementById("sidebarToggleIcon");
                const toggleBtn = document.getElementById("sidebarToggleBtn");
                
                if (!toggleBtn || !toggleIcon || !body) return;

                // Load state
                if (localStorage.getItem("sidebar-collapsed") === "true") {
                    body.classList.add("g-sidenav-hidden");
                    body.classList.remove("g-sidenav-pinned");
                    toggleIcon.classList.remove("fa-bars");
                    toggleIcon.classList.add("fa-chevron-right");
                } else {
                    body.classList.add("g-sidenav-pinned");
                    body.classList.remove("g-sidenav-hidden");
                    toggleIcon.classList.remove("fa-chevron-right");
                    toggleIcon.classList.add("fa-bars");
                }

                // Toggle click
                toggleBtn.addEventListener("click", function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    if (body.classList.contains("g-sidenav-pinned")) {
                        body.classList.remove("g-sidenav-pinned");
                        body.classList.add("g-sidenav-hidden");
                        localStorage.setItem("sidebar-collapsed", "true");
                        toggleIcon.classList.remove("fa-bars");
                        toggleIcon.classList.add("fa-chevron-right");
                    } else {
                        body.classList.remove("g-sidenav-hidden");
                        body.classList.add("g-sidenav-pinned");
                        localStorage.setItem("sidebar-collapsed", "false");
                        toggleIcon.classList.remove("fa-chevron-right");
                        toggleIcon.classList.add("fa-bars");
                    }
                });
            }

            // Run only on DOMContentLoaded to prevent duplicate listeners
            document.addEventListener("DOMContentLoaded", initSidebar);
        })();

        function lockSidenavScroll() {
            const sideCollapse = document.getElementById("sidenav-collapse-main");
            if (sideCollapse) {
                sideCollapse.style.display = "block";
            }
            const sideMain = document.getElementById("sidenav-main");
            if (sideMain) {
                sideMain.scrollTop = 0;
                sideMain.style.overflow = "hidden";
                sideMain.classList.remove("ps", "ps--active-y", "ps--active-x");
                sideMain.querySelectorAll(".ps__rail-y, .ps__rail-x").forEach(function(r) {
                    r.remove();
                });
            }
        }

        lockSidenavScroll();

        document.addEventListener("DOMContentLoaded", function() {
            lockSidenavScroll();
            const body = document.body;
            const toggleIcon = document.getElementById("sidebarToggleIcon");
            
            // Check saved state on load
            if (localStorage.getItem("sidebar-collapsed") === "true") {
                body.classList.add("g-sidenav-hidden");
                body.classList.remove("g-sidenav-pinned");
                if (toggleIcon) {
                    toggleIcon.classList.remove("fa-bars");
                    toggleIcon.classList.add("fa-chevron-right");
                }
            } else {
                body.classList.add("g-sidenav-pinned");
                body.classList.remove("g-sidenav-hidden");
                if (toggleIcon) {
                    toggleIcon.classList.remove("fa-chevron-right");
                    toggleIcon.classList.add("fa-bars");
                }
            }

            const sideMain = document.getElementById("sidenav-main");
            if (sideMain) {
                sideMain.addEventListener("scroll", function() {
                    if (this.scrollTop !== 0) this.scrollTop = 0;
                });
                sideMain.addEventListener("wheel", function(e) {
                    const collapse = document.getElementById("sidenav-collapse-main");
                    if (collapse && !collapse.contains(e.target)) {
                        collapse.scrollTop += e.deltaY;
                        e.preventDefault();
                    }
                    this.scrollTop = 0;
                }, { passive: false });
            }
        });

        window.addEventListener("load", lockSidenavScroll);

        // Handle clicking icons when sidebar is collapsed (Event Delegation)
        document.addEventListener("click", function(e) {
            const link = e.target.closest("#sidenav-main .nav-link");
            if (link && document.body.classList.contains("g-sidenav-hidden") && link.id !== "sidebarToggleBtn") {
                document.body.classList.remove("g-sidenav-hidden");
                document.body.classList.add("g-sidenav-pinned");
                localStorage.setItem("sidebar-collapsed", "false");
                const toggleIcon = document.getElementById("sidebarToggleIcon");
                if (toggleIcon) {
                    toggleIcon.classList.remove("fa-chevron-right");
                    toggleIcon.classList.add("fa-bars");
                }
            }
        });
    </script>
    
    <hr class="horizontal dark mt-0">

    <div class="collapse show navbar-collapse w-auto" id="sidenav-collapse-main" style="display: block !important;">
        <ul class="navbar-nav">

            <!-- Dashboard -->
            <li class="nav-item mt-2">
                <h6 class="ps-4 text-uppercase">Dashboard</h6>
                <a class="nav-link {{ Request::is('dashboard') ? 'active' : '' }}" href="{{ url('dashboard') }}">
                    <div class="icon">
                        <i class="fa-solid fa-gauge"></i>
                    </div>
                    <span class="nav-link-text">Dashboard</span>
                </a>
            </li>

            <!-- Operations -->
            <li class="nav-item mt-3">
                <h6 class="ps-4 text-uppercase">Operations</h6>
                <a class="nav-link {{ Request::is('customers') ? 'active' : '' }}" href="{{ url('customers') }}">
                    <div class="icon">
                        <i class="fa-solid fa-users"></i>
                    </div>
                    <span class="nav-link-text">Customers</span>
                </a>
                @can('Inquiry list')
                <a class="nav-link {{ Request::is('inquiry') ? 'active' : '' }}" href="{{ url('inquiry') }}">
                    <div class="icon">
                        <i class="fa-solid fa-question-circle"></i>
                    </div>
                    <span class="nav-link-text">Inquiry</span>
                </a>
                <a class="nav-link {{ Request::is('followups*') ? 'active' : '' }}" href="{{ route('followups.index') }}">
                    <div class="icon">
                        <i class="fa-solid fa-clock-rotate-left"></i>
                    </div>
                    <span class="nav-link-text">Follow-up List</span>
                </a>
                @endcan
                @can('Reviews list')
                <a class="nav-link {{ Request::is('reviews*') ? 'active' : '' }}" href="{{ route('reviews.index') }}">
                    <div class="icon">
                        <i class="fa-solid fa-star"></i>
                    </div>
                    <span class="nav-link-text">Reviews</span>
                </a>
                @endcan
                @can('Pilgrimage list')
                <a class="nav-link {{ Request::is('pilgrimage*') ? 'active' : '' }}" href="{{ route('pilgrimage.index') }}">
                    <div class="icon">
                        <i class="fa-solid fa-map-location-dot"></i>
                    </div>
                    <span class="nav-link-text">Pilgrimage & Itinerary</span>
                </a>
                @endcan
            </li>

            @can('view reports')
            <!-- Reports -->
            <li class="nav-item">
                <a class="nav-link {{ Request::is('reports') ? 'active' : '' }}" href="{{ route('reports.index') }}">
                    <div class="icon">
                        <i class="fa-solid fa-file-invoice"></i>
                    </div>
                    <span class="nav-link-text">Reports</span>
                </a>
            </li>
            @endcan
            <!-- Preferences -->
            <li class="nav-item mt-3">
                <h6 class="ps-4 text-uppercase">Preferences</h6>
                @can('Inquiry Type list')
                <a class="nav-link {{ Request::is('inquiry-type') ? 'active' : '' }}" href="{{ url('inquiry-type') }}">
                    <div class="icon">
                        <i class="fa-solid fa-list"></i>
                    </div>
                    <span class="nav-link-text">Inquiry Types</span>
                </a>
                @endcan
                @can('Sales References list')
                <a class="nav-link {{ Request::is('sales-reference') ? 'active' : '' }}" href="{{ url('sales-reference') }}">
                    <div class="icon">
                        <i class="fa-solid fa-concierge-bell"></i>
                    </div>
                    <span class="nav-link-text">Sales Reference</span>
                </a>
                @endcan
                @can('Currencies list')
                <a class="nav-link {{ Request::is('currencies*') ? 'active' : '' }}" href="{{ route('currencies.index') }}">
                    <div class="icon">
                        <i class="fa-solid fa-coins"></i>
                    </div>
                    <span class="nav-link-text">Currencies & Rates</span>
                </a>
                @endcan
            </li>

            <!-- Admin -->
            <li class="nav-item mt-3">
                <h6 class="ps-4 text-uppercase">Administration</h6>
                @can('Users Management list')
                    <a class="nav-link {{ Request::is('user-management') ? 'active' : '' }}" href="{{ url('user-management') }}">
                        <div class="icon">
                            <i class="fa-solid fa-user-shield"></i>
                        </div>
                        <span class="nav-link-text">User Management</span>
                    </a>
                @endcan
                @can('Organization Settings edit')
                <a class="nav-link {{ Request::is('organization-settings') ? 'active' : '' }}" href="{{ route('organization_settings.edit') }}">
                    <div class="icon">
                        <i class="fa-solid fa-building"></i>
                    </div>
                    <span class="nav-link-text">Organization Settings</span>
                </a>
                @endcan
                @can('Super-Admin')
                <a class="nav-link {{ Request::is('system-integration-settings*') ? 'active' : '' }}" href="{{ route('integration_settings.edit') }}">
                    <div class="icon">
                        <i class="fa-solid fa-sliders"></i>
                    </div>
                    <span class="nav-link-text">Integration Settings</span>
                </a>
                @endcan
                @can('Roles Management list')
                <a class="nav-link {{ Request::is('roles') ? 'active' : '' }}" href="{{ url('roles') }}">
                    <div class="icon">
                        <i class="fa-solid fa-user-tag"></i>
                    </div>
                    <span class="nav-link-text">Roles Management</span>
                </a>
                @endcan
                @can('Super-Admin')
                    <a class="nav-link {{ Request::is('permission') ? 'active' : '' }}" href="{{ url('permission') }}">
                        <div class="icon">
                            <i class="fa-solid fa-key"></i>
                        </div>
                        <span class="nav-link-text">Permissions</span>
                    </a>
                @endcan
            </li>

        </ul>
    </div>
</aside>
@endif
