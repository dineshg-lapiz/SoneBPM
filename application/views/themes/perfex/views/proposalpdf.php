<?php

defined('BASEPATH') or exit('No direct script access allowed');

$dimensions = $pdf->getPageDimensions();

/*$pdf_logo_url = pdf_logo_url();
$pdf->writeHTMLCell(($dimensions['wk'] - ($dimensions['rm'] + $dimensions['lm'])), '', '', '', $pdf_logo_url, 0, 1, false, true, 'L', true);*/

/*$pdf_logo_url = '<img src="http://indusautomation.co.in/assets/images/bg_image.jpg" />';
$pdf->writeHTMLCell(($dimensions['wk'] - ($dimensions['rm'] + $dimensions['lm'])), '', '', '', $pdf_logo_url, 0, 1, false, true, 'L', true);*/

/*$pdf_logo_url = 'http://indusautomation.co.in/assets/images/bg_image.jpg';
$pdf->Image($pdf_logo_url, 0, 0, 240, 200, '', '', '', false, 300, '', false, false, 0);*/

//$pdf->SetHeaderData('http://indusautomation.co.in/assets/images/bg_image.jpg', 900, 'Hearder'.' 001', '', array(0,64,255), array(0,64,128));
//$pdf->setFooterData(array(0,64,0), array(0,64,128));


/*$bMargin = $pdf->getBreakMargin();
// get current auto-page-break mode
$auto_page_break = $pdf->getAutoPageBreak();
$pdf->SetAutoPageBreak($auto_page_break, $bMargin);
// set the starting point for the page content
$pdf->setPageMark();*/

//$pdf->Ln(15);




/*if($proposal->content=='{proposal_items}'){
	$client_to_details = '<b>' . _l('proposal_to') . '</b>';
	$client_to_details .= '<div style="color:#424242;">';
	$client_to_details .= format_proposal_info($proposal, 'pdf');
	$client_to_details .= '</div>';
	//	$pdf->writeHTMLCell(($dimensions['wk'] / 2) - $dimensions['lm'], $rowcount * 7, '', ($swap == '1' ? $y : ''), $client_to_details, 0, 1, false, true, ($swap == '1' ? 'J' : 'L'), true);

    $client_details =   '<span style="font-weight:bold;font-size:27px;">' . _l('proposal_pdf_heading') . '</span><br />';
    $client_details .= '<b style="color:#4e4e4e;"># ' . $number . '</b>'. '<br />';
    
    $client_details .= '<br />' . _l('proposal_date') . ' : ' . _d($proposal->date) . '<br />';
    if (!empty($proposal->open_till)) {
        $client_details .= _l('proposal_open_till') . ' : ' . _d($proposal->open_till) . '';
        //$prop_info = hooks()->apply_filters('invoice_pdf_header_after_due_date', $invoice_info, $invoice);
    }
    $pro_cust_fields = $proposal->customfields;
    $client_details .= '<br />' . _l('Reference No') . ' : ' . ($pro_cust_fields["proposal_reference"]) . '';
    $client_details .= '<br />' . _l('Sales Person') . ' : ' . ($pro_cust_fields["proposal_sales_person"]) . '<br />';
    $client_details .= _l('Contact No') . ' : ' . ($pro_cust_fields["proposal_contact_no"]) . '';
    
    pdf_multi_row($client_to_details, $client_details, $pdf, ($dimensions['wk'] / 2) - $dimensions['lm']);

    $client_ack_details .= '<p style="text-align:left;">Dear Sir,</p>
    <p style="text-align:left;">We thankfully acknowledge your referred enquiry and take pleasure in quoting the following, subject to our Terms &amp; Conditions</p>';
    $pdf->writeHTMLCell(($dimensions['wk'] - ($dimensions['rm'] + $dimensions['lm'])), '', '', '', $client_ack_details, 0, 1, false, true, 'L', true);

}

$tagvs = [
    'div' => [
        0 => ['h' => 0, 'n' => 0],
        1 => ['h' => 0, 'n' => 0]
    ],
    'p' => [
        0 => ['h' => 0, 'n' => 0],
        1 => ['h' => 0, 'n' => 0]
    ]
];
$pdf->setHtmlVSpace($tagvs);*/

