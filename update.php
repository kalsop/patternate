<?php

	require_once('preheader.php'); // <-- this include file MUST go first before any HTML/output


?>
<!DOCTYPE html PUBLIC"-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
	<head>
	
	
	    <style type="text/css">
	        body {font-size:  20px;}
	        fieldset {width: 29%; float:left; margin-right: 15px;}
	        /*label {display:block; float:left; width: 200px;}*/
	        label + input[type=checkbox] {width: auto;}
	         input[type=text] {display:block; }
	         select {display:block; }
	         input + label {/*margin-top: 20px;*/}
	         legend {margin-top: 30px; font-weight: bold;}
	         /*fieldset label {display:block; float:left; width: 50px; margin-top: 15px;}*/
	         fieldset input + label {margin-right: 15px; padding: 5px;}
	         fieldset {margin-top:  20px;}
	         .collection {margin-left: 20px;}
	         .patternCompanies {list-style: none; padding: 0; margin: 0;}
	        input[type="checkbox"] + label {margin-top: -25px;}
	         input[type="radio"] {display: block;
                 margin-left: 0;
                 margin-top: 20px;}
	         label {display: block;
                 margin-left: 20px;
                 margin-top: -20px;
             }
       
        input[type="checkbox"] {
            font-size: 1.2em;
        }
        label[for="patternNumber"] {display:none;}
	    </style>
	</head>
	<body>
	  
	  <?php
	  
	  


	  
	  
	  
	  // Get selected pattern
	  
  	$query = "SELECT * FROM patterns where id=5";
    $result= mysql_query($query) or die(mysql_error());
    
    
    while ($row = mysql_fetch_assoc($result)) { 
     $rows[] = $row; 
    } 
   
    // Set selected pattern varaibles for later use
    $patternID = $rows[0]['id'];
    $patternNumber = $rows[0]['pattern_number'];
    $patternCompanyID = $rows[0]['pattern_company_id'];
    $sizesExplode = $rows[0]['sizes_id'];  
    $sizes_id_array = explode( ',', $sizesExplode );
    $garmentTypeExplode = $rows[0]['garment_type_id'];
    $garmentType_id_array = explode( ',', $garmentTypeExplode );
    $patternForExplode = $rows[0]['pattern_for_id'];
    $pattern_for_id_array = explode( ',', $patternForExplode );
    $fabricExplode = $rows[0]['fabric_id'];
    $fabric_id_array = explode( ',', $fabricExplode );

	  
	  
	  
	  ?>
	  
	  
	  <form action="update.php" method="post">
	    <fieldset>
	    <legend>Pattern company and collection</legend>
        <ul class="patternCompanies">
	    <?php
  
      // Get list of pattern companies
    
    	$getPatternCompaniesQuery = "SELECT * FROM pattern_company";
      $getPatternCompaniesResult= mysql_query($getPatternCompaniesQuery) or die(mysql_error());
    
       while ($getPatternCompaniesRow = mysql_fetch_assoc($getPatternCompaniesResult)) { 
       $getPatternCompaniesRows[] = $getPatternCompaniesRow; 
     } 
   
     for($i=0;$i<count($getPatternCompaniesRows);$i++) { 
     
     
     
     
       $getPatternCompaniesID = $getPatternCompaniesRows[$i]['id'];
       $getPatternCompaniesName = $getPatternCompaniesRows[$i]['name'];
       $getPatternCompaniesSlug = $getPatternCompaniesRows[$i]['slug'];
           
           echo '<li><input type="radio" name="patternCompany" value="' . $getPatternCompaniesID . '" id="' . $getPatternCompaniesSlug . '"';
           if ($getPatternCompaniesID == $patternCompanyID) {
               echo 'checked ';
           } 
           echo '>';
           echo '<label for="' . $getPatternCompaniesSlug . '">';
           echo $getPatternCompaniesName;
           echo '</label>';
           
           $getPatternCompaniesCollectionQuery = "SELECT * FROM collection WHERE pattern_company_id = $getPatternCompaniesID";
           $getPatternCompaniesCollectionResult= mysql_query($getPatternCompaniesCollectionQuery) or die(mysql_error());
           
           
           while ($getPatternCompaniesCollectionRow = mysql_fetch_assoc($getPatternCompaniesCollectionResult)) { 
               $getPatternCompaniesCollectionRows[] = $getPatternCompaniesCollectionRow; 
           } 
         
           for($z=0;$z<count($getPatternCompaniesCollectionRows);$z++) { 
               
               $getPatternCompaniesCollectionID = $getPatternCompaniesCollectionRows[$z]['id'];
               $getPatternCompaniesCollectionName = $getPatternCompaniesCollectionRows[$z]['name'];
               $getPatternCompaniesCollectionSlug = $getPatternCompaniesCollectionRows[$z]['slug'] . '-' . $getPatternCompaniesSlug;
               $getPatternCompaniesCollectionParentID = $getPatternCompaniesCollectionRows[$z]['pattern_company_id'];
               
              
               if ($getPatternCompaniesCollectionParentID == $getPatternCompaniesID) {
               
               echo '<li class="collection"><input type="radio" name="' . $getPatternCompaniesSlug . '" value="' . $getPatternCompaniesCollectionID . '" id="' . $getPatternCompaniesCollectionSlug . '"';
               echo '>';
               echo '<label for="' . $getPatternCompaniesCollectionSlug . '">';
               echo $getPatternCompaniesCollectionName;
               echo '</label></li>';
            }
            
              
               
       
               // echo '<input type="checkbox" value="' . $getPatternCompaniesCollectionID . '" id="' . $getPatternCompaniesCollectionSlug . '"';
               //                echo '>';
               //                echo '<label for="' . $getPatternCompaniesCollectionSlug . '">';
               //                echo $getPatternCompaniesCollectionName;
               //                echo '</label>';
           
           
           }
          
          
       
   }
  

       ?>
