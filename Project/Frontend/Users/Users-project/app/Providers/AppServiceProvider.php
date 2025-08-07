<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        View::composer('*', function ($view) {
            $verticalMenuJson = file_get_contents(base_path('resources/menu/verticalMenu.json'));
            $verticalMenuData = json_decode($verticalMenuJson, true);
            $horizontalMenuJson = file_get_contents(base_path('resources/menu/horizontalMenu.json'));
            $horizontalMenuData = json_decode($horizontalMenuJson, true);
            $menuData = [$verticalMenuData, $horizontalMenuData];


            $corporateId = Session('corporate_id');
            if ($corporateId) {
                $response = Http::withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer ' . request()->cookie('access_token'),
                ])->get("https://api-user.hygeiaes.com/V1/corporate/corporate-components/getAllComponent/accessRights/corpId/{$corporateId}");

                if ($response->successful() && !empty($response['data'])) {


                    $isSuperAdmin = $response['is_super_admin'] ?? false;
                    $ohcRights = $response['ohc_menu_rights'] ?? [];
                    $mhcRights = $response['mhc_menu_rights'] ?? [];

                    $verticalMenuData = $verticalMenuData;
                    $horizontalMenuData = []; // Assuming it will be filled somewhere else

                    // Permission check helper
                    $hasPermission = function ($rights, $key) {
                        return isset($rights[$key]) && intval($rights[$key]) > 0;
                    };

                    foreach ($response['data'] as $module) {
                        if (!empty($module['submodules'])) {
                            $moduleName = strtolower(str_replace(' ', '-', $module['module_name']));
                            $verticalMenuData['menu'][] = [
                                'name' => strtoupper($module['module_name']),
                                'icon' => null,
                                'slug' => null,
                                'is_header' => true
                            ];
                            $moduleSubmodules = [];

                            foreach ($module['submodules'] as $submodule) {
                                $subModuleName = strtolower(str_replace(' ', '-', $submodule['sub_module_name']));
                                if ($moduleName === "others" && in_array($subModuleName, ['bio-medical-waste', 'inventory', 'invoice'])) {
                                    $newMenuItem = [
                                        'url' => '/others/' . $subModuleName,
                                        'name' => ucwords(str_replace('-', ' ', $subModuleName)),
                                        'icon' => "menu-icon tf-icons ti ti-settings-code",
                                        'slug' => $subModuleName,
                                    ];
                                } else {
                                    $newMenuItem = [
                                        'name' => ucwords(str_replace('-', ' ', $subModuleName)),
                                        'icon' => "menu-icon tf-icons ti ti-settings-code",
                                        'slug' => $subModuleName,
                                        'submenu' => $this->getCustomSubmenus($moduleName, $subModuleName, $mhcRights),
                                    ];
                                }
                                $moduleSubmodules[] = $newMenuItem;
                            }

                            // ----- MHC Static Menus Based on Permissions -----
                            if ($moduleName === 'mhc') {
                                if ($hasPermission($mhcRights, 'reports')) {
                                    $moduleSubmodules[] = [
                                        'name' => 'Reports',
                                        'icon' => "menu-icon tf-icons ti ti-package",
                                        'slug' => 'drug-dispensation',
                                        'submenu' => [
                                            ['url' => $moduleName . '/reports/health-risk', 'name' => 'Health Risk Report', 'slug' => 'reports-health-risk-report', 'moduleName' => $moduleName],
                                            ['url' => $moduleName . '/reports/diagnostic-risk', 'name' => 'Diagnostic Risk Report', 'slug' => 'reports-diagnostic-risk-report', 'moduleName' => $moduleName],
                                        ]
                                    ];
                                }
                                if ($hasPermission($mhcRights, 'employee_monitoring')) {
                                    $moduleSubmodules[] = [
                                        'name' => 'Monitoring',
                                        'icon' => "menu-icon tf-icons ti ti-package",
                                        'slug' => 'monitoring',
                                        'submenu' => [
                                            ['url' => $moduleName . '/reports/health-risk', 'name' => 'Employee Monitoring', 'slug' => 'reports-health-risk-report', 'moduleName' => $moduleName],
                                            ['url' => $moduleName . '/reports/diagnostic-risk', 'name' => 'Individual Reports', 'slug' => 'reports-diagnostic-risk-report', 'moduleName' => $moduleName],
                                        ]
                                    ];
                                }
                            }

                            // ----- OHC Static Menus Based on Permissions -----
                            if ($moduleName === 'ohc') {
                                if ($hasPermission($ohcRights, 'ohc_dashboard')) {
                                    $moduleSubmodules[] = [
                                        'url' => '/mhc/ohc-dashboard',
                                        'name' => 'OHC Dashboard',
                                        'icon' => "menu-icon tf-icons ti ti-package",
                                        'slug' => 'ohc-ohc-dashboard'
                                    ];
                                }

                                // if ($isSuperAdmin) {
                                //  if ($hasPermission($ohcRights, 'corporate_ohc')) {
                                $moduleSubmodules[] = [
                                    'url' => '/mhc/ohc-list',
                                    'name' => 'Corporate OHC',
                                    'icon' => "menu-icon tf-icons ti ti-package",
                                    'slug' => 'corporate-ohc-list'
                                ];
                                // }

                                //  if ($hasPermission($ohcRights, 'drug_dispensation')) {
                                $moduleSubmodules[] = [
                                    'name' => 'Drug Dispensation',
                                    'icon' => "menu-icon tf-icons ti ti-package",
                                    'slug' => 'drug-dispensation',
                                    'submenu' => [
                                        ['url' => '/requests/pending-requests', 'name' => 'Pending Requests', 'slug' => 'requests-pending-requests', 'moduleName' => $moduleName],
                                        ['url' => '/requests/complete-requests', 'name' => 'Completed Requests', 'slug' => 'requests-completed-requests', 'moduleName' => $moduleName],
                                    ]
                                ];
                                // }
                                //}



                                if ($hasPermission($ohcRights, 'prescription')) {
                                    $permission = intval($ohcRights['prescription']);
                                    $submenu = [];

                                    // Always show "View Prescription"
                                    $submenu[] = [
                                        'url' => '/prescription/prescription-view',
                                        'name' => 'View Prescription',
                                        'slug' => 'ohc-prescription-view',
                                        'moduleName' => $moduleName
                                    ];

                                    // Show "Prescription Template" only if permission is 2
                                    if ($permission === 2) {
                                        $submenu[] = [
                                            'url' => '/prescription/prescription-template',
                                            'name' => 'Prescription Template',
                                            'slug' => 'ohc-prescription-template',
                                            'moduleName' => $moduleName
                                        ];
                                    }

                                    $moduleSubmodules[] = [
                                        'name' => 'Prescription',
                                        'icon' => "menu-icon tf-icons ti ti-package",
                                        'slug' => 'ohc-prescription',
                                        'submenu' => $submenu
                                    ];
                                }

                                if ($hasPermission($ohcRights, 'tests')) {
                                    $moduleSubmodules[] = [
                                        'name' => 'Test',
                                        'icon' => "menu-icon tf-icons ti ti-package",
                                        'slug' => 'ohc-test',
                                        'url' => '/ohc/test-list',
                                    ];
                                }

                                if ($hasPermission($ohcRights, 'stocks')) {
                                    $permission = intval($ohcRights['stocks']);
                                    $submenu = [];

                                    if ($permission === 2) {
                                        // Full access: Show all submenus
                                        $submenu = [
                                            [
                                                'url' => '/drugs/drug-template-list',
                                                'name' => 'Templates',
                                                'slug' => 'corporate-ohc-drugs-templates',
                                                'moduleName' => $moduleName
                                            ],
                                            [
                                                'url' => '/pharmacy/pharmacy-stock-list',
                                                'name' => 'Stock',
                                                'slug' => 'corporate-ohc-drugs-stock',
                                                'moduleName' => $moduleName
                                            ],
                                            [
                                                'url' => '/pharmacy/pharmacy-move-list/',
                                                'name' => 'Stock Move',
                                                'slug' => 'corporate-ohc-drugs-stock-move',
                                                'moduleName' => $moduleName
                                            ],
                                        ];
                                    } elseif ($permission === 1) {
                                        // Limited access: Only show "Stock"
                                        $submenu = [
                                            [
                                                'url' => '/pharmacy/pharmacy-stock-list',
                                                'name' => 'Stock',
                                                'slug' => 'corporate-ohc-drugs-stock',
                                                'moduleName' => $moduleName
                                            ]
                                        ];
                                    }

                                    $moduleSubmodules[] = [
                                        'name' => 'Stocks',
                                        'icon' => "menu-icon tf-icons ti ti-package",
                                        'slug' => 'corporate-ohc-drugs',
                                        'submenu' => $submenu
                                    ];
                                }


                                if ($hasPermission($ohcRights, 'ohc_report')) {
                                    $moduleSubmodules[] = [
                                        'name' => 'Reports',
                                        'icon' => "menu-icon tf-icons ti ti-package",
                                        'slug' => 'reports',
                                        'submenu' => [
                                            ['url' => '', 'name' => 'OHC Report', 'slug' => 'corporate-ohc-drugs-templates', 'moduleName' => $moduleName],
                                            ['url' => '', 'name' => 'Sensus Report', 'slug' => 'corporate-ohc-drugs-stock', 'moduleName' => $moduleName],
                                            ['url' => '', 'name' => 'Individual Report', 'slug' => 'corporate-ohc-drugs-stock-move', 'moduleName' => $moduleName],
                                        ]
                                    ];
                                }
                                if ($hasPermission($ohcRights, 'forms')) {
                                    $moduleSubmodules[] = [
                                        'name' => 'Forms',
                                        'icon' => "menu-icon tf-icons ti ti-package",
                                        'slug' => 'forms',
                                    ];
                                }
                            }
                            if ($moduleName === 'pre-employment') {

                            }

                            // Add all submodules to final vertical menu
                            foreach ($moduleSubmodules as $submodule) {
                                $verticalMenuData['menu'][] = $submodule;
                            }
                        }
                    }

                    $verticalMenuData = json_decode(json_encode($verticalMenuData));
                    $horizontalMenuData = json_decode(json_encode($horizontalMenuData));
                    $menuData = [$verticalMenuData, $horizontalMenuData];
                    // print_r($menuData);
                    View::share('ohcRights', $ohcRights);
                    View::share('mhcRights', $mhcRights);
                } View::share('menuData', $menuData);

            }
        });
    }
    /**
     * Get custom submenus based on module and submodule names
     *
     * @param string $moduleName
     * @param string $subModuleName
     * @return array
     */
    private function getCustomSubmenus($moduleName, $subModuleName, $mhcRights)
    {
        $customSubmenus = [];
        if ($subModuleName === 'diagnostic-assessment') {
            $permission = isset($mhcRights['diagnostic_assessment']) ? intval($mhcRights['diagnostic_assessment']) : 0;

            $customSubmenus[]  = [
                'url' => "/{$moduleName}/{$subModuleName}/assign-health-plan-list",
                'name' => 'List',
                'slug' => 'diagnostic-assessment-list',
                'moduleName' => $moduleName
            ];

            if ($permission === 2) {
                $customSubmenus[] = [
                    'url' => "/{$moduleName}/{$subModuleName}/healthplans",
                    'name' => 'Health Plans',
                    'slug' => 'diagnostic-assessment-healthplans',
                    'moduleName' => $moduleName
                ];
                $customSubmenus[] = [
                    'url' => "/{$moduleName}/{$subModuleName}/assign-health-plan",
                    'name' => 'Assign Plan',
                    'slug' => 'diagnostic-assessment-assignplan',
                    'moduleName' => $moduleName
                ];
            }
        } elseif ($subModuleName === 'health-risk-assessment') {
            $permission = isset($mhcRights['hra']) ? intval($mhcRights['hra']) : 0;

            $customSubmenus[] = [
                'url' => "/{$moduleName}/{$subModuleName}/list",
                'name' => 'List',
                'slug' => 'health-risk-assessment-list',
                'moduleName' => $moduleName
            ];

            if ($permission === 2) {
                $customSubmenus[] = [
                    'url' => "/{$moduleName}/{$subModuleName}/templates",
                    'name' => 'Templates',
                    'slug' => 'health-risk-assessment-templates',
                    'moduleName' => $moduleName
                ];
            }
        } elseif ($subModuleName === 'stocks') {
            $customSubmenus = [
                ['url' => "/{$moduleName}/{$subModuleName}/inventory", 'name' => 'Inventory', 'slug' => 'stocks-inventory', 'moduleName' => $moduleName],
                ['url' => "/{$moduleName}/{$subModuleName}/transactions", 'name' => 'Transactions', 'slug' => 'stocks-transactions', 'moduleName' => $moduleName],
            ];
        } elseif ($subModuleName === 'health-registry') {
            $customSubmenus = [
                ['url' => "/{$moduleName}/{$subModuleName}/list-registry", 'name' => 'List registry', 'slug' => 'health-registry-list-registry', 'moduleName' => $moduleName],
                ['url' => "/{$moduleName}/{$subModuleName}/add-registry", 'name' => 'Add registry', 'slug' => 'health-registry-add-registry', 'moduleName' => $moduleName],
            ];
        } elseif ($subModuleName === 'events') {
            $customSubmenus = [
                ['url' => "/{$moduleName}/{$subModuleName}/list-events", 'name' => 'List Events', 'slug' => 'events-list-events', 'moduleName' => $moduleName],
            ];
        }
        return $customSubmenus;
    }
}
