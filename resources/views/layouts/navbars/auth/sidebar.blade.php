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
        .main-content,
        body.g-sidenav-pinned .main-content,
        .sidenav.fixed-start + .main-content,
        .sidenav.fixed-start ~ .main-content {
            margin-left: 17.125rem !important;
        }
        body.g-sidenav-hidden #sidenav-main {
            max-width: 85px !important;
            width: 85px !important;
            overflow: hidden;
            transform: translateX(0) !important;
        }
        body.g-sidenav-hidden .main-content,
        body.g-sidenav-hidden .sidenav.fixed-start + .main-content,
        body.g-sidenav-hidden .sidenav.fixed-start ~ .main-content {
            margin-left: 105px !important;
        }
    }

    @media (min-width: 1200px) {
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
        display: none !important;
    }

    body.g-sidenav-hidden #sidenav-main #sidebarToggleBtn {
        position: static !important;
        transform: none !important;
        margin: 0 auto !important;
        width: 36px !important;
        height: 36px !important;
    }
    }

    /* ===== Mobile / Tablet off-canvas drawer ===== */
    #imsSidenavBackdrop { display: none; }
    @media (max-width: 1199.98px) {
        #sidenav-main {
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            bottom: 0 !important;
            margin: 0 !important;
            width: 272px !important;
            max-width: 86vw !important;
            height: 100vh !important;
            height: 100dvh !important;
            max-height: 100dvh !important;
            border-radius: 0 16px 16px 0 !important;
            transform: translateX(-105%) !important;
            transition: transform .28s ease, box-shadow .28s ease !important;
            z-index: 1065 !important;
            box-shadow: none !important;
        }
        body.ims-sidenav-open #sidenav-main {
            transform: translateX(0) !important;
            box-shadow: 0 10px 40px rgba(0,0,0,.25) !important;
        }
        #imsSidenavBackdrop {
            display: block;
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, .45);
            z-index: 1060;
            opacity: 0;
            visibility: hidden;
            transition: opacity .25s ease, visibility .25s ease;
        }
        body.ims-sidenav-open #imsSidenavBackdrop { opacity: 1; visibility: visible; }
        body.ims-sidenav-open { overflow: hidden; }
        .main-content { margin-left: 0 !important; }
        #sidenav-main .nav-link:hover { transform: none; }
    }
</style>

