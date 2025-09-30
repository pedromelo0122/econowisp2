<?php
/*
 * LaraClassifier - Classified Ads Web Application
 * Copyright (c) BeDigit. All Rights Reserved
 *
 * Website: https://laraclassifier.com
 * Author: Mayeul Akpovi (BeDigit - https://bedigit.com)
 *
 * LICENSE
 * -------
 * This software is provided under a license agreement and may only be used or copied
 * in accordance with its terms, including the inclusion of the above copyright notice.
 * As this software is sold exclusively on CodeCanyon,
 * please review the full license details here: https://codecanyon.net/licenses/standard
 */

namespace App\Http\Controllers\Web\Admin;

use App\Enums\EscrowStatus;
use App\Http\Controllers\Web\Admin\Panel\PanelController;
use App\Http\Requests\Admin\EscrowTransactionRequest;
use App\Models\EscrowTransaction;
use Illuminate\Http\RedirectResponse;

class EscrowTransactionController extends PanelController
{
        public function setup()
        {
            
                $this->xPanel->setModel(EscrowTransaction::class);
                $this->xPanel->with(['post', 'buyer', 'seller', 'currency']);
                $this->xPanel->setRoute(urlGen()->adminUri('escrow-transactions'));
                $this->xPanel->setEntityNameStrings(trans('admin.escrow_transaction'), trans('admin.escrow_transactions'));
                $this->xPanel->denyAccess(['create', 'delete']);
                //$this->xPanel->removeAllButtons();
                $this->xPanel->allowAccess(['list', 'update', 'show']);
                
                if (!request()->input('order')) {
                        $this->xPanel->orderByDesc('created_at');
                }

                if ($this->onIndexPage) {
                        $this->xPanel->addColumn([
                                'name'      => 'reference',
                                'label'     => trans('admin.reference'),
                                'type'      => 'text',
                        ]);

                        $this->xPanel->addColumn([
                                'name'          => 'post_id',
                                'label'         => trans('admin.Listing'),
                                'type'          => 'model_function',
                                'function_name' => 'crudListingColumn',
                                'limit'         => 120,
                        ]);

                        $this->xPanel->addColumn([
                                'name'          => 'buyer_id',
                                'label'         => trans('admin.Buyer'),
                                'type'          => 'model_function',
                                'function_name' => 'crudBuyerColumn',
                        ]);

                        $this->xPanel->addColumn([
                                'name'          => 'seller_id',
                                'label'         => trans('admin.Seller'),
                                'type'          => 'model_function',
                                'function_name' => 'crudSellerColumn',
                        ]);

                        $this->xPanel->addColumn([
                                'name'          => 'amount',
                                'label'         => trans('admin.amount'),
                                'type'          => 'model_function',
                                'function_name' => 'crudAmountColumn',
                        ]);

                        $this->xPanel->addColumn([
                                'name'          => 'status',
                                'label'         => trans('admin.Status'),
                                'type'          => 'model_function',
                                'function_name' => 'crudStatusColumn',
                                'orderable'     => true,
                        ]);

                        $this->xPanel->addColumn([
                                'name'  => 'created_at',
                                'label' => trans('admin.Date'),
                                'type'  => 'datetime',
                        ]);

                        $this->addFilters();
                }

                if ($this->onEditPage) {
                        $this->xPanel->addField([
                                'name'    => 'status',
                                'label'   => trans('admin.Status'),
                                'type'    => 'select_from_array',
                                'options' => EscrowStatus::labels(),
                                'allows_null' => false,
                        ]);

                        $this->xPanel->addField([
                                'name'  => 'hold_until',
                                'label' => trans('admin.escrow_hold_until'),
                                'type'  => 'datetime_picker',
                                'datetime_picker_options' => [
                                        'format' => 'YYYY-MM-DD HH:mm',
                                        'locale' => app()->getLocale(),
                                ],
                                'allows_null' => true,
                        ]);

                        $this->xPanel->addField([
                                'name'  => 'notes',
                                'label' => trans('admin.Notes'),
                                'type'  => 'textarea',
                                'rows'  => 5,
                                'allows_null' => true,
                        ]);
                }
        }

        protected function addFilters(): void
        {
                $this->xPanel->addFilter([
                        'name'  => 'status',
                        'type'  => 'dropdown',
                        'label' => trans('admin.Status'),
                ], EscrowStatus::labels(), function ($value) {
                        $this->xPanel->addClause('where', 'status', $value);
                });

                $this->xPanel->addFilter([
                        'name'  => 'from_to',
                        'type'  => 'date_range',
                        'label' => trans('admin.Date range'),
                ], false, function ($value) {
                        $dates = json_decode($value);
                        if (!empty($dates->from)) {
                                $this->xPanel->addClause('where', 'created_at', '>=', $dates->from);
                        }
                        if (!empty($dates->to)) {
                                $this->xPanel->addClause('where', 'created_at', '<=', $dates->to);
                        }
                });
        }

       /**
 * @return \Illuminate\Http\RedirectResponse
 */
public function updateCrud(?\App\Http\Requests\Admin\Request $request = null): \Illuminate\Http\RedirectResponse
{
    // Validación con tu FormRequest específico (sin romper la firma del padre)
    if ($request) {
        $request->validate(
            (new \App\Http\Requests\Admin\EscrowTransactionRequest)->rules()
        );
    }

    return parent::updateCrud($request);
}
/**
 * Laravel/Backpack en esta versión llama a update(), no a updateCrud().
 * Creamos un alias que reenvía la petición a updateCrud().
 */
public function update(\App\Http\Requests\Admin\Request $request)
{
    return $this->updateCrud($request);
}


}