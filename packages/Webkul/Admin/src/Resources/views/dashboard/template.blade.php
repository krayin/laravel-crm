@extends('admin::layouts.master')

@section('page_title')
    {{ __('admin::app.dashboard.title') }}
@stop

@section('content-wrapper')
    <div class="content full-page">
        <div class="page-header">
            <div class="page-title">
                <h1>{{ __('admin::app.dashboard.title') }}</h1>
            </div>

            <div class="page-action">
            </div>
        </div>

        <div class="page-content">
            <div class="panel">
                <div class="panel-header">
                    Calendar
                </div>

                <div class="panel-body">
                    <vue-cal :time-step="30" hide-view-selector :disable-views="['years', 'year', 'month', 'day']" style="height: 250px" />
                </div>
            </div>

            <div class="panel">
                <div class="panel-header">
                    Buttons
                </div>

                <div class="panel-body">
                    <h2>Small</h2>
                    <button class="btn btn-sm btn-primary">Primary</button>
                    <button class="btn btn-sm btn-primary-outline">Primary Outline</button>
                    <button class="btn btn-sm btn-secondary">Secondary</button>
                    <button class="btn btn-sm btn-secondary-outline">Secondary Outline</button>
                    <button class="btn btn-sm btn-danger">Danger</button>
                    <button class="btn btn-sm btn-danger-outline">Danger Outline</button>
                    <button class="btn btn-sm btn-success">Success</button>
                    <button class="btn btn-sm btn-success-outline">Success Outline</button>
                    <button class="btn btn-sm btn-warning">Warning</button>
                    <button class="btn btn-sm btn-warning-outline">Warning Outline</button>
                    <button class="btn btn-sm btn-white">White</button>
                    <button class="btn btn-sm btn-white-outline">White Outline</button>

                    <h2>Medium</h2>
                    <button class="btn btn-md btn-primary">Primary</button>
                    <button class="btn btn-md btn-primary-outline">Primary Outline</button>
                    <button class="btn btn-md btn-secondary">Secondary</button>
                    <button class="btn btn-md btn-secondary-outline">Secondary Outline</button>
                    <button class="btn btn-md btn-danger">Danger</button>
                    <button class="btn btn-md btn-danger-outline">Danger Outline</button>
                    <button class="btn btn-md btn-success">Success</button>
                    <button class="btn btn-md btn-success-outline">Success Outline</button>
                    <button class="btn btn-md btn-warning">Warning</button>
                    <button class="btn btn-md btn-warning-outline">Warning Outline</button>
                    <button class="btn btn-md btn-white">White</button>
                    <button class="btn btn-md btn-white-outline">White Outline</button>

                    <h2>Large</h2>
                    <button class="btn btn-lg btn-primary">Primary</button>
                    <button class="btn btn-lg btn-primary-outline">Primary Outline</button>
                    <button class="btn btn-lg btn-secondary">Secondary</button>
                    <button class="btn btn-lg btn-secondary-outline">Secondary Outline</button>
                    <button class="btn btn-lg btn-danger">Danger</button>
                    <button class="btn btn-lg btn-danger-outline">Danger Outline</button>
                    <button class="btn btn-lg btn-success">Success</button>
                    <button class="btn btn-lg btn-success-outline">Success Outline</button>
                    <button class="btn btn-lg btn-warning">Warning</button>
                    <button class="btn btn-lg btn-warning-outline">Warning Outline</button>
                    <button class="btn btn-lg btn-white">White</button>
                    <button class="btn btn-lg btn-white-outline">White Outline</button>

                    <h2>Extra Large</h2>
                    <button class="btn btn-xl btn-primary">Primary</button>
                    <button class="btn btn-xl btn-primary-outline">Primary Outline</button>
                    <button class="btn btn-xl btn-secondary">Secondary</button>
                    <button class="btn btn-xl btn-secondary-outline">Secondary Outline</button>
                    <button class="btn btn-xl btn-danger">Danger</button>
                    <button class="btn btn-xl btn-danger-outline">Danger Outline</button>
                    <button class="btn btn-xl btn-success">Success</button>
                    <button class="btn btn-xl btn-success-outline">Success Outline</button>
                    <button class="btn btn-xl btn-warning">Warning</button>
                    <button class="btn btn-xl btn-warning-outline">Warning Outline</button>
                    <button class="btn btn-xl btn-white">White</button>
                    <button class="btn btn-xl btn-white-outline">White Outline</button>
                </div>
            </div>

            <div class="panel">
                <div class="panel-header">
                    Badges
                </div>

                <div class="panel-body">
                    <h2>Small</h2>
                    <span class="badge badge-sm badge-primary">Primary</span>
                    <span class="badge badge-sm badge-primary-outline">Primary Outline</span>
                    <span class="badge badge-sm badge-secondary">Secondary</span>
                    <span class="badge badge-sm badge-secondary-outline">Secondary Outline</span>
                    <span class="badge badge-sm badge-danger">Danger</span>
                    <span class="badge badge-sm badge-danger-outline">Danger Outline</span>
                    <span class="badge badge-sm badge-success">Success</span>
                    <span class="badge badge-sm badge-success-outline">Success Outline</span>
                    <span class="badge badge-sm badge-warning">Warning</span>
                    <span class="badge badge-sm badge-warning-outline">Warning Outline</span>

                    <h2>Small Pill</h2>
                    <span class="badge badge-sm badge-pill badge-primary">Primary</span>
                    <span class="badge badge-sm badge-pill badge-primary-outline">Primary Outline</span>
                    <span class="badge badge-sm badge-pill badge-secondary">Secondary</span>
                    <span class="badge badge-sm badge-pill badge-secondary-outline">Secondary Outline</span>
                    <span class="badge badge-sm badge-pill badge-danger">Danger</span>
                    <span class="badge badge-sm badge-pill badge-danger-outline">Danger Outline</span>
                    <span class="badge badge-sm badge-pill badge-success">Success</span>
                    <span class="badge badge-sm badge-pill badge-success-outline">Success Outline</span>
                    <span class="badge badge-sm badge-pill badge-warning">Warning</span>
                    <span class="badge badge-sm badge-pill badge-warning-outline">Warning Outline</span>

                    <h2>Medium</h2>
                    <span class="badge badge-md badge-primary">Primary</span>
                    <span class="badge badge-md badge-primary-outline">Primary Outline</span>
                    <span class="badge badge-md badge-secondary">Secondary</span>
                    <span class="badge badge-md badge-secondary-outline">Secondary Outline</span>
                    <span class="badge badge-md badge-danger">Danger</span>
                    <span class="badge badge-md badge-danger-outline">Danger Outline</span>
                    <span class="badge badge-md badge-success">Success</span>
                    <span class="badge badge-md badge-success-outline">Success Outline</span>
                    <span class="badge badge-md badge-warning">Warning</span>
                    <span class="badge badge-md badge-warning-outline">Warning Outline</span>

                    <h2>Medium Pill</h2>
                    <span class="badge badge-md badge-pill badge-primary">Primary</span>
                    <span class="badge badge-md badge-pill badge-primary-outline">Primary Outline</span>
                    <span class="badge badge-md badge-pill badge-secondary">Secondary</span>
                    <span class="badge badge-md badge-pill badge-secondary-outline">Secondary Outline</span>
                    <span class="badge badge-md badge-pill badge-danger">Danger</span>
                    <span class="badge badge-md badge-pill badge-danger-outline">Danger Outline</span>
                    <span class="badge badge-md badge-pill badge-success">Success</span>
                    <span class="badge badge-md badge-pill badge-success-outline">Success Outline</span>
                    <span class="badge badge-md badge-pill badge-warning">Warning</span>
                    <span class="badge badge-md badge-pill badge-warning-outline">Warning Outline</span>

                    <h2>Large</h2>
                    <span class="badge badge-lg badge-primary">Primary</span>
                    <span class="badge badge-lg badge-primary-outline">Primary Outline</span>
                    <span class="badge badge-lg badge-secondary">Secondary</span>
                    <span class="badge badge-lg badge-secondary-outline">Secondary Outline</span>
                    <span class="badge badge-lg badge-danger">Danger</span>
                    <span class="badge badge-lg badge-danger-outline">Danger Outline</span>
                    <span class="badge badge-lg badge-success">Success</span>
                    <span class="badge badge-lg badge-success-outline">Success Outline</span>
                    <span class="badge badge-lg badge-warning">Warning</span>
                    <span class="badge badge-lg badge-warning-outline">Warning Outline</span>

                    <h2>Large Pill</h2>
                    <span class="badge badge-lg badge-pill badge-primary">Primary</span>
                    <span class="badge badge-lg badge-pill badge-primary-outline">Primary Outline</span>
                    <span class="badge badge-lg badge-pill badge-secondary">Secondary</span>
                    <span class="badge badge-lg badge-pill badge-secondary-outline">Secondary Outline</span>
                    <span class="badge badge-lg badge-pill badge-danger">Danger</span>
                    <span class="badge badge-lg badge-pill badge-danger-outline">Danger Outline</span>
                    <span class="badge badge-lg badge-pill badge-success">Success</span>
                    <span class="badge badge-lg badge-pill badge-success-outline">Success Outline</span>
                    <span class="badge badge-lg badge-pill badge-warning">Warning</span>
                    <span class="badge badge-lg badge-pill badge-warning-outline">Warning Outline</span>

                    <h2>Extra Large</h2>
                    <span class="badge badge-xl badge-primary">Primary</span>
                    <span class="badge badge-xl badge-primary-outline">Primary Outline</span>
                    <span class="badge badge-xl badge-secondary">Secondary</span>
                    <span class="badge badge-xl badge-secondary-outline">Secondary Outline</span>
                    <span class="badge badge-xl badge-danger">Danger</span>
                    <span class="badge badge-xl badge-danger-outline">Danger Outline</span>
                    <span class="badge badge-xl badge-success">Success</span>
                    <span class="badge badge-xl badge-success-outline">Success Outline</span>
                    <span class="badge badge-xl badge-warning">Warning</span>
                    <span class="badge badge-xl badge-warning-outline">Warning Outline</span>

                    <h2>Extra Large Pill</h2>
                    <span class="badge badge-xl badge-pill badge-primary">Primary</span>
                    <span class="badge badge-xl badge-pill badge-primary-outline">Primary Outline</span>
                    <span class="badge badge-xl badge-pill badge-secondary">Secondary</span>
                    <span class="badge badge-xl badge-pill badge-secondary-outline">Secondary Outline</span>
                    <span class="badge badge-xl badge-pill badge-danger">Danger</span>
                    <span class="badge badge-xl badge-pill badge-danger-outline">Danger Outline</span>
                    <span class="badge badge-xl badge-pill badge-success">Success</span>
                    <span class="badge badge-xl badge-pill badge-success-outline">Success Outline</span>
                    <span class="badge badge-xl badge-pill badge-warning">Warning</span>
                    <span class="badge badge-xl badge-pill badge-warning-outline">Warning Outline</span>
                </div>
            </div>

            <div class="panel">
                <div class="panel-header">
                    Form Controls
                </div>

                <div class="panel-body">
                    <div class="form-group">
                        <label for="name" class="required">Input</label>
                        <input id="name" class="control">
                    </div>

                    <div class="form-group has-error">
                        <label for="name" class="required">Danger Input</label>
                        <input id="name" class="control">
                        <span class="control-error">This is control error</span>
                    </div>

                    <div class="form-group">
                        <label for="name">Select</label>
                        <select id="name" class="control">
                            <option value="1">Option 1</option>
                            <option value="1">Option 2</option>
                            <option value="1">Option 3</option>
                        </select>
                    </div>

                    <div class="form-group date">
                        <label for="name">Date</label>

                        <date>
                            <input type="text" class="control" placeholder="Date"/>
                        </date>
                    </div>

                    <div class="form-group time">
                        <label for="name">Time</label>

                        <time-component>
                            <input type="text" class="control" placeholder="Time"/>
                        </time-component>
                    </div>

                    <div class="form-group datetime">
                        <label for="name">Date Time</label>

                        <datetime>
                            <input type="text" class="control" placeholder="Time"/>
                        </datetime>
                    </div>

                    <div class="form-group">
                        <label for="name">Multiselect</label>
                        <select id="name" class="control" multiple>
                            <option value="1">Option 1</option>
                            <option value="1">Option 2</option>
                            <option value="1">Option 3</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="name">Textarea</label>
                        <textarea id="name" class="control"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="">Radio</label>
    
                        <span class="radio">
                            <input type="radio" id="radio2" name="radio">
                            <label class="radio-view" for="radio2"></label>
                            Radio Value
                        </span>

                        <span class="radio">
                            <input type="radio" id="radio1" name="radio" checked>
                            <label class="radio-view" for="radio1"></label>
                            Radio Value (Checked)
                        </span>
    
                        <span class="radio">
                            <input type="radio" id="radio1" name="radio" disabled="disabled">
                            <label class="radio-view" for="radio1"></label>
                            Radio Value (Disabled)
                        </span>
                    </div>

                    <div class="form-group">
                        <label for="">Checkbox</label>
    
                        <span class="checkbox">
                            <input type="checkbox" id="checkbox2" name="checkbox[]">
                            <label class="checkbox-view" for="checkbox2"></label>
                            Checkbox Value
                        </span>

                        <span class="checkbox">
                            <input type="checkbox" id="checkbox1" name="checkbox[]" checked>
                            <label class="checkbox-view" for="checkbox1"></label>
                            Checkbox Value (Checked)
                        </span>
    
                        <span class="checkbox">
                            <input type="checkbox" id="checkbox2" name="checkbox[]" disabled="disabled">
                            <label class="checkbox-view" for="checkbox2"></label>
                            Checkbox Value (Disabled)
                        </span>
                    </div>

                    <div class="form-group">
                        <label>Switch</label>
                        <label class="switch">
                            <input type="checkbox" id="new" class="control">
                            <span class="slider round"></span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="panel">
                <div class="panel-header">
                    Tabs
                </div>

                <div class="panel-body">
                    <tabs>
                        <tab name="Tab 1" :selected="true">
                            Tab 1 Content
                        </tab>

                        <tab name="Tab 2">
                            Tab 2 Content
                        </tab>

                        <tab name="Tab 3">
                            Tab 3 Content
                        </tab>
                    </tabs>
                </div>
            </div>

            <div class="panel">
                <div class="panel-header">
                    Tabs Pill
                </div>

                <div class="panel-body">
                    <tabs class="pill">
                        <tab name="Tab 1" :selected="true">
                            Tab 1 Content
                        </tab>

                        <tab name="Tab 2">
                            Tab 2 Content
                        </tab>

                        <tab name="Tab 3">
                            Tab 3 Content
                        </tab>
                    </tabs>
                </div>
            </div>

            <div class="panel">
                <div class="panel-header">
                    Tabs Group
                </div>

                <div class="panel-body">
                    <tabs class="group">
                        <tab name="Tab 1" :selected="true">
                            Tab 1 Content
                        </tab>

                        <tab name="Tab 2">
                            Tab 2 Content
                        </tab>

                        <tab name="Tab 3">
                            Tab 3 Content
                        </tab>
                    </tabs>
                </div>
            </div>

            <div class="panel">
                <div class="panel-header">
                    Accordian
                </div>

                <div class="panel-body">
                    <accordian :title="'Accordian 1'" :active="true">
                        <div slot="body">
                            Accordian 1 Content
                        </div>
                    </accordian>


                    <accordian :title="'Accordian 2'">
                        <div slot="body">
                            Accordian 2 Content
                        </div>
                    </accordian>


                    <accordian :title="'Accordian 3'">
                        <div slot="body">
                            Accordian 3 Content
                        </div>
                    </accordian>
                </div>
            </div>

            <div class="panel">
                <div class="panel-header">
                    Alert
                </div>
                
                <div class="panel-body">

                    <div class="alert-wrapper" style="position: relative">
                        <div class="alert success">
                            <span class="icon circle-check-white-icon"></span>
                            <p>Success alert message.</p>
                            <span class="icon close-white-icon"></span>
                        </div>

                        <div class="alert warning">
                            <span class="icon circle-info-white-icon"></span>
                            <p>Warning alert message.</p>
                            <span class="icon close-white-icon"></span>
                        </div>

                        <div class="alert error">
                            <span class="icon circle-close-white-icon"></span>
                            <p>Error alert message.</p>
                            <span class="icon close-white-icon"></span>
                        </div>

                        <div class="alert info">
                            <span class="icon circle-info-white-icon"></span>
                            <p>Info alert message.</p>
                            <span class="icon close-white-icon"></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="panel">
                <div class="panel-header">
                    Modal
                </div>
                
                <div class="panel-body">
                    <button class="btn btn-primary btn-lg" @click="openModal('modelId')">Open Modal</button>

                    <modal id="modelId" :is-open="modalIds.modelId">
                        <h3 slot="header-title">Modal Title</h3>
                        
                        <div slot="header-actions">
                            <button class="btn btn-sm btn-secondary-outline" @click="closeModal('modelId')">Cancel</button>

                            <button class="btn btn-sm btn-primary">Save</button>
                        </div>

                        <div slot="body">
                            Modal Content
                        </div>
                    </modal>
                </div>
            </div>

            <div class="panel">
                <div class="panel-header">
                    Table
                </div>

                <div class="panel-body">
                    <div class="table" style="margin-top: 20px">

                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Code</th>
                                </tr>
                            </thead>
                            
                            <tbody>
                                <tr class="active">
                                    <td>1</td>
                                    <td>Euro</td>
                                    <td>EUR</td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>Euro</td>
                                    <td>EUR</td>
                                </tr>
                            </tbody>
                        </table>
                        
                    </div>
                </div>
            </div>

            <div class="panel">
                <div class="panel-header">
                    Form Container
                </div>

                <div class="panel-body">
                    <div class="form-container">
                        <form>
                            <div class="form-group">
                                <label>First Name</label>

                                <input class="control" type="text" placeholder="Enter First Name" />
                            </div>

                            <div class="form-group">
                                <label>Last Name</label>

                                <input class="control" type="text" placeholder="Enter Last Name" />
                            </div>

                            <button type="button" class="badge badge-xl badge-primary">
                                Submit
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop