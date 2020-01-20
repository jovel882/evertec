@extends('adminlte::page')

@section('title', __('page.home.title'))

@section('content_header')
    <h1 class="m-0 text-dark"><i class="icon fas fa-copy"></i> Ordenes</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="card">
                        <div class="card-header bg-gradient-orange">
                            <h3 class="card-title"><i class="icon fas fa-clone"></i> Ordenes</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-sm-12">
                                    <table id="tbl_orders" class="table table-striped table-bordered" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th>Id</th>
                                                @if ($viewAny)
                                                    <th>Usuario</th>
                                                @endif
                                                <th>Estado</th>
                                                <th>Cantidad</th>
                                                <th>Total</th>
                                                <th>Creado</th>
                                                <th>Actualizado</th>
                                                <th>Eliminado</th>
                                                <th>Acciones <i class="icon fas fa-toolbox"></i></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($orders as $order)
                                                <tr>
                                                    <td>
                                                        {{$order->id}}
                                                    </td>
                                                    @if ($viewAny)
                                                        <td>
                                                            {{$order->name_user}}
                                                        </td>
                                                    @endif
                                                    <td>
                                                        {{$order->status}}
                                                    </td>
                                                    <td>
                                                        {{$order->quantity}}
                                                    </td>
                                                    <td>
                                                        {{$order->total_format}}
                                                    </td>
                                                    <td>
                                                        {{$order->created_at}}
                                                    </td>
                                                    <td>
                                                        {{$order->updated_at}}
                                                    </td>
                                                    <td>
                                                        {{$order->deleted_at}}
                                                    </td>                                                    
                                                    <td>
                                                        <a class="btn bg-warning" target="_blank" href="{{ route('orders.show', ["order" => $order->id]) }}">
                                                            <i class="fas fa-eye"></i> Ver
                                                        </a>
                                                    </td>                                                    
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>                                    
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
@push('js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#tbl_orders').DataTable({
                "processing": true,                
                "columnDefs": [ 
                    {
                        "className": "text-center", 
                        "targets": "_all"
                    },
                    {
                        "targets": [ 7 ],
                        "orderable": false,
                    },                    
                ],
                "order": [0,"desc"],
                "scrollX": true,
                "bPaginate": true,
                "bLengthChange": true,
                "bFilter": true,
                "bInfo": true,
                "bAutoWidth": true,                
            });
        });        
    </script>    
@endpush
