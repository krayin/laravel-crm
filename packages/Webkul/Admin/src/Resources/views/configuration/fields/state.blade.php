@php($stateCode = core()->getConfigData($name) ?? '')

<state
    :name="'{{ $fieldName }}'"
    :state_code="'{{ $stateCode }}'"
    :validations="'{{ $validations }}'"
></state>