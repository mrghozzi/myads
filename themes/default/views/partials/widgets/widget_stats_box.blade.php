<div class="widget-box">
    <!-- WIDGET BOX TITLE -->
    <p class="widget-box-title">{{ $widget->name }}</p>
    <!-- /WIDGET BOX TITLE -->

    <!-- WIDGET BOX CONTENT -->
    <div class="widget-box-content">
        <div class="stats-box-slider">
            <!-- STATS BOX SLIDER CONTROLS -->
            <div class="stats-box-slider-controls">
                <!-- STATS BOX SLIDER CONTROLS TITLE -->
                <p class="stats-box-slider-controls-title">{{ $widget->name }}</p>
                <!-- /STATS BOX SLIDER CONTROLS TITLE -->
    
                <!-- SLIDER CONTROLS -->
                <div id="stats-box-slider-controls" class="slider-controls">
                    <!-- SLIDER CONTROL -->
                    <div class="slider-control negative left">
                        <!-- SLIDER CONTROL ICON -->
                        <svg class="slider-control-icon icon-small-arrow">
                            <use xlink:href="#svg-small-arrow"></use>
                        </svg>
                        <!-- /SLIDER CONTROL ICON -->
                    </div>
                    <!-- /SLIDER CONTROL -->
    
                    <!-- SLIDER CONTROL -->
                    <div class="slider-control negative right">
                        <!-- SLIDER CONTROL ICON -->
                        <svg class="slider-control-icon icon-small-arrow">
                            <use xlink:href="#svg-small-arrow"></use>
                        </svg>
                        <!-- /SLIDER CONTROL ICON -->
                    </div>
                    <!-- /SLIDER CONTROL -->
                </div>
                <!-- /SLIDER CONTROLS -->
            </div>
            <!-- /STATS BOX SLIDER CONTROLS -->
    
            @php
                try {
                    $bannerViews = \Illuminate\Support\Facades\DB::table('state')->where('t_name', 'banner')->count();
                } catch (\Exception $e) {
                    $bannerViews = 0;
                }
            
                try {
                    $linkViews = \Illuminate\Support\Facades\DB::table('state')->where('t_name', 'link')->count();
                } catch (\Exception $e) {
                    $linkViews = 0;
                }
            @endphp

            <!-- STATS BOX SLIDER ITEMS -->
            <div id="stats-box-slider-items" class="stats-box-slider-items">
                <!-- STATS BOX -->
                <div class="stats-box stat-profile-views">
                    <!-- STATS BOX VALUE WRAP -->
                    <div class="stats-box-value-wrap">
                        <!-- STATS BOX VALUE -->
                        <p class="stats-box-value">{{ \Illuminate\Support\Facades\DB::table('banner')->count() }}</p>
                        <!-- /STATS BOX VALUE -->
    
                        <!-- STATS BOX DIFF -->
                        <div class="stats-box-diff">
                            <!-- banner BOX DIFF ICON -->
                            <div>
                                <!-- ICON PLUS SMALL -->
                                <!-- ICON STATUS -->
                                <svg class="icon-status" style="fill: #ff5384;" >
                                    <use xlink:href="#svg-status"></use>
                                </svg>
                                <!-- /ICON STATUS -->
                                <!-- /ICON PLUS SMALL -->
                            </div>
                            <!-- /banner BOX DIFF ICON -->
    
                            <!-- STATS BOX DIFF VALUE -->
                            <p class="stats-box-diff-value">{{ __('messages.bannads') }}</p>
                            <!-- /STATS BOX DIFF VALUE -->
                        </div>
                        <!-- /STATS BOX DIFF -->
                    </div>
                    <!-- /STATS BOX VALUE WRAP -->
    
                    <!-- STATS BOX TITLE -->
                    <p class="stats-box-title">{{ __('messages.Views') }}</p>
                    <!-- /STATS BOX TITLE -->
    
                    <!-- STATS BOX TEXT -->
                    <p class="stats-box-text">{{ $bannerViews }}</p>
                    <!-- /STATS BOX TEXT -->
                </div>
                <!-- /STATS BOX -->
    
                <!-- STATS BOX -->
                <div class="stats-box stat-posts-created">
                    <!-- STATS BOX VALUE WRAP -->
                    <div class="stats-box-value-wrap">
                        <!-- STATS BOX VALUE -->
                        <p class="stats-box-value">{{ \Illuminate\Support\Facades\DB::table('link')->count() }}</p>
                        <!-- /STATS BOX VALUE -->
    
                        <!-- STATS BOX DIFF -->
                        <div class="stats-box-diff">
                            <!-- link BOX DIFF ICON -->
                            <div>
                                <!-- ICON PLUS SMALL -->
                                <!-- ICON STATUS -->
                                <!-- ICON EVENTS WEEKLY -->
                                <svg class="icon-events-weekly"  style="fill: #ff5384;" >
                                    <use xlink:href="#svg-events-weekly"></use>
                                </svg>
                                <!-- /ICON EVENTS WEEKLY -->
                                <!-- /ICON STATUS -->
                                <!-- /ICON PLUS SMALL -->
                            </div>
                            <!-- /link BOX DIFF ICON -->
                             <p class="stats-box-diff-value">{{ __('messages.linkads') }}</p>
                        </div>
                         <!-- /STATS BOX DIFF -->
                    </div>
                    <!-- /STATS BOX VALUE WRAP -->
    
                    <!-- STATS BOX TITLE -->
                    <p class="stats-box-title">{{ __('messages.Views') }}</p>
                    <!-- /STATS BOX TITLE -->
    
                    <!-- STATS BOX TEXT -->
                    <p class="stats-box-text">{{ $linkViews }}</p>
                    <!-- /STATS BOX TEXT -->
                </div>
                <!-- /STATS BOX -->
            </div>
            <!-- /STATS BOX SLIDER ITEMS -->
        </div>
    </div>
    <!-- /WIDGET BOX CONTENT -->
</div>
