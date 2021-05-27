@php($countryCode = core()->getConfigData($name) ?? '')

<country
    :name="'{{ $fieldName }}'"
    :country_code="'{{ $countryCode }}'"
    :validations="'{{ $validations }}'"
></country>