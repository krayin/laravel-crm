<template>
    <div class="form-group input-group color-picker" ref="colorpicker">
        <input
            type="text"
            :name="name"
            class="control"
            v-model="colorValue"
            @focus="showPicker()"
            @input="updateFromInput"
            data-input
        />

        <div class="input-group-append color-picker-container">
            <div class="control" @click="togglePicker()">
                <span
                    class="current-color"
                    :style="'background-color: ' + colorValue"
                ></span>

                <chrome-picker
                    :value="colors"
                    @input="updateFromPicker"
                    v-if="displayPicker" 
                />
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            name: String,

            value: String,
        },

        inject: ['$validator'],

        data() {
            return {
                colors: {
                    hex: '#000000',
                },

                colorValue: '',

                displayPicker: false,
            }
        },

        mounted() {
            this.setColor(this.value || '#000000');
        },

        methods: {
            setColor(color) {
                this.updateColors(color);

                this.colorValue = color;
            },

            updateColors(color) {
                if (color.slice(0, 1) == '#') {
                    this.colors = {
                        hex: color
                    };
                } else if (color.slice(0, 4) == 'rgba') {
                    var rgba = color.replace(/^rgba?\(|\s+|\)$/g,'').split(','),
                        hex = '#' + ((1 << 24) + (parseInt(rgba[0]) << 16) + (parseInt(rgba[1]) << 8) + parseInt(rgba[2])).toString(16).slice(1);

                    this.colors = {
                        hex: hex,
                        a: rgba[3],
                    }
                }
            },

            showPicker() {
                document.addEventListener('click', this.documentClick);

                this.displayPicker = true;
            },

            hidePicker() {
                document.removeEventListener('click', this.documentClick);

                this.displayPicker = false;
            },

            togglePicker() {
                this.displayPicker ? this.hidePicker() : this.showPicker();
            },

            updateFromInput() {
                this.updateColors(this.colorValue);
            },

            updateFromPicker(color) {
                this.colors = color;

                if (color.rgba.a == 1) {
                    this.colorValue = color.hex;
                } else {
                    this.colorValue = 'rgba(' + color.rgba.r + ', ' + color.rgba.g + ', ' + color.rgba.b + ', ' + color.rgba.a + ')';
                }
            },

            documentClick(e) {
                var el = this.$refs.colorpicker,
                    target = e.target;

                if (el !== target && ! el.contains(target)) {
                    this.hidePicker()
                }
            }
        },

        watch: {
            colorValue(val) {
                if (val) {
                    this.updateColors(val);

                    this.$emit('input', val);
                }
            }
        },
    }
</script>

<style>
    .color-picker-container {
        position: relative;
    }

    .color-picker-container .control {
        cursor: pointer;
    }
    
    .vc-chrome {
        position: absolute;
        top: 55px;
        right: 0;
        z-index: 9;
    }
    
    .current-color {
        display: inline-block;
        vertical-align: middle;
        width: 16px;
        height: 16px;
        background-color: #000;
        cursor: pointer;
    }
</style>