<div id="imsSidenavBackdrop" aria-hidden="true"></div>
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
            var BP = 1200;
            function isMobile() { return window.innerWidth < BP; }
            function icon(cls) {
                var i = document.getElementById("sidebarToggleIcon");
                if (!i) return;
                i.classList.remove("fa-bars", "fa-chevron-right", "fa-xmark");
                i.classList.add(cls);
            }
            function applyState() {
                var body = document.body;
                if (isMobile()) {
                    // Mobile / tablet: off-canvas drawer, always starts closed
                    body.classList.remove("g-sidenav-hidden", "g-sidenav-pinned", "ims-sidenav-open");
                    icon("fa-xmark");
                } else {
                    body.classList.remove("ims-sidenav-open");
                    if (localStorage.getItem("sidebar-collapsed") === "true") {
                        body.classList.add("g-sidenav-hidden");
                        body.classList.remove("g-sidenav-pinned");
                        icon("fa-chevron-right");
                    } else {
                        body.classList.add("g-sidenav-pinned");
                        body.classList.remove("g-sidenav-hidden");
                        icon("fa-bars");
                    }
                }
            }
            function openMobile() { document.body.classList.add("ims-sidenav-open"); }
            function closeMobile() { document.body.classList.remove("ims-sidenav-open"); }
            window.imsToggleSidenav = function() {
                if (document.body.classList.contains("ims-sidenav-open")) closeMobile(); else openMobile();
            };

            function lockSidenavScroll() {
                var sideMain = document.getElementById("sidenav-main");
                if (sideMain) {
                    sideMain.scrollTop = 0;
                    sideMain.classList.remove("ps", "ps--active-y", "ps--active-x");
                    sideMain.querySelectorAll(".ps__rail-y, .ps__rail-x").forEach(function(r) { r.remove(); });
                }
            }

            document.addEventListener("DOMContentLoaded", function() {
                applyState();
                lockSidenavScroll();

                var toggleBtn = document.getElementById("sidebarToggleBtn");
                if (toggleBtn) {
                    toggleBtn.addEventListener("click", function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        if (isMobile()) { closeMobile(); return; }
                        var body = document.body;
                        if (body.classList.contains("g-sidenav-pinned")) {
                            body.classList.remove("g-sidenav-pinned");
                            body.classList.add("g-sidenav-hidden");
                            localStorage.setItem("sidebar-collapsed", "true");
                            icon("fa-chevron-right");
                        } else {
                            body.classList.remove("g-sidenav-hidden");
                            body.classList.add("g-sidenav-pinned");
                            localStorage.setItem("sidebar-collapsed", "false");
                            icon("fa-bars");
                        }
                    });
                }

                var sideMain = document.getElementById("sidenav-main");
                if (sideMain) {
                    sideMain.addEventListener("scroll", function() { if (this.scrollTop !== 0) this.scrollTop = 0; });
                }
            });

            window.addEventListener("load", lockSidenavScroll);

            var lastMobile = null;
            window.addEventListener("resize", function() {
                var m = isMobile();
                if (m !== lastMobile) { lastMobile = m; applyState(); }
            });

            document.addEventListener("keydown", function(e) { if (e.key === "Escape") closeMobile(); });

            document.addEventListener("click", function(e) {
                // Hamburger in top navbar (mobile)
                if (e.target.closest("#mobileSidenavToggle")) {
                    e.preventDefault();
                    window.imsToggleSidenav();
                    return;
                }
                // Backdrop closes the drawer
                if (e.target.closest("#imsSidenavBackdrop")) { closeMobile(); return; }

                var link = e.target.closest("#sidenav-main .nav-link");
                if (!link) return;
                if (isMobile()) { closeMobile(); return; }
                // Desktop: clicking an icon while collapsed expands the sidebar
                if (document.body.classList.contains("g-sidenav-hidden")) {
                    document.body.classList.remove("g-sidenav-hidden");
                    document.body.classList.add("g-sidenav-pinned");
                    localStorage.setItem("sidebar-collapsed", "false");
                    icon("fa-bars");
                }
            });
        })();
    </script>
    
    <hr class="horizontal dark mt-0">

        <div class="collapse show navbar-collapse w-auto" id="sidenav-collapse-main" style="display: block !important;">
        <!-- Compact Sidebar Menu Search -->
        <div class="px-3 pt-2 pb-1" id="sidebarMenuSearchWrap">
            <div class="input-group input-group-sm shadow-none" style="background: #f8f9fa; border: 1px solid #e2e8f0; border-radius: 8px; padding: 2px 8px; transition: all 0.2s ease;">
                <span class="input-group-text bg-transparent border-0 p-0 text-secondary me-2">
                    <i class="fa fa-search text-xs"></i>
                </span>
                <input type="text" id="sidebarMenuSearch" class="form-control form-control-sm bg-transparent border-0 p-0 text-xs shadow-none" placeholder="Search menu..." style="outline: none; height: 26px;">
                <button type="button" id="sidebarMenuSearchClear" class="btn btn-link text-secondary p-0 m-0 border-0 d-none" style="font-size: 10px; line-height: 1;">
                    <i class="fa fa-times"></i>
                </button>
            </div>
        </div>
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
                @can('Customers list')
                <a class="nav-link {{ Request::is('customers*') ? 'active' : '' }}" href="{{ url('customers') }}">
                    <div class="icon">
                        <i class="fa-solid fa-users"></i>
                    </div>
                    <span class="nav-link-text">Customers</span>
                </a>
                @endcan
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
            @if(auth()->user()->can('Inquiry Type list') || auth()->user()->can('Sales References list') || auth()->user()->can('Currencies list') || auth()->user()->can('Services list') || auth()->user()->can('Hotels list'))
            <!-- Preferences -->
            <li class="nav-item mt-3">
                <h6 class="ps-4 text-uppercase">Preferences</h6>
                @can('Inquiry Type list')
                <a class="nav-link {{ Request::is('inquiry-type*') ? 'active' : '' }}" href="{{ url('inquiry-type') }}">
                    <div class="icon">
                        <i class="fa-solid fa-list"></i>
                    </div>
                    <span class="nav-link-text">Inquiry Types</span>
                </a>
                @endcan
                @can('Sales References list')
                <a class="nav-link {{ Request::is('sales-reference*') ? 'active' : '' }}" href="{{ url('sales-reference') }}">
                    <div class="icon">
                        <i class="fa-solid fa-concierge-bell"></i>
                    </div>
                    <span class="nav-link-text">Sales Reference</span>
                </a>
                @endcan
                @can('Services list')
                <a class="nav-link {{ Request::is('other_services*') ? 'active' : '' }}" href="{{ url('other_services') }}">
                    <div class="icon">
                        <i class="fa-solid fa-briefcase"></i>
                    </div>
                    <span class="nav-link-text">Services</span>
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
                @if(auth()->user()->can('Hotels list') || auth()->user()->hasRole(['Admin', 'Super-Admin', 'admin', 'super-admin']) || auth()->user()->role_id == 1)
                <a class="nav-link {{ Request::is('hotels*') ? 'active' : '' }}" href="{{ route('hotels.index') }}">
                    <div class="icon">
                        <i class="fa-solid fa-hotel"></i>
                    </div>
                    <span class="nav-link-text">Hotels</span>
                </a>
                @endif
            </li>
            @endif

            @if(auth()->user()->can('Users Management list') || auth()->user()->can('Organization Settings edit') || auth()->user()->can('Super-Admin') || auth()->user()->can('Roles Management list'))
            <!-- Admin -->
            <li class="nav-item mt-3">
                <h6 class="ps-4 text-uppercase">Administration</h6>
                @can('Users Management list')
                    <a class="nav-link {{ Request::is('user-management*') || Request::is('users*') ? 'active' : '' }}" href="{{ url('user-management') }}">
                        <div class="icon">
                            <i class="fa-solid fa-user-shield"></i>
                        </div>
                        <span class="nav-link-text">User Management</span>
                    </a>
                @endcan
                @can('Organization Settings edit')
                <a class="nav-link {{ Request::is('organization-settings*') ? 'active' : '' }}" href="{{ route('organization_settings.edit') }}">
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
                <a class="nav-link {{ Request::is('roles*') ? 'active' : '' }}" href="{{ url('roles') }}">
                    <div class="icon">
                        <i class="fa-solid fa-user-tag"></i>
                    </div>
                    <span class="nav-link-text">Roles Management</span>
                </a>
                @endcan
                @can('Super-Admin')
                    <a class="nav-link {{ Request::is('permission*') ? 'active' : '' }}" href="{{ url('permission') }}">
                        <div class="icon">
                            <i class="fa-solid fa-key"></i>
                        </div>
                        <span class="nav-link-text">Permissions</span>
                    </a>
                @endcan
            </li>
            @endif

        </ul>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var searchInput = document.getElementById('sidebarMenuSearch');
            var clearBtn = document.getElementById('sidebarMenuSearchClear');
            if (!searchInput) return;

            searchInput.addEventListener('input', function() {
                var q = this.value.toLowerCase().trim();
                if (clearBtn) {
                    if (q.length > 0) clearBtn.classList.remove('d-none');
                    else clearBtn.classList.add('d-none');
                }

                var links = document.querySelectorAll('#sidenav-collapse-main .navbar-nav .nav-link');
                links.forEach(function(link) {
                    var textEl = link.querySelector('.nav-link-text');
                    var text = textEl ? textEl.textContent.toLowerCase() : '';
                    var isMatch = q === '' || text.indexOf(q) !== -1;
                    link.style.display = isMatch ? '' : 'none';
                });

                var headings = document.querySelectorAll('#sidenav-collapse-main .navbar-nav h6.text-uppercase');
                headings.forEach(function(h) {
                    var parentLi = h.closest('li.nav-item');
                    if (!parentLi) return;
                    var visibleLinks = parentLi.querySelectorAll('.nav-link:not([style*="display: none"])');
                    h.style.display = (visibleLinks.length > 0 || q === '') ? '' : 'none';
                });
            });

            if (clearBtn) {
                clearBtn.addEventListener('click', function() {
                    searchInput.value = '';
                    searchInput.dispatchEvent(new Event('input'));
                    searchInput.focus();
                });
            }
        });
    </script>
</aside>
@endif
