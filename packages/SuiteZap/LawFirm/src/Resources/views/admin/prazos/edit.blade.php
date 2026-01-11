<x-admin::layouts>
    <x-slot:title>
        @lang('lawfirm::app.processos.edit-title')
    </x-slot>

    <div class="flex flex-col gap-4">
        <!-- Header -->
        <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
            <div class="flex flex-col gap-2">
                <div class="flex cursor-pointer items-center gap-2">
                    <x-admin::breadcrumbs name="lawfirm.processos.edit" :entity="$prazo->processo" />
                </div>
                <div class="text-xl font-bold dark:text-white">
                    Editar Prazo: <span class="text-gray-500">{{ $prazo->titulo }}</span>
                </div>
            </div>
        </div>

        <!-- Formulario -->
        <x-admin::form :action="route('admin.prazos.update', $prazo->id)" method="PUT">
            <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                <div class="grid gap-4">
                    <!-- Título -->
                    <x-admin::form.control-group>
                        <x-admin::form.control-group.label class="required">
                            @lang('lawfirm::app.prazos.form.title')
                        </x-admin::form.control-group.label>
                        <x-admin::form.control-group.control
                            type="text"
                            name="titulo"
                            :value="old('titulo', $prazo->titulo)"
                            rules="required"
                            :label="trans('lawfirm::app.prazos.form.title')"
                        />
                        <x-admin::form.control-group.error control-name="titulo" />
                    </x-admin::form.control-group>

                    <div class="flex gap-4">
                        <!-- Vencimento -->
                        <x-admin::form.control-group class="w-1/2">
                            <x-admin::form.control-group.label class="required">
                                @lang('lawfirm::app.prazos.form.date')
                            </x-admin::form.control-group.label>
                            <x-admin::form.control-group.control
                                type="datetime"
                                name="data_vencimento"
                                :value="old('data_vencimento', $prazo->data_vencimento)"
                                rules="required"
                                :label="trans('lawfirm::app.prazos.form.date')"
                            />
                            <x-admin::form.control-group.error control-name="data_vencimento" />
                        </x-admin::form.control-group>

                        <!-- Tipo -->
                        <x-admin::form.control-group class="w-1/2">
                            <x-admin::form.control-group.label class="required">
                                @lang('lawfirm::app.prazos.form.type')
                            </x-admin::form.control-group.label>
                            <x-admin::form.control-group.control
                                type="select"
                                name="tipo"
                                :value="old('tipo', $prazo->tipo)"
                                rules="required"
                                :label="trans('lawfirm::app.prazos.form.type')"
                            >
                                <option value="comum" {{ $prazo->tipo == 'comum' ? 'selected' : '' }}>@lang('lawfirm::app.prazos.common')</option>
                                <option value="fatal" {{ $prazo->tipo == 'fatal' ? 'selected' : '' }}>@lang('lawfirm::app.prazos.fatal')</option>
                            </x-admin::form.control-group.control>
                            <x-admin::form.control-group.error control-name="tipo" />
                        </x-admin::form.control-group>
                    </div>

                    <!-- Status (Novo campo de edição) -->
                    <x-admin::form.control-group>
                        <x-admin::form.control-group.label class="required">
                            @lang('lawfirm::app.prazos.status')
                        </x-admin::form.control-group.label>
                        <x-admin::form.control-group.control
                            type="select"
                            name="status"
                            :value="old('status', $prazo->status)"
                            rules="required"
                            :label="trans('lawfirm::app.prazos.status')"
                        >
                            <option value="pendente" {{ $prazo->status == 'pendente' ? 'selected' : '' }}>@lang('lawfirm::app.prazos.status-pending')</option>
                            <option value="concluido" {{ $prazo->status == 'concluido' ? 'selected' : '' }}>@lang('lawfirm::app.prazos.status-done')</option>
                        </x-admin::form.control-group.control>
                        <x-admin::form.control-group.error control-name="status" />
                    </x-admin::form.control-group>

                    <!-- Descrição -->
                    <x-admin::form.control-group>
                        <x-admin::form.control-group.label>
                            @lang('lawfirm::app.prazos.form.description')
                        </x-admin::form.control-group.label>
                        <x-admin::form.control-group.control
                            type="textarea"
                            name="descricao"
                            :value="old('descricao', $prazo->descricao)"
                            :label="trans('lawfirm::app.prazos.form.description')"
                            class="h-[150px]"
                        />
                    </x-admin::form.control-group>
                </div>
                
                <div class="flex items-center justify-end gap-x-2.5 mt-4">
                    <a
                        href="{{ route('admin.processos.edit', $prazo->processo_id) }}"
                        class="transparent-button"
                    >
                        @lang('lawfirm::app.prazos.cancel')
                    </a>
                    <button
                        type="submit"
                        class="primary-button"
                    >
                        @lang('lawfirm::app.prazos.save')
                    </button>
                </div>
            </div>
        </x-admin::form>
    </div>
</x-admin::layouts>
