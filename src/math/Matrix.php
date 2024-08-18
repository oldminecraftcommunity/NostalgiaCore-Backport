<?php

class Matrix implements ArrayAccess{

	private $matrix = [];
	private $rows = 0;
	private $columns = 0;

	public function __construct($rows, $columns, array $set = []){
		$this->rows = max(1, (int) $rows);
		$this->columns = max(1, (int) $columns);
		$this->set($set);
	}

	public function set(array $m){
		for($r = 0; $r < $this->rows; ++$r){
			$this->matrix[$r] = [];
			for($c = 0; $c < $this->columns; ++$c){
				$this->matrix[$r][$c] = $m[$r][$c] ?? 0;
			}
		}
	}
	#[\ReturnTypeWillChange]
	public function offsetExists($offset){
		return isset($this->matrix[(int) $offset]);
	}
	#[\ReturnTypeWillChange]
	public function offsetGet($offset){
		return $this->matrix[(int) $offset];
	}
	#[\ReturnTypeWillChange]
	public function offsetSet($offset, $value){
		$this->matrix[(int) $offset] = $value;
	}
	#[\ReturnTypeWillChange]
	public function offsetUnset($offset){
		unset($this->matrix[(int) $offset]);
	}

	public function add(Matrix $matrix){
		if($this->rows !== $matrix->getRows() or $this->columns !== $matrix->getColumns()){
			return false;
		}
		$result = new Matrix($this->rows, $this->columns);
		for($r = 0; $r < $this->rows; ++$r){
			for($c = 0; $c < $this->columns; ++$c){
				$result->setElement($r, $c, $this->matrix[$r][$c] + $matrix->getElement($r, $c));
			}
		}
		return $result;
	}

	public function getRows(){
		return ($this->rows);
	}

	public function getColumns(){
		return ($this->columns);
	}

	public function setElement($row, $column, $value){
		if($row > $this->rows or $row < 0 or $column > $this->columns or $column < 0){
			return false;
		}
		$this->matrix[(int) $row][(int) $column] = $value;
		return true;
	}

	public function getElement($row, $column){
		if($row > $this->rows or $row < 0 or $column > $this->columns or $column < 0){
			return false;
		}
		return $this->matrix[(int) $row][(int) $column];
	}

	public function substract(Matrix $matrix){
		if($this->rows !== $matrix->getRows() or $this->columns !== $matrix->getColumns()){
			return false;
		}
		$result = clone $this;
		for($r = 0; $r < $this->rows; ++$r){
			for($c = 0; $c < $this->columns; ++$c){
				$result->setElement($r, $c, $this->matrix[$r][$c] - $matrix->getElement($r, $c));
			}
		}
		return $result;
	}

	public function multiplyScalar($number){
		$result = clone $this;
		for($r = 0; $r < $this->rows; ++$r){
			for($c = 0; $c < $this->columns; ++$c){
				$result->setElement($r, $c, $this->matrix[$r][$c] * $number);
			}
		}
		return $result;
	}

	public function divideScalar($number){
		$result = clone $this;
		for($r = 0; $r < $this->rows; ++$r){
			for($c = 0; $c < $this->columns; ++$c){
				$result->setElement($r, $c, $this->matrix[$r][$c] / $number);
			}
		}
		return $result;
	}

	public function transpose(){
		$result = new Matrix($this->columns, $this->rows);
		for($r = 0; $r < $this->rows; ++$r){
			for($c = 0; $c < $this->columns; ++$c){
				$result->setElement($c, $r, $this->matrix[$r][$c]);
			}
		}
		return $result;
	}

	public function product(Matrix $matrix){
		if($this->columns !== $matrix->getRows()){
			return false;
		}
		$c = $matrix->getColumns();
		$result = new Matrix($this->rows, $c);
		for($i = 0; $i < $this->rows; ++$i){
			for($j = 0; $j < $c; ++$j){
				$sum = 0;
				for($k = 0; $k < $this->columns; ++$k){
					$sum += $this->matrix[$i][$k] * $matrix->getElement($k, $j);
				}
				$result->setElement($i, $j, $sum);
			}
		}
		return $result;
	}

	//Naive Matrix product, O(n^3)

	public function determinant(){
		if($this->isSquare() !== true){
			return false;
		}
		return match ($this->rows) {
			1 => 0,
			2 => $this->matrix[0][0] * $this->matrix[1][1] - $this->matrix[0][1] * $this->matrix[1][0],
			3 => $this->matrix[0][0] * $this->matrix[1][1] * $this->matrix[2][2] + $this->matrix[0][1] * $this->matrix[1][2] * $this->matrix[2][0] + $this->matrix[0][2] * $this->matrix[1][0] * $this->matrix[2][1] - $this->matrix[2][0] * $this->matrix[1][1] * $this->matrix[0][2] - $this->matrix[2][1] * $this->matrix[1][2] * $this->matrix[0][0] - $this->matrix[2][2] * $this->matrix[1][0] * $this->matrix[0][1],
			default => false,
		};
	}


	//Computation of the determinant of 2x2 and 3x3 matrices

	public function isSquare(){
		return $this->rows === $this->columns;
	}

	public function __toString(){
		$s = "";
		for($r = 0; $r < $this->rows; ++$r){
			$s .= implode(",", $this->matrix[$r]) . ";";
		}
		return "Matrix({$this->rows}x{$this->columns};" . substr($s, 0, -1) . ")";
	}

}
