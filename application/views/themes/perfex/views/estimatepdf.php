<?php

defined('BASEPATH') or exit('No direct script access allowed');

$dimensions = $pdf->getPageDimensions();

//$pdf->ln(15);
/*$info_right_column = '';
$info_left_column  = '';

$info_right_column .= '<span style="font-weight:bold;font-size:27px;">' . _l('estimate_pdf_heading') . '</span><br />';
$info_right_column .= '<b style="color:#4e4e4e;"># ' . $estimate_number . '</b>';

if (get_option('show_status_on_pdf_ei') == 1) {
    $info_right_column .= '<br /><span style="color:rgb(' . estimate_status_color_pdf($status) . ');text-transform:uppercase;">' . format_estimate_status($status, '', false) . '</span>';
}

// Add logo
$info_left_column .= pdf_logo_url();
// Write top left logo and right column info/text
pdf_multi_row($info_left_column, $info_right_column, $pdf, ($dimensions['wk'] / 2) - $dimensions['lm']);

$pdf->ln(10);

$organization_info = '<div style="color:#424242;">';
    $organization_info .= format_organization_info();
$organization_info .= '</div>';

// Estimate to
$estimate_info = '<b>' . _l('estimate_to') . '</b>';
$estimate_info .= '<div style="color:#424242;">';
$estimate_info .= format_customer_info($estimate, 'estimate', 'billing');
$estimate_info .= '</div>';

// ship to to
if ($estimate->include_shipping == 1 && $estimate->show_shipping_on_estimate == 1) {
    $estimate_info .= '<br /><b>' . _l('ship_to') . '</b>';
    $estimate_info .= '<div style="color:#424242;">';
    $estimate_info .= format_customer_info($estimate, 'estimate', 'shipping');
    $estimate_info .= '</div>';
}

$estimate_info .= '<br />' . _l('estimate_data_date') . ': ' . _d($estimate->date) . '<br />';

if (!empty($estimate->expirydate)) {
    $estimate_info .= _l('estimate_data_expiry_date') . ': ' . _d($estimate->expirydate) . '<br />';
}

if (!empty($estimate->reference_no)) {
    $estimate_info .= _l('reference_no') . ': ' . $estimate->reference_no . '<br />';
}

if ($estimate->sale_agent != 0 && get_option('show_sale_agent_on_estimates') == 1) {
    $estimate_info .= _l('sale_agent_string') . ': ' . get_staff_full_name($estimate->sale_agent) . '<br />';
}

if ($estimate->project_id != 0 && get_option('show_project_on_estimate') == 1) {
    $estimate_info .= _l('project') . ': ' . get_project_name_by_id($estimate->project_id) . '<br />';
}

foreach ($pdf_custom_fields as $field) {
    $value = get_custom_field_value($estimate->id, $field['id'], 'estimate');
    if ($value == '') {
        continue;
    }
    $estimate_info .= $field['name'] . ': ' . $value . '<br />';
}

$left_info  = $swap == '1' ? $estimate_info : $organization_info;
$right_info = $swap == '1' ? $organization_info : $estimate_info;

pdf_multi_row($left_info, $right_info, $pdf, ($dimensions['wk'] / 2) - $dimensions['lm']);

// The Table
$pdf->Ln(hooks()->apply_filters('pdf_info_and_table_separator', 6));

// The items table
$items = get_items_table_data($estimate, 'estimate', 'pdf');

$tblhtml = $items->table();

$pdf->writeHTML($tblhtml, true, false, false, false, '');

$pdf->Ln(8);
$tbltotal = '';
$tbltotal .= '<table cellpadding="6" style="font-size:' . ($font_size + 4) . 'px">';
$tbltotal .= '
<tr>
    <td align="right" width="85%"><strong>' . _l('estimate_subtotal') . '</strong></td>
    <td align="right" width="15%">' . app_format_money($estimate->subtotal, $estimate->currency_name) . '</td>
</tr>';

if (is_sale_discount_applied($estimate)) {
    $tbltotal .= '
    <tr>
        <td align="right" width="85%"><strong>' . _l('estimate_discount');
    if (is_sale_discount($estimate, 'percent')) {
        $tbltotal .= ' (' . app_format_number($estimate->discount_percent, true) . '%)';
    }
    $tbltotal .= '</strong>';
    $tbltotal .= '</td>';
    $tbltotal .= '<td align="right" width="15%">-' . app_format_money($estimate->discount_total, $estimate->currency_name) . '</td>
    </tr>';
}

foreach ($items->taxes() as $tax) {
    $tbltotal .= '<tr>
    <td align="right" width="85%"><strong>' . $tax['taxname'] . ' (' . app_format_number($tax['taxrate']) . '%)' . '</strong></td>
    <td align="right" width="15%">' . app_format_money($tax['total_tax'], $estimate->currency_name) . '</td>
</tr>';
}

if ((int)$estimate->adjustment != 0) {
    $tbltotal .= '<tr>
    <td align="right" width="85%"><strong>' . _l('estimate_adjustment') . '</strong></td>
    <td align="right" width="15%">' . app_format_money($estimate->adjustment, $estimate->currency_name) . '</td>
</tr>';
}

$tbltotal .= '
<tr style="background-color:#f0f0f0;">
    <td align="right" width="85%"><strong>' . _l('estimate_total') . '</strong></td>
    <td align="right" width="15%">' . app_format_money($estimate->total, $estimate->currency_name) . '</td>
</tr>';

$tbltotal .= '</table>';

$pdf->writeHTML($tbltotal, true, false, false, false, '');

if (get_option('total_to_words_enabled') == 1) {
    // Set the font bold
    $pdf->SetFont($font_name, 'B', $font_size);
    $pdf->writeHTMLCell('', '', '', '', _l('num_word') . ': ' . $CI->numberword->convert($estimate->total, $estimate->currency_name), 0, 1, false, true, 'C', true);
    // Set the font again to normal like the rest of the pdf
    $pdf->SetFont($font_name, '', $font_size);
    $pdf->Ln(4);
}

if (!empty($estimate->clientnote)) {
    $pdf->Ln(4);
    $pdf->SetFont($font_name, 'B', $font_size);
    $pdf->Cell(0, 0, _l('estimate_note'), 0, 1, 'L', 0, '', 0);
    $pdf->SetFont($font_name, '', $font_size);
    $pdf->Ln(2);
    $pdf->writeHTMLCell('', '', '', '', $estimate->clientnote, 0, 1, false, true, 'L', true);
}

if (!empty($estimate->terms)) {
    $pdf->Ln(4);
    $pdf->SetFont($font_name, 'B', $font_size);
    $pdf->Cell(0, 0, _l('terms_and_conditions') . ":", 0, 1, 'L', 0, '', 0);
    $pdf->SetFont($font_name, '', $font_size);
    $pdf->Ln(2);
    $pdf->writeHTMLCell('', '', '', '', $estimate->terms, 0, 1, false, true, 'L', true);
}*/

