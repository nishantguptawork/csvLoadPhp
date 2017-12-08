<html>
    
	<?php
	
	// To properly format the JSON data
	function pretty_json($json) {
		$json = preg_replace( "/\"(\d+)\"/", '$1', $json );
		$result      = '';
		$pos         = 0;
		$strLen      = strlen($json);
		$indentStr   = '  ';
		$newLine     = "\n";
		$prevChar    = '';
		$outOfQuotes = true;
	
		for ($i=0; $i<=$strLen; $i++) {
			$char = substr($json, $i, 1);
			if ($char == '"' && $prevChar != '\\') {
				$outOfQuotes = !$outOfQuotes;

			} else if(($char == '}' || $char == ']') && $outOfQuotes) {
				$result .= $newLine;
				$pos --;
				for ($j=0; $j<$pos; $j++) {
					$result .= $indentStr;
				}
			}
			$result .= $char;
			
			if (($char == ',' || $char == '{' || $char == '[') && $outOfQuotes) {
				$result .= $newLine;
				if ($char == '{' || $char == '[') {
					$pos ++;
				}
	
				for ($j = 0; $j < $pos; $j++) {
					$result .= $indentStr;
				}
			}
	
			$prevChar = $char;
		}
	
		return $result;
	}
	
	
	
	$amount = 0;
	$row = 0;
	
	// Read the values here
	if (isset($_GET["amount"])) {
		$amount = $_GET["amount"];
	}
	
	// Open CSV file
	if (($handle = fopen("emi.csv", "r")) !== FALSE) {
	
		$result_array = array();
		
		// Parse the rows
		while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
		
			$num = count($data);

			$row++;
			
			if (1 == $row) {
				continue;
			}
			
			// Read the columns in the row
			$lender = $data[0];
			$months = $data[1];
			$rate = $data[2];
			$minimum_amount = $data[3];
			
			// Filtering logic
			if ($amount >= ($minimum_amount)) {
			
				$tenures = array(
					"months" => $months,
					"rate" => $rate,
					"minimum_amount" => $minimum_amount,
				);
				
				$bank_data = array(
					"bank" => $lender,
					"tenures" => $tenures
				);
				
				array_push($result_array, $bank_data);
			}
		}
		
		fclose($handle);
		
		echo "<pre>".pretty_json(json_encode($result_array))."<pre>";
	}

	?>
    
</html>