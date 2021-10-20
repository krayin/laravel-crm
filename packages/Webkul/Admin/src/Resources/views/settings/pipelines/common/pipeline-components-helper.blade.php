@push('scripts')
    <script>
        let stagesComponentMethods = {
            addStage: function () {
                this.stages.splice((this.stages.length - 2), 0, {
                    'id': 'stage_' + this.stageCount++,
                    'code': '',
                    'name': '',
                    'probability': 100
                });
            },

            removeStage: function (stage) {
                const index = this.stages.indexOf(stage);

                Vue.delete(this.stages, index);
            },

            isDragable: function (stage) {
                if (stage.code == 'new' || stage.code == 'won' || stage.code == 'lost') {
                    return false;
                }

                return true;
            },

            slugify: function (name) {
                return name
                    .toString()

                    .toLowerCase()

                    .replace(/[^\w\u0621-\u064A\u4e00-\u9fa5\u3402-\uFA6D\u3041-\u30A0\u30A0-\u31FF- ]+/g, '')

                    // replace whitespaces with dashes
                    .replace(/ +/g, '-')

                    // avoid having multiple dashes (---- translates into -)
                    .replace('![-\s]+!u', '-')

                    .trim();
            },

            checkDuplicateNames: function ({ target }) {
                let filteredStages = this.stages.filter((stage) => {
                    return stage.name == target.value;
                });

                if (filteredStages.length > 1) {
                    this.errors.add({
                        field: target.name,
                        msg: '{!! __('admin::app.settings.pipelines.duplicate-name') !!}',
                    });

                    this.$root.toggleButtonDisable(true);
                } else {
                    this.$root.toggleButtonDisable(false);
                }
            },

            isDuplicateStageNameExists: function () {
                let stageNames = this.stages.map((stage) => stage.name);

                return stageNames.some((name, index) => stageNames.indexOf(name) != index);
            },
        };

        let stagesComponentWatchers = {
            stages: function (stages) {
                if (! this.isDuplicateStageNameExists()) {
                    this.$root.toggleButtonDisable(false);
                }
            }
        };
    </script>
@endpush
