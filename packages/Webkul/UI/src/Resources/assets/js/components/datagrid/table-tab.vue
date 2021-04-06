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
                    'selectedTab': selectedTab.key,
                })
            }
        }
    }
</script>
