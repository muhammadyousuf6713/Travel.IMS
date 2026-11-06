<!--
=========================================================
* Soft UI Dashboard - v1.0.3
=========================================================

* Product Page: https://www.creative-tim.com/product/soft-ui-dashboard
* Copyright 2021 Creative Tim (https://www.creative-tim.com)
* Licensed under MIT (https://www.creative-tim.com/license)

* Coded by Creative Tim

=========================================================

* The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
-->
<!DOCTYPE html>

@if (\Request::is('rtl'))
    <html dir="rtl" lang="ar">
@else
    <html lang="en">
@endif

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <script>
        // Check for saved theme preference or use default
        if (localStorage.getItem('dark-mode') === 'true') {
            document.documentElement.classList.add('dark-mode');
            window.addEventListener('DOMContentLoaded', () => {
                document.body.classList.add('dark-mode');
                const toggle = document.getElementById('darkModeToggle');
                if (toggle) toggle.checked = true;
            });
        }
    </script>

    @if (env('IS_DEMO'))
        <x-demo-metas></x-demo-metas>
    @endif

    <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('assets/img/apple-icon.png') }}">
    
    @if(isset($orgSetting) && $orgSetting->favicon)
        <link rel="icon" type="image/x-icon" href="{{ asset($orgSetting->favicon) }}">
    @else
        <link rel="icon" type="image/png" href="{{ asset('assets/img/favicon.png') }}">
    @endif
    
    <title>
        {{ isset($orgSetting) && $orgSetting->name ? $orgSetting->name : 'Flugzeit Aviation' }}
    </title>
    <!--     Fonts and icons     -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
    <!-- Nucleo Icons -->
    <link href="{{ asset('assets/css/nucleo-icons.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/nucleo-svg.css') }}" rel="stylesheet" />
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="{{ asset('assets/css/nucleo-svg.css') }}" rel="stylesheet" />
    <!-- CSS Files -->
    <link id="pagestyle" href="{{ asset('assets/css/soft-ui-dashboard.css?v=1.0.3') }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/custom.css') }}" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- DataTables CSS (Bootstrap 5) -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.4.1/css/responsive.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">
    
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
    
    <!-- Toastr CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet"/>
    <style>
        :root {
            --bs-primary: {{ $orgSetting->theme_color ?? '#cb0c9f' }};
            --bs-primary-rgb: {{ hexdec(substr($orgSetting->theme_color ?? '#cb0c9f', 1, 2)) }}, {{ hexdec(substr($orgSetting->theme_color ?? '#cb0c9f', 3, 2)) }}, {{ hexdec(substr($orgSetting->theme_color ?? '#cb0c9f', 5, 2)) }};
        }
        .bg-gradient-primary {
            background-image: linear-gradient(310deg, var(--bs-primary) 0%, var(--bs-primary) 100%) !important;
        }
        .text-primary {
            color: var(--bs-primary) !important;
        }
        /* Consistent Border Radius Standard */
        .badge {
            border-radius: 4px !important;
            padding: 5px 10px !important;
        }
        .btn-primary, .btn-az-primary {
            background-color: var(--bs-primary) !important;
            border-color: var(--bs-primary) !important;
            color: #fff !important;
        }
        .card {
            border-radius: 6px !important;
        }
        /* Expanded Datatables row details */
        table.dataTable>tbody>tr.child ul.dtr-details>li .dtr-data {
            font-size: 1.1rem !important;
            white-space: pre-wrap;
            line-height: 1.6;
        }
    </style>
</head>