$items = get_items_table_data($estimate, 'estimate', 'pdf');

$items_html = $items->table();
$tbl_items = $items_html;

$items_html .= '<table cellpadding="6" style="font-size:' . ($font_size + 4) . 'px">';
$items_html .= '
<tr>
    <td align="right" width="85%"><strong>' . _l('estimate_subtotal') . '</strong></td>
    <td align="right" width="15%">' . app_format_money($estimate->subtotal, $estimate->currency_name) . '</td>
</tr>';

if (is_sale_discount_applied($estimate)) {
    $items_html .= '
    <tr>
        <td align="right" width="85%"><strong>' . _l('estimate_discount');
    if (is_sale_discount($estimate, 'percent')) {
        $items_html .= ' (' . app_format_number($estimate->discount_percent, true) . '%)';
    }
    $items_html .= '</strong>';
    $items_html .= '</td>';
    $items_html .= '<td align="right" width="15%">-' . app_format_money($estimate->discount_total, $estimate->currency_name) . '</td>
    </tr>';
}

foreach ($items->taxes() as $tax) {
    $items_html .= '<tr>
    <td align="right" width="85%"><strong>' . $tax['taxname'] . ' (' . app_format_number($tax['taxrate']) . '%)' . '</strong></td>
    <td align="right" width="15%">' . app_format_money($tax['total_tax'], $estimate->currency_name) . '</td>
</tr>';
}

if ((int)$estimate->adjustment != 0) {
    $items_html .= '<tr>
    <td align="right" width="85%"><strong>' . _l('estimate_adjustment') . '</strong></td>
    <td align="right" width="15%">' . app_format_money($estimate->adjustment, $estimate->currency_name) . '</td>
</tr>';
}

$items_html .= '
<tr style="background-color:#f0f0f0;">
    <td align="right" width="85%"><strong>' . _l('estimate_total') . '</strong></td>
    <td align="right" width="15%">' . app_format_money($estimate->total, $estimate->currency_name) . '</td>
</tr>';

$items_html .= '</table>';

