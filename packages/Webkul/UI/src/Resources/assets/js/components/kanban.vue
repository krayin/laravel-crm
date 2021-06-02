<template>
    <kanban-board :stages="stages" :blocks="blocks" @update-block="updateBlock">
        <div v-for="stage in stages" :slot="stage" :key="stage">
            <h2>{{ stage }}</h2>
        </div>

        <div v-for="block in blocks" :slot="block.id" :key="block.id">
            <div class="lead-title">
                {{ block.title }}
            </div>

            <div class="lead-person">
                <i class="icon avatar-dark-icon"></i> {{ block.person_name }}
            </div>
            
            <div class="lead-cost">
                <i class="icon dollar-circle-icon"></i> {{ block.lead_value }}
            </div>
        </div>
    </kanban-board>
</template>

<script>
    export default {
        props: ['getUrl', 'updateUrl'],

        data: function () {
            return {
                stages: [],
                blocks: [],
            }
        },

        created: function () {
            this.getData();
        },

        methods: {
            getData: function () {
                this.$http.get(this.getUrl)
                    .then(response => {
                        this.stages = response.data.stages;
                        this.blocks = response.data.blocks;
                    })
                    .catch(error => {});
            },

            updateBlock: function (id, status) {
                this.$http.post(this.updateUrl, {
                    id, status
                }).then(response => {
                    this.addFlashMessages({message : response.data.message });
                })
                .catch(error => {});
            },
        }
    }
</script>

<style lang="scss">
    @import '../../sass/kanban.scss';
</style>