<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Saleorder_merge_fields extends App_merge_fields
{
    public function build()
    {
        return [
			[
                    'name'      => 'SaleOrder ID',
                    'key'       => '{saleorder_id}',
                    'available' => [
                        'saleorder',
                    ],
                ],
            [
                'name'      => 'Invoice Link',
                'key'       => '{saleorder_link}',
                'available' => [
                    'saleorder',
                ],
                'templates' => [
                    'subscription-payment-succeeded',
                ],
                'exclude' => [
                    'invoices-batch-payments',
                ],
            ],
            [
                'name'      => 'Invoice Number',
                'key'       => '{saleorder_number}',
                'available' => [
                    'saleorder',
                ],
                'templates' => [
                    'subscription-payment-succeeded',
                ],
                'exclude' => [
                    'invoices-batch-payments',
                ],
            ],
            [
                'name'      => 'Invoice Duedate',
                'key'       => '{saleorder_duedate}',
                'available' => [
                    'saleorder',
                ],
                'exclude' => [
                    'invoices-batch-payments',
                ],
            ],
            [
                'name'      => 'Invoice Date',
                'key'       => '{saleorder_date}',
                'available' => [
                    'saleorder',
                ],
                'templates' => [
                    'subscription-payment-succeeded',
                ],
                'exclude' => [
                    'invoices-batch-payments',
                ],
                'exclude' => [
                    'invoices-batch-payments',
                ],

            ],
            [
                'name'      => 'Invoice Status',
                'key'       => '{saleorder_status}',
                'available' => [
                    'saleorder',
                ],
                'templates' => [
                    'subscription-payment-succeeded',
                ],
                'exclude' => [
                    'invoices-batch-payments',
                ],
            ],
            [
                'name'      => 'Invoice Sale Agent',
                'key'       => '{saleorder_sale_agent}',
                'available' => [
                    'saleorder',
                ],
                'exclude' => [
                    'invoices-batch-payments',
                ],
            ],
            [
                'name'      => 'Invoice Total',
                'key'       => '{saleorder_total}',
                'available' => [
                    'saleorder',
                ],
                'templates' => [
                    'subscription-payment-succeeded',
                ],
                'exclude' => [
                    'invoices-batch-payments',
                ],
            ],
            [
                'name'      => 'Invoice Subtotal',
                'key'       => '{saleorder_subtotal}',
                'available' => [
                    'saleorder',
                ],
                'templates' => [
                    'subscription-payment-succeeded',
                ],
                'exclude' => [
                    'invoices-batch-payments',
                ],
            ],
            [
                'name'      => 'Invoice Amount Due',
                'key'       => '{saleorder_amount_due}',
                'available' => [
                    'saleorder',
                ],
                'exclude' => [
                    'invoices-batch-payments',
                ],
            ],
            [
                'name'      => 'Invoice Days Overdue',
                'key'       => '{total_days_overdue}',
                'available' => [
                    'saleorder',
                ],
                'exclude' => [
                    'invoices-batch-payments',
                ],
            ],
            [
                'name'      => 'Payment Recorded Total',
                'key'       => '{payment_total}',
                'available' => [

                ],
                'templates' => [
                    'subscription-payment-succeeded',
                    'invoice-payment-recorded-to-staff',
                    'invoice-payment-recorded',
                ],
            ],
            [
                'name'      => 'Payment Recorded Date',
                'key'       => '{payment_date}',
                'available' => [

                ],
                'templates' => [
                    'subscription-payment-succeeded',
                    'invoice-payment-recorded-to-staff',
                    'invoice-payment-recorded',
                ],
            ],
            [
                'name'      => 'Project name',
                'key'       => '{project_name}',
                'available' => [
                    'saleorder',
                ],
                'exclude' => [
                    'invoices-batch-payments',
                ],
            ],
			[
				'name'      => 'Organization Info',
				'key'       => '{organization_info}',
				'available' => [
					'saleorder',
				],
			],
			[
				'name'      => 'Bill To',
				'key'       => '{bill_to}',
				'available' => [
					'saleorder',
				],
			],
			[
				'name'      => 'Ship To',
				'key'       => '{ship_to}',
				'available' => [
					'saleorder',
				],
			],
        ];
    }

    /**
     * Merge fields for invoices
     * @param  mixed $saleorder_id invoice id
     * @param  mixed $payment_id payment id
     * @return array
     */
    public function format($saleorder_id, $payment_id = false)
    {
        $fields = [];
        $this->ci->db->where('id', $saleorder_id);
        $invoice = $this->ci->db->get(db_prefix() . 'salesorder')->row();

        if (!$invoice) {
            return $fields;
        }

        $currency = get_currency($invoice->currency);

        $fields['{payment_total}'] = '';
        $fields['{payment_date}']  = '';

        if ($payment_id) {
            $this->ci->db->where('id', $payment_id);
            $payment = $this->ci->db->get(db_prefix() . 'invoicepaymentrecords')->row();

            $fields['{payment_total}'] = app_format_money($payment->amount, $currency);
            $fields['{payment_date}']  = _d($payment->date);
        }

		$fields['{saleorder_id}']          = $saleorder_id;
        $fields['{saleorder_amount_due}'] = app_format_money(get_invoice_total_left_to_pay($saleorder_id, $invoice->total), $currency);
        $fields['{saleorder_sale_agent}'] = get_staff_full_name($invoice->sale_agent);
        $fields['{saleorder_total}']      = app_format_money($invoice->total, $currency);
        $fields['{saleorder_subtotal}']   = app_format_money($invoice->subtotal, $currency);

        $fields['{saleorder_link}']       = site_url('invoice/' . $saleorder_id . '/' . $invoice->hash);
        $fields['{saleorder_number}']     = format_salesorder_number($saleorder_id);
        $fields['{saleorder_duedate}']    = _d($invoice->duedate);
        $fields['{total_days_overdue}'] = get_total_days_overdue($invoice->duedate);
        $fields['{saleorder_date}']       = _d($invoice->date);
        $fields['{saleorder_status}']     = format_invoice_status($invoice->status, '', false);
        $fields['{project_name}']       = get_project_name_by_id($invoice->project_id);
        $fields['{saleorder_short_url}']  = get_invoice_shortlink($invoice);
		
		$fields['{organization_info}']  = format_organization_info();
		$fields['{bill_to}']  = format_customer_info($invoice, 'invoice', 'billing');
		$fields['{ship_to}']  = format_customer_info($invoice, 'invoice', 'shipping');

        $custom_fields = get_custom_fields('salesorder');
        foreach ($custom_fields as $field) {
            $fields['{' . $field['slug'] . '}'] = get_custom_field_value($saleorder_id, $field['id'], 'salesorder');
        }

        return hooks()->apply_filters('saleorder_merge_fields', $fields, [
            'id'      => $saleorder_id,
            'saleorder' => $invoice,
        ]);
    }
}
