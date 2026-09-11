<x-layout title="Dashboard Admin Operasional - SIMAT-RK">
    @section('page-title', 'Dashboard Admin')
    @section('breadcrumb', 'Beranda / Admin Operasional')

    @include('dashboards.admin_partials.welcome_banner')
    @include('dashboards.admin_partials.metric_cards')
    @include('dashboards.admin_partials.chart_growth')
    @include('dashboards.admin_partials.chart_donut_grid')
    @include('dashboards.admin_partials.quick_actions_grid')
    @include('dashboards.admin_partials.scripts')
</x-layout>
