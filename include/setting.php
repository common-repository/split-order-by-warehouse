<?php 
// Save/Update configuration value
global $wpdb;
if(sanitize_text_field(!empty($_POST['submit']))){
	  $configVal = sanitize_text_field($_POST['split_by_warehouse_falg']);
	  $splitorderprowarehouse = sanitize_text_field($_POST['splitorderwarehouse']);
	  $optionVal = get_option( 'split_by_warehouse_falg' );
	  $option_name = 'split_by_warehouse_falg' ;
	  $option_name_split_order = 'splitorderwarehouse' ;
      $new_value = $configVal;
      update_option( $option_name, $new_value );
      update_option( $option_name_split_order, $splitorderprowarehouse );
     echo "<div class='form-save-msg'>Changes Saved!</div>";
}
  $optionVal = get_option( 'split_by_warehouse_falg' );
  $splitorderpro = get_option( 'splitorderwarehouse' );
?>

 <h1>General Configuration</h1>
    <div class="row">
        <div class="form-group">
            <form action="" method="post">
                <div><label for="sort" class="col-sm-2 control-label"> Enable split order </label>
                    <select class="form-control" name="split_by_warehouse_falg" id="sort">
					<option value="yes" <?php
                        if ($optionVal == 'yes') {
                            echo 'selected';
                        }
                        ?>>Yes</option>
                        <option value="no" <?php
                        if ($optionVal == 'no') {
                            echo 'selected';
                        }
                        ?>>No</option>
                        
                    </select> 
                </div> 
                <br>
                <div>

                    <label for="sort" class="col-sm-2 control-label"> Split Warehouse  Conditions </label>
                    <select class="form-control" name="splitorderwarehouse" id="sort">
                        <option value="default" <?php
                        if ($splitorderpro == 'default') {
                            echo 'selected';
                        }
                        ?>>Default</option>
                    
						

                    </select> 
                    
                    <input type="submit" name="submit" value="save config">
                </div>
            </form>
        </div>
    </div>