<?php

defined('BASEPATH') or exit('No direct script access allowed');

$dimensions = $pdf->getPageDimensions();

//$pdf->ln(15);

/*$info_right_column = '';
$info_left_column  = '';

$info_right_column .= '<span style="font-weight:bold;font-size:27px;">' . _l('salesorder_pdf_heading') . '</span><br />';
$info_right_column .= '<b style="color:#4e4e4e;"># ' . $invoice_number . '</b>';

if (get_option('show_status_on_pdf_ei') == 1) {
    $info_right_column .= '<br /><span style="color:rgb(' . invoice_status_color_pdf($status) . ');text-transform:uppercase;">' . format_invoice_status($status, '', false) . '</span>';
}

if ($status != Invoices_model::STATUS_PAID && $status != Invoices_model::STATUS_CANCELLED && get_option('show_pay_link_to_invoice_pdf') == 1
    && found_invoice_mode($payment_modes, $invoice->id, false)) {
    $info_right_column .= ' - <a style="color:#84c529;text-decoration:none;text-transform:uppercase;" href="' . site_url('invoice/' . $invoice->id . '/' . $invoice->hash) . '"><1b>' . _l('view_invoice_pdf_link_pay') . '</1b></a>';
}

// Add logo
$info_left_column .= pdf_logo_url();

// Write top left logo and right column info/text
pdf_multi_row($info_left_column, $info_right_column, $pdf, ($dimensions['wk'] / 2) - $dimensions['lm']);

$pdf->ln(10);

$organization_info = '<div style="color:#424242;">';

$organization_info .= format_organization_info();

$organization_info .= '</div>';

// Bill to
$invoice_info = '<b>' . _l('invoice_bill_to') . ':</b>';
$invoice_info .= '<div style="color:#424242;">';
    $invoice_info .= format_customer_info($invoice, 'invoice', 'billing');
$invoice_info .= '</div>';

// ship to to
if ($invoice->include_shipping == 1 && $invoice->show_shipping_on_invoice == 1) {
    $invoice_info .= '<br /><b>' . _l('ship_to') . ':</b>';
    $invoice_info .= '<div style="color:#424242;">';
    $invoice_info .= format_customer_info($invoice, 'invoice', 'shipping');
    $invoice_info .= '</div>';
}

$invoice_info .= '<br />' . _l('invoice_data_date') . ' ' . _d($invoice->date) . '<br />';

$invoice_info = hooks()->apply_filters('invoice_pdf_header_after_date', $invoice_info, $invoice);

if (!empty($invoice->duedate)) {
    $invoice_info .= _l('invoice_data_duedate') . ' ' . _d($invoice->duedate) . '<br />';
    $invoice_info = hooks()->apply_filters('invoice_pdf_header_after_due_date', $invoice_info, $invoice);
}

if ($invoice->sale_agent != 0 && get_option('show_sale_agent_on_invoices') == 1) {
    $invoice_info .= _l('sale_agent_string') . ': ' . get_staff_full_name($invoice->sale_agent) . '<br />';
    $invoice_info = hooks()->apply_filters('invoice_pdf_header_after_sale_agent', $invoice_info, $invoice);
}

if ($invoice->project_id != 0 && get_option('show_project_on_invoice') == 1) {
    $invoice_info .= _l('project') . ': ' . get_project_name_by_id($invoice->project_id) . '<br />';
    $invoice_info = hooks()->apply_filters('invoice_pdf_header_after_project_name', $invoice_info, $invoice);
}

$invoice_info = hooks()->apply_filters('invoice_pdf_header_before_custom_fields', $invoice_info, $invoice);

foreach ($pdf_custom_fields as $field) {
    $value = get_custom_field_value($invoice->id, $field['id'], 'invoice');
    if ($value == '') {
        continue;
    }
    $invoice_info .= $field['name'] . ': ' . $value . '<br />';
}

$invoice_info      = hooks()->apply_filters('invoice_pdf_header_after_custom_fields', $invoice_info, $invoice);
$organization_info = hooks()->apply_filters('invoicepdf_organization_info', $organization_info, $invoice);
$invoice_info      = hooks()->apply_filters('invoice_pdf_info', $invoice_info, $invoice);

$left_info  = $swap == '1' ? $invoice_info : $organization_info;
$right_info = $swap == '1' ? $organization_info : $invoice_info;

pdf_multi_row($left_info, $right_info, $pdf, ($dimensions['wk'] / 2) - $dimensions['lm']);

// The Table
$pdf->Ln(hooks()->apply_filters('pdf_info_and_table_separator', 6));

// The items table
$items = get_items_table_data($invoice, 'invoice', 'pdf');

$tblhtml = $items->table();
$tbl_items = $tblhtml;

$pdf->writeHTML($tblhtml, true, false, false, false, '');

$pdf->Ln(8);

$tbltotal = '';
$tbltotal .= '<table cellpadding="6" style="font-size:' . ($font_size + 4) . 'px">';
$tbltotal .= '
<tr>
    <td align="right" width="85%"><strong>' . _l('invoice_subtotal') . '</strong></td>
    <td align="right" width="15%">' . app_format_money($invoice->subtotal, $invoice->currency_name) . '</td>
</tr>';

if (is_sale_discount_applied($invoice)) {
    $tbltotal .= '
    <tr>
        <td align="right" width="85%"><strong>' . _l('invoice_discount');
    if (is_sale_discount($invoice, 'percent')) {
        $tbltotal .= ' (' . app_format_number($invoice->discount_percent, true) . '%)';
    }
    $tbltotal .= '</strong>';
    $tbltotal .= '</td>';
    $tbltotal .= '<td align="right" width="15%">-' . app_format_money($invoice->discount_total, $invoice->currency_name) . '</td>
    </tr>';
}

foreach ($items->taxes() as $tax) {
    $tbltotal .= '<tr>
    <td align="right" width="85%"><strong>' . $tax['taxname'] . ' (' . app_format_number($tax['taxrate']) . '%)' . '</strong></td>
    <td align="right" width="15%">' . app_format_money($tax['total_tax'], $invoice->currency_name) . '</td>
</tr>';
}

if ((int) $invoice->adjustment != 0) {
    $tbltotal .= '<tr>
    <td align="right" width="85%"><strong>' . _l('invoice_adjustment') . '</strong></td>
    <td align="right" width="15%">' . app_format_money($invoice->adjustment, $invoice->currency_name) . '</td>
</tr>';
}

$tbltotal .= '
<tr style="background-color:#f0f0f0;">
    <td align="right" width="85%"><strong>' . _l('invoice_total') . '</strong></td>
    <td align="right" width="15%">' . app_format_money($invoice->total, $invoice->currency_name) . '</td>
</tr>';

if (count($invoice->payments) > 0 && get_option('show_total_paid_on_salesorder') == 1) {
    $tbltotal .= '
    <tr>
        <td align="right" width="85%"><strong>' . _l('invoice_total_paid') . '</strong></td>
        <td align="right" width="15%">-' . app_format_money(sum_from_table(db_prefix() . 'invoicepaymentrecords', [
        'field' => 'amount',
        'where' => [
            'invoiceid' => $invoice->id,
        ],
    ]), $invoice->currency_name) . '</td>
    </tr>';
}

if (get_option('show_credits_applied_on_invoice') == 1 && $credits_applied = total_credits_applied_to_invoice($invoice->id)) {
    $tbltotal .= '
    <tr>
        <td align="right" width="85%"><strong>' . _l('applied_credits') . '</strong></td>
        <td align="right" width="15%">-' . app_format_money($credits_applied, $invoice->currency_name) . '</td>
    </tr>';
}

if (get_option('show_amount_due_on_salesorder') == 1 && $invoice->status != Invoices_model::STATUS_CANCELLED) {
    $tbltotal .= '<tr style="background-color:#f0f0f0;">
       <td align="right" width="85%"><strong>' . _l('invoice_amount_due') . '</strong></td>
       <td align="right" width="15%">' . app_format_money($invoice->total_left_to_pay, $invoice->currency_name) . '</td>
   </tr>';
}

$tbltotal .= '</table>';
$pdf->writeHTML($tbltotal, true, false, false, false, '');

if (get_option('total_to_words_enabled') == 1) {
    // Set the font bold
    $pdf->SetFont($font_name, 'B', $font_size);
    $pdf->writeHTMLCell('', '', '', '', _l('num_word') . ': ' . $CI->numberword->convert($invoice->total, $invoice->currency_name), 0, 1, false, true, 'C', true);
    // Set the font again to normal like the rest of the pdf
    $pdf->SetFont($font_name, '', $font_size);
    $pdf->Ln(4);
}

if (count($invoice->payments) > 0 && get_option('show_transactions_on_salesorder_pdf') == 1) {
    $pdf->Ln(4);
    $border = 'border-bottom-color:#000000;border-bottom-width:1px;border-bottom-style:solid; 1px solid black;';
    $pdf->SetFont($font_name, 'B', $font_size);
    $pdf->Cell(0, 0, _l('invoice_received_payments') . ':', 0, 1, 'L', 0, '', 0);
    $pdf->SetFont($font_name, '', $font_size);
    $pdf->Ln(4);
    $tblhtml = '<table width="100%" bgcolor="#fff" cellspacing="0" cellpadding="5" border="0">
        <tr height="20"  style="color:#000;border:1px solid #000;">
        <th width="25%;" style="' . $border . '">' . _l('invoice_payments_table_number_heading') . '</th>
        <th width="25%;" style="' . $border . '">' . _l('invoice_payments_table_mode_heading') . '</th>
        <th width="25%;" style="' . $border . '">' . _l('invoice_payments_table_date_heading') . '</th>
        <th width="25%;" style="' . $border . '">' . _l('invoice_payments_table_amount_heading') . '</th>
    </tr>';
    $tblhtml .= '<tbody>';
    foreach ($invoice->payments as $payment) {
        $payment_name = $payment['name'];
        if (!empty($payment['paymentmethod'])) {
            $payment_name .= ' - ' . $payment['paymentmethod'];
        }
        $tblhtml .= '
            <tr>
            <td>' . $payment['paymentid'] . '</td>
            <td>' . $payment_name . '</td>
            <td>' . _d($payment['date']) . '</td>
            <td>' . app_format_money($payment['amount'], $invoice->currency_name) . '</td>
            </tr>
        ';
    }
    $tblhtml .= '</tbody>';
    $tblhtml .= '</table>';
    $pdf->writeHTML($tblhtml, true, false, false, false, '');
}

if (found_invoice_mode($payment_modes, $invoice->id, true, true)) {
    $pdf->Ln(4);
    $pdf->SetFont($font_name, 'B', $font_size);
    $pdf->Cell(0, 0, _l('invoice_html_offline_payment') . ':', 0, 1, 'L', 0, '', 0);
    $pdf->SetFont($font_name, '', $font_size);

    foreach ($payment_modes as $mode) {
        if (is_numeric($mode['id'])) {
            if (!is_payment_mode_allowed_for_invoice($mode['id'], $invoice->id)) {
                continue;
            }
        }
        if (isset($mode['show_on_pdf']) && $mode['show_on_pdf'] == 1) {
            $pdf->Ln(1);
            $pdf->Cell(0, 0, $mode['name'], 0, 1, 'L', 0, '', 0);
            $pdf->Ln(2);
            $pdf->writeHTMLCell('', '', '', '', $mode['description'], 0, 1, false, true, 'L', true);
        }
    }
}

if (!empty($invoice->clientnote)) {
    $pdf->Ln(4);
    $pdf->SetFont($font_name, 'B', $font_size);
    $pdf->Cell(0, 0, _l('invoice_note'), 0, 1, 'L', 0, '', 0);
    $pdf->SetFont($font_name, '', $font_size);
    $pdf->Ln(2);
    $pdf->writeHTMLCell('', '', '', '', $invoice->clientnote, 0, 1, false, true, 'L', true);
}

if (!empty($invoice->terms)) {
    $pdf->Ln(4);
    $pdf->SetFont($font_name, 'B', $font_size);
    $pdf->Cell(0, 0, _l('terms_and_conditions') . ':', 0, 1, 'L', 0, '', 0);
    $pdf->SetFont($font_name, '', $font_size);
    $pdf->Ln(2);
    $pdf->writeHTMLCell('', '', '', '', $invoice->terms, 0, 1, false, true, 'L', true);
}*/

