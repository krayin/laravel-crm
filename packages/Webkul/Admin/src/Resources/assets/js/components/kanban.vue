<template>
    <kanban-board :stages="stages" :blocks="blocks" @update-block="updateBlock">
        <div v-for="stage in stages" :slot="stage" :key="`stage-${stage}`">
            <h2>
                {{ stage }}
                <span class="float-right">{{ totalCounts[stage] || 0 }}</span>
            </h2>

            <a @click="openAddModal">{{ detailText }}</a>
        </div>

        <div v-for="block in blocks" :slot="block.id" :key="`block-${block.id}`">
            <div class="lead-title">{{ block.title }}</div>

            <div class="icons">
                <a :href="block.view_url" class="icon eye-icon"></a>
                <i class="icon drag-icon"></i>
            </div>

            <div class="lead-person">
                <i class="icon avatar-dark-icon"></i>{{ block.person_name }}
            </div>
            
            <div class="lead-cost">
                <i class="icon dollar-circle-icon"></i>{{ currencySymbol }}{{ block.lead_value }}
            </div>
        </div>
    </kanban-board>
</template>

<script>
    export default {
        props: ['getUrl', 'updateUrl', 'detailText'],

        data: function () {
            return {
                stages: [],
                blocks: [],
                debounce: null,
                totalCounts: [],
                currencySymbol: '',
            }
        },

        created: function () {
            this.getData();

            queueMicrotask(() => {
                $('#search-field').keyup(({target}) => {
                    clearTimeout(this.debounce);
    
                    this.debounce = setTimeout(() => {
                        this.search(target.value)
                    }, 2000);
                });
            });
        },

        mounted: function () {
            EventBus.$on('updateKanbanFilter', this.updateFilter);
        },

        methods: {
            getData: function (searchedKeyword, filterValues) {
                this.updateURI(searchedKeyword, filterValues);

                this.$http.get(`${this.getUrl}${searchedKeyword ? `?search=${searchedKeyword}` : ''}${filterValues || ''}`)
                    .then(response => {
                        this.stages = response.data.stages;
                        this.blocks = response.data.blocks;
                        this.totalCounts = response.data.total_count;
                        this.currencySymbol = response.data.currency_symbol;
                    })
                    .catch(error => {});
            },

            updateBlock: function (id, status) {
                this.$http.post(this.updateUrl, {
                    id, status
                }).then(response => {
                    this.getData();
                    
                    this.addFlashMessages({message : response.data.message });
                })
                .catch(error => {});
            },

            search: function (searchedKeyword) {
                this.getData(searchedKeyword);
            },

            openAddModal: function () {
                $('#add-new').click();
            },

            updateFilter: function (data) {
                let href = data.key ? `?${data.key}[${data.cond}]=${data.value}` : false;

                this.getData(false, href);
            },

            updateURI: function (searchedKeyword, filterValues) {
                // const urlParams = new URLSearchParams(window.location.search);

                // urlParams.set('order', 'date');

                // window.history.pushState({path: urlParams});
            }
        }
    }
</script>