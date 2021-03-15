<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}" />
        <link rel="icon" href="/images/devbox_fav.png" type="image/png" sizes="16x16">
        <title>DevBox</title>
        <style>
            [v-cloak] {
                display: none !important;
            }
        </style>
        <link rel="stylesheet" href="{{ mix('css/app.css') }}">
        <link rel="stylesheet" href="{{ mix('css/custom.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/fontawesome/css/all.min.css') }}">
    </head>
    <body>
        <div id="app" v-cloak>
            <page-loader :is-Page-Loading="isPageLoadingActive"></page-loader>
            <header-navmenu class="mb-3"></header-navmenu>
            <div class="container mb-5">
                <div class="row">
                    <div class="col-md-3">
                        <myoption-navmenu class="mb-3"
                            :post-box-count="sidebarCount.postBoxCount"
                            :category-count="sidebarCount.categoryCount"
                            :keyword-count="sidebarCount.keywordCount"
                            :task-count="sidebarCount.taskCount"
                            :note-count="sidebarCount.noteCount" 
                            :blog-count="sidebarCount.blogCount"></myoption-navmenu>
                    </div>
                    <div class="col-md-9">
                        <router-view :key="$route.path"></router-view>
                        <!-- <router-view name="boxpost"></router-view> -->
                    </div>
                </div>
            </div>
        </div>
        <script src="{{ mix('js/app.js') }}"></script>
        <script src="{{ mix('js/custom.js') }}"></script>
    </body>
</html>
