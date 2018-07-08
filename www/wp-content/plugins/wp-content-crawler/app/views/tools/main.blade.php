<div class="wrap container-tools">
    <h1>{{ _wpcc('Tools') }}</h1>
    <br>

    <div class="content">
        <div class="details">
            @include('tools/crawl-post')
        </div>
        <div class="details">
            @include('tools/recrawl-post')
        </div>
        <div class="details">
            @include('tools/clear-urls')
        </div>
        <div class="details">
            @include('tools/unlock-urls')
        </div>

        <?php

        /**
         * Fires at the end of closing tag of the content area in Tools page
         *
         * @since 1.6.3
         */
        do_action('wpcc/view/tools');

        ?>

    </div>

</div>