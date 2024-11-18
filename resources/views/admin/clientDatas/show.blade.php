@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.clientData.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.client-datas.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.clientData.fields.id') }}
                        </th>
                        <td>
                            {{ $clientData->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.clientData.fields.client') }}
                        </th>
                        <td>
                            {{ $clientData->client->name ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.clientData.fields.age') }}
                        </th>
                        <td>
                            {{ $clientData->age }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.clientData.fields.gender') }}
                        </th>
                        <td>
                            {{ App\Models\ClientData::GENDER_RADIO[$clientData->gender] ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.clientData.fields.primary_objective') }}
                        </th>
                        <td>
                            {{ App\Models\ClientData::PRIMARY_OBJECTIVE_RADIO[$clientData->primary_objective] ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.clientData.fields.fitness_level') }}
                        </th>
                        <td>
                            {{ App\Models\ClientData::FITNESS_LEVEL_RADIO[$clientData->fitness_level] ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.clientData.fields.condition') }}
                        </th>
                        <td>
                            {{ App\Models\ClientData::CONDITION_RADIO[$clientData->condition] ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.clientData.fields.condition_obs') }}
                        </th>
                        <td>
                            {{ $clientData->condition_obs }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.client-datas.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection