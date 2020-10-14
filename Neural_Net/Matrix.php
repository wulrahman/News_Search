<?php


class Matrix {

	public function matrixOperation($operation, $matrix) {

		$ret = [];

		foreach($matrix as $row_number => $values) {

			foreach($values as $col_number => $value) {

				$ret[$row_number][$col_number] = $this->$operation($value);

			}

		}

		return $ret;
	}

    public function matrixSums($matrix) {

		$ret = (float) 0;

		foreach($matrix as $values) {

			foreach($values as $value) {

				$ret+= (float) $value;

			}

		}

		return $ret;
	}

	public function relu($x) {

		return $x * (($x > 0) ? 1 : 0);

	}

	public function log($x) {

		return log($x);

	}

	public function sigmoid($x) {

		return ( 1 / (1 + 1/(exp($x)) ) );

	}

	public function negative($x) {

		return $x * (-1);

	}

	public function tanh($x) {

		return tanh($x);

	}

	public function matrixSoftmax($matrix) {
        
		$softmaxed = [];

		foreach($matrix as $row_number => $row) {
            
			$row_somm= 0;
            
			foreach($row as $col_number => $value) {
                
				$row_somm += exp($value);
			}
            
			foreach($row as $col_number => $value) {
                
				if ($row_somm == 0) {
                    
                    $softmaxed[$row_number][$col_number] = isset($softmaxed[$row_number][$col_number]) ? $softmaxed[$row_number][$col_number] : 0;
                    
                }
				else {
                    
                    $softmaxed[$row_number][$col_number] = exp($value) / $row_somm;
                }
                
                
			}
            
		}

		return $softmaxed;

	}

	public function matrixSumValue($matrix, $val_par) {
        
		$ret = [];

		$val_par = $this->extractValue($val_par);

		foreach($matrix as $row_number => $values) {
            
			foreach($values as $col_number => $value) {
                
				$ret[$row_number][$col_number] = $value + $val_par;
                
			}
            
		}

		return $ret;
        
	}

	public function matrixDifference ($matrix1, $matrix2) {

		if(count($matrix1) > count($matrix2)) {

			foreach($matrix1 as $key => $value) {
			
				$random_key = array_rand($matrix2, 1);

				$arrays[$key] = $this->matrixSub([$value], [$matrix2[$random_key]])[0];

			}

		}
		else if(count($matrix1) > count($matrix2)) {
			
			foreach($matrix2 as $key => $value) {
			
				$random_key = array_rand($matrix1, 1);

				$arrays[$random_key] = $this->matrixSub([$matrix1[$random_key]], [[$value]])[0];

			}

		}
		else {
			$arrays = $this->matrixSub($matrix1, $matrix2);

		}

		return $arrays;
		
	}
    
    public function matrixspower($matrix, $power) {
        
		$ret = [];

		foreach($matrix as $row_number => $values) {
            
			foreach($values as $col_number => $value) {
                
				$ret[$row_number][$col_number] = pow($value, $power);
                
			}
            
		}

		return $ret;
        
	}

	public function matrixTimesValue($matrix, $val_par) {
        
		$ret = [];
		// extracting the value in case of a matrix 1x1 dimention
		$val_par = $this->extractValue($val_par);

		foreach($matrix as $row_number => $values) {
            
			foreach($values as $col_number => $value) {
                
				$ret[$row_number][$col_number] = $value * $val_par;
                
			}
            
		}

		return $ret;
        
	}

	public function extractValue($val) {

		if (is_numeric($val)) {
			
			return $val;

		}

		if (count($val) > 1) {
			
			throw new Exception('Not correct value! count($val) > 1 ');

		}

		if (!isset($val[0])) {
			
			throw new Exception('Not correct value! !isset($val[0])');

		}

		if (count($val[0]) > 1) {
			
			throw new Exception('Not correct value! count($val[0]) > 1');

		}

		return $val[0][0];

	}


	public function matrixSum($matrix1, $matrix2) {
		
		$rows_matrix1 = count($matrix1);
		$cols_matrix1 = count($matrix1[0]);
		$rows_matrix2 = count($matrix2);
		$cols_matrix2 = count($matrix2[0]);
        
		if (($cols_matrix1 != $cols_matrix2) or ($rows_matrix1 != $rows_matrix2)) {
            
				throw new Exception('The matrices cannot be added!');
				
        }

		$sum = [];

		for ($row=0; $row<$rows_matrix1; $row++) {
            
			for ($col=0; $col<$cols_matrix1; $col++) {
                
				$sum[$row][$col] = $matrix1[$row][$col] + $matrix2[$row][$col];
                
			}
            
		}

		return $sum;
        
	}

	public function matrixSub($matrix1, $matrix2) {

		$rows_matrix1 = count($matrix1);
		$cols_matrix1 = count($matrix1[0]);
		$rows_matrix2 = count($matrix2);
		$cols_matrix2 = count($matrix2[0]);
        
		if (($cols_matrix1 != $cols_matrix2) or ($rows_matrix1 != $rows_matrix2)) {
            
            throw new Exception('The matrices cannot be subtracted!');
            
        }

		$sum = [];

		for ($row=0; $row<$rows_matrix1; $row++) {
            
			for ($col=0; $col<$cols_matrix1; $col++) {
                
				$sum[$row][$col] = $matrix1[$row][$col] - $matrix2[$row][$col];
                
			}
            
		}

		return $sum;
	}


	public function matrixProductValueByValue($matrix1, $matrix2) {

		$rows_matrix1 = count($matrix1);
		$cols_matrix1 = count($matrix1[0]);
		$rows_matrix2 = count($matrix2);
		$cols_matrix2 = count($matrix2[0]);
        
		if (($cols_matrix1 != $cols_matrix2) or ($rows_matrix1 != $rows_matrix2)) {
            
            throw new Exception('The matrices cannot be multiplied Value by Value!');
            
        }

		$sum = [];

		for ($row=0; $row<$rows_matrix1; $row++) {
            
			for ($col=0; $col<$cols_matrix1; $col++) {
                
				$sum[$row][$col] = $matrix1[$row][$col] * $matrix2[$row][$col];
                
			}
            
		}

		return $sum;
        
	}

	public function arrayArgmax($array) {
        
		if (!count($array)) {
            
            return false;
            
        }

		$res = array_keys($array, max($array));
        
		if (isset($res[0])) {
            
            return $res[0];
        }

		return false;
        
	}

	public function matrixDotProduct($matrix1, $matrix2) {

		$rows_matrix1 = count($matrix1);
		$cols_matrix1 = count($matrix1[0]);
		$rows_matrix2 = count($matrix2);
		$cols_matrix2 = count($matrix2[0]);
        
        if ($cols_matrix1 != $rows_matrix2 ) {
            
            throw new Exception('The matrices cannot dot be multiplied!');
            
        }

		$prod = [];

		for ($row1=0; $row1<$rows_matrix1; $row1++) {
            
			for ($col2=0; $col2<$cols_matrix2; $col2++) {
                                
				for ($row2=0; $row2<$rows_matrix2; $row2++) {
                    
					$prod[$row1][$col2] = $matrix1[$row1][$row2] * $matrix2[$row2][$col2];
				}
                
			}
            
		}

		return $prod;

	}

	public function matrixTranspose($matrix) {
        
		$transpose= [];

		foreach($matrix as $row_number => $values) {
            
			foreach($values as $col_number => $value) {
                
				$transpose[$col_number][$row_number] = $value;
                
			}
            
		}

		return $transpose;
        
	}

	public function arrayTranspose($array) {
        
		if (is_array($array[0])) {
			//return $array;
			throw new Exception('$array is not an array of numbers!');
		}

		$transpose= [];

		foreach($array as $row_number => $value) {
            
			$transpose[$row_number][0] = $value;
            
		}

		return $transpose;
        
	}


	public function sumMatrixVertically($matrix1, $matrix2) {
        
        $rows_matrix1 = count($matrix1);
		$cols_matrix1 = count($matrix1[0]);
		$rows_matrix2 = count($matrix2);
		$cols_matrix2 = count($matrix2[0]);

		if ($rows_matrix1 != $rows_matrix2) {
            
            throw new Exception('The matrix have different rows number!');
            
        }
        
		if (($cols_matrix1 != 1) and ($cols_matrix2 != 1)) {
            
            throw new Exception('One of the matrix must be a vesctor!');
            
        }

		// checking who is the matrix and vector between $m1 or $m1
		$matrix = ($cols_matrix1 != 1) ? $matrix1 : $matrix2;
		$vector = ($cols_matrix1 == 1) ? $matrix1 : $matrix2;

		$new = [];

		foreach($matrix as $row_number => $row) {
            
			foreach($row as $col_number => $element) {
                
				$new[$row_number][$col_number] = $element + $vector[$row_number][0];
                
			}
            
		}

		return $new;
	}


	public function sumMatrixElementVertically($matrix) {
        
		$new = [];

		if (is_array($matrix[0])) {
            
			foreach ($matrix as $line => $row) {
                
				if (!isset($new[$line])) $new[$line][0] = 0;
                
				foreach($row as $element) {
                    
					$new[$line][0] += $element;
                    
				}
                
			}
            
		}

		return $new;
	}


	public function getScalarValue($matrix) {
        
		if (is_array($matrix)) {
            
			if (is_array($matrix[0])) {
                
                return $matrix[0][0];
                
            }
			else {
                
                return $matrix[0];
                
            }
            
		}
		
		return $matrix;
        
	}
	

