@php
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;
$configData = Helper::appClasses();
@endphp

<style>
    .menu-header {
        padding: 1rem 1.25rem;
        margin: 0.5rem 0;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
    }
    .menu-divider,
    .menu-separator,
    .menu-header hr {
        display: none;
    }
    .menu-header-text {
        font-size: 1rem;
        font-weight: 600;
        color: #a3a4cc;
        letter-spacing: 0.5px;
    }
    .menu-header+.menu-item {
        margin-top: 0.5rem;
    }
</style>

<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <input id="corporateId" type="hidden" value="{{ session('corporate_id') }}">

    @if (!isset($navbarFull))
    <div class="app-brand demo">
        <a href="{{ url('/') }}" class="app-brand-link">
            <span class="app-brand-logo demo">@include('_partials.macros', ['height' => 20])</span>
            <span class="app-brand-text demo menu-text fw-bold">{{ config('variables.templateName') }}</span>
        </a>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
            <i class="ti menu-toggle-icon d-none d-xl-block align-middle"></i>
            <i class="ti ti-x d-block d-xl-none ti-md align-middle"></i>
        </a>
    </div>
    @endif

    <div class="menu-inner-shadow"></div>

    @php
    $userType = session('user_type', 'MasterUser');
    @endphp

    @if ($userType === 'CorporateAdminUser')
        {{-- Original CorporateAdminUser menu logic --}}
        <ul class="menu-inner py-1" id="dynamic-menu">
            @foreach ($menuData[0]->menu as $menu)
                @if (!empty($menu->is_header))
                    <li class="menu-header text-uppercase">
                        <span class="menu-header-text">{{ __($menu->name) }}</span>
                    </li>
                @else
                    @php
                    $currentRouteName = Route::currentRouteName();
                    $activeClass = '';
                    if ($currentRouteName === 'subModuleName-pageView') {
                        $segments = explode('/', trim(parse_url(request()->url(), PHP_URL_PATH), '/'));
                        $activeClass = ($menu->slug === ($segments[count($segments)-2] ?? null)) ? 'active open' : '';
                    } elseif (is_array($menu->slug)) {
                        foreach ($menu->slug as $slug) {
                            if (str_starts_with($currentRouteName, $slug)) {
                                $activeClass = 'active open';
                                break;
                            }
                        }
                    } elseif (str_starts_with($currentRouteName, $menu->slug ?? '')) {
                        $activeClass = 'active open';
                    }
                    @endphp

                    <li class="menu-item {{ $activeClass }}">
                        <a href="{{ isset($menu->url) ? url($menu->url) : 'javascript:void(0);' }}"
                            class="{{ isset($menu->submenu) ? 'menu-link menu-toggle' : 'menu-link' }}"
                            @if (!empty($menu->target)) target="_blank" @endif>
                            @isset($menu->icon)<i class="{{ $menu->icon }}"></i>@endisset
                            <div>{{ __($menu->name ?? '') }}</div>
                            @isset($menu->badge)
                                <div class="badge bg-{{ $menu->badge[0] }} rounded-pill ms-auto">{{ $menu->badge[1] }}</div>
                            @endisset
                        </a>
                        @isset($menu->submenu)
                            @include('layouts.sections.menu.submenu', ['menu' => $menu->submenu])
                        @endisset
                    </li>
                @endif
            @endforeach
        </ul>

    @elseif ($userType === 'MasterUser')
        {{-- MasterUser menu defined via PHP array --}}
        @php
$masterUserMenu = [
    [
        'name' => 'Dashboard',
        'icon' => 'fas fa-tachometer-alt',
        'route' => 'employee-user-dashboard-analytics',
        'slug' => 'dashboard'
    ],
    [
        'name' => 'Personal Information',
        'icon' => 'fas fa-user-md',
        'route' => 'employee-user-personal-info',
        'slug' => 'personal-info'
    ],
    [
        'name' => 'Prescription',
        'icon' => 'ti ti-package',
        'route' => 'employee-user-prescription',
        'slug' => 'prescription'
    ],
    [
        'name' => 'Corporate Data User',
        'icon' => 'fas fa-user-md',
        'slug' => 'employee-user-corporate-data',
        'submenu' => [
            [
                'name' => 'Diagnostic Assessment',
                'route' => 'employee-user-diagnostic-assessment'
            ],
            [
                'name' => 'HRA',
                'route' => 'employee-user-hra'
            ],
            [
                'name' => 'Events',
                'route' => 'employee-user-events'
            ],
            [
                'name' => 'Out Patient',
                'route' => 'employee-user-out-patient'
            ],
            [
                'name' => 'OTC',
                'route' => 'employee-user-otc'
            ],
        ]
    ],
    [
        'name' => 'Health Monitoring',
        'icon' => 'ti ti-package',
        'slug' => 'employee-health-monitoring',
        'submenu' => [
            [
                'name' => 'Test',
                'route' => 'employee-user-test'
            ],
            [
                'name' => 'Hospitalization Details',
                'route' => 'employee-user-hospitalization-details'
            ],
            [
                'name' => 'Condition',
                'route' => 'employee-user-condition'
            ],
        ]
    ],
    [
        'name' => 'Reports',
        'icon' => 'ti ti-package',
        'slug' => 'reports',
        'submenu' => [
            [
                'name' => 'Reports (Table)',
                'route' => 'employee-user-reports'
            ],
            [
                'name' => 'Reports (Graph)',
                'route' => 'employee-user-reports-graph'
            ],
            [
                'name' => 'Reports (Multiple Test Graph)',
                'route' => 'employee-user-reports-graph-multipletest'
            ],
        ]
    ],
    [
        'name' => 'Settings',
        'icon' => 'ti ti-package',
        'route' => 'employee-user-settings',
        'slug' => 'settings'
    ],
];
@endphp

     <ul class="menu-inner py-1" id="dynamic-menu">
    @foreach ($masterUserMenu as $menu)
        @php
            // Determine active state for main menu and its submenu
            $isActive = '';
            $hasSubmenu = isset($menu['submenu']) && is_array($menu['submenu']);
            $routeMatch = isset($menu['route']) && request()->routeIs($menu['route']);

            if ($routeMatch) {
                $isActive = 'active open';
            } elseif ($hasSubmenu) {
                foreach ($menu['submenu'] as $sub) {
                    if (request()->routeIs($sub['route'])) {
                        $isActive = 'active open';
                        break;
                    }
                }
            }
        @endphp

        <li class="menu-item {{ $isActive }}">
            <a href="{{ isset($menu['route']) && !$hasSubmenu ? route($menu['route']) : 'javascript:void(0);' }}"
               class="menu-link {{ $hasSubmenu ? 'menu-toggle' : '' }}">
                <i class="menu-icon {{ $menu['icon'] }}"></i>
                <div>{{ $menu['name'] }}</div>
            </a>

            @if ($hasSubmenu)
                <ul class="menu-sub">
                    @foreach ($menu['submenu'] as $sub)
                        <li class="menu-item {{ request()->routeIs($sub['route']) ? 'active' : '' }}">
                            <a href="{{ route($sub['route']) }}" class="menu-link">
                                <div>{{ $sub['name'] }}</div>
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </li>
    @endforeach
</ul>


    @endif
</aside>