$items = get_items_table_data($proposal, 'proposal', 'pdf')
        ->set_headings('estimate');

//print "<pre>";print count($proposal->items);exit;


$items_html = $items->table();
//$items_html .= '<br />'; $items_html .= '';
$items_html .= '<table cellpadding="6" style="font-size:' . ($font_size + 4) . 'px">';

$items_html .= '
<tr>
    <td align="right" width="85%"><strong>' . _l('estimate_subtotal') . '</strong></td>
    <td align="right" width="15%">' . app_format_money($proposal->subtotal, $proposal->currency_name) . '</td>
</tr>';

if (is_sale_discount_applied($proposal)) {
    $items_html .= '
    <tr>
        <td align="right" width="85%"><strong>' . _l('estimate_discount');
    if (is_sale_discount($proposal, 'percent')) {
        $items_html .= ' (' . app_format_number($proposal->discount_percent, true) . '%)';
    }
    $items_html .= '</strong>';
    $items_html .= '</td>';
    $items_html .= '<td align="right" width="15%">-' . app_format_money($proposal->discount_total, $proposal->currency_name) . '</td>
    </tr>';
}
//echo $items_html;exit;
foreach ($items->taxes() as $tax) {
    $items_html .= '<tr>
    <td align="right" width="85%"><strong>' . $tax['taxname'] . ' (' . app_format_number($tax['taxrate']) . '%)' . '</strong></td>
    <td align="right" width="15%">' . app_format_money($tax['total_tax'], $proposal->currency_name) . '</td>
</tr>';
}

if ((int)$proposal->adjustment != 0) {
    $items_html .= '<tr>
    <td align="right" width="85%"><strong>' . _l('estimate_adjustment') . '</strong></td>
    <td align="right" width="15%">' . app_format_money($proposal->adjustment, $proposal->currency_name) . '</td>
</tr>';
}
$items_html .= '
<tr style="background-color:#f0f0f0;">
    <td align="right" width="85%"><strong>' . _l('estimate_total') . '</strong></td>
    <td align="right" width="15%">' . app_format_money($proposal->total, $proposal->currency_name) . '</td>
</tr>';
$items_html .= '</table>';

if (get_option('total_to_words_enabled') == 1) {
    $items_html .= '<br /><br /><br />';
    $items_html .= '<strong style="text-align:center;">' . _l('num_word') . ': ' . str_replace('0','',$CI->numberword->convert($proposal->total, $proposal->currency_name)) . '</strong>';
}

$proposal->content = str_replace('{proposal_items}', $items_html, $proposal->content);


