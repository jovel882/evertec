@extends('adminlte::page')

@section('title', __('page.home.title'))

@section('content_header')
    <h1 class="m-0 text-dark"><i class="icon fas fa-file-alt"></i> Orden</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    @if(count( $errors ) > 0)
                        <div class="alert alert-danger alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-fw fa-times-circle"></i></button>
                            @foreach ($errors->all() as $error)
                                <div style="margin-bottom: 1em;">
                                    <i class="fa fa-fw fa-exclamation-circle"></i> {{ $error }}
                                </div>
                            @endforeach
                        </div>                                                                    
                    @endif
                    @if(session('update'))
                        @php
                            switch (session('update')['status']) {
                                case 'PAYED':
                                    $class = 'success';
                                    $icon = 'thumbs-up';
                                    break;
                                case 'PENDING':
                                    $class = 'warning';
                                    $icon = 'hourglass-half';
                                    break;
                                case 'REJECTED':
                                    $class = 'danger';
                                    $icon = 'times-circle';
                                    break;                                
                                case 'REFUNDED':
                                    $class = 'danger';
                                    $icon = 'history';
                                    break;                                
                                default:
                                    $class = 'info';
                                    $icon = 'plus-circle';
                                    break;
                            }
                        @endphp
                        <div class="alert alert-{{ $class }} alert-dismissible"> 
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-fw fa-times-circle"></i></button>
                            <h5>{{ session('update')['status'] }}</h5>
                            <i class="fa fa-fw fa-{{ $icon }}"></i> El estado de esta transaccion es {{ session('update')['status'] }}.
                            <div style="margin-bottom: 1em;">
                                Esta es la razon:  {{ session('update')['message'] }}
                            </div> 
                        </div>    
                    @endif                                                            
                    <div class="card">
                        <div class="card-header bg-gradient-orange">
                            <h3 class="card-title"><i class="icon fas fa-receipt"></i> Detalle de la Orden</h3>
                        </div>                        
                        <div class="card-body">
                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <i class="fa fa-fw fa-tags"></i> <label>Cantidad:</label>
                                        <span class="text-orange">{{ $order->quantity }}</span>
                                    </div>                                
                                </div>    
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <i class="fa fa-fw fa-dollar-sign"></i> <label>Total:</label>
                                        <span class="text-orange">{{ $order->total_format }}</span>
                                    </div>                                
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <i class="fa fa-fw fa-eye"></i> <label>Estado:</label>
                                        <span class="text-orange">{{ $order->status }}</span>
                                    </div>                                
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <i class="fa fa-fw fa-calendar-plus"></i> <label>Creada:</label>
                                        <span class="text-orange">{{ $order->created_at }}</span>
                                    </div>                                
                                </div>    
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <i class="fa fa-fw fa-calendar-check"></i> <label>Actualizada:</label>
                                        <span class="text-orange">{{ $order->updated_at }}</span>
                                    </div>                                
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <i class="fa fa-fw fa-calendar-minus"></i> <label>Eliminada:</label>
                                        <span class="text-orange">{{ $order->deleted_at }}</span>
                                    </div>                                
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <a class="btn bg-secondary" href="{{ route('orders.index') }}">
                                <i class="fas fa-chevron-circle-left"></i> Volver
                            </a>
                            @if(!$viewAny)
                                @switch($order->status)
                                    @case("CREATED")
                                        @if ($order->transactions->count() == 0 || $order->transactions->first()->current_status != "PENDING")
                                            <a href="{{ route('orders.pay',["order" => $order->id]) }}" class="btn bg-warning float-right">
                                                <i class="fa fa-fw fa-credit-card"></i> Pagar
                                            </a>
                                        @else
                                            <div class="alert alert-warning float-right">
                                                <h5><i class="icon fas fa-exclamation-triangle"></i> Esta orden tiene una transaccion pendientes, hasta que no se resuelva este no se puede realizar otro intento de pago.</h5>
                                            </div>                                            
                                        @endif                                        
                                        @break
                                    @case("PAYED")
                                        <div class="alert alert-success float-right">
                                            <h5><i class="icon fas fa-thumbs-up"></i> Esta orden ya se encuentra pagada.</h5>
                                        </div>
                                        @break
                                    @default
                                        <div class="alert alert-danger float-right">
                                            <h5><i class="icon fas fa-ban"></i> Esta orden ya no puede ser pagada.</h5>
                                        </div>
                                @endswitch
                            @endif
                        </div>                        
                    </div>
                    <div class="card">
                        <div class="card-header bg-gradient-olive">
                            <h3 class="card-title"><i class="icon fas fa-file-invoice-dollar"></i> Transacciones</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i>
                                </button>
                            </div>                            
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-sm-12">
                                    <table id="tbl_transactions" class="table table-striped table-bordered" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th>Estado</th>
                                                <th>Referencia</th>
                                                <th>Creado</th>
                                                <th>Actualizado</th>
                                                <th>Eliminado</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($order->transactions as $transaction)
                                                <tr>
                                                    <td>
                                                        {{$transaction->current_status}}
                                                    </td>
                                                    <td>
                                                        {{$transaction->reference}}
                                                    </td>
                                                    <td>
                                                        {{$transaction->created_at}}
                                                    </td>
                                                    <td>
                                                        {{$transaction->updated_at}}
                                                    </td>
                                                    <td>
                                                        {{$transaction->deleted_at}}
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
            $('#tbl_transactions').DataTable({
                "processing": true,                
                "columnDefs": [ 
                    {
                        "className": "text-center", 
                        "targets": "_all"
                    },                    
                ],
                "order": [2, "desc"],
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
