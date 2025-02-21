@extends('layouts.admin')
@section('content')
@can('client_data_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route('admin.client-datas.create') }}">
                {{ trans('global.add') }} {{ trans('cruds.clientData.title_singular') }}
            </a>
        </div>
    </div>
@endcan
<div class="card">
    <div class="card-header">
        {{ trans('cruds.clientData.title_singular') }} {{ trans('global.list') }}
    </div>

    <div class="card-body">
        <table class=" table table-bordered table-striped table-hover ajaxTable datatable datatable-ClientData">
            <thead>
                <tr>
                    <th width="10">

                    </th>
                    <th>
                        {{ trans('cruds.clientData.fields.id') }}
                    </th>
                    <th>
                        {{ trans('cruds.clientData.fields.client') }}
                    </th>
                    <th>
                        {{ trans('cruds.clientData.fields.age') }}
                    </th>
                    <th>
                        {{ trans('cruds.clientData.fields.gender') }}
                    </th>
                    <th>
                        {{ trans('cruds.clientData.fields.primary_objective') }}
                    </th>
                    <th>
                        {{ trans('cruds.clientData.fields.fitness_level') }}
                    </th>
                    <th>
                        {{ trans('cruds.clientData.fields.primary_type') }}
                    </th>
                    <th>
                        {{ trans('cruds.clientData.fields.training_time') }}
                    </th>
                    <th>
                        {{ trans('cruds.clientData.fields.training_frequency') }}
                    </th>
                    <th>
                        {{ trans('cruds.clientData.fields.condition') }}
                    </th>
                    <th>
                        {{ trans('cruds.clientData.fields.condition_obs') }}
                    </th>
                    <th>
                        &nbsp;
                    </th>
                </tr>
            </thead>
        </table>
    </div>
</div>



@endsection
@section('scripts')
@parent
<script>
    $(function () {
  let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)
@can('client_data_delete')
  let deleteButtonTrans = '{{ trans('global.datatables.delete') }}';
  let deleteButton = {
    text: deleteButtonTrans,
    url: "{{ route('admin.client-datas.massDestroy') }}",
    className: 'btn-danger',
    action: function (e, dt, node, config) {
      var ids = $.map(dt.rows({ selected: true }).data(), function (entry) {
          return entry.id
      });

      if (ids.length === 0) {
        alert('{{ trans('global.datatables.zero_selected') }}')

        return
      }

      if (confirm('{{ trans('global.areYouSure') }}')) {
        $.ajax({
          headers: {'x-csrf-token': _token},
          method: 'POST',
          url: config.url,
          data: { ids: ids, _method: 'DELETE' }})
          .done(function () { location.reload() })
      }
    }
  }
  dtButtons.push(deleteButton)
@endcan

  let dtOverrideGlobals = {
    buttons: dtButtons,
    processing: true,
    serverSide: true,
    retrieve: true,
    aaSorting: [],
    ajax: "{{ route('admin.client-datas.index') }}",
    columns: [
      { data: 'placeholder', name: 'placeholder' },
{ data: 'id', name: 'id' },
{ data: 'client_name', name: 'client.name' },
{ data: 'age', name: 'age' },
{ data: 'gender', name: 'gender' },
{ data: 'primary_objective', name: 'primary_objective' },
{ data: 'fitness_level', name: 'fitness_level' },
{ data: 'primary_type', name: 'primary_type' },
{ data: 'training_time', name: 'training_time' },
{ data: 'training_frequency', name: 'training_frequency' },
{ data: 'condition', name: 'condition' },
{ data: 'condition_obs', name: 'condition_obs' },
{ data: 'actions', name: '{{ trans('global.actions') }}' }
    ],
    orderCellsTop: true,
    order: [[ 1, 'desc' ]],
    pageLength: 100,
  };
  let table = $('.datatable-ClientData').DataTable(dtOverrideGlobals);
  $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
      $($.fn.dataTable.tables(true)).DataTable()
          .columns.adjust();
  });
  
});

</script>
@endsection