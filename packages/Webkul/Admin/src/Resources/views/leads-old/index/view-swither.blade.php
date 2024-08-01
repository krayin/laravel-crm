<div class="switch-pipeline-container">
    <div class="form-group">
        @php
            $pipelineRepository = app('Webkul\Lead\Repositories\PipelineRepository');

            if (! $pipelineId = request('pipeline_id')) {
                $pipelineId = $pipelineRepository->getDefaultPipeline()->id;
            }
        @endphp

        <select class="control" onchange="window.location.href = this.value">
            @foreach (app('Webkul\Lead\Repositories\PipelineRepository')->all() as $pipeline)
                @php
                    if ($viewType = request('view_type')) {
                        $url = route('admin.leads.index', [
                            'pipeline_id' => $pipeline->id,
                            'view_type'   => $viewType
                        ]);
                    } else {
                        $url = route('admin.leads.index', ['pipeline_id' => $pipeline->id]);
                    }
                @endphp

                <option value="{{ $url }}" {{ $pipelineId == $pipeline->id ? 'selected' : '' }}>
                    {{ $pipeline->name }}
                </option> 
            @endforeach
        </select>
    </div>
</div>

<div class="switch-view-container">
    @if (request('view_type'))
        <a href="{{ route('admin.leads.index') }}" class="icon-container">
            <i class="icon layout-column-line-icon"></i>
        </a>

        <a class="icon-container active">
            <i class="icon table-line-active-icon"></i>
        </a>
    @else
        <a  class="icon-container active">
            <i class="icon layout-column-line-active-icon"></i>
        </a>

        <a href="{{ route('admin.leads.index', ['view_type' => 'table']) }}" class="icon-container">
            <i class="icon table-line-icon"></i>
        </a>
    @endif
</div>