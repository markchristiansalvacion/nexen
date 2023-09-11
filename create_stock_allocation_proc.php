<?php
require_once 'includes/load.php';


if(!is_array_has_empty_input($_POST)){

    //print_r_html($_POST);

    $created_by = $db->escape_string($_SESSION['name']);
    $date_today = date('Y-m-d');
    $ref_no = generate_reference_no($db,22);
    // print_r_html($ref_no);

    $required_qty = $_POST['req_qty'];

    echobr("Required: ". $required_qty);
    //print_r_html($_POST);

    $insert_error = 0;
    
    while($required_qty != 0){

        //print_r_html($required_qty,"Current Required QTY");

        $get_sku = $db->query('SELECT
        a.id, 
        a.lpn,
        a.sku_code,
        tb_items.material_description,
        a.qty_case - a.allocated_qty AS available_qty,
        a.expiry AS exp_date,
        a.actual_bin_loc,
        tb_bin_location_bac.deep AS deep,
        tb_items.shelf_life,
        DATEDIFF(a.expiry,CURDATE()) AS days_to_expiry,
        DATEDIFF(a.expiry,CURDATE())/(tb_items.shelf_life*30) AS shelf_life_percentage
        FROM tb_inventory_adjustment a 
        INNER JOIN tb_items ON tb_items.sap_code = a.sku_code
        INNER JOIN tb_bin_location_bac ON tb_bin_location_bac.location_code = a.bin_loc
        WHERE DATEDIFF(a.expiry,CURDATE())/(tb_items.shelf_life*30) >= ?
        AND a.transaction_type = ?
        AND a.qty_case - a.allocated_qty <> 0
        AND a.sku_code = ? 
        AND a.putaway_status <> ?
        AND a.lpn_status = ?
        ORDER BY shelf_life_percentage ASC,available_qty ASC, bin_loc ASC, deep ASC
        LIMIT 1 ',$_POST['req_shelf_life'],"INB",$_POST['sku_code'],"Pending","Active")->fetch_array();

        $available_qty = $get_sku['available_qty'];
        
       // print_r_html($get_sku);
        // echobr("Available Qty:".$available_qty);

        // print_r_html($available_qty);
        // print_r_html($get_sku,"Suggested SKU");
        
        /**
         * INSERT TO PICKLIST TABLE
         * 1. Always check if available qty is greater than required
         * 2. If not then proceed with the normal allocation and use the available qty as the qty to allocate
         */
        if($available_qty >= $required_qty){
            
        
            echobr("Available Qty ".$available_qty." is Greater than or Equalt to the Required Qty ".$required_qty);

            /** Use the required qty as the allocated number */
            // print_r_html($get_sku['available_qty'],'Suggested Qty:');
            $insert_to_picklist = $db->query('INSERT INTO tb_picklist (ref_no,so_no,ia_id, to_id,allocated_lpn, allocated_sku_code,allocated_qty, allocated_expiry, bin_loc, date_created, created_by) VALUE(?,?,?,?,?,?,?,?,?,?,?)', $ref_no,$_POST['so_no'],$get_sku['id'], $_POST['to_db_id'],$get_sku['lpn'], $get_sku['sku_code'],$required_qty,$get_sku['exp_date'],$get_sku['actual_bin_loc'],$date_today,$created_by);
            
            // echobr("Inserted Qty to Picklist:".$required_qty);

            if($insert_to_picklist->affected_rows()){

                /** UPDATE TB_Inventory Adjustment */
                $update_tb_ia = $db->query('UPDATE tb_inventory_adjustment SET allocated_qty = allocated_qty + ? WHERE id = ?',$required_qty,$get_sku['id']);

                if($update_tb_ia->affected_rows()){

                    /** UPDATE Transfer Order Table Picked Qty */
                    $update_tb_to = $db->query('UPDATE tb_transfer_order SET allocated_qty = allocated_qty + ? , allocated_by = ? WHERE id = ?',$required_qty,$created_by,$_POST['to_db_id']);

                    if($update_tb_to->affected_rows()){
                        $required_qty = $required_qty - $required_qty;
                        // echobr("New Required Qty:".$required_qty);
                        continue;
                    }else{
                        /** FAILED UPDATE
                         * REMOVE INSERTED TO PICKLIST
                         */
                    }
                  
                }else{
                    /** FAILED UPDATE
                     * REMOVE INSERTED TO PICKLIST
                     */
                }
                
            }else{

                /**
                 * FAILED INSERT
                 * REMOVE INSERTED TO PICKLIST TABLE
                 * AND DEDUCT PREVIOUSLY ALLOCATED IN TB INVENTORY ADJUSTMENT
                 */
            }
            
        }else{ 
            
            echobr("Available Qty ".$available_qty." is Less than the Required Qty ".$required_qty);
            //print_r_html($get_sku);
            /**
             * Available Qty could be less than or equal to the required quantity use the available qty as the qty to be allocated
             */
            $insert_to_picklist = $db->query('INSERT INTO tb_picklist (ref_no,so_no,ia_id, to_id,allocated_lpn, allocated_sku_code,allocated_qty, allocated_expiry, bin_loc, date_created, created_by) VALUE(?,?,?,?,?,?,?,?,?,?,?)', $ref_no,$_POST['so_no'],$get_sku['id'], $_POST['to_db_id'],$get_sku['lpn'], $get_sku['sku_code'],$available_qty,$get_sku['exp_date'],$get_sku['actual_bin_loc'],$date_today,$created_by);

            echobr("Inserted Qty to Picklist:".$available_qty);

            if($insert_to_picklist->affected_rows()){

                /** UPDATE TB_Inventory Adjustment */
                $update_tb_ia = $db->query('UPDATE tb_inventory_adjustment SET allocated_qty = allocated_qty + ? WHERE id = ?',$available_qty,$get_sku['id']);

                if($update_tb_ia->affected_rows()){

                    /** UPDATE Transfer Order Table Picked Qty */
                    $update_tb_to = $db->query('UPDATE tb_transfer_order SET allocated_qty = allocated_qty + ? , allocated_by = ? WHERE id = ?',$available_qty,$created_by,$_POST['to_db_id']);

                    if($update_tb_to->affected_rows()){
                        $required_qty = $required_qty - $available_qty;
                        // echobr("New Required Qty:".$required_qty);
                        continue;
                    }else{
                        /** FAILED UPDATE
                         * REMOVE INSERTED TO PICKLIST
                         */
                    }
                  
                }else{
                    /** FAILED UPDATE
                     * REMOVE INSERTED TO PICKLIST
                     */
                }
                
            }else{

                /**
                 * FAILED INSERT
                 * REMOVE INSERTED TO PICKLIST TABLE
                 * AND DEDUCT PREVIOUSLY ALLOCATED IN TB INVENTORY ADJUSTMENT
                 */
            }
        }
    
    }

    /** IF NO ERROR THEN PROCEED WITH UPDATING THE FOLLOWING
     * 1. Update the status of the transfer order
     * 2. Send Success Message
     */

        if($insert_error == 0){
            /**Update Transfer Order Allocation Status */
            $update_to_status = $db->query('UPDATE tb_transfer_order SET status = ? WHERE id = ? ',"Picklist Issuance", $_POST['to_db_id']);

            if($update_to_status->affected_rows()){
                $_SESSION['msg_heading'] = "Stock Allocation Success!";
                $_SESSION['msg'] = "Automatic Stock Allocation Complete!";
                $_SESSION['msg_type'] = "success";
                redirect("create_stock_allocation?sku_code={$_POST['sku_code']}&customer_code={$_POST['customer_code']}&rdd={$_POST['rdd']}&alloc_status={$_POST['alloc_status']}");
            }
        }



}else{
    // Error
    $_SESSION['msg_heading'] = "Upload Error!";
    $_SESSION['msg'] = "Transaction Failed. There are missing items upon validation. If this persist, please contact your System Administrator.";
    $_SESSION['msg_type'] = "error";
    redirect("create_stock_allocation?sku_code={$_POST['sku_code']}&customer_code={$_POST['customer_code']}&rdd={$_POST['rdd']}&alloc_status={$_POST['alloc_status']}");
}

?>