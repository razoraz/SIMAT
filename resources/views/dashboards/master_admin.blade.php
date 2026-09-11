<x-layout title="Dashboard Master Admin - SIMAT-RK">
    @section('page-title', 'Dashboard Master Admin')
    @section('breadcrumb', 'Beranda / Master Admin System')

    @include('dashboards.master_admin_partials.welcome_banner')
    @include('dashboards.master_admin_partials.metric_cards')
    @include('dashboards.master_admin_partials.chart_growth')
    @include('dashboards.master_admin_partials.chart_donut_grid')
    @include('dashboards.master_admin_partials.quick_actions_grid')
    @include('dashboards.master_admin_partials.scripts')
</x-layout>