$items = get_items_table_data($invoice, 'saleorder', 'pdf');

$items_html = $items->table();
$tbl_items = $items_html;

//$tbltotal = '';
$items_html .= '<table cellpadding="6" style="font-size:' . ($font_size + 4) . 'px">';
$items_html .= '
<tr>
    <td align="right" width="85%"><strong>' . _l('invoice_subtotal') . '</strong></td>
    <td align="right" width="15%">' . app_format_money($invoice->subtotal, $invoice->currency_name) . '</td>
</tr>';

if (is_sale_discount_applied($invoice)) {
    $items_html .= '
    <tr>
        <td align="right" width="85%"><strong>' . _l('invoice_discount');
    if (is_sale_discount($invoice, 'percent')) {
        $items_html .= ' (' . app_format_number($invoice->discount_percent, true) . '%)';
    }
    $items_html .= '</strong>';
    $items_html .= '</td>';
    $items_html .= '<td align="right" width="15%">-' . app_format_money($invoice->discount_total, $invoice->currency_name) . '</td>
    </tr>';
}

foreach ($items->taxes() as $tax) {
    $items_html .= '<tr>
    <td align="right" width="85%"><strong>' . $tax['taxname'] . ' (' . app_format_number($tax['taxrate']) . '%)' . '</strong></td>
    <td align="right" width="15%">' . app_format_money($tax['total_tax'], $invoice->currency_name) . '</td>
</tr>';
}

