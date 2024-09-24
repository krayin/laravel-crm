<v-avatar {{ $attributes }}>
    <div class="shimmer h-9 w-9 rounded-full"></div>
</v-avatar>

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-avatar-template"
    >
        <div
            class="flex h-9 min-w-9 items-center justify-center rounded-full text-xs font-medium"
            :class="colorClasses()"
        >
            @{{ name.split(' ').slice(0, 2).map(word => word[0].toUpperCase()).join('') }}
        </div>
    </script>

    <script type="module">
        app.component('v-avatar', {
            template: '#v-avatar-template',

            props: {
                name: {
                    type: String,
                    default: '',
                },
            },

            data() {
                return {
                    colors: {
                        background: [
                            'bg-yellow-200',
                            'bg-red-200',
                            'bg-lime-200',
                            'bg-blue-200',
                            'bg-orange-200',
                            'bg-green-200',
                            'bg-pink-200',
                            'bg-yellow-400'
                        ],

                        text: [
                            'text-yellow-900',
                            'text-red-900',
                            'text-lime-900',
                            'text-blue-900',
                            'text-orange-900',
                            'text-green-900',
                            'text-pink-900',
                            'text-yellow-900',
                        ],
                    },
                }
            },

            methods: {
                colorClasses() {
                    let index = Math.floor(Math.random() * this.colors.background.length);

                    return [
                        this.colors.background[index],
                        this.colors.text[index],
                    ];
                },
            },
        });
    </script>
@endPushOnce