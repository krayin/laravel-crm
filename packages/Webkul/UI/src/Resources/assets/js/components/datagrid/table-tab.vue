<template>
    <div :class="`tabs ${tabClass ? tabClass : ''}`" :style="tabStyle">
        <ul>
            <li :key="index" v-for="(tab, index) in tabs" :class="{ 'active': tab.is_active }" @click="selectTab(tab)">
                <a>{{ tab.label }}</a>
            </li>
        </ul>
    </div>
</template>

<script>
    export default {
        props: ['tabs', 'tabKey', 'tabStyle', 'tabClass'],

        data: function() {
            return {
            }
        },

        methods: {
            selectTab(selectedTab) {
                EventBus.$emit('change_tab_data', {
                    'type': this.tabKey,
                    selectedTab: selectedTab.key,
                })
            }
        }
    }
</script>

<style lang="scss" scoped>
    .tabs {
        margin-top: 20px;
        display: inline-block;
        
        ul li {
            font-size: 15px;

            &.active {
                background: #0E90D9;
                border-radius: 22px;

                a {
                    color: white;
                    box-shadow: none;
                }
            }

            a {
                padding: 5px 15px;
            }
        }
    }
</style>