if ((int) $invoice->adjustment != 0) {
    $items_html .= '<tr>
    <td align="right" width="85%"><strong>' . _l('invoice_adjustment') . '</strong></td>
    <td align="right" width="15%">' . app_format_money($invoice->adjustment, $invoice->currency_name) . '</td>
</tr>';
}

$items_html .= '
<tr style="background-color:#f0f0f0;">
    <td align="right" width="85%"><strong>' . _l('invoice_total') . '</strong></td>
    <td align="right" width="15%">' . app_format_money($invoice->total, $invoice->currency_name) . '</td>
</tr>';

if (count($invoice->payments) > 0 && get_option('show_total_paid_on_salesorder') == 1) {
    $items_html .= '
    <tr>
        <td align="right" width="85%"><strong>' . _l('invoice_total_paid') . '</strong></td>
        <td align="right" width="15%">-' . app_format_money(sum_from_table(db_prefix() . 'invoicepaymentrecords', [
        'field' => 'amount',
        'where' => [
            'invoiceid' => $invoice->id,
        ],
    ]), $invoice->currency_name) . '</td>
    </tr>';
}

if (get_option('show_credits_applied_on_invoice') == 1 && $credits_applied = total_credits_applied_to_invoice($invoice->id)) {
    $items_html .= '
    <tr>
        <td align="right" width="85%"><strong>' . _l('applied_credits') . '</strong></td>
        <td align="right" width="15%">-' . app_format_money($credits_applied, $invoice->currency_name) . '</td>
    </tr>';
}

