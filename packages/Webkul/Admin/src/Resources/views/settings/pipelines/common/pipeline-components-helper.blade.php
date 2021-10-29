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

                this.removeUniqueNameErrors();
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

            extendValidator: function () {
                this.$validator.extend('unique_name', {
                    getMessage: (field) => '{!! __('admin::app.settings.pipelines.duplicate-name') !!}',

                    validate: (value) => {
                        let filteredStages = this.stages.filter((stage) => {
                            return stage.name.toLowerCase() == value.toLowerCase();
                        });

                        if (filteredStages.length > 1) {
                            return false;
                        }

                        this.removeUniqueNameErrors();

                        return true;
                    }
                });
            },

            isDuplicateStageNameExists: function () {
                let stageNames = this.stages.map((stage) => stage.name);

                return stageNames.some((name, index) => stageNames.indexOf(name) != index);
            },

            removeUniqueNameErrors: function () {
                if (! this.isDuplicateStageNameExists()) {
                    this.errors
                        .items
                        .filter(error => error.rule === 'unique_name')
                        .map(error => error.id)
                        .forEach((id) => {
                            this.errors.removeById(id);
                        });
                }
            }
        };
    </script>
@endpush
