<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}" />
        <link rel="icon" href="/images/devbox_fav.png" type="image/png" sizes="16x16">
        <title>DevBox</title>
        <style>
            [v-cloak] .v-cloak--block {
                display: block!important;
            }

            [v-cloak] .v-cloak--inline {
                display: inline!important;
            }

            [v-cloak] .v-cloak--inlineBlock {
                display: inline-block!important;
            }

            [v-cloak] .v-cloak--hidden {
                display: none!important;
            }

            [v-cloak] .v-cloak--invisible {
                visibility: hidden!important;
            }

            .v-cloak--block,
            .v-cloak--inline,
            .v-cloak--inlineBlock {
                display: none!important;
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
                @if(Auth::check())
                <div class="row" v-show="$root.isUserLoggedIn">
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
                @else
                <div class="row" v-show="!$root.isUserLoggedIn">
                    <div class="col-md-3"></div>
                    <div class="col-md-6">
                        <router-view :key="$route.path"></router-view>
                    </div>
                    <div class="col-md-3"></div>
                </div>
                @endif
            </div>
        </div>
        <script src="{{ mix('js/app.js') }}"></script>
        <script src="{{ mix('js/custom.js') }}"></script>
    </body>
</html>