if (get_option('show_amount_due_on_salesorder') == 1 && $invoice->status != Invoices_model::STATUS_CANCELLED) {
    $items_html .= '<tr style="background-color:#f0f0f0;">
       <td align="right" width="85%"><strong>' . _l('invoice_amount_due') . '</strong></td>
       <td align="right" width="15%">' . app_format_money($invoice->total_left_to_pay, $invoice->currency_name) . '</td>
   </tr>';
}

$items_html .= '</table>';

if($invoice->template_id!=""){
	$template = $invoice->templates;
	$content = $template["content"];
	
	
	/*$content = str_replace('{so_number}', '# '.$invoice_number, $content);
	$content = str_replace('{organization_info}', format_organization_info(), $content);
	$content = str_replace('{status}', '<span style="color:rgb(' . invoice_status_color_pdf($status) . ');text-transform:uppercase;">' . format_invoice_status($status, '', false) . '</span>', $content);
	$content = str_replace('{bill_to}', format_customer_info($invoice, 'invoice', 'billing'),$content);
	
	$content = str_replace('{created_at}', _d($invoice->date), $content);
	$content = str_replace('{open_till}', _d($invoice->duedate), $content);
	//$content = str_replace('{proposal_proposal_to}', format_proposal_info($proposal, 'pdf'), $content);
	//$content = str_replace('{proposal_city}', ($proposal->city), $content);
	
	
	if (!empty($invoice->clientnote)) {
		$note = '<br><br><b>'._l('invoice_note').'</b><br>';
		$note .= $invoice->clientnote;
	}
	if (!empty($invoice->terms)) {
		$terms .= '<br><br><b>'._l('terms_and_conditions').'</b><br>';
		$terms .= $invoice->terms;
	}
	
	$content = str_replace('{proposal_items}', $tbl_items, $content);
	$content = str_replace('{total}', $tbltotal, $content);
	
	$content = $content.$note.$terms;
	
	$invoice->content = $content;*/
	if (str_contains($content, '{saleorder_id}')) {
		$content = str_replace('{saleorder_id}', $invoice->id, $content);
	}
	if (str_contains($content, '{saleorder_number}')) {
		$content = str_replace('{saleorder_number}', '# '.format_salesorder_number($invoice->id), $content);
	}
	if (str_contains($content, '{saleorder_link}')) {
		$content = str_replace('{saleorder_link}', site_url('salesorder/' . $invoice->id . '/' . $invoice->hash), $content);
	}
	if (str_contains($content, '{saleorder_duedate}')) {
		$content = str_replace('{saleorder_duedate}', _d($invoice->duedate), $content);
	}
	if (str_contains($content, '{saleorder_date}')) {
		$content = str_replace('{saleorder_date}', _d($invoice->date), $content);
	}
	if (str_contains($content, '{saleorder_status}')) {
		$content = str_replace('{saleorder_status}', '<span style="color:rgb(' . invoice_status_color_pdf($status) . ');text-transform:uppercase;">' . format_invoice_status($invoice->status, '', false) . '</span>', $content);
	}
	if (str_contains($content, '{saleorder_sale_agent}')) {
		$content = str_replace('{saleorder_sale_agent}', get_staff_full_name($invoice->sale_agent), $content);
	}
	
	$currency = get_currency($invoice->currency);
	
	if (str_contains($content, '{saleorder_total}')) {
		$content = str_replace('{saleorder_total}', app_format_money($invoice->total, $currency), $content);
	}
	if (str_contains($content, '{saleorder_subtotal}')) {
		$content = str_replace('{saleorder_subtotal}', app_format_money($invoice->subtotal, $currency), $content);
	}
	if (str_contains($content, '{saleorder_amount_due}')) {
		$content = str_replace('{saleorder_amount_due}', app_format_money(get_invoice_total_left_to_pay($saleorder_id, $invoice->total), $currency), $content);
	}
	if (str_contains($content, '{total_days_overdue}')) {
		$content = str_replace('{total_days_overdue}', get_total_days_overdue($invoice->duedate), $content);
	}
	if (str_contains($content, '{project_name}')) {
		$content = str_replace('{project_name}', get_project_name_by_id($invoice->project_id), $content);
	}
	
	if (str_contains($content, '{saleorder_short_url}')) {
		$content = str_replace('{saleorder_short_url}', get_invoice_shortlink($invoice), $content);
	}
	if (str_contains($content, '{organization_info}')) {
		$content = str_replace('{organization_info}', format_organization_info(), $content);
	}
	if (str_contains($content, '{bill_to}')) {
		$content = str_replace('{bill_to}', format_customer_info($invoice, 'invoice', 'billing'), $content);
	}
	if (str_contains($content, '{ship_to}')) {
		$content = str_replace('{ship_to}', format_customer_info($invoice, 'invoice', 'shipping'), $content);
	}
		
	$custom_fields = get_custom_fields('salesorder');
	foreach ($custom_fields as $field) {
		//$fields['{' . $field['slug'] . '}'] = get_custom_field_value($proposal_id, $field['id'], 'proposal');
		if (str_contains($content, '{' . $field['slug'] . '}')) {
			$content = str_replace('{' . $field['slug'] . '}', get_custom_field_value($invoice->id, $field['id'], 'salesorder'), $content);
		}
	}
	
	if (str_contains($content, '{logo_url}')) {
		$content = str_replace('{logo_url}', base_url('uploads/company/' . get_option('company_logo')), $content);
	}
	$logo_width = hooks()->apply_filters('merge_field_logo_img_width', '');
	if (str_contains($content, '{logo_image_with_url}')) {
		$content = str_replace('{logo_image_with_url}', '<a href="' . site_url() . '" target="_blank"><img src="' . base_url('uploads/company/' . get_option('company_logo')) . '"' . ($logo_width != '' ? ' width="' . $logo_width . '"' : '') . '></a>', $content);
	}
	if (str_contains($content, '{dark_logo_image_with_url}')) {
		if (get_option('company_logo_dark') != '') {
			$content = str_replace('{dark_logo_image_with_url}', '<a href="' . site_url() . '" target="_blank"><img src="' . base_url('uploads/company/' . get_option('company_logo_dark')) . '"' . ($logo_width != '' ? ' width="' . $logo_width . '"' : '') . '></a>', $content);
        }else{
			$content = str_replace('{dark_logo_image_with_url}', '', $content);
		}
	}
	if (str_contains($content, '{crm_url}')) {
		$content = str_replace('{crm_url}', rtrim(site_url(), '/'), $content);
	}
	if (str_contains($content, '{admin_url}')) {
		$content = str_replace('{admin_url}', admin_url(), $content);
	}
	if (str_contains($content, '{main_domain}')) {
		$content = str_replace('{main_domain}', get_option('main_domain'), $content);
	}
	if (str_contains($content, '{companyname}')) {
		$content = str_replace('{companyname}', get_option('companyname'), $content);
	}
	if (str_contains($content, '{terms_and_conditions_url}')) {
		$content = str_replace('{terms_and_conditions_url}', terms_url(), $content);
	}
	if (str_contains($content, '{privacy_policy_url}')) {
		$content = str_replace('{privacy_policy_url}', privacy_policy_url(), $content);
	}
	
	
	if (!empty($invoice->clientnote)) {
		$items_html .= '<br><br><b>'._l('invoice_note').'</b><br>';
		$items_html .= $invoice->clientnote;
	}
	if (!empty($invoice->terms)) {
		$items_html .= '<br><br><b>'._l('terms_and_conditions').'</b><br>';
		$items_html .= $invoice->terms;
	}
	
	$content = str_replace('{saleorder_items}', $items_html, $content);
	
	/*$content = preg_replace('/\s+/', ' ', $content);
    $content = trim($content);*/
	
	$invoice->content = $content;
}
//echo $proposal->content;exit;
// Get the proposals css
// Theese lines should aways at the end of the document left side. Dont indent these lines
// <br />
$html = <<<EOF
<style>
.footer-design img{
width:500px;
</style>
<p style="font-size:20px;">
<span style="font-size:15px;"></span>
</p>
<div style="width:675px !important;" class="d-flex-jb">
$invoice->content
</div>

EOF;

$pdf->writeHTML($html, true, false, true, false, '');