	public function getRandMatrix($rows, $cols) {
        
		if ($rows < 1) {
            
            throw new Exception('Matrix ROWS must be greater than 0!');
            
        }
        
		if ($cols< 1) {
            
            throw new Exception('Matrix COLUMNS must be greater than 0!');
            
        }

		$matrix = [];
                
		for($row=0; $row<$rows; $row++) {
            
			for($col=0; $col<$cols; $col++) {
                
				$matrix[$row][$col] = rand(-1000000,1000000)/1000000;
                
			}
            
		}

		return $matrix;
	}


    //
    public function ReShapeMatrix($matrix, $rows, $cols) {
        
		if ($rows < 1) {
            
            throw new Exception('Matrix ROWS must be greater than 0!');
            
        }
        
		if ($cols< 1) {
            
            throw new Exception('Matrix COLUMNS must be greater than 0!');
            
        }
    
        $row_number = count($matrix);
		$col_number = count($matrix[0]);
        
        $new = array();
        
        for ($row=0; $row<$row_number; $row++) {
            
			for ($col=0; $col<$col_number; $col++) {
                
				$new[] = $matrix[$row][$col];
                
			}
            
		}

		$matrixs = [];

        $i = 0;
        
		for($row=0; $row<$rows; $row++) {
            
			for($col=0; $col<$cols; $col++) {
                            
                if(array_key_exists($i, $new)) {
                    
                    $matrixs[$row][$col] = $new[$i];
                    
                }
                else {
                    
                    $matrixs[$row][$col] = 0;

                }
                
                $i++;
			}
		}

		return $matrixs;
	}
    
    public function reshape_to_match($action, $matrix1, $matrix2) {
        
        $matrix1_column = count($matrix1[0]);
                
        $matrix1_row = count($matrix1);
            
        $matrix2_column = count($matrix2[0]);
            
        $matrix2_row = count($matrix2);
        
        if($action == ".") {
        
            if($matrix1_column !== $matrix2_row) {

                if($matrix1_column > $matrix2_row) {
                    
                    $new_row =ceil(($matrix2_row * $matrix2_column)/$matrix1_column);
                    
                    if(intval($new_row) == 0) {
                        $new_row = $matrix1_row;
                    }

                    $matrix2 = $this->ReShapeMatrix($matrix2, $matrix1_column, $new_row);

                }
                else {
                    
                    $new_row = ceil(($matrix1_row * $matrix1_column)/$matrix2_row);
                    
                    
                    if(intval($new_row) == 0) {
                        $new_row = $matrix2_column;
                    }

                    $matrix1 = $this->ReShapeMatrix($matrix1, $new_row, $matrix2_row);

                }

            }
            
        }
        else if($action == "-" || $action == "+" || $action == "*"){

            if($matrix1_row !== $matrix2_row || $matrix1_column !== $matrix2_column) {

                if(($matrix1_row*$matrix1_column) >  ($matrix2_row*$matrix2_column)) {

                    $matrix2 = $this->ReShapeMatrix($matrix2, $matrix1_row, $matrix1_column);

                }
                else {

                    $matrix1 = $this->ReShapeMatrix($matrix1, $matrix2_row, $matrix2_column);

                }

            }
        }
        
        return array(0 => $matrix1, 1 => $matrix2);
        
	}
	
	public function getZeroMatrix($rows, $cols) {
        
		if ($rows < 1) {
            
            throw new Exception('Matrix ROWS must be greater than 0!');
            
        }
        
		if ($cols< 1) {
            
            throw new Exception('Matrix COLUMNS must be greater than 0!');
            
        }

		$matrix = [];

		for($row=0; $row<$rows; $row++) {
            
			for($col=0; $col<$cols; $col++) {
                
				$matrix[$row][$col] = 0;
                
			}
            
		}

		return $matrix;
	}
    
    public function getOneMatrix($rows, $cols) {
        
		if ($rows < 1) {
            
            throw new Exception('Matrix ROWS must be greater than 0!');
            
        }
        
		if ($cols< 1) {
            
            throw new Exception('Matrix COLUMNS must be greater than 0!');

        }
        
		$matrix = [];

		for($row=0; $row<$rows; $row++) {
            
			for($col=0; $col<$cols; $col++) {
                
				$matrix[$row][$col] = 1;
			}
            
            
		}

		return $matrix;
        
	}

	function dd($val=null, $stop=true) {
		
		echo '<pre>';
		
		print_r($val);
		
		echo '</pre>';
		
		if ($stop) {
			
			die();
		}
		
	}


	function sm($matrix, $label=null) {
		
		if ($label) {
			
			echo "<pre> ----------- $label ----------- </pre>";
			
		}

		echo '<pre>';
		
		foreach($matrix as $row) {
			
			echo '|	';
			
			foreach($row as $num) {
				
				echo $num . '	';
				
			}
			
			echo '	|<br />';
			
		}
		
		echo '</pre>';

		if ($label) {
			
			echo '<pre> --------------------------------- </pre><br />';
			
		}
	}


}

