<?php
function print_r_html($o, $title = EmptyString)
{
    if ($title) echobr($title . ":");
?>
    <pre><?php
            print_r($o);
            ?></pre><?php
                }

                function remove_junk($str)
                {
                    $str = nl2br($str);
                    $str = htmlspecialchars(strip_tags($str, ENT_QUOTES));
                    return $str;
                }


                function echobr($s = STR_VOID, $a = STR_VOID, $b = STR_VOID, $c = STR_VOID, $d = STR_VOID, $e = STR_VOID, $f = STR_VOID, $g = STR_VOID, $h = STR_VOID)
                {
                    echo '<br>', $s, $a, $b, $c, $d, $e, $f, $g, $h;
                }

                /**
                 * Function to check if submitted Fields are Filled
                 * Applicable to forms where only the fields are not array
                 * @param [array] $arr_fields
                 * @return bool
                 */
                function are_fields_filled($arr_fields)
                {
                    foreach ($arr_fields as $field_name => $field_val) {
                        if (trim($field_val) == EmptyString) {
                            return false;
                        }
                    }
                    return true;
                }

                /**
                 * Function to Check if all array fields are filled
                 * Return true if there is a space or an empty input
                 * @param [type] $arr
                 * @return bool
                 */
                function are_all_array_fields_filled($arr)
                {

                    foreach ($arr as $arr_key => $in_arr) {
                        if (are_strings_equal($arr_key, "waybill_no") || are_strings_equal($arr_key, "vat_option")) {
                            if (are_strings_equal($in_arr, EmptyString) || are_strings_equal($in_arr, EmptyString)) {
                                return false;
                            } else {
                                continue;
                            }
                        }
                        foreach ($in_arr as $key => $val) {
                            if (are_strings_equal(trim($val), EmptyString)) {
                                return false;
                            }
                        }
                    }
                    return true;
                }


                /**
                 * Redirect a page 
                 *
                 * @param [type] $url
                 * @param boolean $permanent
                 * @return void
                 */
                function redirect($url, $permanent = false)
                {
                    if (headers_sent() === false) {
                        header('Location: ' . $url, true, ($permanent === true) ? 301 : 302);
                    }

                    exit();
                }

                /**
                 * Function to compare strings
                 *
                 * @param [type] $a
                 * @param [type] $b
                 * @param boolean $ignore_case
                 * @return void
                 */
                function are_strings_equal($a, $b, $ignore_case = true)
                {
                    //if(is_array($a)) {print_r_html($a,'this array arrived to '.debug_backtrace2string());}

                    $a = '(' . $a;
                    $b = '(' . $b;
                    //why do this?
                    //1 to have native strings, this is important to later be able to use ===
                    //2 to solve issue when comparing strings with ==== that seem numbers, this way no automatic conversion shoulf happen by PHP, issues happened with strings like '8000014E-1423190654', PHP thinks it is a number in cientific notation

                    if ($ignore_case) {
                        $a = strtolower($a);
                        $b = strtolower($b);
                    } else  //be sure we have 2 strings in a and b because if not then === will always fail
                    {
                    }
                    return ($a === $b);   //We do not use == because if you send "1.2" and "1.200" the result is true because this is they way PHP works with numeric strings using ==
                }

                /**
                 * Function to Generate a UPC number
                 */
                function generate_property_no($db, $strength = 10)
                {
                    //$permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                    $db_property_no_count = $db->query('SELECT ref_no from tb_items')->num_rows();

                    if ($db_property_no_count == 0) {
                        $max_property_no = '30000000';
                        $item_property_no = (int)$max_property_no + 1;
                    } else {

                        $db_property_no = $db->query('SELECT ref_no from tb_items')->fetch_all();
                        $aux = max($db_property_no);
                        extract($aux);
                        $max_property_no = $property_no;
                        $item_property_no = (int)$max_property_no + 1;
                    }

                    return $item_property_no;
                }


                /**
                 * Function to Generate SKU Number
                 */

                function get_bet_adjustment($db, $received, $item, $batch_code, $lpn, $doc, $slocation)
                {
                    /**
                     *  Allowed Combination:
                     * 
                     *  Date Received + Item Code + Batch Code + Document No. + LPN + Location
                     *  Date Received + Item Code + Batch Code
                     *  Item Code + Batch Code
                     *  Item Code Only
                     *  Batch Code Only
                     *  Date Received Only
                     *  LPN Only
                     *  Location Only
                     *  Document No. Only
                     * 
                     *  
                     */

                    if ($received != '' && $item != '' && $batch_code != '' && $lpn != '' && $doc != '' && $slocation != '') {

                        /**
                         * ALL HAVE DETAILS
                         */

                        $inbound_adjustment = $db->query('SELECT * FROM tb_inbound where item_code = ? AND batch_no = ? AND lpn = ? AND document_no = ? and bin_location = ? AND date_received = ?', $item, $batch_code, $lpn, $doc, $slocation, $received)->fetch_all();

                        return $inbound_adjustment;
                    } elseif ($received != '' && $item != '' && $batch_code != '') {

                        /**
                         * Date Received + Item Code + Batch Code
                         */

                        $inbound_adjustment = $db->query('SELECT * FROM tb_inbound where item_code = ? AND batch_no = ? AND date_received = ?', $item, $batch_code, $received)->fetch_all();

                        return $inbound_adjustment;
                    } elseif ($item != '' && $batch_code != '') {

                        /**
                         * Item Code + Batch Code
                         */

                        $inbound_adjustment = $db->query('SELECT * FROM tb_inbound where item_code = ? AND batch_no = ?', $item, $batch_code)->fetch_all();

                        return $inbound_adjustment;
                    } elseif ($item != '' && $received == '' && $batch_code == '' && $lpn == '' && $doc == '' && $slocation == '') {

                        /**
                         * Item Code Only
                         */

                        $inbound_adjustment = $db->query('SELECT * FROM tb_inbound where item_code = ?', $item)->fetch_all();

                        return $inbound_adjustment;
                    } elseif ($item == '' && $received == '' && $batch_code != '' && $lpn == '' && $doc == '' && $slocation == '') {

                        /**
                         * Batch Code Only
                         */

                        $inbound_adjustment = $db->query('SELECT * FROM tb_inbound where batch_no = ?', $batch_code)->fetch_all();

                        return $inbound_adjustment;
                    } elseif ($item == '' && $received != '' && $batch_code == '' && $lpn == '' && $doc == '' && $slocation == '') {

                        /**
                         * Date Received Only
                         */

                        $inbound_adjustment = $db->query('SELECT * FROM tb_inbound where date_received = ?', $received)->fetch_all();

                        return $inbound_adjustment;
                    } elseif ($item == '' && $received == '' && $batch_code == '' && $lpn != '' && $doc == '' && $slocation == '') {

                        /**
                         * LPN Only
                         */

                        $inbound_adjustment = $db->query('SELECT * FROM tb_inbound where lpn = ?', $lpn)->fetch_all();

                        return $inbound_adjustment;
                    } elseif ($item == '' && $received == '' && $batch_code == '' && $lpn == '' && $doc == '' && $slocation != '') {

                        /**
                         *  Location Only
                         */

                        $inbound_adjustment = $db->query('SELECT * FROM tb_inbound where bin_location = ?', $slocation)->fetch_all();

                        return $inbound_adjustment;
                    } elseif ($item == '' && $received == '' && $batch_code == '' && $lpn == '' && $doc != '' && $slocation == '') {

                        /**
                         *  Document No. Only
                         */

                        $inbound_adjustment = $db->query('SELECT * FROM tb_inbound where document_no = ?', $doc)->fetch_all();

                        return $inbound_adjustment;
                    } else {
                        return "wrong_combination";
                    }
                }
                function get_bet_inbound($db, $start_date, $end_date)
                {

                    $inbound_report = $db->query('SELECT
                                        tb_inbound.item_code,tb_inbound.batch_no,tb_inbound.qty_pcs,tb_inbound.qty_pcs AS Bal,
                                        tb_inbound.expiry,tb_inbound.date_created,tb_inbound.bin_location,
                                        tb_inbound.created_by, tb_inbound.document_no,
                                        tb_items.material_description
                                         FROM tb_inbound
                                            INNER JOIN tb_items ON tb_items.item_code = tb_inbound.item_code
                                            WHERE tb_inbound.date_created BETWEEN ? AND ? GROUP BY batch_no
                                            ORDER BY item_code DESC', $start_date, $end_date)->fetch_all();

                    return $inbound_report;
                }
                function  get_bet_get_loc($db, $brcd)
                {
                    $get_in_location = $db->query('SELECT id,item_code,batch_no,qty_pcs,expiry,lpn,bin_location FROM tb_inbound WHERE bin_location=?', $brcd)->fetch_all();
                    return $get_in_location;
                }
                function get_bet_inbound_asn($db, $scan)
                {

                    $inbound_asn = $db->query('SELECT ref_no,document_no,time_slot,ATA,plate_no,time_arrived,loading_bay,time_departed FROM tb_inbound where document_no=? GROUP BY document_no', $scan)->fetch_all();

                    return $inbound_asn;
                }
                function get_fullfillment($db, $doc)
                {

                    $inbound_fullfillment = $db->query('SELECT a.document_no,a.item_code,a.batch_no,a.qty_pcs,a.lpn,a.date_time,a.fullfilled_by,c.vendor_name,b.vendor_code,d.material_description
                    FROM tb_fullfillment a
                     INNER JOIN tb_inbound b On b.document_no = a.document_no
                     INNER JOIN tb_vendor c On c.vendor_id = b.vendor_code
                     INNER JOIN tb_items d ON d.item_code = a.item_code
                     WHERE a.document_no =? GROUP BY a.batch_no', $doc)->fetch_all();

                    return $inbound_fullfillment;
                }
                function get_out_fullfillment($db, $doc)
                {

                    $outbound_fullfillment = $db->query('SELECT a.ref_no,a.document_no,a.item_code,a.batch_no,a.qty_pcs,a.lpn,a.date_time,a.fullfilled_by,b.ship_date,c.branch_name,d.material_description,e.status
                    FROM tb_fullfillment a
                    INNER JOIN tb_outbound b ON b.document_no = a.document_no
                    INNER JOIN tb_branches c ON c.branch_code = b.destination_code
                    INNER JOIN tb_items d ON d.item_code = a.item_code
                    INNER JOIN tb_picklist e On e.document_no = a.document_no
                     WHERE a.transaction_type=? AND b.document_name=? GROUP BY a.batch_no', 'Outbound', $doc)->fetch_all();

                    return $outbound_fullfillment;
                }
                function get_dispatch($db, $doc)
                {
                    $dispatch_report = $db->query('SELECT
                    a.ref_no,
                    a.document_no,
                    a.driver,
                    a.helper,
                    a.plate_no,
                    a.truck_type,
                    a.call_time,
                    a.arrival_time,
                    a.departed_time,
                    a.actual_dispatch,
                     a.loading_start,
                     a.loading_end,
                  
                      b.branch_name
                      FROM tb_outbound a 
                      INNER JOIN tb_branches b ON b.branch_code = a.destination_code
                      WHERE a.document_name=? GROUP BY document_no', $doc)->fetch_all();
                    return $dispatch_report;
                }
                function get_delivery($db, $doc)
                {
                    $delivery_report = $db->query('SELECT 
                    a.source_ref, 
                    a.document_no, 
                    a.branch_received_date, 
                    a.received_by,
                    a.ir_ref_no, 
                    a.ir_remarks, 
                    a.rr_ref_no,
                    a.truck_arrival,
                    a.branch_in,
                    a.branch_out,
                    a.fds_comp,
                    a.window_comp,
                    a.in_full,
                    a.created_by,
                    c.branch_name
                     FROM tb_transport a
                     INNER JOIN tb_outbound b ON b.document_no = a.document_no
                     INNER JOIN tb_branches c ON c.branch_code = b.destination_code WHERE b.document_name=? GROUP BY document_no', $doc)->fetch_all();

                    return $delivery_report;
                }


                function get_bet_ageing($db, $start_date, $end_date)
                {

                    $ageing_report = $db->query('SELECT tb_inbound.item_code,
                      tb_inbound.batch_no,  
                      tb_items.material_description,
                      tb_inbound.qty_pcs,
                      tb_inbound.expiry,
                      tb_inbound.mfg,TIMESTAMPDIFF(MONTH,now(), tb_inbound.expiry) as Shelf,
                    DATEDIFF(tb_inbound.expiry,now()) as freshness
                    FROM tb_inbound
                    INNER JOIN tb_items ON tb_items.item_code = tb_inbound.item_code
                    WHERE tb_inbound.expiry  BETWEEN ? and ?
                    ORDER BY ref_no DESC', $start_date, $end_date)->fetch_all();

                    return $ageing_report;
                }
                function get_bet_wall_to_wall($db, $cs_ref)
                {
                    $get_ref_no = $db->query('SELECT * FROM tb_counted_sheet WHERE ref_no = ?', $cs_ref)
                        ->fetch_all();
                    return $get_ref_no;
                }

                function get_bet_replenish($db, $destination_bin, $source_bin)
                {

                    $replen_report = $db->query('SELECT tb_inbound.id ,tb_inbound.item_code,
                    tb_inbound.batch_no,
                    tb_inbound.expiry,
                    tb_inbound.mfg,
                    tb_inbound.running_balance, 
                    tb_inbound.bin_location,
                    tb_inbound.destination_loc,
                    tb_items.material_description
                    FROM tb_inbound
                    INNER JOIN tb_items ON tb_items.item_code = tb_inbound.item_code
                    WHERE tb_inbound.bin_location =?', $source_bin)->fetch_all();



                    return  $replen_report;
                }


                function get_bet_batch($db, $list)
                {


                    $get_batch = $db->query('SELECT 
                    tb_outbound.document_no, 
                    tb_inbound.item_code
                    FROM tb_outbound 
                    INNER JOIN tb_inbound ON  tb_inbound.item_code = tb_outbound.item_code
                    WHERE tb_outbound.document_no LIKE % ? % ', $list)->fetch_all();

                    return $get_batch;
                }


                function get_bet_aging_fifo($db, $start_date, $end_date, $category)
                {

                    $ageing_report = $db->query('SELECT 
                  
                    tb_inbound.item_code,
                    tb_inbound.batch_no,
                    tb_inbound.qty_pcs,
                   tb_inbound.date_received,  
                    TIMESTAMPDIFF(DAY,tb_inbound.date_received,now()) as Shelf,
                    tb_items.material_description,
                    tb_items.category_code
                    FROM tb_inbound
                  
                     INNER JOIN tb_items ON tb_items.item_code = tb_inbound.item_code
                    WHERE tb_items.category_code=? AND tb_inbound.date_received BETWEEN ?  AND  ?
                  ', $category, $start_date, $end_date)->fetch_all();

                    return $ageing_report;
                }



                function get_bet_str($db, $start_date, $end_date)
                {

                    $outbound_report = $db->query('SELECT a.document_no,a.item_code,a.item_description,a.bin_loc,a.batch_no,a.qty_pcs,a.expiry,b.ship_date
                    FROM tb_picklist a
                    INNER JOIN tb_outbound b on b.document_no = a.document_no 
                    WHERE b.ship_date BETWEEN ? AND ? GROUP BY batch_no

                        ', $start_date, $end_date)->fetch_all();

                    return $outbound_report;
                }


                function generate_sku_code($db, $sloc, $item_name, $item_program, $item_category)
                {

                    $sku = "";

                    //Store Location

                    $db_sloc = $db->query('SELECT sloc_name from tb_store_loc WHERE sloc_id = ? ', $sloc)->fetch_array();
                    extract($db_sloc);

                    $sku .= $sloc_name;

                    //Item name
                    $item_name = trim($item_name, " ");
                    $item_name = explode(" ", $item_name);
                    foreach ($item_name as $arr_key => $arr_item_name) {
                        $arr_item_name = str_replace("/", "", $arr_item_name);
                        $arr_item_name = trim($arr_item_name, " ");
                        $arr_item_name = trim($arr_item_name, " ");
                        $arr_item_name = trim($arr_item_name, "&quot;");
                        $arr_item_name = trim($arr_item_name, "'");
                        $arr_item_name = trim($arr_item_name, "(");
                        $arr_item_name = trim($arr_item_name, ")");
                        $arr_item_name = trim($arr_item_name, " ");
                        $arr_item_name = stripslashes($arr_item_name);
                        $sku .= substr($arr_item_name, 0, 4);
                    }

                    //Item Category
                    $db_category = $db->query('SELECT category_short_name FROM tb_category where category_id = ?', $item_category)->fetch_array();
                    extract($db_category);
                    $sku .= $category_short_name;

                    //Item Program
                    $db_program = $db->query('SELECT program_short_name FROM tb_programs where program_id = ?', $item_program)->fetch_array();
                    extract($db_program);
                    $sku .= $program_short_name;

                    return strtoupper($sku);
                }

                /**
                 * Function to generate a series control number for the waybill
                 *
                 * @param [type] $db
                 * @param [string] $branch
                 * @return string
                 */

                function generate_waybill_number($db, $branch)
                {

                    if ($branch == "D") {

                        $db_control_num_count = $db->query('SELECT control_no FROM tb_waybill WHERE waybill_branch = \'D\'')->num_rows();

                        if ((int)$db_control_num_count == 0) {
                            /** First Entry */
                            return $control_num = '0000001';
                        } else {

                            $db_control_num = $db->query('SELECT control_no FROM tb_waybill WHERE waybill_branch = \'D\'')->fetch_all();

                            /** Get the largest control number from the database */
                            $aux = max($db_control_num);


                            extract(str_replace("DVO-", "", $aux));
                            $max_no = $control_no;
                            $control_num = $max_no + 1;
                            $control_num_len = strlen($control_num);


                            switch ($control_num_len) {
                                case 1:
                                    return 'DVO-000000' . $control_num;
                                    break;
                                case 2:
                                    return 'DVO-00000' . $control_num;
                                    break;
                                case 3:
                                    return 'DVO-0000' . $control_num;
                                    break;
                                case 4:
                                    return 'DVO-000' . $control_num;
                                    break;
                                case 5:
                                    return 'DVO-00' . $control_num;
                                    break;
                                case 6:
                                    return 'DVO-0' . $control_num;
                                    break;
                                default:
                                    return $control_num;
                            }
                        }
                    }

                    if ($branch == "C") {

                        $db_control_num_count = $db->query('SELECT control_no FROM tb_waybill WHERE waybill_branch = \'C\'')->num_rows();

                        if ((int)$db_control_num_count == 0) {
                            /** First Entry */
                            return $control_num = '1000000';
                        } else {

                            $db_control_num = $db->query('SELECT control_no FROM tb_waybill WHERE waybill_branch = \'C\'')->fetch_all();

                            /** Get the largest control number from the database */
                            $aux = max($db_control_num);
                            extract(str_replace("CEB-", "", $aux));
                            $max_no = $control_no;
                            $control_num = $max_no + 1;

                            return "CEB-" . $control_num;
                        }
                    }
                }


                /**
                 * Function to Generate Billing Reference Number
                 *
                 * @param integer $strength
                 * @return string
                 */

                function generate_billing_ref_no($strength = 10)
                {
                    //$permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                    $permitted_chars = '0123456789';
                    /**
                     * Is it better to have a random generated string composed of strings and number or 
                     * to have a series reference number is purely number with a string prefix
                     */
                    $input_length = strlen($permitted_chars);
                    $random_string = "";
                    for ($i = 0; $i < $strength; $i++) {
                        $random_character = $permitted_chars[mt_rand(0, $input_length - 1)];
                        $random_string .= $random_character;
                        $random_string .= '';
                    }
                    $random_string .= "-BL";


                    return $random_string;
                }


                function get_sub_total($db, $ref_num)
                {

                    $aux = $db->query('SELECT SUM(charge_amount) as sub_total FROM tb_bill WHERE billing_reference_no = ?', $ref_num)->fetch_array();

                    return $aux['sub_total'];
                }

                function get_total_bill($db, $ref_num)
                {
                    $aux = $db->query('SELECT SUM(charge_amount) as sub_total,vat_option FROM tb_bill WHERE billing_reference_no = ?', $ref_num)->fetch_array();

                    if ($aux['vat_option'] == 12) {
                        $total = $aux['sub_total'] + $aux['sub_total'] * .12;

                        return number_format($total, 2);
                    }

                    return number_format($aux['sub_total'], 2);
                    //return $aux['sub_total'];
                }



                /*
                Check for Array if it has Space or Empty String Input
                */

                function is_array_has_empty_input($arr_name)
                {
                    foreach ($arr_name as $arr_key => $arr_val) {
                        if (are_strings_equal(trim($arr_val), EmptyString)) {
                            return true;
                        }
                    }
                    return false;
                }

                /**
                 * Check if CSV Has Valid Input
                 */

                 

                /**
                 * Function to Generate Reference Number
                 * @param  [type] $transaction [transaction type 1=order, 2=outbound, 3=inbound, 4=stock receive ,5=stock return]
                 * @return [type]              [string reference number]
                 */

                function generate_ref_num($transaction, $strength = 15)
                {

                    $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

                    if ($transaction == 1) {
                        $input_length = strlen($permitted_chars);
                        $random_string = 'ORD';
                        for ($i = 0; $i < $strength; $i++) {
                            $random_character = $permitted_chars[mt_rand(0, $input_length - 1)];
                            $random_string .= $random_character;
                        }

                        return $random_string;
                    }

                    if ($transaction == 2) {
                        $input_length = strlen($permitted_chars);
                        $random_string = 'OUT';
                        for ($i = 0; $i < $strength; $i++) {
                            $random_character = $permitted_chars[mt_rand(0, $input_length - 1)];
                            $random_string .= $random_character;
                        }

                        return $random_string;
                    }

                    if ($transaction == 3) {
                        $input_length = strlen($permitted_chars);
                        $random_string = 'INB';
                        for ($i = 0; $i < $strength; $i++) {
                            $random_character = $permitted_chars[mt_rand(0, $input_length - 1)];
                            $random_string .= $random_character;
                        }

                        return $random_string;
                    }

                    if ($transaction == 4) {
                        $input_length = strlen($permitted_chars);
                        $random_string = 'STCKRCV';
                        for ($i = 0; $i < $strength; $i++) {
                            $random_character = $permitted_chars[mt_rand(0, $input_length - 1)];
                            $random_string .= $random_character;
                        }

                        return $random_string;
                    }

                    if ($transaction == 5) {
                        $input_length = strlen($permitted_chars);
                        $random_string = 'RET';
                        for ($i = 0; $i < $strength; $i++) {
                            $random_character = $permitted_chars[mt_rand(0, $input_length - 1)];
                            $random_string .= $random_character;
                        }

                        return $random_string;
                    }
                }

                /**
                 * Function to Generate Pull Out Endorsement Form Reference No
                 * Start Ref No: AGLPEF-000001
                 * End Ref No: AGLPEF-100000 
                 * Transaction Code** 
                 * ASN                          6 AGLASN-XXXX
                 * ASSEMBLY BUILD               20 AB-XXXXXXX
                 * TRANSFER ORDER               21 TO-XXXXXXX
                 * PICKLIST                     22 PLIST-XXXXXXX
                 * TRUCK ALLOCATION             23 tb_transport_allocation Reference No
                 * TRUCK ALLOCATION             24 tb_transport_allocation system_do_no
                 * TRUCK ALLOCATION             25 tb_transport_allocation system_shipment_no
                 * INBOUND IR                   26 tb_inbound_ir
                 * @return string
                 */
                function generate_reference_no($db, $transaction)
                {

                    /**
                     * ASN
                     */
                    if ($transaction == 6) {

                        $db_pef_count = $db->query('SELECT ref_no FROM tb_asn')->num_rows();

                        //print_r_html("DB PEF COUNT: ".$db_pef_count);

                        if ((int)$db_pef_count == 0) {

                            /** First Entry */
                            return $control_no = '000001';
                        } else {

                            $db_pef_no = $db->query('SELECT ref_no FROM tb_asn')->fetch_all();

                            /** Get the largest control number from the database */

                            $aux = max($db_pef_no);
                            extract($aux);
                            $max_ref = $ref_no;
                            $new_ref = $max_ref + 1;
                            $ref_no_len = strlen($new_ref);

                            switch ($ref_no_len) {
                                case 1:
                                    return '00000' . $new_ref;
                                    break;
                                case 2:
                                    return '0000' . $new_ref;
                                    break;
                                case 3:
                                    return '000' . $new_ref;
                                    break;
                                case 4:
                                    return '00' . $new_ref;
                                    break;
                                case 5:
                                    return '0' . $new_ref;
                                    break;
                                default:
                                    return $new_ref;
                            }
                        }
                    }

                    
                    /**
                     * ASSEMBLY BUILD
                     */
                    if ($transaction == 20) {

                        $db_ab_count = $db->query('SELECT ab_ref_no FROM tb_assembly_build')->num_rows();


                        if ((int)$db_ab_count == 0) {

                            /** First Entry */
                            return $control_no = '000001';
                        } else {

                            $db_ab_count = $db->query('SELECT ab_ref_no FROM tb_assembly_build')->fetch_all();

                            /** Get the largest control number from the database */

                            $aux = max($db_ab_count);
                            extract($aux);
                            $max_ref = $ab_ref_no;
                            $new_ref = $max_ref + 1;
                            $ref_no_len = strlen($new_ref);

                            switch ($ref_no_len) {
                                case 1:
                                    return '00000' . $new_ref;
                                    break;
                                case 2:
                                    return '0000' . $new_ref;
                                    break;
                                case 3:
                                    return '000' . $new_ref;
                                    break;
                                case 4:
                                    return '00' . $new_ref;
                                    break;
                                case 5:
                                    return '0' . $new_ref;
                                    break;
                                default:
                                    return $new_ref;
                            }
                        }
                    } 

                    /** TRANSFER ORDER */

                    if ($transaction == 21) {

                        $db_to_count = $db->query('SELECT to_no FROM tb_transfer_order')->num_rows();


                        if ((int)$db_to_count == 0) {

                            /** First Entry */
                            return $control_no = '000001';
                        } else {

                            $db_to_count = $db->query('SELECT to_no FROM tb_transfer_order')->fetch_all();

                            /** Get the largest control number from the database */

                            $aux = max($db_to_count);
                            extract($aux);
                            $max_ref = $to_no;
                            $new_ref = (int)$max_ref + 1;
                            $ref_no_len = strlen($new_ref);

                            switch ($ref_no_len) {
                                case 1:
                                    return '00000' . $new_ref;
                                    break;
                                case 2:
                                    return '0000' . $new_ref;
                                    break;
                                case 3:
                                    return '000' . $new_ref;
                                    break;
                                case 4:
                                    return '00' . $new_ref;
                                    break;
                                case 5:
                                    return '0' . $new_ref;
                                    break;
                                default:
                                    return $new_ref;
                            }
                        }
                    }

                    /**PICKLIST TABLE */

                    if ($transaction == 22) {

                        $db_pick_to_count = $db->query('SELECT ref_no FROM tb_picklist')->num_rows();


                        if ((int)$db_pick_to_count == 0) {

                            /** First Entry */
                            return $control_no = '000001';
                        } else {

                            $db_pick_to_count = $db->query('SELECT ref_no FROM tb_picklist')->fetch_all();

                            /** Get the largest control number from the database */

                            $aux = max($db_pick_to_count);
                            extract($aux);
                            $max_ref = $ref_no;
                            $new_ref = (int)$max_ref + 1;
                            $ref_no_len = strlen($new_ref);

                            switch ($ref_no_len) {
                                case 1:
                                    return '00000' . $new_ref;
                                    break;
                                case 2:
                                    return '0000' . $new_ref;
                                    break;
                                case 3:
                                    return '000' . $new_ref;
                                    break;
                                case 4:
                                    return '00' . $new_ref;
                                    break;
                                case 5:
                                    return '0' . $new_ref;
                                    break;
                                default:
                                    return $new_ref;
                            }
                        }
                    }

                    /** TB_TRANSPORT_ALLOCATION TABLE */

                    if ($transaction == 23) {

                        $db_pick_to_count = $db->query('SELECT ref_no FROM tb_transport_allocation')->num_rows();


                        if ((int)$db_pick_to_count == 0) {

                            /** First Entry */
                            return $control_no = '000001';
                        } else {

                            $db_pick_to_count = $db->query('SELECT ref_no FROM tb_transport_allocation')->fetch_all();

                            /** Get the largest control number from the database */

                            $aux = max($db_pick_to_count);
                            extract($aux);
                            $max_ref = $ref_no;
                            $new_ref = (int)$max_ref + 1;
                            $ref_no_len = strlen($new_ref);

                            switch ($ref_no_len) {
                                case 1:
                                    return '00000' . $new_ref;
                                    break;
                                case 2:
                                    return '0000' . $new_ref;
                                    break;
                                case 3:
                                    return '000' . $new_ref;
                                    break;
                                case 4:
                                    return '00' . $new_ref;
                                    break;
                                case 5:
                                    return '0' . $new_ref;
                                    break;
                                default:
                                    return $new_ref;
                            }
                        }
                    }

                    /** TB_TRANSPORT_ALLOCATION TABLE
                     * SYSTEM_DO_NO
                     */

                    if ($transaction == 24) {

                        $db_pick_to_count = $db->query('SELECT system_do_no FROM tb_transport_allocation')->num_rows();


                        if ((int)$db_pick_to_count == 0) {

                            /** First Entry */
                            return $control_no = '000001';
                        } else {

                            $db_pick_to_count = $db->query('SELECT system_do_no FROM tb_transport_allocation')->fetch_all();

                            /** Get the largest control number from the database */

                            $aux = max($db_pick_to_count);
                            extract($aux);
                            $max_ref = $system_do_no;
                            $new_ref = (int)$max_ref + 1;
                            $ref_no_len = strlen($new_ref);

                            switch ($ref_no_len) {
                                case 1:
                                    return '00000' . $new_ref;
                                    break;
                                case 2:
                                    return '0000' . $new_ref;
                                    break;
                                case 3:
                                    return '000' . $new_ref;
                                    break;
                                case 4:
                                    return '00' . $new_ref;
                                    break;
                                case 5:
                                    return '0' . $new_ref;
                                    break;
                                default:
                                    return $new_ref;
                            }
                        }
                    }

                    /** TB_TRANSPORT_ALLOCATION TABLE
                     * SYSTEM_SHIPMENT_NO
                     */

                     if ($transaction == 25) {

                        $db_pick_to_count = $db->query('SELECT system_shipment_no FROM tb_transport_allocation')->num_rows();


                        if ((int)$db_pick_to_count == 0) {

                            /** First Entry */
                            return $control_no = '000001';
                        } else {

                            $db_pick_to_count = $db->query('SELECT system_shipment_no FROM tb_transport_allocation')->fetch_all();

                            /** Get the largest control number from the database */

                            $aux = max($db_pick_to_count);
                            extract($aux);
                            $max_ref = $system_shipment_no;
                            $new_ref = (int)$max_ref + 1;
                            $ref_no_len = strlen($new_ref);

                            switch ($ref_no_len) {
                                case 1:
                                    return '00000' . $new_ref;
                                    break;
                                case 2:
                                    return '0000' . $new_ref;
                                    break;
                                case 3:
                                    return '000' . $new_ref;
                                    break;
                                case 4:
                                    return '00' . $new_ref;
                                    break;
                                case 5:
                                    return '0' . $new_ref;
                                    break;
                                default:
                                    return $new_ref;
                            }
                        }
                    }

                    /**
                     * ASSEMBLY BUILD
                     */
                    if ($transaction == 26) {

                        $db_ir_count = $db->query('SELECT ir_ref_no FROM tb_inbound_ir')->num_rows();


                        if ((int)$db_ir_count == 0) {

                            /** First Entry */
                            return $control_no = '000001';
                        } else {

                            $db_ir_count = $db->query('SELECT ir_ref_no FROM tb_inbound_ir')->fetch_all();

                            /** Get the largest control number from the database */

                            $aux = max($db_ir_count);
                            extract($aux);
                            $max_ref = $ir_ref_no;
                            $new_ref = $max_ref + 1;
                            $ref_no_len = strlen($new_ref);

                            switch ($ref_no_len) {
                                case 1:
                                    return '00000' . $new_ref;
                                    break;
                                case 2:
                                    return '0000' . $new_ref;
                                    break;
                                case 3:
                                    return '000' . $new_ref;
                                    break;
                                case 4:
                                    return '00' . $new_ref;
                                    break;
                                case 5:
                                    return '0' . $new_ref;
                                    break;
                                default:
                                    return $new_ref;
                            }
                        }
                    } 




                }




                function get_total_qty($arr)
                {
                    $total = 0;

                    foreach ($arr as $arr_key => $arr_val) {
                        $total = $total + $arr_val['qty'];
                    }

                    return $total;
                }


                function get_all_items_from_db($db)
                {
                    $all_items = array();

                    $db_items = $db->query('SELECT * FROM items ORDER BY item_model ASC')->fetch_all();

                    foreach ($db_items as $db_key => $db_val) {
                        $all_items[$db_val['item_id']] = $db_val;
                    }

                    return $all_items;
                }

                function get_opening_balance($db, $start_date)
                {

                    $asar_balance = array();

                    $all_items = get_all_items_from_db($db);

                    $aux_inb = $db->query('SELECT * FROM tb_inbound WHERE inbound_date < ?', $start_date)->fetch_all();

                    //print_r_html($aux_inb);

                    $aux_out = $db->query('SELECT * FROM tb_outbound where outbound_date < ?', $start_date)->fetch_all();

                    //print_r_html($aux_out);

                    foreach ($all_items as $item_id => $arr_val) {

                        $asar_balance[$item_id]['item_id'] = $arr_val['item_id'];
                        $asar_balance[$item_id]['in'] = 0;
                        $asar_balance[$item_id]['out'] = 0;
                        $asar_balance[$item_id]['ending_balance'] = 0;
                    }


                    if (!empty($aux_inb)) {
                        foreach ($aux_inb as $db_key => $db_val) {
                            if (isset($asar_balance[$db_val['item_id']])) {
                                $asar_balance[$db_val['item_id']]['in'] = $asar_balance[$db_val['item_id']]['in'] + $db_val['qty'];
                            }
                        }
                    }

                    if (!empty($aux_out)) {
                        foreach ($aux_out as $db_key => $db_val) {
                            if (isset($asar_balance[$db_val['item_id']])) {
                                $asar_balance[$db_val['item_id']]['out'] = $asar_balance[$db_val['item_id']]['out'] + $db_val['qty'];
                            }
                        }
                    }

                    foreach ($asar_balance as $item_id => $arr_val) {
                        $asar_balance[$item_id]['ending_balance'] = $arr_val['in'] - $arr_val['out'];
                    }

                    //print_r_html($asar_balance);

                    return $asar_balance;
                }

                function get_item_opening_balance($db, $start_date, $item_id)
                {

                    $aux = $db->query('SELECT * FROM tb_inbound WHERE inbound_date < ? and item_id = ?', $start_date, $item_id)->fetch_all();

                    return $aux;
                }

                function get_db_inbound_transactions($db, $start_date, $end_date)
                {

                    $aux = $db->query('SELECT tb_inbound.inbound_id, tb_inbound.inbound_date, tb_inbound.ref_no,
                    tb_inbound.stock_receive_no,items.item_id,items.item_model,items.item_description,items.item_unit,tb_inbound.qty,tb_inbound.remarks,
                    tb_inbound.created_by
                     FROM tb_inbound
                     INNER JOIN items ON items.item_id = tb_inbound.item_id
                     WHERE inbound_date BETWEEN? and ?
                      ORDER BY ref_no DESC', $start_date, $end_date)->fetch_all();

                    return $aux;
                }

                function get_item_db_inbound_transactions($db, $start_date, $end_date, $item_id)
                {

                    $aux = $db->query('SELECT tb_inbound.inbound_id, tb_inbound.inbound_date, tb_inbound.ref_no,
                    tb_inbound.stock_receive_no,items.item_id,items.item_model,items.item_description,items.item_unit,tb_inbound.qty,tb_inbound.remarks,
                     tb_inbound.created_by
                       FROM tb_inbound
                        INNER JOIN items ON items.item_id = tb_inbound.item_id
                        WHERE inbound_date BETWEEN? AND ?
                        AND tb_inbound.item_id = ?
                ORDER BY ref_no DESC', $start_date, $end_date, $item_id)->fetch_all();

                    return $aux;
                }

                function get_db_outbound_transactions($db, $start_date, $end_date)
                {

                    $aux = $db->query('SELECT tb_outbound.outbound_id, tb_outbound.outbound_date, tb_outbound.ref_no,
                    tb_outbound.book_order_no,tb_outbound.consignee,tb_outbound.plate_no,tb_outbound.serial_no,items.item_id,items.item_model,items.item_description,items.item_unit,tb_outbound.qty,tb_outbound.remarks,
                    tb_outbound.created_by
                    FROM tb_outbound
                    INNER JOIN items ON items.item_id = tb_outbound.item_id
                    WHERE outbound_date BETWEEN? and ?
                    ORDER BY ref_no DESC', $start_date, $end_date)->fetch_all();

                    return $aux;
                }

                function get_item_db_outbound_transactions($db, $start_date, $end_date, $item_id)
                {

                    $aux = $db->query('SELECT tb_outbound.outbound_id, tb_outbound.outbound_date, tb_outbound.ref_no,
                    tb_outbound.book_order_no,tb_outbound.consignee,tb_outbound.plate_no,tb_outbound.serial_no,items.item_id,items.item_model,items.item_description,items.item_unit,tb_outbound.qty,tb_outbound.remarks,
                    tb_outbound.created_by
                    FROM tb_outbound
                    INNER JOIN items ON items.item_id = tb_outbound.item_id
                    WHERE outbound_date BETWEEN ? AND ?
                    AND tb_outbound.item_id = ?
                    ORDER BY ref_no DESC', $start_date, $end_date, $item_id)->fetch_all();

                    return $aux;
                }


                /**
                 * DASHBOARD FUNCTIONS
                 *
                 * @param [type] $db
                 * @return void
                 */
                function  get_new_str_transactions($db)
                {
                    $date_today = date('Y-m-d');

                    $aux = $db->query('SELECT * FROM tb_outbound WHERE date = ?', $date_today)->fetch_all();

                    $aux_count = count($aux);

                    return $aux_count;
                }
                function get_new_inbound_transactions($db)
                {
                    $date_today = date('Y-m-d');

                    $aux = $db->query('SELECT * FROM tb_inbound WHERE system_transaction_date = ?', $date_today)->fetch_all();

                    $aux_count = count($aux);

                    return $aux_count;
                }
                function get_all_items($db)
                {

                    $aux = $db->query('SELECT * FROM tb_inbound')->fetch_all();

                    $aux_count = count($aux);

                    return $aux_count;
                }

                function get_all_validated($db)
                {
                    $date = date('Y-m-d');
                    $aux = $db->query('SELECT * FROM tb_count_sheet WHERE date = ? AND status=?', $date, 'Validated')
                        ->fetch_all();

                    $aux_count_validated = count($aux);

                    return $aux_count_validated;
                }
                function get_new_lock_items($db)
                {


                    $aux = $db->query('SELECT * FROM tb_lock_items')->fetch_all();

                    $aux_count = count($aux);

                    return $aux_count;
                }
                function get_new_outbound_transactions($db)
                {
                    $date_today = date('Y-m-d');

                    $aux = $db->query('SELECT * FROM tb_outbound WHERE ship_date = ?', $date_today)->fetch_all();

                    $aux_count = count($aux);

                    return $aux_count;
                }

                function get_new_pullout_transactions($db)
                {
                    $date_today = date('Y-m-d');

                    $aux = $db->query('SELECT DISTINCT ref_no FROM tb_order WHERE order_date = ?', $date_today)->fetch_all();

                    $aux_count = count($aux);

                    return $aux_count;
                }
                function get_return_transactions($db)
                {
                    $date_today = date('Y-m-d');

                    $aux = $db->query('SELECT DISTINCT ref_no FROM tb_return WHERE transaction_date = ?', $date_today)->fetch_all();

                    $aux_count = count($aux);

                    return $aux_count;
                }

                function get_db_avail_items($db)
                {
                    $avail_count = 0;

                    $query = "SELECT * FROM tb_items ";
                    $result = mysqli_query($db, $query);

                    $avail_count = mysqli_num_rows($result);

                    return $avail_count;
                }


                function generate_lpn($strength = 15)
                {
                    // $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                    $permitted_chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
                    // $permitted_chars = '0123456789';
                    /**
                     * Is it better to have a random generated string composed of strings and number or 
                     * to have a series reference number is purely number with a string prefix
                     */
                    $input_length = strlen($permitted_chars);
                    $random_string = "";
                    for ($i = 0; $i < $strength; $i++) {
                        $random_character = $permitted_chars[mt_rand(0, $input_length - 1)];
                        $random_string .= $random_character;
                        $random_string .= '';
                    }


                    return $random_string;
                }


                function put_away_transaction($db)
                {
                    $date_today = date('Y-m-d');

                    $aux = $db->query('SELECT * FROM tb_asn  WHERE eta >= ? AND date_created = ?', $date_today, $date_today)->fetch_all();

                    $aux_count = count($aux);

                    return $aux_count;
                }
                function bad_order_transactions($db)
                {
                    $date_today = date('Y-m-d');

                    $aux = $db->query('SELECT * FROM tb_asn  WHERE eta >= ? AND date_created = ?', $date_today, $date_today)->fetch_all();

                    $aux_count = count($aux);

                    return $aux_count;
                }


                //GENERATE REPORTS
                function get_bet_lpn($db, $lpn)
                {
                    $inbound_lpn = $db->query('SELECT id,lpn,bin_loc,actual_bin_loc,expiry,sku_code,qty_case,allocated_qty FROM tb_inventory_adjustment where lpn = ? AND transaction_type = ?', $lpn,"INB")->fetch_all();

                    return $inbound_lpn;
                }

                function get_bet_bin_loc($db, $bin_loc)
                {
                    $aisle_details = array();

                    $all_aisle_location = $db->query('SELECT * FROM tb_bin_location_bac WHERE aisle = ? ORDER BY aisle ASC , `columns` ASC , high ASC , deep ASC', $bin_loc)->fetch_all();

                    foreach ($all_aisle_location as $arr_key => $arr_det) {

                        $get_inbound_detail = $db->query('SELECT 
                        a.lpn,
                        a.sku_code,
                        a.qty_case,
                        a.allocated_qty,
                        a.expiry,
                        a.actual_bin_loc,
                        tb_items.material_description
                         FROM tb_inventory_adjustment a 
                         INNER JOIN tb_items ON tb_items.sap_code = a.sku_code
                         WHERE a.bin_loc = ? AND a.qty_case - a.allocated_qty <> 0 
                         AND a.transaction_type = ? 
                         AND a.putaway_status = ?', $arr_det['location_code'],"INB","Done")->fetch_all();

                        if (!empty($get_inbound_detail)) {
                            foreach ($get_inbound_detail as $asar_key => $asar_val) {
                                $aisle_details[] = $asar_val;
                            }
                        } else {
                            $aisle_details[]['bin_location'] = $arr_det['location_code'];
                        }
                    }

                    return $aisle_details;
                }

                function get_sku_location ($db,$sku_code){

                    $db_sku_loc = $db->query('SELECT 
                    a.id,
                    a.lpn,
                    a.sku_code,
                    tb_items.material_description,
                    a.qty_case,
                    a.allocated_qty,
                    a.qty_case - a.allocated_qty AS available_qty,
                    a.expiry,
                    a.actual_bin_loc
                    FROM tb_inventory_adjustment a 
                    INNER JOIN tb_items ON tb_items.sap_code = a.sku_code
                    WHERE a.sku_code = ? AND a.qty_case - a.allocated_qty <> 0 
                    AND a.transaction_type = ?
                    AND a.putaway_status = ?
                    ORDER BY a.actual_bin_loc ASC',$sku_code,"INB","Done")->fetch_all();

                    return $db_sku_loc;
                }

                function get_bet_loc($db, $bin_loc)
                {
                    $aisle_details = array();

                    $all_aisle_location = $db->query('SELECT * FROM tb_bin_location_bac WHERE location_code= ? ORDER BY location_code ASC', $bin_loc)->fetch_all();

                    foreach ($all_aisle_location as $arr_key => $arr_det) {

                        $get_inbound_detail = $db->query('SELECT * FROM tb_inbound WHERE bin_location = ? AND qty_pcs - dispatch_qty <> 0 ', $arr_det['location_code'])->fetch_all();

                        if (!empty($get_inbound_detail)) {
                            foreach ($get_inbound_detail as $asar_key => $asar_val) {
                                $aisle_details[] = $asar_val;
                            }
                        } else {
                            $aisle_details[]['bin_location'] = $arr_det['location_code'];
                        }
                    }

                    return $aisle_details;
                }
                function  get_bet_bin_loc_count($db, $bin_loc, $warehouse)

                {
                    $aisle_details = array();

                    $all_aisle_location = $db->query('SELECT * FROM tb_bin_location_bac WHERE aisle = ? AND warehuse ORDER BY location_code ASC', $bin_loc,)->fetch_all();

                    foreach ($all_aisle_location as $arr_key => $arr_det) {

                        $get_inbound_detail = $db->query('SELECT * FROM tb_inbound WHERE bin_location = ? AND qty_pcs - out_pcs <> 0 ', $arr_det['location_code'])->fetch_all();

                        if (!empty($get_inbound_detail)) {
                            foreach ($get_inbound_detail as $asar_key => $asar_val) {
                                $aisle_details[] = $asar_val;
                            }
                        } else {
                            $aisle_details[]['bin_location'] = $arr_det['location_code'];
                        }
                    }

                    return $aisle_details;
                }

                function get_bet_summ($db, $item_code)
                {

                    $db_items = $db->query('SELECT 
                    tb_inbound.item_code,
                    tb_inbound.batch_no,
                    tb_inbound.running_balance,
                    tb_inbound.expiry,
                    tb_inbound.mfg,
                    tb_items.material_description
                      FROM tb_inbound INNER JOIN tb_items ON tb_items.item_code = tb_inbound.item_code 
                      where tb_inbound.item_code = ?
                      ', $item_code)->fetch_all();

                    return $db_items;
                }
                function get_bet_dispatch($db, $dispatch_list)
                {
                    $db_dispatch = $db->query('SELECT tb_transport_allocation.*,tb_outbound.truck_allocation
                   
                      FROM tb_transport_allocation 
                      INNER JOIN tb_outbound ON tb_outbound.document_no = tb_transport_allocation.document_no
                      where tb_transport_allocation.document_no= ?
                      ', $dispatch_list)->fetch_all();

                    return $db_dispatch;
                }


                function get_bet_items($db)
                {
                    $all_items = array();

                    $db_items = $db->query('SELECT tb_inbound.item_code,
                    tb_inbound.batch_no,
                       tb_inbound.bin_location,
                          tb_inbound.lpn,
                          tb_inbound.qty_pcs,
                    tb_inbound.qty_pcs,
                    tb_inbound.expiry,
                    tb_inbound.mfg,
                     tb_items.material_description
                      FROM tb_inbound INNER JOIN tb_items ON tb_items.item_code = tb_inbound.item_code ')->fetch_all();

                    foreach ($db_items as $db_key => $db_val) {
                        $all_items[$db_val['item_code']] = $db_val;
                    }

                    return $all_items;
                }

                function get_prev_inbound($db)
                {
                    $all_in = array();
                    $aux_inb = $db->query('SELECT tb_inbound.id,tb_inbound.item_code,
                    tb_inbound.batch_no,
                    tb_inbound.qty_pcs as open_qty,
                     tb_inbound.bin_location,
                      tb_inbound.lpn,
                    tb_inbound.expiry,
                    tb_inbound.mfg,
                    tb_items.material_description
                    
                     FROM tb_inbound
                     inner join tb_items on tb_items.item_code = tb_inbound.item_code')->fetch_all();

                    foreach ($aux_inb  as $db_key => $db_val) {
                        $all_in[$db_val['id']] = $db_val;
                    }

                    return  $all_in;
                }

                function get_bet_inbound_trans($db)
                {
                    //$asar_balance = array();


                    $all_transac = array();
                    $aux_inb = $db->query('SELECT tb_inbound.id,tb_inbound.item_code,
                   tb_inbound.batch_no,
                   tb_inbound.qty_pcs as balance,
                 tb_inbound.expiry,
                  tb_inbound.mfg,
                  tb_items.material_description
                   
                     FROM tb_inbound
                     INNER JOIN tb_items ON tb_items.item_code= tb_inbound.item_code ')->fetch_all();
                    foreach ($aux_inb  as $db_key => $db_val) {
                        $all_transac[$db_val['id']] = $db_val;
                    }


                    return  $all_transac;
                }

                function get_bet_outbound_trans($db)
                {
                    //$asar_balance = array();
                    $all_out = array();
                    $aux_out = $db->query('SELECT  tb_picklist.document_no,tb_picklist.id,tb_picklist.item_code,tb_picklist.qty_pcs,
                  tb_picklist.batch_no,tb_picklist.expiry,tb_picklist.mfg,
                    tb_items.material_description
                
                    FROM tb_picklist
                    INNER JOIN tb_items ON tb_items.item_code = tb_picklist.item_code 
                     ')->fetch_all();
                    foreach ($aux_out  as $db_key => $db_val) {
                        $all_out[$db_val['id']] = $db_val;
                    }

                    return  $all_out;
                }

                function get_batch_4_to_6_months($db)
                {
                    $db_stock_level = $db->query('SELECT 
                                   
                                    tb_inbound.item_code, 
                                    tb_inbound.batch_no, 
                                    DATEDIFF(now(),tb_inbound.expiry) AS Duration,
                                    tb_inbound.expiry, 
                                    tb_inbound.mfg,
                                    tb_inbound.bin_location,
                                    tb_inbound.running_balance AS available,
                                    tb_inbound.id as recID,
                                    tb_inbound.in_picklist,
                                    tb_inbound.status,
                                    tb_items.material_description
                                    FROM tb_inbound 
                                    INNER JOIN tb_bin_location ON tb_bin_location.location_code =tb_inbound.bin_location
                                    INNER JOIN  tb_items ON tb_items.item_code = tb_inbound.item_code
                                   WHERE  tb_bin_location.location_type=? AND DATEDIFF(tb_inbound.expiry, now()) BETWEEN ? AND ?', 'Pickface', 120, 186)->fetch_all();

                    return $db_stock_level;
                }
                function get_bet_item_loc($db, $item_code)
                {
                    $get_items = $db->query('SELECT a.bin_location, a.item_code,a.batch_no, b.material_description, b.pack_size, b.case_per_pallet,b.pcs_per_pallet FROM tb_inbound a INNER JOIN tb_items b ON a.item_code = b.item_code WHERE a.item_code=?', $item_code)->fetch_all();
                    return $get_items;
                }

                function get_bet_location($db, $aisle)
                {

                    $location = $db->query('SELECT 
                    tb_bin_location_bac.id,
                    tb_bin_location_bac.aisle, 
                     tb_bin_location_bac.location_code,
                     tb_bin_location_bac.location_type, 
                    tb_bin_location_bac.status, 
                    tb_bin_location_bac.warehouse,
                     tb_bin_location_bac.item_code,
                    tb_inbound.bin_location,
                     tb_inbound.id as in_id
                    FROM tb_bin_location_bac 
                    INNER JOIN tb_inbound  ON tb_inbound.item_code = tb_bin_location_bac.item_code
                    WHERE tb_bin_location_bac.aisle =? GROUP BY aisle
                    ', $aisle)->fetch_all();

                    return $location;
                }


                /** ------------------------------------- */
                /** FUNCTION GENERATE REF FOR STR CO-LOAD */
                /**-------------------------------------- */
                function generate_ref_coload($db, $cluster)
                {


                    if ($cluster == "A") {

                        $db_control_num_count = $db->query('SELECT ref_no FROM tb_outbound WHERE truck_allocation=\'A\'')->num_rows();

                        if ((int)$db_control_num_count == 0) {
                            /** First Entry */
                            return $control_num = '0000001';
                        } else {

                            $db_control_num = $db->query('SELECT ref_no FROM tb_outbound WHERE truck_allocation=\'A\'')->fetch_all();

                            /** Get the largest control number from the database */
                            $aux = max($db_control_num);


                            extract(str_replace("DVO-", "", $aux));
                            $max_no = $control_no;
                            $control_num = $max_no + 1;
                            $control_num_len = strlen($control_num);


                            switch ($control_num_len) {
                                case 1:
                                    return 'DVO-000000' . $control_num;
                                    break;
                                case 2:
                                    return 'DVO-00000' . $control_num;
                                    break;
                                case 3:
                                    return 'DVO-0000' . $control_num;
                                    break;
                                case 4:
                                    return 'DVO-000' . $control_num;
                                    break;
                                case 5:
                                    return 'DVO-00' . $control_num;
                                    break;
                                case 6:
                                    return 'DVO-0' . $control_num;
                                    break;
                                default:
                                    return $control_num;
                            }
                        }
                        if ($cluster == "B") {

                            $db_control_num_count = $db->query('SELECT ref_no FROM tb_outbound WHERE truck_allocation=\'B\'')->num_rows();

                            if ((int)$db_control_num_count == 0) {
                                /** First Entry */
                                return $control_num = '1000000';
                            } else {

                                $db_control_num = $db->query('SELECT ref_no FROM tb_outbound WHERE truck_allocation=\'B\'')->fetch_all();

                                /** Get the largest control number from the database */
                                $aux = max($db_control_num);
                                extract(str_replace("CEB-", "", $aux));
                                $max_no = $control_no;
                                $control_num = $max_no + 1;

                                return "CEB-" . $control_num;
                            }
                        }
                    }
                }


                function determine_if_accept_batch($in_arr, $date_today)
                {
                    if (are_strings_equal($in_arr['batch_code'], "n/a")) {
                        return true;
                    } else {
                        $days_to_expire = date_diff(date_create($in_arr['exp']), date_create($date_today));
                        if ($days_to_expire->format("%a") <= 182) {
                            return false;
                        }
                    }
                    return true;
                }

                function is_batch_correct($batch_array)
                {
                    foreach ($batch_array as $key => $batch) {
                        if (are_strings_equal($batch, "n/a")) {
                            continue;
                        } else {
                            $batch_year = substr($batch, 0, 1);
                            $batch_month = substr($batch, 2, 1);
                            if (array_key_exists($batch_year, C_YEAR) && array_key_exists($batch_month, C_MONTH)) {
                                continue;
                            } else {
                                return false;
                            }
                        }
                    }
                    return true;
                }


                function is_pallet_have_excess_pcs($arr_name)
                {
                    foreach ($arr_name as $arr_key => $arr_val) {
                        if (are_strings_equal($arr_val['palletization'], "Excess")) {
                            return $arr_key;
                        }
                    }
                    return false;
                }

                function building_full_pallet($db, $full_pallet, $mixed_pallet, $last_inserted_pallet_count)
                {

                    if (!empty($mixed_pallet)) {

                        while (!empty($mixed_pallet)) {

                            while (array_still_has_greater($mixed_pallet)) {

                                foreach ($mixed_pallet as $arr_key => $arr_val) {

                                    if (are_strings_equal($arr_val['palletization'], "Greater")) {


                                        $get_db_details = $db->query('SELECT id, item_code, pack_size, weight_per_box, cbm_per_box, case_per_pallet, pcs_per_pallet, case_per_tier, layer_high FROM tb_items WHERE item_code = ?', $arr_val['item_id'])->fetch_array();

                                        $g_pcs = $arr_val['qty'];

                                        $required_pcs_per_pallet = $get_db_details['pcs_per_pallet'];

                                        while ($g_pcs > $required_pcs_per_pallet) {
                                            if (!empty($full_pallet)) {
                                                $array_key = max(array_keys($full_pallet)) + 1;
                                            } else {
                                                $array_key = 1;
                                            }
                                            $pallet_tag = 'P0' . $last_inserted_pallet_count . '-' . $arr_val['batch_code'];
                                            $full_pallet[$array_key] = $arr_val;
                                            $full_pallet[$array_key]['qty'] = $required_pcs_per_pallet;
                                            $full_pallet[$array_key]['pallet_tag'] = $pallet_tag;
                                            $full_pallet[$array_key]['palletization'] = "Full";
                                            $last_inserted_pallet_count++;
                                            $g_pcs = $g_pcs - $required_pcs_per_pallet;
                                        }

                                        /**
                                         * REMOVE INITIAL KEY IN THE ARRAY
                                         */
                                        unset($mixed_pallet[$arr_key]);
                                        if (!empty($full_pallet)) {
                                            $array_key = max(array_keys($full_pallet)) + 1;
                                        } else {
                                            $array_key = 1;
                                        }
                                        $pallet_tag = 'P0' . $last_inserted_pallet_count . '-' . $arr_val['batch_code'];
                                        $mixed_pallet[$array_key] = $arr_val;
                                        $mixed_pallet[$array_key]['qty'] = $g_pcs;
                                        $mixed_pallet[$array_key]['pallet_tag'] = $pallet_tag;
                                        $mixed_pallet[$array_key]['palletization'] = "Less";
                                        $last_inserted_pallet_count++;
                                    }
                                }
                            }

                            while (array_still_has_less($mixed_pallet)) {

                                $remaining_items_in_mixed_array = count($mixed_pallet);
                                // echobr("Remaining Items In Mixed Array: ". $remaining_items_in_mixed_array);

                                /**
                                 * Get the Minimum Qty in the Mixed Array with Less Tag
                                 */

                                $all_less_items = array();
                                foreach ($mixed_pallet as $ar_mp => $mp_val) {
                                    if (are_strings_equal($mp_val['palletization'], "Less")) {
                                        $all_less_items[$ar_mp]['qty'] = $mp_val['qty'];
                                        $all_less_items[$ar_mp]['item_id'] = $mp_val['item_id'];
                                        $all_less_items[$ar_mp]['mfg'] = $mp_val['mfg'];
                                        $all_less_items[$ar_mp]['exp'] = $mp_val['exp'];
                                        $all_less_items[$ar_mp]['batch_code'] = $mp_val['batch_code'];
                                        $all_less_items[$ar_mp]['pallet_tag'] = $mp_val['pallet_tag'];
                                        $all_less_items[$ar_mp]['palletization'] = $mp_val['palletization'];
                                        $all_less_items[$ar_mp]['remarks'] = $mp_val['remarks'];
                                        $all_less_items[$ar_mp]['arr_id'] = $ar_mp;
                                    }
                                }

                                $get_max = max($all_less_items);

                                $get_min = min($all_less_items);

                                $minmax_total_pcs = $get_min['qty'] + $get_max['qty'];

                                if ($remaining_items_in_mixed_array != 1) {

                                    if ($minmax_total_pcs <= $required_pcs_per_pallet) {

                                        /**
                                         * INSERT MAX TO FULL PALLET ARRAY
                                         */

                                        $array_key = max(array_keys($full_pallet)) + 1;
                                        $full_pallet[$array_key] = $get_max;
                                        $full_pallet[$array_key]['palletization'] = "Full";

                                        /**UNSET IN MIXED PALLET */

                                        unset($mixed_pallet[$get_max['arr_id']]);

                                        /**
                                         * INSERT MIN TO FULL PALLET ARRAY
                                         */

                                        $array_key = max(array_keys($full_pallet)) + 1;
                                        $full_pallet[$array_key] = $get_min;
                                        $full_pallet[$array_key]['pallet_tag'] = $get_max['pallet_tag'];
                                        $full_pallet[$array_key]['palletization'] = "Full";

                                        /**UNSET IN MIXED PALLET */

                                        unset($mixed_pallet[$get_min['arr_id']]);

                                        // print_r_html($full_pallet,"FULL PALLET");
                                        // print_r_html($mixed_pallet,"MIXED PALLET");



                                    } else {

                                        $balance = $required_pcs_per_pallet - $get_max['qty'];
                                        $get_min_remaining_balance = $get_min['qty'] - $balance;

                                        // echobr("BALANCE ".$balance);

                                        /**
                                         * INSERT MAX TO FULL PALLET ARRAY
                                         */

                                        $array_key = max(array_keys($full_pallet)) + 1;
                                        $full_pallet[$array_key] = $get_max;
                                        $full_pallet[$array_key]['palletization'] = "Full";

                                        /**UNSET IN MIXED PALLET */
                                        unset($mixed_pallet[$get_max['arr_id']]);

                                        /**
                                         * INSERT THE BALANCE TO FULL PALLET ARRAY
                                         */

                                        $array_key = max(array_keys($full_pallet)) + 1;
                                        $full_pallet[$array_key] = $get_min;
                                        $full_pallet[$array_key]['qty'] = $balance;
                                        $full_pallet[$array_key]['pallet_tag'] = $get_max['pallet_tag'];
                                        $full_pallet[$array_key]['palletization'] = "Full";

                                        /**
                                         * UPDATE THE MIXED ARRAY WITH THE DEDUCTED QTY FROM GET MIN
                                         */

                                        $array_key = max(array_keys($full_pallet)) + 1;
                                        $mixed_pallet[$get_min['arr_id']]['qty'] = $get_min_remaining_balance;
                                        $mixed_pallet[$get_min['arr_id']]['item_id'] = $get_min['item_id'];
                                        $mixed_pallet[$get_min['arr_id']]['mfg'] = $get_min['mfg'];
                                        $mixed_pallet[$get_min['arr_id']]['exp'] = $get_min['exp'];
                                        $mixed_pallet[$get_min['arr_id']]['batch_code'] = $get_min['batch_code'];
                                        $mixed_pallet[$get_min['arr_id']]['pallet_tag'] = $get_min['pallet_tag'];
                                        $mixed_pallet[$get_min['arr_id']]['palletization'] = $get_min['palletization'];
                                        $mixed_pallet[$get_min['arr_id']]['remarks'] = $mp_val['remarks'];
                                    }
                                } else {

                                    /**
                                     * INSERT THE LAST PALLET FROM MIXED TO FULL PALLET ARRAY
                                     */

                                    $array_key = max(array_keys($full_pallet)) + 1;
                                    $full_pallet[$array_key] = $get_min;
                                    $full_pallet[$array_key]['palletization'] = "Full";

                                    /**UNSET IN MIXED PALLET */
                                    unset($mixed_pallet[$get_min['arr_id']]);
                                }
                            }
                        } //while loop end

                    } else {

                        return $full_pallet;
                    }

                    return $full_pallet;
                }

                function array_multisum(array $arr): float
                {
                    $sum = 0;
                    foreach ($arr as $arr_key => $arr_val) {
                        $sum += $arr_val['qty'];
                    }
                    return $sum;
                }


                function array_still_has_greater($arr)
                {
                    foreach ($arr as $arr_key => $arr_val) {
                        if (are_strings_equal(trim($arr_val['palletization']), "Greater")) {
                            return true;
                        }
                    }
                    return false;
                }

                function array_still_has_less($arr)
                {
                    foreach ($arr as $arr_key => $arr_val) {
                        if (are_strings_equal(trim($arr_val['palletization']), "Less")) {
                            return true;
                        }
                    }
                    return false;
                }

                function get_asn_status($arr, $arr_key)
                {
                    $goods_receipt = 0;
                    $not_posted = 0;
                    $posted_w_issue = 0;

                    if (array_key_exists($arr_key, $arr)) {

                        foreach ($arr as $key => $val) {

                            foreach ($val as $arr_key => $db_val) {
                                if ($db_val['status'] == 1) {
                                    $goods_receipt++;
                                }

                                if ($db_val['status']  == 0) {
                                    $not_posted++;
                                }

                                if ($db_val['status']  == 2) {
                                    $posted_w_issue++;
                                }
                            }
                        }

                        if ($goods_receipt == 0 || $not_posted != 0 || $posted_w_issue == 0) {
                            return 0;
                        }

                        if ($goods_receipt != 0 || $not_posted == 0 || $posted_w_issue == 0) {
                            return 1;
                        }

                        if ($goods_receipt != 0 || $not_posted != 0 || $posted_w_issue != 0) {
                            return 2;
                        }
                    } else {
                        return 0;
                    }
                }

                function is_document_allocated($db, $document_no)
                {

                    $db_rec = $db->query('SELECT id,ref_no,document_no,allocation FROM tb_outbound where document_no = ? ', $document_no)->fetch_all();

                    foreach ($db_rec as $arr_key => $arr_val) {
                        if (are_strings_equal(trim($arr_val['allocation']), "NO")) {
                            return false;
                        }
                    }
                    return true;
                }




                    ?>