if($proposal->template_id!=""){
	$template = $proposal->templates;
	$content = $template["content"];
	
	//$content = str_replace('{proposal_number}', format_proposal_number($proposal->id), $content);
	//$content = str_replace('{proposal_created_at}', _d($proposal->date), $content);
	//$content = str_replace('{proposal_open_till}', _d($proposal->open_till), $content);
	//$content = str_replace('{proposal_proposal_to}', format_proposal_info($proposal, 'pdf'), $content);
	//$content = str_replace('{proposal_city}', ($proposal->city), $content);
	
	if (str_contains($content, '{proposal_id}')) {
		$content = str_replace('{proposal_id}', $proposal->id, $content);
	}
	if (str_contains($content, '{proposal_number}')) {
		$content = str_replace('{proposal_number}', format_proposal_number($proposal->id), $content);
	}
	if (str_contains($content, '{proposal_link}')) {
		$content = str_replace('{proposal_link}', site_url('proposal/' . $proposal->id . '/' . $proposal->hash), $content);
	}
	if (str_contains($content, '{proposal_subject}')) {
		$content = str_replace('{proposal_subject}', $proposal->subject, $content);
	}
	
	if ($proposal->currency != 0) {
		$currency = get_currency($proposal->currency);
	} else {
		$currency = get_base_currency();
	}
	
	if (str_contains($content, '{proposal_total}')) {
		$content = str_replace('{proposal_total}', app_format_money($proposal->total, $currency), $content);
	}
	if (str_contains($content, '{proposal_subtotal}')) {
		$content = str_replace('{proposal_subtotal}', app_format_money($proposal->subtotal, $currency), $content);
	}
	if (str_contains($content, '{proposal_open_till}')) {
		$content = str_replace('{proposal_open_till}', _d($proposal->open_till), $content);
	}
	if (str_contains($content, '{proposal_proposal_to}')) {
		//$content = str_replace('{proposal_proposal_to}', $proposal->proposal_to, $content);
		$content = str_replace('{proposal_proposal_to}', format_proposal_info($proposal, 'pdf'), $content);
	}
	if (str_contains($content, '{proposal_address}')) {
		$content = str_replace('{proposal_address}', $proposal->address, $content);
	}
	
	if (str_contains($content, '{proposal_email}')) {
		$content = str_replace('{proposal_email}', $proposal->email, $content);
	}
	if (str_contains($content, '{proposal_phone}')) {
		$content = str_replace('{proposal_phone}', $proposal->phone, $content);
	}
	if (str_contains($content, '{proposal_city}')) {
		$content = str_replace('{proposal_city}', $proposal->city, $content);
	}
	if (str_contains($content, '{proposal_state}')) {
		$content = str_replace('{proposal_state}', $proposal->state, $content);
	}
	if (str_contains($content, '{proposal_zip}')) {
		$content = str_replace('{proposal_zip}', $proposal->zip, $content);
	}
	
	if (str_contains($content, '{proposal_country}')) {
		$content = str_replace('{proposal_country}', $proposal->short_name, $content);
	}
	if (str_contains($content, '{proposal_assigned}')) {
		$content = str_replace('{proposal_assigned}', get_staff_full_name($proposal->assigned), $content);
	}
	if (str_contains($content, '{proposal_short_url}')) {
		$content = str_replace('{proposal_short_url}', get_proposal_shortlink($proposal), $content);
	}
	
	if (str_contains($content, '{proposal_created_at}')) {
		$content = str_replace('{proposal_created_at}', _dt($proposal->datecreated), $content);
	}
	if (str_contains($content, '{proposal_date}')) {
		$content = str_replace('{proposal_date}', _d($proposal->date), $content);
	}
	
	$custom_fields = get_custom_fields('proposal');
	foreach ($custom_fields as $field) {
		//$fields['{' . $field['slug'] . '}'] = get_custom_field_value($proposal_id, $field['id'], 'proposal');
		if (str_contains($content, '{' . $field['slug'] . '}')) {
			$content = str_replace('{' . $field['slug'] . '}', get_custom_field_value($proposal->id, $field['id'], 'proposal'), $content);
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
	
	
	if (!empty($proposal->clientnote)) {
		$items_html .= '<br><br><b>'._l('invoice_note').'</b><br>';
		$items_html .= $proposal->clientnote;
	}
	if (!empty($proposal->terms)) {
		$items_html .= '<br><br><b>'._l('terms_and_conditions').'</b><br>';
		$items_html .= $proposal->terms;
	}
	
	$content = str_replace('{proposal_items}', $items_html, $content);
	
	/*$content = preg_replace('/\s+/', ' ', $content);
    $content = trim($content);*/
	
	$proposal->content = $content;
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
$proposal->content
</div>

EOF;

$pdf->writeHTML($html, true, false, true, false, '');

/*if (!empty($proposal->clientnote)) {
    $pdf->Ln(4);
    $pdf->SetFont($font_name, 'B', $font_size);
    $pdf->Cell(0, 0, _l('invoice_note'), 0, 1, 'L', 0, '', 0);
    $pdf->SetFont($font_name, '', $font_size);
    $pdf->Ln(2);
    $pdf->writeHTMLCell('', '', '', '', $proposal->clientnote, 0, 1, false, true, 'L', true);
}

if (!empty($proposal->terms)) {
    $pdf->Ln(4);
    $pdf->SetFont($font_name, 'B', $font_size);
    $pdf->Cell(0, 0, _l('terms_and_conditions') . ':', 0, 1, 'L', 0, '', 0);
    $pdf->SetFont($font_name, '', $font_size);
    $pdf->Ln(2);
    $pdf->writeHTMLCell('', '', '', '', $proposal->terms, 0, 1, false, true, 'L', true);
}*/
