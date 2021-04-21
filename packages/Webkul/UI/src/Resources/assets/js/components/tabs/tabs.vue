<template>
    <div>
        <div class="tabs">
            <ul>
                <li :key="index" v-for="(tab, index) in tabs" :class="{ 'active': tab.isActive }" @click="selectTab(tab)">
                    <a>{{ tab.name }}</a>
                </li>
            </ul>
        </div>

        <div class="tabs-content" v-if="! hideTabsContent">
            <slot></slot>
        </div>
    </div>
</template>

<script>
    export default {
        props: [
            'tabsCollection',
            'eventKey',
            'eventData',
            'eventValueKey'
        ],
        
        data: function () {
            return {
                tabs: [],
                hideTabsContent: false,
            }
        },

        mounted: function () {
            if (this.$children.length > 0) {
                this.tabs = this.$children;
            } else {
                this.hideTabsContent = true;
                this.tabs = this.tabsCollection;
            }
        },

        watch: {
            tabsCollection: function (newValue, oldValue) {
                this.tabs = newValue;
            }
        },

        methods: {
            selectTab: function (selectedTab) {
                this.tabs.forEach(tab => {
                    tab.isActive = (tab.name == selectedTab.name);
                });

                if (this.eventKey) {
                    var eventData = {};

                    if (this.eventData) {
                        eventData = this.eventData;
                    }

                    eventData[this.eventValueKey] = selectedTab.key;

                    EventBus.$emit(this.eventKey, eventData);
                }
            }
        }
    }
</script>