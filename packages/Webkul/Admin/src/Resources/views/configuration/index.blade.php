@extends('admin::layouts.master')

@section('page_title')
    {{ __('admin::app.configuration.title') }}
@stop

@section('content-wrapper')
    <div class="content full-page adjacent-center">
        <form method="POST" action="" @submit.prevent="onSubmit" enctype="multipart/form-data">

            <div class="page-header">

                {{ Breadcrumbs::render('configuration.slug', request('slug')) }}

                <div class="page-title">
                    <h1>
                        {{ __('admin::app.configuration.title') }}
                    </h1>
                </div>
            </div>

            <div class="page-content">
                <div class="form-container">
                    @php($configItems = system_config()->getItems())

                    <div class="nav-aside">
                        <ul class="sub-menubar">
                            @foreach ($configItems as $item)
                                <li class="sub-menu-item {{ $item->getKey() == request()->route('slug') ? 'active' : '' }}">
                                    <a href="{{ route('admin.configuration.index', $item->getKey()) }}">
                                        {{ $item->getName() }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <div class="panel" style="overflow: hidden">
                        <div class="panel-header">
                            <button type="submit" class="btn btn-md btn-primary">
                                {{ __('admin::app.configuration.save-btn-title') }}
                            </button>

                            <a href="{{ route('admin.settings.users.index') }}">
                                {{ __('admin::app.settings.users.back') }}
                            </a>
                        </div>
                    
                        @csrf()

                        <tabs>
                            @foreach ($configItems as $key => $child)
                                <tab :name="'{{ $child->getName() }}'">
                                    @foreach ($child->getChildren() as $field)

                                        {{-- @include ('admin::configuration.field-type', ['field' => $field]) --}}

                                    @endforeach
                                </tab>
                            @endforeach
                        </tabs>
                    </div>
                </div>
            </div>
        </form>
    </div>
@stop