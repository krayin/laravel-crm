<template>
    <div>
        <div class="tabs">
            <ul>
                <li :key="index" v-for="(tab, index) in tabs" :class="{ 'active': tab.isActive,  'has-error': tab.hasError}" @click="selectTab(tab)">
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

        inject: ['$validator'],
        
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

            this.selectDefaultTab();
        },

        watch: {
            tabsCollection: function (newValue, oldValue) {
                this.tabs = newValue;
            }
        },

        methods: {
            selectDefaultTab: function () {
                var hasActiveTab = false;

                this.tabs.forEach(tab => {
                    if (tab.isActive) {
                        hasActiveTab = true;
                    }
                });

                if (! hasActiveTab) {
                    this.tabs[0].isActive = true;
                }
            },

            selectTab: function (selectedTab) {
                if(this.$children.length > 0) {
                   this.tabs.forEach((tab) => {
                        tab.isActive = (selectedTab.name == tab.name)
                   }); 
                } else {
                    this.tabs.forEach((tab) => {
                        if (tab.isActive) {
                            this.$parent.filters.forEach((filter, index) => {
                                if (
                                filter.val == tab.key ||
                                (selectedTab.key != "custom" && filter.column == "duration")
                                ) {
                                    delete this.$parent.filters[index];
                                }
                            });
                        }
                    });
                }
                                
                if (! this.eventKey) {
                    return;
                }

                var eventData = this.eventData ? this.eventData : {};

                eventData[this.eventValueKey] = selectedTab.key;

                EventBus.$emit(this.eventKey, eventData);
            }
        }
    }
</script>