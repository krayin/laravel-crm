<template>
    <kanban-board :stages="stages" :blocks="blocks" @update-block="updateBlock">
        <div v-for="stage in stages" :slot="stage" :key="`stage-${stage}`">
            <h2>
                {{ stage }}
                <span class="float-right">{{ totalCounts[stage] || 0 }}</span>
            </h2>

            <a @click="openAddModal(stage, stagesId)">{{ detailText }}</a>
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
                stagesId: {},
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
                        this.blocks = response.data.blocks;
                        this.stagesId = response.data.stages;
                        this.totalCounts = response.data.total_count;
                        this.stages = Object.values(response.data.stages);
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

            openAddModal: function (stage, stagesId) {
                $('#add-new').click();

                setTimeout(() => {
                    for (let stageId in stagesId) {
                        if (stagesId[stageId] == stage) {
                            $('#lead_stage_id').val(stageId);

                            break;
                        }
                    }
                });
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