<div class="wrap">
    <h1>{{ _wpcc('Site Tester') }}</h1>

    <div class="content">
        <form action="" id="tester-form" type="post">
{{--            {!! wp_nonce_field('wcc-site-tester', \WPCCrawler\Constants::$NONCE_NAME) !!}--}}

            {{-- ADD NONCE AND ACTION --}}
            @include('partials.form-nonce-and-action')

            <label for="site_id">{{ _wpcc('Site') }} </label>
            <select name="site_id" id="site_id">
                @foreach($sites as $site)
                    <option value="{{ $site->ID}}">{{ $site->post_title }}</option>
                @endforeach
            </select>

            <label for="test_type">{{ _wpcc('Test Type') }}</label>
            <select name="test_type" id="test_type">
                @foreach(\WPCCrawler\Factory::testController()->getGeneralTestTypes() as $testName => $testType)
                    <option value="{{ $testType }}">{{ $testName }}</option>
                @endforeach
            </select>

            <label for="test_url_part">{{ _wpcc('Test URL') }}</label>
            <input type="text" name="test_url_part" id="test_url_part" placeholder="{{ _wpcc('Full URL or URL without domain') }}">
            <button class="button" type="submit">{{ _wpcc('Test') }}</button>
        </form>

        <div id="test-results" class="hidden">

        </div>
    </div>
</div>