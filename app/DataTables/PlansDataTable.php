<?php

namespace App\DataTables;

use App\Models\Plan;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class PlansDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->setRowId('id')
            ->addColumn('actions', function(Plan $plan) {
                $editAction = "editarPlan($plan->id)";
                $deleteUrl = "";
                $csrfToken = csrf_token();
                return view('partials.actions', compact('editAction', 'deleteUrl', 'csrfToken'));
            });
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(Plan $model): QueryBuilder
    {
        return $model->newQuery();
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
                    ->setTableId('plans-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    //->dom('Bfrtip')
                    ->orderBy(0, 'asc')
                    ->buttons([
                        Button::make('excel'),
                        Button::make('csv'),
                        Button::make('pdf'),
                        Button::make('print'),
                        Button::make('reset'),
                        Button::make('reload')
                    ])
                    ->parameters([
                        'language' => [
                            'url' => url('https://cdn.datatables.net/plug-ins/2.0.8/i18n/es-MX.json'),
                        ],
                        'select' => false
                    ]);
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            Column::make('id'),
            Column::make('nombre'),
            Column::make('limite')
                ->render("data + ' DTEs al mes'"),
            Column::make('precio')
                ->render("'$' + data"),
            Column::make('precio_adicional')
                ->render("'$' + data"),
            Column::computed('actions')
                ->exportable(false)
                ->printable(false)
                ->addClass('text-center')
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Planes_' . date('YmdHis');
    }
}
