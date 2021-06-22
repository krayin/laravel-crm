{{-- <header-banner></header-banner> --}}

@push('scripts')
    <script type="text/x-template" id="header-banner-template">
        <div class="banner">
            Hello

            <i class="icon close-white-icon" @click="removeBanner"></i>
        </div>
    </script>

    <script>
        Vue.component('header-banner', {
            template: '#header-banner-template',

            mounted: function () {
                $('body').addClass('top-banner');
            },

            methods: {
                removeBanner: function () {
                    $('body').removeClass('top-banner');
                }
            }
        });
    </script>
@endpush