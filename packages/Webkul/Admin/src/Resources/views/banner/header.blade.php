<header-banner></header-banner>

@push('scripts')
    <script type="text/x-template" id="header-banner-template">
        <div class="banner header-banner">
            <div class="hovered-container">
                <img src="{{ asset('/vendor/webkul/admin/assets/images/vishal.jpeg') }}" />
            </div>

            <img class="thumbnail" src="{{ asset('/vendor/webkul/admin/assets/images/vishal.jpeg') }}" />

            <span>CRM is dedicated to the beloved memory of Vishal Kushwaha</span>

            <i class="icon close-white-icon" @click="removeBanner"></i>
        </div>
    </script>

    <script>
        Vue.component('header-banner', {
            template: '#header-banner-template',

            mounted: function () {
                $('body').addClass('top-banner');

                $('.header-banner').hover(() => {
                    $('.hovered-container').toggleClass('show');
                });
            },

            methods: {
                removeBanner: function () {
                    $('body').removeClass('top-banner');
                }
            }
        });
    </script>
@endpush