if($estimate->template_id!=""){
	$template = $estimate->templates;
	$content = $template["content"];
	
	
	/*$content = str_replace('{so_number}', '# '.$estimate_number, $content);
	$content = str_replace('{organization_info}', format_organization_info(), $content);
	$content = str_replace('{status}', '<span style="color:rgb(' . estimate_status_color_pdf($status) . ');text-transform:uppercase;">' . format_estimate_status($status, '', false) . '</span>', $content);
	$content = str_replace('{bill_to}', format_customer_info($estimate, 'estimate', 'billing'),$content);
	
	$content = str_replace('{created_at}', _d($estimate->date), $content);
	$content = str_replace('{open_till}', _d($estimate->expirydate), $content);*/
	//$content = str_replace('{proposal_proposal_to}', format_proposal_info($proposal, 'pdf'), $content);
	//$content = str_replace('{proposal_city}', ($proposal->city), $content);
	if (str_contains($content, '{estimate_id}')) {
		$content = str_replace('{estimate_id}', $estimate->id, $content);
	}
	if (str_contains($content, '{estimate_number}')) {
		$content = str_replace('{estimate_number}', '# '.format_estimate_number($estimate->id), $content);
	}
	if (str_contains($content, '{estimate_reference_no}')) {
		$content = str_replace('{estimate_reference_no}', $estimate->reference_no, $content);
	}
	if (str_contains($content, '{estimate_link}')) {
		$content = str_replace('{estimate_link}', site_url('estimate/' . $estimate_id . '/' . $estimate->hash), $content);
	}
	if (str_contains($content, '{estimate_expirydate}')) {
		$content = str_replace('{estimate_expirydate}', _d($estimate->expirydate), $content);
	}
	if (str_contains($content, '{estimate_date}')) {
		$content = str_replace('{estimate_date}', _d($estimate->date), $content);
	}
	if (str_contains($content, '{estimate_status}')) {
		$content = str_replace('{estimate_status}', '<span style="color:rgb(' . estimate_status_color_pdf($status) . ');text-transform:uppercase;">' . format_estimate_status($estimate->status, '', false) . '</span>', $content);
	}
	if (str_contains($content, '{estimate_sale_agent}')) {
		$content = str_replace('{estimate_sale_agent}', get_staff_full_name($estimate->sale_agent), $content);
	}
	
	$currency = get_currency($estimate->currency);
	
	if (str_contains($content, '{estimate_total}')) {
		$content = str_replace('{estimate_total}', app_format_money($estimate->total, $currency), $content);
	}
	if (str_contains($content, '{estimate_subtotal}')) {
		$content = str_replace('{estimate_subtotal}', app_format_money($estimate->subtotal, $currency), $content);
	}
	
	if (str_contains($content, '{project_name}')) {
		$content = str_replace('{project_name}', get_project_name_by_id($estimate->project_id), $content);
	}
	
	if (str_contains($content, '{estimate_short_url}')) {
		$content = str_replace('{estimate_short_url}', get_estimate_shortlink($estimate), $content);
	}
	if (str_contains($content, '{organization_info}')) {
		$content = str_replace('{organization_info}', format_organization_info(), $content);
	}
	if (str_contains($content, '{bill_to}')) {
		$content = str_replace('{bill_to}', format_customer_info($estimate, 'estimate', 'billing'), $content);
	}
	if (str_contains($content, '{ship_to}')) {
		$content = str_replace('{ship_to}', format_customer_info($estimate, 'estimate', 'shipping'), $content);
	}
		
	$custom_fields = get_custom_fields('estimate');
	foreach ($custom_fields as $field) {
		//$fields['{' . $field['slug'] . '}'] = get_custom_field_value($proposal_id, $field['id'], 'proposal');
		if (str_contains($content, '{' . $field['slug'] . '}')) {
			$content = str_replace('{' . $field['slug'] . '}', get_custom_field_value($estimate->id, $field['id'], 'estimate'), $content);
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
	
	if (!empty($estimate->clientnote)) {
		$items_html .= '<br><br><b>'._l('invoice_note').'</b><br>';
		$items_html .= $estimate->clientnote;
	}
	if (!empty($estimate->terms)) {
		$items_html .= '<br><br><b>'._l('terms_and_conditions').'</b><br>';
		$items_html .= $estimate->terms;
	}
	
	$content = str_replace('{estimate_items}', $items_html, $content);
	//$content = str_replace('{total}', $items_html, $content);
	
	//$content = $content.$note.$terms;
	
	$estimate->content = $content;
}

$html = <<<EOF
<style>
.footer-design img{
width:500px;
</style>
<p style="font-size:20px;">
<span style="font-size:15px;"></span>
</p>
<div style="width:675px !important;" class="d-flex-jb">
$estimate->content
</div>

EOF;

$pdf->writeHTML($html, true, false, true, false, '');