</fieldset>

      
      <!--<a href="">Add new pattern company</a>--> 
    
    <fieldset>
        <legend>Pattern number</legend>
      <label for="patternNumber">Pattern number</label>
      <input type="text" id="patternNumber" value="<?php echo $patternNumber; ?>">
      </fieldset>
      <fieldset>
      <legend>Sizes</legend>
      
        
        <?php
  
      // Get list of sizes
    
        $getSizesQuery = "SELECT * FROM sizes order by name ASC";
                      $getSizesResult= mysql_query($getSizesQuery) or die(mysql_error());
                    
                       while ($getSizesRow = mysql_fetch_assoc($getSizesResult)) { 
                       $getSizesRows[] = $getSizesRow; 
                     } 
                   
                     for($j=0;$j<count($getSizesRows);$j++) { 
                     
                       $sizesID = $getSizesRows[$j]['id'];
                       $sizesName = $getSizesRows[$j]['name'];
                       //echo $sizesID . " ";
                       //echo $sizesName . "   ";
                   
             
                       
                       
                       echo '<input type="checkbox" value="' . $sizesID . '" id="' . $sizesID . '"';

                       // Pre-select existing sizes
                       // 1. Get array of sizes from the selected pattern - ($sizesExplode)
                       // 2. Cycle through the array and check when printing each size from the available size list
                       // 3. If the size ID is in the array, then check the check box 
                        
                        for ($n=0;$n<count($sizes_id_array);++$n) {
                            if ($sizes_id_array[$n] == $sizesID) {
                                echo 'checked ';
                            } 
                        }
                        
                        echo '>';
                        
                        echo '<label for="' . $sizesID . '">';
                     
                       echo $sizesName;
                       
                       echo '</label>';

                    }
                    ?>
                </fieldset>
                
                <fieldset>
                <legend>Garment type</legend>
        
        <?php
  
  
        // Get garment types
    
        $getGarmentTypeQuery = "SELECT * FROM garment_type order by name ASC";
                      $getGarmentTypeResult= mysql_query($getGarmentTypeQuery) or die(mysql_error());
                    
                       while ($getGarmentTypeRow = mysql_fetch_assoc($getGarmentTypeResult)) { 
                       $getGarmentTypeRows[] = $getGarmentTypeRow; 
                     } 
                   
                     for($m=0;$m<count($getGarmentTypeRows);$m++) { 
                     
                       $garmentTypeID = $getGarmentTypeRows[$m]['id'];
                       $garmentTypeName = $getGarmentTypeRows[$m]['name'];
                       $garmentTypeSlug = $getGarmentTypeRows[$m]['slug'];
             
                       
                       
                       echo '<input type="checkbox" value="' . $garmentTypeID . '" id="' . $garmentTypeSlug . '"';

                       // Pre-select existing sizes
                       // 1. Get array of sizes from the selected pattern - ($sizesExplode)
                       // 2. Cycle through the array and check when printing each size from the available size list
                       // 3. If the size ID is in the array, then check the check box 
                        
                        for ($n=0;$n<count($garmentType_id_array);++$n) {
                            if ($garmentType_id_array[$n] == $garmentTypeID) {
                                echo 'checked ';
                            } 
                        }
                        
                        echo '>';
                        echo '<label for="' . $garmentTypeSlug . '">';
                     
                       echo $garmentTypeName;
                       
                       echo '</label>';

                    }
                
                
                  
                
                      
  
  
      ?>
      </fieldset>
      
      
      <fieldset>
      <legend>Pattern for</legend>
      
      
      <?php 
      
      // Get pattern for
    
        $getPatternForQuery = "SELECT * FROM pattern_for order by name ASC";
                      $getPatternForResult= mysql_query($getPatternForQuery) or die(mysql_error());
                    
                       while ($getPatternForRow = mysql_fetch_assoc($getPatternForResult)) { 
                       $getPatternForRows[] = $getPatternForRow; 
                     } 
                   
                     for($o=0;$o<count($getPatternForRows);$o++) { 
                     
                       $patternForID = $getPatternForRows[$o]['id'];
                       $patternForName = $getPatternForRows[$o]['name'];
                       $patternForSlug = $getPatternForRows[$o]['slug'];
             
                      
                       
                       echo '<input type="checkbox" value="' . $patternForID . '" id="' . $patternForSlug . '"';

                       // Pre-select existing sizes
                       // 1. Get array of sizes from the selected pattern - ($sizesExplode)
                       // 2. Cycle through the array and check when printing each size from the available size list
                       // 3. If the size ID is in the array, then check the check box 
                        
                        for ($p=0;$p<count($pattern_for_id_array);++$p) {
                            if ($pattern_for_id_array[$p] == $patternForID) {
                                echo 'checked ';
                            } else {
                                echo 'djsklj ';
                            } 
                        }
                        
                        echo '>';
                         echo '<label for="' . $patternForSlug . '">';
                     
                       echo $patternForName;
                       
                       echo '</label>';

                    }
                    ?>
                </fieldset>
                
                <fieldset>
                    
            <legend>Fabric</legend>
      
      <?php 
      
      // Get fabric
    
        $getFabricQuery = "SELECT * FROM fabrics order by name ASC";
                      $getFabricResult= mysql_query($getFabricQuery) or die(mysql_error());
                    
                       while ($getFabricRow = mysql_fetch_assoc($getFabricResult)) { 
                       $getFabricRows[] = $getFabricRow; 
                     } 
                   
                     for($q=0;$q<count($getFabricRows);$q++) { 
                     
                       $fabricID = $getFabricRows[$q]['id'];
                       $fabricName = $getFabricRows[$q]['name'];
                       $fabricSlug = $getFabricRows[$q]['slug'];
             
                      
                       
                       echo '<input type="checkbox" value="' . $fabricID . '" id="' . $fabricSlug . '"';

                       // Pre-select existing sizes
                       // 1. Get array of sizes from the selected pattern - ($sizesExplode)
                       // 2. Cycle through the array and check when printing each size from the available size list
                       // 3. If the size ID is in the array, then check the check box 
                        
                        for ($r=0;$r<count($fabric_id_array);++$r) {
                            if ($fabric_id_array[$r] == $fabricID) {
                                echo 'checked ';
                            } else {
                                echo 'djsklj ';
                            } 
                        }
                        
                        echo '>';
                         echo '<label for="' . $fabricSlug . '">';
                     
                       echo $fabricName;
                       
                       echo '</label>';

                    }
                    ?>
                    </fieldset>
        <input type="hidden" name="patternID" value="<?php echo $patternID; ?>">
      <input type="submit" id="update" name="update" value="Update">
    </form>
 

	  
	</body>
</html>