<body
    class="g-sidenav-show  bg-gray-100 {{ \Request::is('rtl') ? 'rtl' : (Request::is('virtual-reality') ? 'virtual-reality' : '') }} ">
    @auth
        @yield('auth')
    @endauth
    @guest
        @yield('guest')
    @endguest

    @if (session()->has('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                toastr.success("{{ session('success') }}");
            });
        </script>
    @endif
    @if (session()->has('error'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                toastr.error(@json(session('error')));
            });
        </script>
    @endif
    @if (session()->has('warning'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                toastr.warning(@json(session('warning')));
            });
        </script>
    @endif
    @if (session()->has('info'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                toastr.info(@json(session('info')));
            });
        </script>
    @endif
    <!--   Core JS Files   -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Toastr JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
        toastr.options = {
            "closeButton": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "timeOut": "4000",
        };
    </script>
    <script>
        $(document).ready(function() {
            // Initialize Select2 on all select elements that don't have the 'no-select2' or 'ql-' class, and are not part of Swal
            function initSelect2() {
                $('select:not(.no-select2, .ql-header, .ql-font, .ql-size, .swal2-select, .ajax-city-select2)').each(function() {
                    // Exclude DataTables length dropdown
                    if ($(this).closest('.dataTables_length').length > 0) {
                        return;
                    }
                    
                    if (!$(this).hasClass('select2-hidden-accessible')) {
                        var placeholderText = $(this).attr('placeholder') || $(this).data('placeholder');
                        var select2Options = {
                            width: '100%',
                            dropdownParent: $(this).closest('.modal').length ? $(this).closest('.modal') : $(document.body)
                        };
                        
                        if (placeholderText) {
                            select2Options.placeholder = $(this).prop('multiple') ? placeholderText : { id: '', text: placeholderText };
                            select2Options.allowClear = !$(this).prop('multiple'); 
                        }
                        
                        $(this).select2(select2Options);
                    }
                });
            }
            
            initSelect2();
            
            // Re-initialize Select2 on DOM modifications (for dynamic rows), ignoring SweetAlert containers
            var observer = new MutationObserver(function(mutations) {
                var newSelects = false;
                mutations.forEach(function(mutation) {
                    if (mutation.addedNodes.length > 0) {
                        for (var i = 0; i < mutation.addedNodes.length; i++) {
                            var node = mutation.addedNodes[i];
                            if (node.nodeType === 1) { // Element node
                                var $node = $(node);
                                // Ignore SweetAlert elements entirely
                                if ($node.hasClass('swal2-container') || $node.closest('.swal2-container').length > 0) {
                                    continue;
                                }
                                if ($node.find('select:not(.no-select2, .ql-header, .ql-font, .ql-size, .swal2-select, .ajax-city-select2)').length > 0 || ($node.is('select') && !$node.hasClass('no-select2') && !$node.hasClass('swal2-select'))) {
                                    newSelects = true;
                                }
                            }
                        }
                    }
                });
                if (newSelects) {
                    initSelect2();
                }
            });
            
            observer.observe(document.body, { childList: true, subtree: true });
        });
    </script>
    <script src="{{ asset('assets/js/core/popper.min.js') }}"></script>
    <script src="{{ asset('assets/js/core/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/perfect-scrollbar.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/smooth-scrollbar.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/fullcalendar.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/chartjs.min.js') }}"></script>
    @stack('rtl')
    @stack('dashboard')
    <!-- DataTables Core & Plugins -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.4.1/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.4.1/js/responsive.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>

    @stack('scripts')
    <script>
        var win = navigator.platform.indexOf('Win') > -1;
        if (win && document.querySelector('#sidenav-scrollbar')) {
            var options = {
                damping: '0.5'
            }
            Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
        }

        // Dark Mode Logic
        if (localStorage.getItem('dark-mode') === 'true') {
            document.body.classList.add('dark-mode');
        }

        function darkMode(el) {
            const body = document.getElementsByTagName('body')[0];
            if (body.classList.contains('dark-mode')) {
                body.classList.remove('dark-mode');
                localStorage.setItem('dark-mode', 'false');
            } else {
                body.classList.add('dark-mode');
                localStorage.setItem('dark-mode', 'true');
            }
        }
    </script>

    <!-- Github buttons -->
    
    <!-- Control Center for Soft Dashboard: parallax effects, scripts for the example pages etc -->
    <script src="{{ asset('assets/js/soft-ui-dashboard.min.js?v=1.0.3') }}"></script>
    
    <style>
        /* Force dynamic organization primary color on all primary elements loaded later in body */
        .btn-primary,
        .btn-primary:hover,
        .btn-primary:focus,
        .btn-primary:active,
        .btn-az-primary,
        .btn-az-primary:hover,
        .btn-az-primary:focus,
        .btn-az-primary:active,
        .btn-az-primary:disabled,
        .bg-gradient-primary,
        .bg-gradient-primary:hover,
        .btn-outline-primary:hover,
        .btn-outline-primary:active,
        .btn-outline-primary.active,
        .tab-btn .btn-az-primary.visited strong,
        .tab-btn .active strong,
        .tab-btn .active span,
        .tab-btn .btn-az-primary.visited span,
        .nav-pills .nav-link.active,
        .page-item.active .page-link,
        .form-check-input:checked {
            background-color: var(--bs-primary) !important;
            background-image: linear-gradient(310deg, var(--bs-primary) 0%, var(--bs-primary) 100%) !important;
            border-color: var(--bs-primary) !important;
            color: #fff !important;
        }
        .btn-outline-primary {
            border-color: var(--bs-primary) !important;
            color: var(--bs-primary) !important;
        }
        .form-check-input[type=checkbox] {
            border: 2px solid var(--bs-primary) !important;
        }
        
        /* Fix DataTables length dropdown layout */
        .dataTables_length label {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 0;
        }
        .dataTables_length select {
            width: auto;
            display: inline-block;
            padding: 0.25rem 2rem 0.25rem 0.5rem;
        }
        .btn-outline-primary:hover {
            background-color: var(--bs-primary) !important;
            color: #fff !important;
        }
        .text-primary,
        .tab-btn .active span,
        .tab-btn .btn-az-primary.visited span,
        .page-link {
            color: var(--bs-primary) !important;
        }
    </style>
    <!-- Shared UI layer (loaded last so it wins over theme + inline styles) -->
    <link href="{{ asset('assets/css/ims-ui.css') }}?v={{ @filemtime(public_path('assets/css/ims-ui.css')) }}" rel="stylesheet">
    <script>
        function initializeAjaxCityDropdowns() {
            $('.ajax-city-select2').each(function() {
                var $this = $(this);

                if ($this.hasClass('select2-hidden-accessible')) {
                    $this.select2('destroy');
                }
                
                var placeholderText = $this.data('placeholder') || 'Search City...';
                
                $this.select2({
                    placeholder: $this.prop('multiple') ? placeholderText : { id: '', text: placeholderText },
                    allowClear: !$this.prop('multiple'),
                    minimumInputLength: 2,
                    width: '100%',
                    ajax: {
                        url: '{{ route("autocomplete_city") }}',
                        dataType: 'json',
                        delay: 350,
                        data: function(params) {
                            return {
                                q: params.term,
                                page: params.page || 1
                            };
                        },
                        processResults: function(data, params) {
                            params.page = params.page || 1;
                            
                            // Map the raw array of objects from the backend to Select2's expected format
                            var mappedData = $.map(data, function(obj) {
                                return {
                                    id: obj.name,
                                    text: obj.name
                                };
                            });
                            
                            return {
                                results: mappedData,
                                pagination: {
                                    more: false // Disable infinite scroll if backend doesn't support pagination
                                }
                            };
                        },
                        cache: true
                    }
                });
            });
        }

        $(document).ready(function() {
            initializeAjaxCityDropdowns();
        });
    
        // Universal scroll boundary isolation: Prevents wheel scrolling over dropdowns/modals from moving background screen
        document.addEventListener('wheel', function(e) {
            const scrollable = e.target.closest('.select2-results__options, .select2-dropdown, .modal-body, .followup-history-scrollbox, .followup-modal-list, .dropdown-menu');
            
            if (document.body.classList.contains('modal-open')) {
                const insideModal = e.target.closest('.modal-content, .select2-dropdown, .select2-container');
                if (!insideModal) {
                    e.preventDefault();
                    return;
                }
            }

            if (!scrollable) return;

            const hasScroll = scrollable.scrollHeight > scrollable.clientHeight;
            if (!hasScroll) return;

            const scrollTop = scrollable.scrollTop;
            const maxScroll = scrollable.scrollHeight - scrollable.clientHeight;
            const delta = e.deltaY;

            if ((delta < 0 && scrollTop <= 0) || (delta > 0 && scrollTop >= maxScroll - 1)) {
                e.preventDefault();
            } else {
                e.stopPropagation();
            }
        }, { passive: false });
    </script>
</body>

</html>
