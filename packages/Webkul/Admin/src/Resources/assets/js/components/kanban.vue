<template>
    <kanban-board :stages="stages" :blocks="blocks" @update-block="updateBlock">
        <div v-for="stage in stages" :slot="stage" :key="`stage-${stage}`">
            <h2>
                {{ stage }}
                <span class="float-right">{{ totalCounts[stage] || 0 }}</span>
            </h2>

            <a :href="createUrl + '?lead_stage_id=' + openAddModal(stage, stagesId)">{{ detailText }}</a>
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
        props: ['getUrl', 'createUrl', 'updateUrl', 'detailText', 'noDataText'],

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
                this.$root.pageLoaded = false;
                this.updateURI(searchedKeyword, filterValues);

                this.$http.get(`${this.getUrl}${searchedKeyword ? `?search=${searchedKeyword}` : ''}${filterValues || ''}`)
                    .then(response => {
                        this.$root.pageLoaded = true;

                        this.blocks = response.data.blocks;
                        this.stagesId = response.data.stages;
                        this.totalCounts = response.data.total_count;
                        this.stages = Object.values(response.data.stages);
                        this.currencySymbol = response.data.currency_symbol;

                        setTimeout(() => {
                            this.toggleEmptyStateIcon();
                        })
                    })
                    .catch(error => {
                        this.$root.pageLoaded = true;
                    });
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
                var leadStageId = 0;

                for (let stageId in stagesId) {
                    if (stagesId[stageId] == stage) {
                        leadStageId = stageId;

                        break;
                    }
                }

                return leadStageId;
            },

            updateFilter: function (data) {
                let href = data.key ? `?${data.key}[${data.cond}]=${data.value}` : false;

                this.getData(false, href);
            },

            updateURI: function (searchedKeyword, filterValues) {
                // const urlParams = new URLSearchParams(window.location.search);

                // window.history.pushState({path: urlParams}, '');
            },

            toggleEmptyStateIcon: function () {
                $('.empty-icon-container').remove();

                $('ul.drag-inner-list').each((index, item) => {
                    if (! $(item).children('.drag-item').length) {
                        $(item).append(`
                            <div class='empty-icon-container disable-drag'>
                                <div class="icon-text-container">
                                    <i class='icon empty-kanban-icon'></i>
                                    <span>${this.noDataText}</span>
                                </div>
                            </div>
                        `)
                    }
                });
            }
        }
    }
</script>