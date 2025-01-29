@extends('layouts.admin')
@section('content')
@can('personal_trainer_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route('admin.personal-trainers.create') }}">
                {{ trans('global.add') }} {{ trans('cruds.personalTrainer.title_singular') }}
            </a>
        </div>
    </div>
@endcan
<div class="card">
    <div class="card-header">
        {{ trans('cruds.personalTrainer.title_singular') }} {{ trans('global.list') }}
    </div>

    <div class="card-body">
        <table class=" table table-bordered table-striped table-hover ajaxTable datatable datatable-PersonalTrainer">
            <thead>
                <tr>
                    <th width="10">

                    </th>
                    <th>
                        {{ trans('cruds.personalTrainer.fields.id') }}
                    </th>
                    <th>
                        {{ trans('cruds.personalTrainer.fields.name') }}
                    </th>
                    <th>
                        {{ trans('cruds.personalTrainer.fields.email') }}
                    </th>
                    <th>
                        {{ trans('cruds.personalTrainer.fields.phone') }}
                    </th>
                    <th>
                        {{ trans('cruds.personalTrainer.fields.facebook') }}
                    </th>
                    <th>
                        {{ trans('cruds.personalTrainer.fields.instagram') }}
                    </th>
                    <th>
                        {{ trans('cruds.personalTrainer.fields.linkedin') }}
                    </th>
                    <th>
                        {{ trans('cruds.personalTrainer.fields.tiktok') }}
                    </th>
                    <th>
                        {{ trans('cruds.personalTrainer.fields.photos') }}
                    </th>
                    <th>
                        {{ trans('cruds.personalTrainer.fields.spots') }}
                    </th>
                    <th>
                        {{ trans('cruds.personalTrainer.fields.price') }}
                    </th>
                    <th>
                        {{ trans('cruds.personalTrainer.fields.certificate_type') }}
                    </th>
                    <th>
                        {{ trans('cruds.personalTrainer.fields.professional_certificate') }}
                    </th>
                    <th>
                        {{ trans('cruds.personalTrainer.fields.expiration') }}
                    </th>
                    <th>
                        {{ trans('cruds.personalTrainer.fields.user') }}
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
@can('personal_trainer_delete')
  let deleteButtonTrans = '{{ trans('global.datatables.delete') }}';
  let deleteButton = {
    text: deleteButtonTrans,
    url: "{{ route('admin.personal-trainers.massDestroy') }}",
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
    ajax: "{{ route('admin.personal-trainers.index') }}",
    columns: [
      { data: 'placeholder', name: 'placeholder' },
{ data: 'id', name: 'id' },
{ data: 'name', name: 'name' },
{ data: 'email', name: 'email' },
{ data: 'phone', name: 'phone' },
{ data: 'facebook', name: 'facebook' },
{ data: 'instagram', name: 'instagram' },
{ data: 'linkedin', name: 'linkedin' },
{ data: 'tiktok', name: 'tiktok' },
{ data: 'photos', name: 'photos', sortable: false, searchable: false },
{ data: 'spots', name: 'spots.name' },
{ data: 'price', name: 'price' },
{ data: 'certificate_type', name: 'certificate_type' },
{ data: 'professional_certificate', name: 'professional_certificate' },
{ data: 'expiration', name: 'expiration' },
{ data: 'user_name', name: 'user.name' },
{ data: 'actions', name: '{{ trans('global.actions') }}' }
    ],
    orderCellsTop: true,
    order: [[ 1, 'desc' ]],
    pageLength: 100,
  };
  let table = $('.datatable-PersonalTrainer').DataTable(dtOverrideGlobals);
  $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
      $($.fn.dataTable.tables(true)).DataTable()
          .columns.adjust();
  });
  
});

</script>
@endsection